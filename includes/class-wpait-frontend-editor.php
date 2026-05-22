<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Frontend_Editor
{
    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
        add_action('wp_ajax_wpait_save_translation', array(__CLASS__, 'save_translation'));
        add_action('wp_ajax_wpait_auto_translate_frontend', array(__CLASS__, 'auto_translate'));
    }

    public static function enabled(): bool
    {
        return '1' === WPAIT_Settings::get('frontend_editor', '1')
            && current_user_can('manage_options')
            && WPAIT_Router::current_language() !== WPAIT_Settings::source_language();
    }

    public static function enqueue() {
        if (!self::enabled()) {
            return;
        }

        wp_enqueue_style('wpait-frontend', WPAIT_PLUGIN_URL . 'assets/css/frontend.css', array(), WPAIT_VERSION);
        wp_enqueue_script('wpait-frontend-editor', WPAIT_PLUGIN_URL . 'assets/js/frontend-editor.js', array(), WPAIT_VERSION, true);
        wp_localize_script(
            'wpait-frontend-editor',
            'WPAIT_EDITOR',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpait_frontend_editor'),
                'sourceLanguage' => WPAIT_Settings::source_language(),
                'targetLanguage' => WPAIT_Router::current_language(),
                'editLabel' => __('AI Translate edit', 'wpait-multilingual-ai-translate'),
                'promptLabel' => __('Edit translation', 'wpait-multilingual-ai-translate'),
                'autoTranslateLabel' => __('Auto Translate', 'wpait-multilingual-ai-translate'),
                'translatingLabel' => __('AI translating...', 'wpait-multilingual-ai-translate'),
                'translationReadyLabel' => __('AI Translation Ready', 'wpait-multilingual-ai-translate'),
                'translateFailedLabel' => __('Translation failed. Please try again.', 'wpait-multilingual-ai-translate'),
                'savingLabel' => __('Saving...', 'wpait-multilingual-ai-translate'),
                'savedLabel' => __('Saved', 'wpait-multilingual-ai-translate'),
                'errorLabel' => __('Could not save translation', 'wpait-multilingual-ai-translate'),
            )
        );
    }

    public static function save_translation() {
        check_ajax_referer('wpait_frontend_editor', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'wpait-multilingual-ai-translate')), 403);
        }

        $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
        $translated_text = isset($_POST['translatedText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['translatedText'])) : '';
        $source_language = isset($_POST['sourceLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : WPAIT_Settings::source_language();
        $target_language = isset($_POST['targetLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : '';

        if ('' === $source_text || '' === $translated_text || '' === $target_language) {
            wp_send_json_error(array('message' => __('Missing translation data.', 'wpait-multilingual-ai-translate')), 400);
        }

        $saved = WPAIT_Translations::save($source_text, $translated_text, $source_language, $target_language, 'html', 'manual', 'manual');

        if (!$saved) {
            wp_send_json_error(array('message' => __('Translation was not saved.', 'wpait-multilingual-ai-translate')), 500);
        }

        wp_send_json_success(array('message' => __('Translation saved.', 'wpait-multilingual-ai-translate')));
    }

    public static function auto_translate() {
        check_ajax_referer('wpait_frontend_editor', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'wpait-multilingual-ai-translate')), 403);
        }

        $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
        $source_language = isset($_POST['sourceLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : WPAIT_Settings::source_language();
        $target_language = isset($_POST['targetLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : '';

        if ('' === $source_text || '' === $target_language) {
            wp_send_json_error(array('message' => __('Missing translation data.', 'wpait-multilingual-ai-translate')), 400);
        }

        if ($source_language === $target_language) {
            wp_send_json_error(array('message' => __('Source and target languages are the same.', 'wpait-multilingual-ai-translate')), 400);
        }

        if ('' === WPAIT_Settings::openai_api_key()) {
            wp_send_json_error(array('message' => __('No translation provider configured.', 'wpait-multilingual-ai-translate')), 400);
        }

        $hash = WPAIT_Translations::hash($source_text);
        $provider = new WPAIT_OpenAI_Provider();
        $translated = $provider->translate_batch(array($hash => $source_text), $source_language, $target_language);

        if (is_wp_error($translated)) {
            $error_data = $translated->get_error_data();
            $status = is_array($error_data) && !empty($error_data['status']) ? absint($error_data['status']) : 500;
            $message = self::provider_error_message_for_editor($translated, $status);

            wp_send_json_error(array('message' => $message), $status);
        }

        if (!is_array($translated) || !array_key_exists($hash, $translated) || '' === trim((string) $translated[$hash])) {
            wp_send_json_error(array('message' => __('Translation failed. Please try again.', 'wpait-multilingual-ai-translate')), 500);
        }

        wp_send_json_success(
            array(
                'translation' => (string) $translated[$hash],
                'message' => __('Translated via OpenAI', 'wpait-multilingual-ai-translate'),
                'provider' => 'OpenAI',
            )
        );
    }

    private static function provider_error_message_for_editor($error, int $status = 500): string
    {
        if (!$error instanceof WP_Error) {
            return __('Translation failed. Please try again.', 'wpait-multilingual-ai-translate');
        }

        $code = $error->get_error_code();
        $message = strtolower($error->get_error_message());

        if (false !== strpos($code, 'missing') || false !== strpos($message, 'api key is missing')) {
            return __('No translation provider configured.', 'wpait-multilingual-ai-translate');
        }

        if (429 === $status || false !== strpos($code, 'quota') || false !== strpos($code, 'cooldown') || false !== strpos($message, 'rate limit') || false !== strpos($message, 'quota')) {
            return __('Provider rate limit reached.', 'wpait-multilingual-ai-translate');
        }

        return __('Translation failed. Please try again.', 'wpait-multilingual-ai-translate');
    }
}

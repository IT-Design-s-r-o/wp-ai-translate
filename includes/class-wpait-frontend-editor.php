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
                'editLabel' => __('AI Translate edit', 'ai-translate-woocommerce-elementor'),
                'promptLabel' => __('Edit translation', 'ai-translate-woocommerce-elementor'),
                'autoTranslateLabel' => __('Auto Translate', 'ai-translate-woocommerce-elementor'),
                'translatingLabel' => __('AI translating...', 'ai-translate-woocommerce-elementor'),
                'translationReadyLabel' => __('AI Translation Ready', 'ai-translate-woocommerce-elementor'),
                'translateFailedLabel' => __('Translation failed. Please try again.', 'ai-translate-woocommerce-elementor'),
                'savingLabel' => __('Saving...', 'ai-translate-woocommerce-elementor'),
                'savedLabel' => __('Saved', 'ai-translate-woocommerce-elementor'),
                'errorLabel' => __('Could not save translation', 'ai-translate-woocommerce-elementor'),
            )
        );
    }

    public static function save_translation() {
        check_ajax_referer('wpait_frontend_editor', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ai-translate-woocommerce-elementor')), 403);
        }

        $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
        $translated_text = isset($_POST['translatedText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['translatedText'])) : '';
        $source_language = isset($_POST['sourceLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : WPAIT_Settings::source_language();
        $target_language = isset($_POST['targetLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : '';

        if ('' === $source_text || '' === $translated_text || '' === $target_language) {
            wp_send_json_error(array('message' => __('Missing translation data.', 'ai-translate-woocommerce-elementor')), 400);
        }

        $saved = WPAIT_Translations::save($source_text, $translated_text, $source_language, $target_language, 'html', 'manual', 'manual');

        if (!$saved) {
            wp_send_json_error(array('message' => __('Translation was not saved.', 'ai-translate-woocommerce-elementor')), 500);
        }

        wp_send_json_success(array('message' => __('Translation saved.', 'ai-translate-woocommerce-elementor')));
    }

    public static function auto_translate() {
        check_ajax_referer('wpait_frontend_editor', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ai-translate-woocommerce-elementor')), 403);
        }

        $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
        $source_language = isset($_POST['sourceLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : WPAIT_Settings::source_language();
        $target_language = isset($_POST['targetLanguage']) ? WPAIT_Languages::normalize_code(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : '';

        if ('' === $source_text || '' === $target_language) {
            wp_send_json_error(array('message' => __('Missing translation data.', 'ai-translate-woocommerce-elementor')), 400);
        }

        if ($source_language === $target_language) {
            wp_send_json_error(array('message' => __('Source and target languages are the same.', 'ai-translate-woocommerce-elementor')), 400);
        }

        if ('' === WPAIT_Settings::openai_api_key()) {
            wp_send_json_error(array('message' => __('No translation provider configured.', 'ai-translate-woocommerce-elementor')), 400);
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
            wp_send_json_error(array('message' => __('Translation failed. Please try again.', 'ai-translate-woocommerce-elementor')), 500);
        }

        wp_send_json_success(
            array(
                'translation' => (string) $translated[$hash],
                'message' => __('Translated via OpenAI', 'ai-translate-woocommerce-elementor'),
                'provider' => 'OpenAI',
            )
        );
    }

    private static function provider_error_message_for_editor($error, int $status = 500): string
    {
        if (!$error instanceof WP_Error) {
            return __('Translation failed. Please try again.', 'ai-translate-woocommerce-elementor');
        }

        $code = $error->get_error_code();
        $message = strtolower($error->get_error_message());

        if (false !== strpos($code, 'missing') || false !== strpos($message, 'api key is missing')) {
            return __('No translation provider configured.', 'ai-translate-woocommerce-elementor');
        }

        if (429 === $status || false !== strpos($code, 'quota') || false !== strpos($code, 'cooldown') || false !== strpos($message, 'rate limit') || false !== strpos($message, 'quota')) {
            return __('Provider rate limit reached.', 'ai-translate-woocommerce-elementor');
        }

        return __('Translation failed. Please try again.', 'ai-translate-woocommerce-elementor');
    }
}

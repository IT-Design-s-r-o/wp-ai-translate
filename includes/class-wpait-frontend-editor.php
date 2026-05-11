<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Frontend_Editor
{
    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
        add_action('wp_ajax_wpait_save_translation', array(__CLASS__, 'save_translation'));
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
                'editLabel' => __('AI Translate edit', 'wp-ai-translate'),
                'promptLabel' => __('Edit translation', 'wp-ai-translate'),
                'savedLabel' => __('Saved', 'wp-ai-translate'),
                'errorLabel' => __('Could not save translation', 'wp-ai-translate'),
            )
        );
    }

    public static function save_translation() {
        check_ajax_referer('wpait_frontend_editor', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'wp-ai-translate')), 403);
        }

        $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
        $translated_text = isset($_POST['translatedText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['translatedText'])) : '';
        $source_language = isset($_POST['sourceLanguage']) ? WPAIT_Languages::normalize_code(wp_unslash((string) $_POST['sourceLanguage'])) : WPAIT_Settings::source_language();
        $target_language = isset($_POST['targetLanguage']) ? WPAIT_Languages::normalize_code(wp_unslash((string) $_POST['targetLanguage'])) : '';

        if ('' === $source_text || '' === $translated_text || '' === $target_language) {
            wp_send_json_error(array('message' => __('Missing translation data.', 'wp-ai-translate')), 400);
        }

        $saved = WPAIT_Translations::save($source_text, $translated_text, $source_language, $target_language, 'html', 'manual', 'manual');

        if (!$saved) {
            wp_send_json_error(array('message' => __('Translation was not saved.', 'wp-ai-translate')), 500);
        }

        wp_send_json_success(array('message' => __('Translation saved.', 'wp-ai-translate')));
    }
}


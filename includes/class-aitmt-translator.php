<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AITMT_Translator
{
    public static function translate_segments(array $segments, string $target_language, string $context = 'html'): array
    {
        $target_language = AITMT_Languages::normalize_code($target_language);
        $source_language = AITMT_Settings::source_language();

        if ($target_language === $source_language || empty($segments)) {
            return array();
        }

        $clean_segments = array();
        foreach ($segments as $hash => $text) {
            $text = AITMT_Translations::normalize_text((string) $text);
            if (self::is_translatable_text($text)) {
                $clean_segments[$hash] = $text;
            }
        }

        if (empty($clean_segments)) {
            return array();
        }

        $existing = AITMT_Translations::get_existing_map($clean_segments, $source_language, $target_language, $context);
        $missing = array_diff_key($clean_segments, $existing);

        if (!empty($missing) && '1' === AITMT_Settings::get('auto_translate', '1')) {
            $limit = absint(AITMT_Settings::get('max_segments_per_request', 40));
            $missing = array_slice($missing, 0, max(1, $limit), true);

            $provider = new AITMT_OpenAI_Provider();
            $translated = $provider->translate_batch($missing, $source_language, $target_language);

            if (!is_wp_error($translated) && is_array($translated)) {
                $status = '1' === AITMT_Settings::get('draft_mode', '0') ? 'draft' : 'published';
                AITMT_Translations::save_batch($missing, $translated, $source_language, $target_language, $context, $status, 'openai');

                if ('draft' !== $status || current_user_can('manage_options')) {
                    $existing = array_merge($existing, $translated);
                }
            }
        }

        return $existing;
    }

    public static function is_translatable_text(string $text): bool
    {
        $text = trim($text);

        $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        if ($length < 2) {
            return false;
        }

        if (preg_match('/^[\d\s[:punct:]]+$/u', $text)) {
            return false;
        }

        if (preg_match('/^\{.*\}$/s', $text) || preg_match('/^\[.*\]$/s', $text)) {
            return false;
        }

        return true;
    }
}

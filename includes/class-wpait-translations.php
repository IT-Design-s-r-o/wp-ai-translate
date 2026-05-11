<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Translations
{
    public static function table(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'wpait_translations';
    }

    public static function normalize_text(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return null === $text ? '' : $text;
    }

    public static function hash(string $text): string
    {
        return hash('sha256', self::normalize_text($text));
    }

    public static function get_existing_map(array $segments, string $source_language, string $target_language, string $context = 'html'): array
    {
        global $wpdb;

        if (empty($segments)) {
            return array();
        }

        $hashes = array_keys($segments);
        $placeholders = implode(',', array_fill(0, count($hashes), '%s'));
        $args = array_merge($hashes, array($source_language, $target_language, $context));

        $query = $wpdb->prepare(
            "SELECT source_hash, translated_text FROM " . self::table() . " WHERE source_hash IN ({$placeholders}) AND source_language = %s AND target_language = %s AND context = %s AND status IN ('published', 'manual')",
            $args
        );

        $rows = $wpdb->get_results($query, ARRAY_A);
        $map = array();

        foreach ((array) $rows as $row) {
            $map[$row['source_hash']] = $row['translated_text'];
        }

        return $map;
    }

    public static function save(string $source_text, string $translated_text, string $source_language, string $target_language, string $context = 'html', string $status = 'published', string $provider = 'manual', $object_id = null): bool
    {
        global $wpdb;

        $source_text = self::normalize_text($source_text);
        $translated_text = trim($translated_text);

        if ('' === $source_text || '' === $translated_text) {
            return false;
        }

        $now = current_time('mysql');

        $result = $wpdb->replace(
            self::table(),
            array(
                'source_hash' => self::hash($source_text),
                'source_language' => WPAIT_Languages::normalize_code($source_language),
                'target_language' => WPAIT_Languages::normalize_code($target_language),
                'context' => sanitize_key($context),
                'object_id' => $object_id,
                'source_text' => $source_text,
                'translated_text' => $translated_text,
                'status' => sanitize_key($status),
                'provider' => sanitize_key($provider),
                'created_at' => $now,
                'updated_at' => $now,
            ),
            array('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        return false !== $result;
    }

    public static function save_batch(array $source_segments, array $translated_segments, string $source_language, string $target_language, string $context = 'html', string $status = 'published', string $provider = 'openai') {
        foreach ($translated_segments as $hash => $translation) {
            if (!isset($source_segments[$hash])) {
                continue;
            }

            self::save($source_segments[$hash], (string) $translation, $source_language, $target_language, $context, $status, $provider);
        }
    }
}

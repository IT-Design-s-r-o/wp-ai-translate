<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Activator
{
    public static function activate() {
        self::create_tables();
        update_option('wpait_rewrite_needs_flush', 1, false);
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;

        $table = $wpdb->prefix . 'wpait_translations';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            source_hash char(64) NOT NULL,
            source_language varchar(16) NOT NULL,
            target_language varchar(16) NOT NULL,
            context varchar(80) NOT NULL DEFAULT 'html',
            object_id bigint(20) unsigned NULL,
            source_text longtext NOT NULL,
            translated_text longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'published',
            provider varchar(40) NOT NULL DEFAULT 'manual',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY wpait_lookup (source_hash, source_language, target_language, context),
            KEY target_language (target_language),
            KEY status (status)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}


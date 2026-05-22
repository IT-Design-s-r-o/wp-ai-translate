<?php
/**
 * Uninstall cleanup for AIT Multilingual Translate.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!defined('AITMT_REMOVE_DATA_ON_UNINSTALL') || !AITMT_REMOVE_DATA_ON_UNINSTALL) {
    return;
}

global $wpdb;
delete_option('aitmt_options');
delete_option('aitmt_rewrite_needs_flush');

$aitmt_table_name = esc_sql($wpdb->prefix . 'aitmt_translations');
if (preg_match('/^[A-Za-z0-9_]+$/', $aitmt_table_name)) {
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- The table name is validated and escaped above; SQL placeholders cannot be used for identifiers.
    $wpdb->query("DROP TABLE IF EXISTS `{$aitmt_table_name}`");
}

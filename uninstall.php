<?php
/**
 * Uninstall cleanup for AI Translate for WooCommerce & Elementor.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!defined('WPAIT_REMOVE_DATA_ON_UNINSTALL') || !WPAIT_REMOVE_DATA_ON_UNINSTALL) {
    return;
}

global $wpdb;
delete_option('wpait_options');
delete_option('wpait_rewrite_needs_flush');

$wpait_table_name = esc_sql($wpdb->prefix . 'wpait_translations');
if (preg_match('/^[A-Za-z0-9_]+$/', $wpait_table_name)) {
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- The table name is validated and escaped above; SQL placeholders cannot be used for identifiers.
    $wpdb->query("DROP TABLE IF EXISTS `{$wpait_table_name}`");
}

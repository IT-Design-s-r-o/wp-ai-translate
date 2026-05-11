<?php
/**
 * Uninstall cleanup for WP AI Translate.
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
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpait_translations");

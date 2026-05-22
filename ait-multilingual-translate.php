<?php
/**
 * Plugin Name: AIT Multilingual Translate
 * Description: AI-powered multilingual translation plugin compatible with WooCommerce and Elementor, with SEO-friendly URLs and frontend editing support.
 * Plugin URI: https://wp-ai.itdesign.biz
 * Version: 0.3.32
 * Author: sotter IT Design
 * Author URI: https://wp-ai.itdesign.biz
 * Text Domain: ait-multilingual-translate
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AITMT_VERSION', '0.3.32');
define('AITMT_PLUGIN_FILE', __FILE__);
define('AITMT_PUBLIC_NAME', 'AIT Multilingual Translate');
define('AITMT_PUBLIC_SLUG', 'ait-multilingual-translate');

if (!defined('AITMT_EDITION')) {
    define('AITMT_EDITION', 'public_beta');
}

define('AITMT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AITMT_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('AITMT_USE_LEGACY_FULL_ENGINE') || !AITMT_USE_LEGACY_FULL_ENGINE) {
    register_activation_hook(__FILE__, 'aitmt_fallback_activate');
    register_deactivation_hook(__FILE__, 'aitmt_fallback_deactivate');
    aitmt_register_fallback_admin();

    return;
}

if (!file_exists(AITMT_PLUGIN_DIR . 'includes/class-aitmt-activator.php')) {
    aitmt_register_fallback_admin();

    return;
}

require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-activator.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-languages.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-settings.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-translations.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-openai-provider.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-translator.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-router.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-switcher.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-frontend-editor.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-output-buffer.php';
require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-plugin.php';

register_activation_hook(__FILE__, array('AITMT_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('AITMT_Activator', 'deactivate'));

add_action('plugins_loaded', array('AITMT_Plugin', 'init'));

function aitmt_register_fallback_admin()
{
    aitmt_fallback_prime_language_request();

    add_action('admin_menu', 'aitmt_fallback_admin_menu');
    add_action('admin_enqueue_scripts', 'aitmt_fallback_enqueue_admin_icon_assets');
    add_action('load-nav-menus.php', 'aitmt_fallback_add_nav_menu_metabox');
    add_action('admin_head-nav-menus.php', 'aitmt_fallback_add_nav_menu_metabox');
    add_action('admin_init', 'aitmt_fallback_maybe_redirect_onboarding');
    add_action('admin_enqueue_scripts', 'aitmt_fallback_enqueue_admin_assets');
    add_action('admin_init', 'aitmt_fallback_register_settings');
    add_action('admin_init', 'aitmt_fallback_maybe_create_translation_table');
    add_action('admin_init', 'aitmt_fallback_maybe_flush_rewrites');
    add_action('admin_post_aitmt_save_settings', 'aitmt_fallback_save_settings_handler');
    add_action('admin_post_aitmt_debug_settings', 'aitmt_fallback_debug_settings_handler');
    add_action('admin_post_aitmt_debug_test', 'aitmt_fallback_debug_test_handler');
    add_action('admin_post_aitmt_debug_clear', 'aitmt_fallback_debug_clear_handler');
    add_action('admin_post_aitmt_debug_download', 'aitmt_fallback_debug_download_handler');
    add_action('admin_post_aitmt_clear_provider_cooldown', 'aitmt_fallback_clear_provider_cooldown_handler');
    add_action('admin_post_aitmt_submit_bug_report', 'aitmt_fallback_submit_bug_report_handler');
    add_action('admin_post_aitmt_submit_feedback', 'aitmt_fallback_submit_feedback_handler');
    add_action('admin_post_aitmt_onboarding_save', 'aitmt_fallback_onboarding_save_handler');
    add_action('admin_post_aitmt_onboarding_finish', 'aitmt_fallback_onboarding_finish_handler');
    add_action('admin_post_aitmt_scan_site', 'aitmt_fallback_scan_site_handler');
    add_action('admin_post_aitmt_save_manual_translation', 'aitmt_fallback_save_manual_translation_handler');
    add_action('admin_post_aitmt_save_translation_matrix', 'aitmt_fallback_save_translation_matrix_handler');
    add_action('admin_post_aitmt_export_translations', 'aitmt_fallback_export_translations_handler');
    add_action('admin_post_aitmt_import_translations', 'aitmt_fallback_import_translations_handler');
    add_action('admin_post_aitmt_process_queue', 'aitmt_fallback_process_queue_handler');
    add_action('admin_post_aitmt_translate_all_queue', 'aitmt_fallback_translate_all_queue_handler');
    add_action('admin_post_aitmt_clear_queue', 'aitmt_fallback_clear_queue_handler');
    add_action('wp_ajax_aitmt_save_translation', 'aitmt_fallback_ajax_save_translation');
    add_action('wp_ajax_aitmt_fallback_save_translation', 'aitmt_fallback_ajax_save_translation');
    add_action('wp_ajax_aitmt_auto_translate_frontend', 'aitmt_fallback_ajax_auto_translate_frontend');
    add_action('init', 'aitmt_fallback_register_rewrites');
    add_filter('request', 'aitmt_fallback_filter_language_request', 0);
    add_filter('query_vars', 'aitmt_fallback_query_vars');
    add_filter('redirect_canonical', 'aitmt_fallback_disable_language_canonical_redirect', 10, 2);
    add_action('template_redirect', 'aitmt_fallback_capture_route_debug', -6);
    add_action('template_redirect', 'aitmt_fallback_redirect_conflicting_language_url', -4);
    add_action('template_redirect', 'aitmt_fallback_redirect_language_home', -1);
    add_action('template_redirect', 'aitmt_fallback_remember_language', -2);
    add_action('template_redirect', 'aitmt_fallback_start_translation', 0);
    add_action('wp_enqueue_scripts', 'aitmt_fallback_enqueue_frontend_editor');
    add_action('wp_body_open', 'aitmt_fallback_render_header_switcher');
    add_action('wp_footer', 'aitmt_fallback_render_footer_switcher');
    add_action('admin_bar_menu', 'aitmt_fallback_admin_bar_editor_node', 100);
    add_action('widgets_init', 'aitmt_fallback_register_widget');
    add_action('elementor/widgets/register', 'aitmt_fallback_register_elementor_widget');
    add_action('elementor/widgets/widgets_registered', 'aitmt_fallback_register_elementor_widget_legacy');
    add_action('save_post', 'aitmt_fallback_scan_saved_post', 20, 3);
    add_action('aitmt_fallback_process_queue_event', 'aitmt_fallback_cron_process_queue');
    add_filter('cron_schedules', 'aitmt_fallback_cron_schedules');
    add_filter('wp_nav_menu_objects', 'aitmt_fallback_nav_menu_objects', 20, 2);
    add_filter('nav_menu_item_title', 'aitmt_fallback_nav_menu_item_title', 20, 4);
    add_filter('nav_menu_link_attributes', 'aitmt_fallback_nav_menu_link_attributes', 20, 4);
    add_shortcode('aitmt_language_switcher', 'aitmt_fallback_shortcode');
    add_filter('plugin_action_links_' . plugin_basename(AITMT_PLUGIN_FILE), 'aitmt_fallback_plugin_links');
}

function aitmt_fallback_prime_language_request()
{
    if (is_admin() || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_CRON') && DOING_CRON)) {
        return;
    }

    if (empty($_SERVER['REQUEST_URI'])) {
        return;
    }

    $request_uri = sanitize_text_field(wp_unslash((string) $_SERVER['REQUEST_URI']));
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);

    if ('' === $path || aitmt_fallback_is_untranslated_system_path($path)) {
        return;
    }

    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');

    if ('' === $relative) {
        return;
    }

    $segments = array_values(array_filter(explode('/', $relative), 'strlen'));
    $enabled = aitmt_fallback_enabled_languages();
    $path_languages = array();

    while (isset($segments[0])) {
        $language = aitmt_fallback_normalize_language(rawurldecode((string) $segments[0]));
        if (!$language || !in_array($language, $enabled, true)) {
            break;
        }

        $path_languages[] = $language;
        array_shift($segments);
        $segments = array_values($segments);
    }

    if (empty($path_languages)) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public language switching is a read-only GET action.
    $requested_language = isset($_GET['lang']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_GET['lang']))) : '';
    $language = ($requested_language && in_array($requested_language, $enabled, true)) ? $requested_language : (string) $path_languages[0];
    $query = (string) wp_parse_url($request_uri, PHP_URL_QUERY);
    $stripped_relative = implode('/', $segments);
    $base_path = '/' === $home_path ? '' : '/' . trim($home_path, '/');
    $new_path = '' === $stripped_relative ? ($base_path ? $base_path . '/' : '/') : $base_path . '/' . $stripped_relative;

    if ('/' !== $path && '/' === substr($path, -1) && '/' !== substr($new_path, -1)) {
        $new_path .= '/';
    }

    $GLOBALS['aitmt_original_request_uri'] = $request_uri;
    $GLOBALS['aitmt_request_language'] = $language;
    $GLOBALS['aitmt_path_language'] = (string) $path_languages[0];
    $GLOBALS['aitmt_request_path_languages'] = $path_languages;
    $GLOBALS['aitmt_stripped_relative_path'] = $stripped_relative;
    $_SERVER['REQUEST_URI'] = $new_path . ('' !== $query ? '?' . $query : '');

    foreach (array('REDIRECT_URL', 'PATH_INFO') as $server_key) {
        if (empty($_SERVER[$server_key])) {
            continue;
        }

        $server_path = sanitize_text_field(wp_unslash((string) $_SERVER[$server_key]));
        $server_relative = trim(aitmt_fallback_strip_home_path($server_path, $home_path), '/');
        $server_segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $server_relative), 'strlen')));
        $server_new_path = empty($server_segments) ? ($base_path ? $base_path . '/' : '/') : $base_path . '/' . implode('/', $server_segments);

        if ('/' !== $server_path && '/' === substr($server_path, -1) && '/' !== substr($server_new_path, -1)) {
            $server_new_path .= '/';
        }

        $_SERVER[$server_key] = $server_new_path;
    }
}

function aitmt_fallback_request_uri($original = false)
{
    if ($original && !empty($GLOBALS['aitmt_original_request_uri'])) {
        return (string) $GLOBALS['aitmt_original_request_uri'];
    }

    return isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash((string) $_SERVER['REQUEST_URI'])) : '/';
}

function aitmt_fallback_is_untranslated_system_path($path)
{
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path((string) $path, $home_path), '/');
    $segments = array_values(array_filter(explode('/', $relative), 'strlen'));

    if (empty($segments)) {
        return false;
    }

    $first = strtolower(rawurldecode((string) $segments[0]));

    return in_array($first, array('wp-admin', 'wp-login.php', 'wp-json', 'wp-content', 'wp-includes', 'xmlrpc.php'), true);
}

function aitmt_fallback_is_static_asset_path($path)
{
    $path = (string) wp_parse_url((string) $path, PHP_URL_PATH);

    return (bool) preg_match('/\.(css|js|mjs|json|map|png|jpg|jpeg|gif|webp|svg|ico|xml|txt|pdf|zip|woff|woff2|ttf|eot|mp4|mp3|webm)$/i', $path);
}

function aitmt_fallback_admin_menu()
{
    add_menu_page(
        __('AI Translate', 'ait-multilingual-translate'),
        __('AI Translate', 'ait-multilingual-translate'),
        'manage_options',
        AITMT_PUBLIC_SLUG,
        'aitmt_fallback_settings_page',
        aitmt_fallback_menu_icon_url(),
        58
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('AIT Multilingual Translate', 'ait-multilingual-translate'),
        __('Dashboard', 'ait-multilingual-translate'),
        'manage_options',
        AITMT_PUBLIC_SLUG,
        'aitmt_fallback_settings_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Translations', 'ait-multilingual-translate'),
        __('Translations', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-translations',
        'aitmt_fallback_translations_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Scanner', 'ait-multilingual-translate'),
        __('Scanner', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-scanner',
        'aitmt_fallback_scanner_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Report Bug', 'ait-multilingual-translate'),
        __('Report Bug', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-report-bug',
        'aitmt_fallback_report_bug_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Feedback', 'ait-multilingual-translate'),
        __('Feedback', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-feedback',
        'aitmt_fallback_feedback_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Setup Wizard', 'ait-multilingual-translate'),
        __('Setup Wizard', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-onboarding',
        'aitmt_fallback_onboarding_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Support', 'ait-multilingual-translate'),
        __('Support', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-support',
        'aitmt_fallback_support_page'
    );

    add_submenu_page(
        AITMT_PUBLIC_SLUG,
        __('Debugger', 'ait-multilingual-translate'),
        __('Debugger', 'ait-multilingual-translate'),
        'manage_options',
        'ait-multilingual-translate-debugger',
        'aitmt_fallback_debugger_page'
    );
}

function aitmt_fallback_enqueue_admin_icon_assets()
{
    wp_register_style('aitmt-admin-icon', false, array(), AITMT_VERSION);
    wp_enqueue_style('aitmt-admin-icon');
    wp_add_inline_style('aitmt-admin-icon', '.toplevel_page_ait-multilingual-translate .wp-menu-image img{height:20px!important;object-fit:contain;padding-top:7px!important;width:20px!important}');
}

function aitmt_fallback_menu_icon_url()
{
    if (file_exists(AITMT_PLUGIN_DIR . 'assets/img/logo.png')) {
        return AITMT_PLUGIN_URL . 'assets/img/logo.png';
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path fill="#a7aaad" d="M3 2h13.5c.8 0 1.5.7 1.5 1.5V7h-4v11h-4V7H6v11H2V3c0-.6.4-1 1-1Zm3 7v5H4V9h2Zm9-5H4v1.5h11V4Z"/></svg>';

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function aitmt_fallback_logo_url()
{
    if (file_exists(AITMT_PLUGIN_DIR . 'assets/img/logo.png')) {
        return AITMT_PLUGIN_URL . 'assets/img/logo.png';
    }

    return aitmt_fallback_embedded_logo_url();
}

function aitmt_fallback_embedded_logo_url()
{
    return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48cmVjdCB4PSIzIiB5PSIzIiB3aWR0aD0iODgiIGhlaWdodD0iOTQiIHJ4PSI3IiBmaWxsPSIjZmZjOTI4Ii8+PHBhdGggZmlsbD0iIzAwMCIgZD0iTTE2IDg3YzAtOSA3LTE2IDE2LTE2aDR2MjZIMTZWODdabTE4LTYzaDUyYzYgMCAxMCA0IDEwIDEwdjEySDc2djUxSDUyVjQ2SDM0Yy02IDAtMTAtNC0xMC0xMHYtMmMwLTYgNC0xMCAxMC0xMFoiLz48cGF0aCBmaWxsPSIjZmZmIiBkPSJNOTEgM2g2djM4aC02eiIvPjwvc3ZnPg==';
}

function aitmt_fallback_asset_contents($relative_path)
{
    $path = AITMT_PLUGIN_DIR . ltrim((string) $relative_path, '/');

    return aitmt_fallback_read_local_file($path);
}

function aitmt_fallback_filesystem()
{
    global $wp_filesystem;

    if (!function_exists('WP_Filesystem')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if (!$wp_filesystem) {
        WP_Filesystem();
    }

    return $wp_filesystem;
}

function aitmt_fallback_read_local_file($path)
{
    $filesystem = aitmt_fallback_filesystem();
    $path = wp_normalize_path((string) $path);

    if (!$filesystem || !$filesystem->exists($path) || !$filesystem->is_readable($path)) {
        return '';
    }

    $contents = $filesystem->get_contents($path);

    return is_string($contents) ? $contents : '';
}

function aitmt_fallback_write_local_file($path, $contents)
{
    $filesystem = aitmt_fallback_filesystem();
    $path = wp_normalize_path((string) $path);

    if (!$filesystem) {
        return false;
    }

    return (bool) $filesystem->put_contents($path, (string) $contents, defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : 0644);
}

function aitmt_fallback_edition_label()
{
    return __('Public Beta Build', 'ait-multilingual-translate');
}

function aitmt_fallback_is_professional()
{
    return false;
}

function aitmt_is_pro()
{
    return (bool) apply_filters('aitmt_is_pro', aitmt_fallback_is_professional());
}

function aitmt_has_feature($feature)
{
    $feature = sanitize_key((string) $feature);
    $features = array(
        'unlimited_languages',
        'bulk_translate',
        'translation_memory',
        'advanced_woocommerce',
        'seo_translation',
        'slug_translation',
        'export_import',
        'priority_support',
    );

    return (bool) apply_filters('aitmt_has_feature', true, $feature, $features);
}

function aitmt_fallback_admin_page_slugs()
{
    return array(
        AITMT_PUBLIC_SLUG,
        'ait-multilingual-translate-translations',
        'ait-multilingual-translate-scanner',
        'ait-multilingual-translate-report-bug',
        'ait-multilingual-translate-feedback',
        'ait-multilingual-translate-onboarding',
        'ait-multilingual-translate-support',
        'ait-multilingual-translate-debugger',
    );
}

function aitmt_fallback_is_plugin_admin_screen($hook = '')
{
    if ('nav-menus.php' === (string) $hook) {
        return true;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $screen_id = $screen && !empty($screen->id) ? (string) $screen->id : '';
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen detection only reads the page slug.
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash((string) $_GET['page'])) : '';

    foreach (aitmt_fallback_admin_page_slugs() as $slug) {
        if ($slug === $page || false !== strpos((string) $hook, $slug) || false !== strpos($screen_id, $slug)) {
            return true;
        }
    }

    return false;
}

function aitmt_fallback_enqueue_admin_assets($hook)
{
    if (!aitmt_fallback_is_plugin_admin_screen($hook)) {
        return;
    }

    wp_enqueue_style('aitmt-admin', AITMT_PLUGIN_URL . 'assets/css/admin.css', array(), AITMT_VERSION);
    wp_enqueue_script('aitmt-admin', AITMT_PLUGIN_URL . 'assets/js/admin.js', array(), AITMT_VERSION, true);

    $admin_css = aitmt_fallback_asset_contents('assets/css/admin.css');
    wp_add_inline_style('aitmt-admin', $admin_css ? $admin_css : aitmt_fallback_admin_inline_css());
    wp_add_inline_style('aitmt-admin', aitmt_fallback_admin_polish_css());

    $admin_js = aitmt_fallback_asset_contents('assets/js/admin.js');
    wp_add_inline_script('aitmt-admin', $admin_js ? $admin_js : aitmt_fallback_admin_inline_script(), 'after');
}

function aitmt_fallback_admin_inline_css()
{
    return '.aitmt-admin-title{align-items:center;display:flex;gap:14px;margin:18px 0 12px}.aitmt-admin-title img{background:transparent;border-radius:8px;box-shadow:none;object-fit:contain}.aitmt-admin-title.is-dashboard{align-items:flex-start}.aitmt-admin-title.is-dashboard img{height:150px;width:150px}.aitmt-admin-title h1{margin:0;padding:0}.aitmt-admin-title p{color:#646970;margin:3px 0 0}.aitmt-admin-title .aitmt-admin-meta{color:#1d2327;font-size:12px}.aitmt-admin-tabs{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0 16px;max-width:1180px}.aitmt-admin-tabs a{background:#fff;border:1px solid #c3c4c7;border-radius:4px;color:#1d2327;padding:7px 10px;text-decoration:none}.aitmt-admin-tabs a:hover{border-color:#2271b1;color:#2271b1}.aitmt-fallback-card,.aitmt-wide-card{background:#fff;border:1px solid #dcdcde;border-radius:6px;margin:16px 0;padding:8px 20px 18px;width:calc(100% - 40px)}.aitmt-fallback-card h2,.aitmt-wide-card h2{border-bottom:1px solid #f0f0f1;margin:0 -20px 12px;padding:14px 20px}.aitmt-fallback-language-grid,.aitmt-language-list{border:1px solid #dcdcde;border-radius:6px;display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));max-height:360px;overflow:auto;padding:8px}.aitmt-actions-row,.aitmt-matrix-actions,.aitmt-matrix-filters,.aitmt-matrix-pagination,.aitmt-debug-actions{align-items:center;display:flex;flex-wrap:wrap;gap:8px;margin:12px 0}.aitmt-matrix-actions form,.aitmt-matrix-pagination form,.aitmt-debug-actions form{align-items:center;display:inline-flex;gap:8px;margin:0}.aitmt-matrix-actions .submit,.aitmt-matrix-filters .submit,.aitmt-matrix-pagination .submit,.aitmt-debug-actions .submit,.aitmt-debug-settings .submit{margin:0;padding:0}.aitmt-matrix-filters,.aitmt-debug-settings,.aitmt-debug-actions{background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;padding:10px 12px}.aitmt-matrix-filters label{display:flex;flex-direction:column;gap:4px;margin:0}.aitmt-debug-table th{width:260px}.aitmt-debug-result{background:#f6f7f7;border-left:4px solid #72aee6;margin:12px 0;padding:10px 12px}.aitmt-debug-result.is-bad{background:#fcf0f1;border-left-color:#d63638}.aitmt-debug-result.is-good{background:#edfaef;border-left-color:#00a32a}.aitmt-log-table code{white-space:pre-wrap;word-break:break-word}.aitmt-matrix-table textarea,.aitmt-translations-table textarea{min-height:76px;width:100%}.aitmt-matrix-table .source-cell{max-width:460px}.aitmt-matrix-pagination .button[aria-disabled=true]{opacity:.55;pointer-events:none}.aitmt-matrix-pagination .aitmt-page-number.is-current{background:#1d2327;border-color:#1d2327;color:#fff}.aitmt-export-import{border:1px solid #dcdcde;border-radius:6px;margin:12px 0;padding:10px 12px}.aitmt-export-import summary{cursor:pointer;font-weight:600}.aitmt-export-import-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));margin-top:12px}';
}

function aitmt_fallback_admin_polish_css()
{
    return '.aitmt-admin-title img{display:block;height:34px;width:34px}.aitmt-admin-title.is-dashboard img{height:150px;width:150px}.toplevel_page_ait-multilingual-translate .wp-menu-image img{height:20px!important;object-fit:contain;padding-top:7px!important;width:20px!important}.aitmt-section-heading{font-size:15px;margin:10px 0 4px}.aitmt-mode-basic .aitmt-advanced-only,.aitmt-fallback-language-grid label.is-hidden,.aitmt-language-option.is-hidden{display:none!important}.aitmt-matrix-filters input[type=search],.aitmt-matrix-filters select,.aitmt-matrix-filters .button{height:34px;margin:0}.aitmt-matrix-filters>.button,.aitmt-matrix-filters>input[type=submit]{align-self:flex-end}.aitmt-segmented{display:inline-flex}.aitmt-segmented input{position:absolute;opacity:0}.aitmt-segmented span{background:#fff;border:1px solid #c3c4c7;display:inline-block;min-width:78px;padding:7px 12px;text-align:center}.aitmt-segmented input:checked+span{background:#2271b1;border-color:#2271b1;color:#fff}.aitmt-form-grid,.aitmt-onboarding-steps{display:grid;gap:14px 18px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}.aitmt-checkbox-row,.aitmt-notice-actions,.aitmt-support-buttons,.aitmt-finish-onboarding{align-items:center;display:flex;flex-wrap:wrap;gap:8px}';
}

function aitmt_fallback_admin_inline_script()
{
    return <<<'JS'
(function(){if(window.AITMT_ADMIN_READY){return;}window.AITMT_ADMIN_READY=true;function initLanguageSearch(){var searches=Array.prototype.slice.call(document.querySelectorAll('.aitmt-language-search, .aitmt-fallback-language-search'));searches.forEach(function(search){var scope=search.closest?search.closest('td, .aitmt-wide-card, .aitmt-fallback-card, .form-table'):document;var options=Array.prototype.slice.call((scope||document).querySelectorAll('.aitmt-language-option, .aitmt-fallback-language-grid label'));if(!options.length){options=Array.prototype.slice.call(document.querySelectorAll('.aitmt-language-option, .aitmt-fallback-language-grid label'));}search.addEventListener('input',function(){var needle=search.value.trim().toLowerCase();options.forEach(function(option){var text=option.textContent.toLowerCase();option.classList.toggle('is-hidden',!!needle&&text.indexOf(needle)===-1);});});});}function initAdminMode(){var wrap=document.querySelector('.aitmt-admin-page');var radios=Array.prototype.slice.call(document.querySelectorAll('input[name="aitmt_options[admin_mode]"]'));if(!wrap||!radios.length){return;}function applyMode(){var checked=radios.filter(function(radio){return radio.checked;})[0];var mode=checked&&checked.value==='advanced'?'advanced':'basic';wrap.classList.toggle('aitmt-mode-basic',mode==='basic');wrap.classList.toggle('aitmt-mode-advanced',mode==='advanced');}radios.forEach(function(radio){radio.addEventListener('change',applyMode);});applyMode();}function initAdmin(){initLanguageSearch();initAdminMode();}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initAdmin);}else{initAdmin();}})();
JS;
}

function aitmt_fallback_frontend_inline_css()
{
    return '.aitmt-fallback-switcher-wrap{box-sizing:border-box;padding:8px 16px;text-align:right;width:100%;z-index:99}.aitmt-fallback-switcher-header{clear:both}.aitmt-fallback-switcher-footer{clear:both;margin-top:10px}.aitmt-fallback-switcher,.aitmt-fallback-switcher-dropdown{font:inherit}.aitmt-fallback-switcher-dropdown{background:#fff;border:1px solid currentColor;border-radius:6px;min-height:36px;padding:4px 28px 4px 10px}.aitmt-fallback-switcher-list{align-items:center;display:inline-flex;flex-wrap:wrap;gap:6px;justify-content:flex-end}.aitmt-fallback-switcher-link{align-items:center;border:1px solid currentColor;border-radius:6px;display:inline-flex;gap:6px;line-height:1.2;min-height:32px;padding:5px 9px;text-decoration:none}.aitmt-fallback-switcher-link.is-current{font-weight:700;opacity:.75}.aitmt-fallback-language-code{font-size:.78em;opacity:.72}.aitmt-elementor-switcher .aitmt-fallback-switcher-wrap{display:inline-block;padding:0}.aitmt-inline-editor-toolbar{align-items:center;background:#1d2327;border-radius:6px;bottom:18px;box-shadow:0 8px 22px rgba(0,0,0,.22);box-sizing:border-box;color:#fff;display:flex;gap:8px;left:18px;max-width:calc(100vw - 36px);padding:8px;position:fixed;z-index:999999}.aitmt-inline-editor-brand{align-items:center;color:#fff;display:flex;gap:6px;font-size:12px;line-height:1.2;max-width:300px;text-decoration:none}.aitmt-inline-editor-brand:hover,.aitmt-inline-editor-brand:focus{color:#fff;text-decoration:underline}.aitmt-inline-editor-brand img{background:#fff;border-radius:4px;height:22px;width:22px}.aitmt-inline-editor-toolbar button{background:#2271b1;border:0;border-radius:4px;color:#fff;cursor:pointer;font:inherit;min-height:34px;padding:6px 10px}.aitmt-inline-editor-toolbar button.is-active{background:#00a32a}.aitmt-inline-editor-status{font-size:12px;max-width:340px}.aitmt-inline-editor-modal{align-items:center;background:rgba(0,0,0,.42);display:none;inset:0;justify-content:center;padding:20px;position:fixed;z-index:1000000}.aitmt-inline-editor-modal.is-open{display:flex}.aitmt-inline-editor-dialog{background:#fff;border-radius:8px;box-shadow:0 18px 50px rgba(0,0,0,.28);color:#1d2327;max-width:760px;padding:20px;width:min(760px,100%)}.aitmt-inline-editor-dialog h2{font-size:18px;line-height:1.3;margin:0 0 12px}.aitmt-inline-editor-textarea{border:1px solid #8c8f94;border-radius:6px;box-sizing:border-box;font:inherit;line-height:1.45;min-height:180px;padding:10px 12px;resize:vertical;width:100%}.aitmt-inline-editor-dialog-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:14px}.aitmt-inline-editor-dialog-actions button{background:#2271b1;border:0;border-radius:4px;color:#fff;cursor:pointer;font:inherit;min-height:34px;padding:7px 13px}.aitmt-inline-editor-dialog-actions button.is-secondary{background:#f6f7f7;border:1px solid #c3c4c7;color:#1d2327}body.aitmt-editor-active .aitmt-editable{cursor:pointer;outline:2px dashed #2271b1;outline-offset:2px}body.aitmt-editor-active .aitmt-editable:hover{background:rgba(34,113,177,.14)}@media(max-width:640px){.aitmt-inline-editor-toolbar{align-items:flex-start;bottom:10px;flex-direction:column;left:10px;right:10px;max-width:none}}';
}

function aitmt_fallback_plugin_folder()
{
    return basename(dirname(AITMT_PLUGIN_FILE));
}

function aitmt_fallback_is_update_safe_folder()
{
    return AITMT_PUBLIC_SLUG === aitmt_fallback_plugin_folder();
}

function aitmt_fallback_register_settings()
{
    register_setting('aitmt_fallback_settings', 'aitmt_options', 'aitmt_fallback_sanitize');
    aitmt_fallback_sync_cron_schedule();
}

function aitmt_fallback_save_settings_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_save_settings');

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The settings array is sanitized by aitmt_fallback_sanitize() below.
    $raw_input = isset($_POST['aitmt_options']) ? wp_unslash($_POST['aitmt_options']) : array();
    $input = is_array($raw_input) ? $raw_input : array();
    $old_options = aitmt_fallback_options();
    $options = aitmt_fallback_sanitize($input);

    update_option('aitmt_options', $options, false);
    aitmt_fallback_sync_cron_schedule();

    if (
        !isset($old_options['url_mode'], $options['url_mode'])
        || $old_options['url_mode'] !== $options['url_mode']
        || !isset($old_options['hide_default_language'], $options['hide_default_language'])
        || $old_options['hide_default_language'] !== $options['hide_default_language']
        || wp_json_encode($old_options['enabled_languages']) !== wp_json_encode($options['enabled_languages'])
        || $old_options['source_language'] !== $options['source_language']
    ) {
        aitmt_fallback_register_rewrites();
        flush_rewrite_rules(false);
        update_option('aitmt_rewrite_version', AITMT_VERSION, false);
    }

    wp_safe_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=' . AITMT_PUBLIC_SLUG)));
    exit;
}

function aitmt_fallback_register_rewrites()
{
    $languages = array_values(array_unique(array_filter(array_map('aitmt_fallback_normalize_language', aitmt_fallback_enabled_languages()))));
    $language_pattern = !empty($languages) ? '(' . implode('|', array_map('preg_quote', $languages)) . ')' : '([a-z]{2,5})';

    add_rewrite_tag('%aitmt_lang%', $language_pattern);
    add_rewrite_tag('%aitmt_path%', '(.+)');
    add_rewrite_rule('^' . $language_pattern . '/?$', 'index.php?aitmt_lang=$matches[1]', 'top');
    add_rewrite_rule('^' . $language_pattern . '/(.+)/?$', 'index.php?aitmt_lang=$matches[1]&aitmt_path=$matches[2]', 'top');
}

function aitmt_fallback_activate()
{
    aitmt_fallback_maybe_create_translation_table();
    aitmt_fallback_register_rewrites();
    flush_rewrite_rules(false);
    update_option('aitmt_rewrite_version', AITMT_VERSION, false);

    if (!get_option('aitmt_onboarding_completed')) {
        update_option('aitmt_onboarding_pending', '1', false);
    }
}

function aitmt_fallback_deactivate()
{
    wp_clear_scheduled_hook('aitmt_fallback_process_queue_event');
    flush_rewrite_rules(false);
}

function aitmt_fallback_maybe_flush_rewrites()
{
    if (get_option('aitmt_rewrite_version') === AITMT_VERSION) {
        return;
    }

    aitmt_fallback_register_rewrites();
    flush_rewrite_rules(false);
    update_option('aitmt_rewrite_version', AITMT_VERSION, false);
}

function aitmt_fallback_query_vars($vars)
{
    $vars[] = 'aitmt_lang';
    $vars[] = 'aitmt_path';

    return $vars;
}

function aitmt_fallback_filter_language_request($query_vars)
{
    $query_vars = is_array($query_vars) ? $query_vars : array();

    if (!empty($GLOBALS['aitmt_request_language']) && !empty($GLOBALS['aitmt_stripped_relative_path'])) {
        $resolved_query_vars = aitmt_fallback_resolve_path_query_vars((string) $GLOBALS['aitmt_stripped_relative_path']);

        if (!empty($resolved_query_vars)) {
            unset($query_vars['pagename'], $query_vars['attachment'], $query_vars['page'], $query_vars['aitmt_lang'], $query_vars['aitmt_path']);
            return array_merge($query_vars, aitmt_fallback_normalize_resolved_query_vars($resolved_query_vars));
        }
    }

    if (empty($query_vars['aitmt_lang'])) {
        return $query_vars;
    }

    $language = aitmt_fallback_normalize_language((string) $query_vars['aitmt_lang']);
    if (!in_array($language, aitmt_fallback_enabled_languages(), true)) {
        unset($query_vars['aitmt_lang'], $query_vars['aitmt_path']);
        return $query_vars;
    }

    $query_vars['aitmt_lang'] = $language;

    if (empty($query_vars['aitmt_path'])) {
        $front_page_id = 'page' === get_option('show_on_front') ? absint(get_option('page_on_front')) : 0;
        if ($front_page_id) {
            $query_vars['page_id'] = $front_page_id;
        }

        unset($query_vars['pagename'], $query_vars['name'], $query_vars['attachment']);
        return $query_vars;
    }

    $path = trim((string) $query_vars['aitmt_path'], '/');

    if ('' === $path) {
        return $query_vars;
    }

    $resolved_query_vars = aitmt_fallback_resolve_path_query_vars($path);
    if (!empty($resolved_query_vars)) {
        unset($query_vars['pagename'], $query_vars['attachment'], $query_vars['page']);
        return array_merge($query_vars, aitmt_fallback_normalize_resolved_query_vars($resolved_query_vars));
    }

    $query_vars['pagename'] = $path;

    return $query_vars;
}

function aitmt_fallback_resolve_path_query_vars($path)
{
    $path = trim(rawurldecode((string) $path), '/');
    $path_segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $path))));
    $path = implode('/', $path_segments);

    if ('' === $path) {
        return array();
    }

    $product_query_vars = aitmt_fallback_resolve_product_query_vars($path_segments);
    if (!empty($product_query_vars)) {
        return $product_query_vars;
    }

    $rewrite_query_vars = aitmt_fallback_resolve_rewrite_query_vars($path);
    if (!empty($rewrite_query_vars)) {
        return aitmt_fallback_normalize_resolved_query_vars($rewrite_query_vars);
    }

    $taxonomy_query_vars = aitmt_fallback_resolve_taxonomy_query_vars($path_segments);
    if (!empty($taxonomy_query_vars)) {
        return $taxonomy_query_vars;
    }

    $candidates = array(
        home_url(trailingslashit($path)),
        home_url($path),
    );

    foreach (array_unique($candidates) as $candidate) {
        $post_id = url_to_postid($candidate);
        if ($post_id) {
            return aitmt_fallback_post_query_vars($post_id);
        }
    }

    $public_post_types = get_post_types(array('public' => true), 'names');
    if (!empty($public_post_types)) {
        $post = get_page_by_path($path, OBJECT, $public_post_types);
        if ($post) {
            return aitmt_fallback_post_query_vars($post->ID);
        }
    }

    if (post_type_exists('product')) {
        $segments = array_values(array_filter(explode('/', $path)));
        $slug = end($segments);

        if ($slug) {
            $product = get_page_by_path($slug, OBJECT, 'product');
            if ($product) {
                return aitmt_fallback_post_query_vars($product->ID);
            }
        }
    }

    return array();
}

function aitmt_fallback_resolve_product_query_vars($segments)
{
    $segments = array_values((array) $segments);

    if (count($segments) < 2 || !post_type_exists('product')) {
        return array();
    }

    $first = sanitize_title((string) $segments[0]);
    $slug = sanitize_title((string) end($segments));

    if (!$first || !$slug || in_array($first, aitmt_fallback_taxonomy_url_bases(), true)) {
        return array();
    }

    $product = get_page_by_path($slug, OBJECT, 'product');

    return $product ? aitmt_fallback_post_query_vars($product->ID) : array();
}

function aitmt_fallback_normalize_resolved_query_vars($query_vars)
{
    $query_vars = is_array($query_vars) ? $query_vars : array();

    if (!empty($query_vars['product']) && post_type_exists('product')) {
        $product = get_page_by_path(sanitize_title((string) $query_vars['product']), OBJECT, 'product');

        if ($product) {
            return aitmt_fallback_post_query_vars($product->ID);
        }

        unset($query_vars['taxonomy'], $query_vars['term'], $query_vars['product_cat'], $query_vars['product_tag']);
    }

    if (!empty($query_vars['name']) && !empty($query_vars['post_type']) && 'product' === $query_vars['post_type'] && post_type_exists('product')) {
        $product = get_page_by_path(sanitize_title((string) $query_vars['name']), OBJECT, 'product');

        if ($product) {
            return aitmt_fallback_post_query_vars($product->ID);
        }
    }

    unset($query_vars['aitmt_lang'], $query_vars['aitmt_path']);

    return $query_vars;
}

function aitmt_fallback_taxonomy_url_bases()
{
    $woocommerce_permalinks = function_exists('wc_get_permalink_structure') ? wc_get_permalink_structure() : (array) get_option('woocommerce_permalinks', array());
    $bases = array('product-category', 'product-tag');

    if (!empty($woocommerce_permalinks['category_base'])) {
        $bases[] = trim((string) $woocommerce_permalinks['category_base'], '/');
    }

    if (!empty($woocommerce_permalinks['tag_base'])) {
        $bases[] = trim((string) $woocommerce_permalinks['tag_base'], '/');
    }

    return array_values(array_filter(array_unique(array_map('sanitize_title', $bases))));
}

function aitmt_fallback_resolve_rewrite_query_vars($path)
{
    global $wp_rewrite;

    $path = trim(rawurldecode((string) $path), '/');
    if ('' === $path || !$wp_rewrite || !method_exists($wp_rewrite, 'wp_rewrite_rules')) {
        return array();
    }

    $rewrite_rules = $wp_rewrite->wp_rewrite_rules();
    if (empty($rewrite_rules) || !is_array($rewrite_rules)) {
        return array();
    }

    $decoded_path = rawurldecode($path);
    $candidates = array_unique(array(
        $path,
        trailingslashit($path),
        $decoded_path,
        trailingslashit($decoded_path),
    ));

    foreach ($rewrite_rules as $match => $query) {
        if (false !== strpos((string) $query, 'aitmt_lang')) {
            continue;
        }

        foreach ($candidates as $candidate) {
            if (!preg_match('#^' . $match . '#', $candidate, $matches)) {
                continue;
            }

            $query = preg_replace('!^.+\?!', '', (string) $query);
            $query = preg_replace_callback(
                '!\$matches\[([0-9]+)\]!',
                function ($found) use ($matches) {
                    $index = isset($found[1]) ? absint($found[1]) : 0;
                    return isset($matches[$index]) ? rawurlencode($matches[$index]) : '';
                },
                $query
            );

            $query_vars = array();
            wp_parse_str($query, $query_vars);

            if (empty($query_vars) || !is_array($query_vars)) {
                continue;
            }

            unset($query_vars['aitmt_lang'], $query_vars['aitmt_path']);

            return $query_vars;
        }
    }

    return array();
}

function aitmt_fallback_resolve_taxonomy_query_vars($segments)
{
    $segments = array_values((array) $segments);
    if (count($segments) < 2) {
        return array();
    }

    $first = sanitize_title((string) $segments[0]);
    $last = sanitize_title((string) end($segments));
    if (!$first || !$last) {
        return array();
    }

    $woocommerce_permalinks = function_exists('wc_get_permalink_structure') ? wc_get_permalink_structure() : (array) get_option('woocommerce_permalinks', array());
    $category_bases = array('product-category');
    $tag_bases = array('product-tag');

    if (!empty($woocommerce_permalinks['category_base'])) {
        $category_bases[] = trim((string) $woocommerce_permalinks['category_base'], '/');
    }

    if (!empty($woocommerce_permalinks['tag_base'])) {
        $tag_bases[] = trim((string) $woocommerce_permalinks['tag_base'], '/');
    }

    $category_bases = array_filter(array_unique(array_map('sanitize_title', $category_bases)));
    $tag_bases = array_filter(array_unique(array_map('sanitize_title', $tag_bases)));

    if (taxonomy_exists('product_cat') && in_array($first, $category_bases, true)) {
        $term = get_term_by('slug', $last, 'product_cat');
        if ($term && !is_wp_error($term)) {
            return array(
                'taxonomy' => 'product_cat',
                'term' => $last,
                'product_cat' => $last,
            );
        }
    }

    if (taxonomy_exists('product_tag') && in_array($first, $tag_bases, true)) {
        $term = get_term_by('slug', $last, 'product_tag');
        if ($term && !is_wp_error($term)) {
            return array(
                'taxonomy' => 'product_tag',
                'term' => $last,
                'product_tag' => $last,
            );
        }
    }

    return array();
}

function aitmt_fallback_post_query_vars($post_id)
{
    $post = get_post(absint($post_id));
    if (!$post) {
        return array();
    }

    if ('page' === $post->post_type) {
        return array('page_id' => (int) $post->ID);
    }

    $query_vars = array(
        'post_type' => $post->post_type,
        'name' => $post->post_name,
    );

    $post_type_object = get_post_type_object($post->post_type);
    if ($post_type_object && !empty($post_type_object->query_var)) {
        $query_vars[(string) $post_type_object->query_var] = $post->post_name;
    }

    if ('post' === $post->post_type) {
        $query_vars['p'] = (int) $post->ID;
    }

    if ('product' === $post->post_type) {
        $query_vars['product'] = $post->post_name;
    }

    return $query_vars;
}

function aitmt_fallback_disable_language_canonical_redirect($redirect_url, $requested_url)
{
    $language = aitmt_fallback_language_from_path();

    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        return $redirect_url;
    }

    $path = (string) wp_parse_url(aitmt_fallback_request_uri(true), PHP_URL_PATH);
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');
    $segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $relative))));

    if (empty($segments)) {
        return $redirect_url;
    }

    return false;
}

function aitmt_fallback_redirect_conflicting_language_url()
{
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    $request_uri = aitmt_fallback_request_uri(true);
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');
    $segments = array_values(array_filter(explode('/', $relative)));
    $prefix_languages = aitmt_fallback_leading_language_segments($segments);
    $path_language = aitmt_fallback_language_from_path();
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public language switching is a read-only GET action.
    $target_language = isset($_GET['lang']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_GET['lang']))) : '';

    if ($target_language && in_array($target_language, aitmt_fallback_enabled_languages(), true) && $path_language && $path_language !== $target_language) {
        $clean_url = aitmt_fallback_clean_current_url_for_language($target_language, true);
        aitmt_fallback_redirect_if_url_changed($clean_url);
        return;
    }

    if (count($prefix_languages) > 1) {
        $clean_url = aitmt_fallback_clean_current_url_for_language((string) $prefix_languages[0], false);
        aitmt_fallback_redirect_if_url_changed($clean_url);
        return;
    }
}

function aitmt_fallback_redirect_if_url_changed($clean_url)
{
    if (!$clean_url) {
        return;
    }

    $current_url = aitmt_fallback_current_url();
    if ($current_url && untrailingslashit($current_url) === untrailingslashit($clean_url)) {
        return;
    }

    wp_safe_redirect($clean_url, 302);
    exit;
}

function aitmt_fallback_leading_language_segments($segments)
{
    $segments = array_values((array) $segments);
    $all_languages = aitmt_fallback_enabled_languages();
    $languages = array();

    foreach ($segments as $segment) {
        $language = aitmt_fallback_normalize_language((string) $segment);
        if (!$language || !in_array($language, $all_languages, true)) {
            break;
        }

        $languages[] = $language;
    }

    return $languages;
}

function aitmt_fallback_clean_current_url_for_language($language, $force_query_language = false)
{
    $language = aitmt_fallback_normalize_language($language);
    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        return '';
    }

    $request_uri = aitmt_fallback_request_uri(true);
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $query = (string) wp_parse_url($request_uri, PHP_URL_QUERY);
    $query_args = array();

    if ('' !== $query) {
        wp_parse_str($query, $query_args);
    }
    unset($query_args['lang']);

    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');
    $segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $relative))));
    $options = aitmt_fallback_options();
    $source = aitmt_fallback_source_language();

    if ('directory' === $options['url_mode'] && !empty($segments) && ($language !== $source || '1' !== $options['hide_default_language'])) {
        array_unshift($segments, $language);
        $base_url = home_url(trailingslashit(implode('/', $segments)));
    } else {
        $base_url = home_url(empty($segments) ? '/' : trailingslashit(implode('/', $segments)));
        if ($force_query_language || $language !== $source || '1' !== $options['hide_default_language']) {
            $query_args['lang'] = $language;
        }
    }

    return add_query_arg($query_args, $base_url);
}

function aitmt_fallback_current_url()
{
    $scheme = is_ssl() ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash((string) $_SERVER['HTTP_HOST'])) : '';
    $request_uri = aitmt_fallback_request_uri(true);

    if (!$host || !$request_uri) {
        return '';
    }

    return $scheme . $host . $request_uri;
}

function aitmt_fallback_capture_route_debug()
{
    if (is_admin() || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || !is_user_logged_in() || !current_user_can('manage_options')) {
        return;
    }

    $request_path = (string) wp_parse_url(aitmt_fallback_request_uri(true), PHP_URL_PATH);
    if (aitmt_fallback_is_untranslated_system_path($request_path) || aitmt_fallback_is_static_asset_path($request_path)) {
        return;
    }

    global $wp, $wp_query;

    $query_vars = array();
    $interesting_keys = array(
        'p',
        'page_id',
        'pagename',
        'name',
        'post_type',
        'product',
        'product_cat',
        'product_tag',
        'taxonomy',
        'term',
        'aitmt_lang',
        'aitmt_path',
    );

    foreach ($interesting_keys as $key) {
        if (isset($wp_query->query_vars[$key]) && '' !== (string) $wp_query->query_vars[$key]) {
            $query_vars[$key] = is_scalar($wp_query->query_vars[$key]) ? (string) $wp_query->query_vars[$key] : wp_json_encode($wp_query->query_vars[$key]);
        }
    }

    $route_debug = array(
        'created_at' => current_time('mysql'),
        'original_request_uri' => aitmt_fallback_request_uri(true),
        'routed_request_uri' => aitmt_fallback_request_uri(false),
        'path_language' => aitmt_fallback_language_from_path(),
        'requested_language' => aitmt_fallback_requested_language(),
        'current_language' => aitmt_fallback_current_language(),
        'matched_rule' => isset($wp->matched_rule) ? (string) $wp->matched_rule : '',
        'matched_query' => isset($wp->matched_query) ? (string) $wp->matched_query : '',
        'query_vars' => $query_vars,
        'queried_object_id' => get_queried_object_id(),
        'is_404' => is_404() ? 'yes' : 'no',
        'is_product' => function_exists('is_product') && is_product() ? 'yes' : 'no',
        'is_shop' => function_exists('is_shop') && is_shop() ? 'yes' : 'no',
        'is_product_category' => function_exists('is_product_category') && is_product_category() ? 'yes' : 'no',
    );

    update_option('aitmt_last_route_debug', $route_debug, false);
    aitmt_fallback_log_event('route', (string) $route_debug['original_request_uri'], $route_debug);
}

function aitmt_fallback_redirect_language_home()
{
    $language = get_query_var('aitmt_lang');

    if (!$language || get_query_var('aitmt_path') || is_front_page()) {
        return;
    }

    $language = aitmt_fallback_normalize_language((string) $language);
    $source = aitmt_fallback_source_language();

    if (!$language || $language === $source || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        return;
    }

    wp_safe_redirect(add_query_arg('lang', $language, home_url('/')), 302);
    exit;
}

function aitmt_fallback_plugin_links($links)
{
    $url = admin_url('admin.php?page=' . AITMT_PUBLIC_SLUG);
    array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'ait-multilingual-translate') . '</a>');

    return $links;
}

function aitmt_fallback_admin_redirect_url_from_post($default_page = 'ait-multilingual-translate')
{
    $allowed_pages = array(
        'ait-multilingual-translate',
        'ait-multilingual-translate-translations',
        'ait-multilingual-translate-scanner',
        'ait-multilingual-translate-debugger',
        'ait-multilingual-translate-onboarding',
        'ait-multilingual-translate-report-bug',
        'ait-multilingual-translate-feedback',
        'ait-multilingual-translate-support',
    );
    // phpcs:disable WordPress.Security.NonceVerification.Missing -- Redirect metadata is only consumed after the calling admin-post handler verifies its nonce.
    $page = isset($_POST['redirect_page']) ? sanitize_key(wp_unslash((string) $_POST['redirect_page'])) : $default_page;

    if (!in_array($page, $allowed_pages, true)) {
        $page = $default_page;
    }

    $args = array('page' => $page);

    if ('ait-multilingual-translate-translations' === $page) {
        $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 25;
        $args['per_page'] = in_array($per_page, array(25, 50, 100), true) ? $per_page : 25;
        $args['paged'] = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;
        $matrix_search = isset($_POST['matrix_search']) ? sanitize_text_field(wp_unslash((string) $_POST['matrix_search'])) : '';
        $matrix_status = isset($_POST['matrix_status']) ? sanitize_key(wp_unslash((string) $_POST['matrix_status'])) : 'all';

        if ('' !== $matrix_search) {
            $args['matrix_search'] = $matrix_search;
        }

        if (in_array($matrix_status, array('translated', 'untranslated'), true)) {
            $args['matrix_status'] = $matrix_status;
        }
    }
    // phpcs:enable WordPress.Security.NonceVerification.Missing

    return add_query_arg($args, admin_url('admin.php'));
}

function aitmt_fallback_maybe_redirect_onboarding()
{
    if (!current_user_can('manage_options') || !is_admin() || wp_doing_ajax()) {
        return;
    }

    if ('1' !== get_option('aitmt_onboarding_pending', '0')) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Onboarding redirect checks a read-only admin page slug.
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash((string) $_GET['page'])) : '';
    if ('ait-multilingual-translate-onboarding' === $page) {
        return;
    }

    delete_option('aitmt_onboarding_pending');
    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-onboarding'));
    exit;
}

function aitmt_fallback_admin_mode()
{
    $options = aitmt_fallback_options();

    return 'advanced' === (string) $options['admin_mode'] ? 'advanced' : 'basic';
}

function aitmt_fallback_support_development_url()
{
    $options = aitmt_fallback_options();
    $url = !empty($options['donation_paypal_url']) ? $options['donation_paypal_url'] : 'https://paypal.me/wpaitranslate';

    return esc_url_raw($url);
}

function aitmt_fallback_support_development_button($class = 'button')
{
    return sprintf(
        '<a class="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer">%3$s</a>',
        esc_attr($class),
        esc_url(aitmt_fallback_support_development_url()),
        esc_html__('Support Development', 'ait-multilingual-translate')
    );
}

function aitmt_fallback_allowed_switcher_html()
{
    $allowed = wp_kses_allowed_html('post');
    $attrs = array(
        'aria-current' => true,
        'aria-hidden' => true,
        'aria-label' => true,
        'class' => true,
        'data-aitmt-language-switcher' => true,
        'data-aitmt-no-translate' => true,
        'decoding' => true,
        'href' => true,
        'hreflang' => true,
        'id' => true,
        'lang' => true,
        'loading' => true,
        'rel' => true,
        'role' => true,
        'src' => true,
        'target' => true,
        'translate' => true,
    );

    foreach (array('a', 'div', 'details', 'img', 'nav', 'span', 'summary') as $tag) {
        $allowed[$tag] = isset($allowed[$tag]) ? array_merge($allowed[$tag], $attrs) : $attrs;
    }

    return $allowed;
}

function aitmt_fallback_kses_switcher($html)
{
    return wp_kses((string) $html, aitmt_fallback_allowed_switcher_html());
}

function aitmt_fallback_support_development_block($compact = false)
{
    ?>
    <div class="aitmt-support-development-block <?php echo esc_attr($compact ? 'is-compact' : ''); ?>">
        <h3><?php esc_html_e('Support AIT Multilingual Translate', 'ait-multilingual-translate'); ?></h3>
        <p><?php esc_html_e('AIT Multilingual Translate is currently in Public Beta and includes temporary full feature access while the platform is actively tested and improved.', 'ait-multilingual-translate'); ?></p>
        <p><?php esc_html_e('Users who support the project with a donation during the Public Beta period may receive a significant discount or special early-supporter offer for the future commercial release of AIT Multilingual Translate.', 'ait-multilingual-translate'); ?></p>
        <p><?php esc_html_e('Your support helps improve the plugin, optimize AI providers, expand language support, and accelerate development.', 'ait-multilingual-translate'); ?></p>
        <p class="aitmt-support-buttons">
            <?php
            echo wp_kses_post(aitmt_fallback_support_development_button('button button-primary aitmt-support-donation-button'));
            ?>
        </p>
    </div>
    <?php
}

function aitmt_fallback_public_beta_notice()
{
    ?>
    <div class="notice notice-info inline aitmt-public-beta-notice">
        <p><?php esc_html_e('AIT Multilingual Translate is currently in Public Beta. Please make a backup before bulk translating production websites.', 'ait-multilingual-translate'); ?></p>
        <p class="aitmt-notice-actions">
            <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-report-bug')); ?>"><?php esc_html_e('Report Bug', 'ait-multilingual-translate'); ?></a>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-feedback')); ?>"><?php esc_html_e('Send Feedback', 'ait-multilingual-translate'); ?></a>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Button HTML is built from escaped attributes and translated text.
            echo wp_kses_post(aitmt_fallback_support_development_button('button aitmt-support-donation-button'));
            ?>
        </p>
    </div>
    <?php
}

function aitmt_fallback_redact_sensitive_text($text)
{
    $text = (string) $text;
    $patterns = array(
        '/sk-[A-Za-z0-9_\-]{8,}/i',
        '/xai-[A-Za-z0-9_\-]{8,}/i',
        '/AIzaSy[A-Za-z0-9_\-]{8,}/i',
        '/sk-ant-[A-Za-z0-9_\-]{8,}/i',
        '/(api[_\- ]?key|token|secret|password|passwd|cookie|authorization)(["\':=\s]+)([^,\s"\']+)/i',
        '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i',
        '/\+?\d[\d\s().\-]{7,}\d/',
        '/\b(billing|shipping|address|customer|first_name|last_name|phone|email)\b[^,\n\r]*/i',
    );

    return preg_replace($patterns, '[redacted]', $text);
}

function aitmt_fallback_technical_report($include_log = false)
{
    $options = aitmt_fallback_options();
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    $active_plugins = array();

    foreach ((array) get_option('active_plugins', array()) as $plugin_file) {
        $active_plugins[] = isset($plugins[$plugin_file]['Name']) ? $plugins[$plugin_file]['Name'] : $plugin_file;
    }

    $theme = wp_get_theme();
    $wc_version = defined('WC_VERSION') ? WC_VERSION : 'not active';
    $route = get_option('aitmt_last_route_debug', array());
    $route = is_array($route) ? $route : array();

    $report = array(
        'plugin_version' => AITMT_VERSION,
        'plugin_edition' => aitmt_fallback_edition_label(),
        'wp_version' => get_bloginfo('version'),
        'php_version' => PHP_VERSION,
        'woocommerce_version' => $wc_version,
        'active_theme' => $theme->get('Name') . ' ' . $theme->get('Version'),
        'active_plugins' => $active_plugins,
        'enabled_languages' => aitmt_fallback_enabled_languages(),
        'source_language' => aitmt_fallback_source_language(),
        'current_language' => aitmt_fallback_current_language(),
        'selected_provider' => aitmt_fallback_provider_label($options['provider']),
        'queue_status' => array(
            'queued_strings' => aitmt_fallback_queued_count(),
            'saved_translations' => aitmt_fallback_translation_count(),
            'batch_size' => (int) $options['max_segments_per_request'],
            'background_queue' => '1' === $options['cron_enabled'] ? 'enabled' : 'disabled',
        ),
        'route_status' => array_intersect_key($route, array_flip(array('created_at', 'original_request_uri', 'routed_request_uri', 'path_language', 'requested_language', 'current_language', 'matched_rule', 'matched_query', 'is_404', 'is_product', 'is_shop', 'is_product_category'))),
    );

    if ($include_log) {
        $events = get_option('aitmt_debug_events', array());
        $report['debug_events'] = is_array($events) ? array_slice($events, 0, 30) : array();

        $path = aitmt_fallback_debug_log_path();
        if (file_exists($path) && is_readable($path)) {
            $report['debug_file_tail'] = substr(aitmt_fallback_read_local_file($path), -12000);
        }
    }

    return aitmt_fallback_redact_sensitive_text(wp_json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function aitmt_fallback_mail_public_beta_message($subject, $fields, $include_log)
{
    $body = '';
    foreach ($fields as $label => $value) {
        $value = (string) $value;
        if ('Email' !== (string) $label) {
            $value = aitmt_fallback_redact_sensitive_text($value);
        }
        $body .= $label . ":\n" . $value . "\n\n";
    }

    $body .= "Technical report:\n" . aitmt_fallback_technical_report($include_log) . "\n";

    return wp_mail('info@itdesign.biz', $subject, $body, array('Content-Type: text/plain; charset=UTF-8'));
}

function aitmt_fallback_submit_bug_report_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_submit_bug_report');

    if (empty($_POST['aitmt_consent'])) {
        wp_safe_redirect(add_query_arg('aitmt_sent', 'consent', admin_url('admin.php?page=ait-multilingual-translate-report-bug')));
        exit;
    }

    $fields = array(
        'Name' => isset($_POST['name']) ? sanitize_text_field(wp_unslash((string) $_POST['name'])) : '',
        'Email' => isset($_POST['email']) ? sanitize_email(wp_unslash((string) $_POST['email'])) : '',
        'Website' => isset($_POST['website']) ? esc_url_raw(wp_unslash((string) $_POST['website'])) : home_url('/'),
        'Problem type' => isset($_POST['problem_type']) ? sanitize_text_field(wp_unslash((string) $_POST['problem_type'])) : '',
        'Short description' => isset($_POST['short_description']) ? sanitize_textarea_field(wp_unslash((string) $_POST['short_description'])) : '',
        'Steps to reproduce' => isset($_POST['steps']) ? sanitize_textarea_field(wp_unslash((string) $_POST['steps'])) : '',
    );
    $include_log = !empty($_POST['attach_log']);
    $sent = aitmt_fallback_mail_public_beta_message('[AIT Multilingual Translate Public Beta] Bug report', $fields, $include_log);

    wp_safe_redirect(add_query_arg('aitmt_sent', $sent ? '1' : '0', admin_url('admin.php?page=ait-multilingual-translate-report-bug')));
    exit;
}

function aitmt_fallback_submit_feedback_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_submit_feedback');

    if (empty($_POST['aitmt_consent'])) {
        wp_safe_redirect(add_query_arg('aitmt_sent', 'consent', admin_url('admin.php?page=ait-multilingual-translate-feedback')));
        exit;
    }

    $fields = array(
        'Name' => isset($_POST['name']) ? sanitize_text_field(wp_unslash((string) $_POST['name'])) : '',
        'Email' => isset($_POST['email']) ? sanitize_email(wp_unslash((string) $_POST['email'])) : '',
        'Type' => isset($_POST['feedback_type']) ? sanitize_text_field(wp_unslash((string) $_POST['feedback_type'])) : '',
        'Message' => isset($_POST['message']) ? sanitize_textarea_field(wp_unslash((string) $_POST['message'])) : '',
    );
    $sent = aitmt_fallback_mail_public_beta_message('[AIT Multilingual Translate Public Beta] Feedback', $fields, false);

    wp_safe_redirect(add_query_arg('aitmt_sent', $sent ? '1' : '0', admin_url('admin.php?page=ait-multilingual-translate-feedback')));
    exit;
}

function aitmt_fallback_onboarding_save_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_onboarding_save');

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The settings array is sanitized by aitmt_fallback_sanitize() below.
    $raw_input = isset($_POST['aitmt_options']) ? wp_unslash($_POST['aitmt_options']) : array();
    $input = is_array($raw_input) ? $raw_input : array();
    $old_options = aitmt_fallback_options();
    $options = aitmt_fallback_sanitize(wp_parse_args($input, $old_options));
    update_option('aitmt_options', $options, false);
    aitmt_fallback_register_rewrites();
    flush_rewrite_rules(false);
    update_option('aitmt_rewrite_version', AITMT_VERSION, false);

    wp_safe_redirect(add_query_arg('aitmt_saved', '1', admin_url('admin.php?page=ait-multilingual-translate-onboarding')));
    exit;
}

function aitmt_fallback_onboarding_finish_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_onboarding_finish');
    update_option('aitmt_onboarding_completed', current_time('mysql'), false);
    delete_option('aitmt_onboarding_pending');

    wp_safe_redirect(admin_url('admin.php?page=' . AITMT_PUBLIC_SLUG));
    exit;
}

function aitmt_fallback_sanitize($input)
{
    $input = is_array($input) ? $input : array();
    $languages = array();

    if (!empty($input['enabled_languages'])) {
        $raw_languages = is_array($input['enabled_languages'])
            ? $input['enabled_languages']
            : explode(',', (string) $input['enabled_languages']);

        foreach ($raw_languages as $language) {
            $language = strtolower(trim(str_replace('_', '-', (string) $language)));
            $language = sanitize_key(strtok($language, '-'));

            if ($language) {
                $languages[] = $language;
            }
        }
    }

    $source_language = empty($input['source_language']) ? '' : strtolower((string) $input['source_language']);
    $source_language = 'auto' === $source_language ? '' : sanitize_key($source_language);
    $url_mode = isset($input['url_mode']) && 'query' === $input['url_mode'] ? 'query' : 'directory';
    $admin_mode = isset($input['admin_mode']) && 'advanced' === sanitize_key((string) $input['admin_mode']) ? 'advanced' : 'basic';
    $allowed_providers = array('openai', 'gemini', 'grok', 'google_translate', 'deepl');
    $provider = isset($input['provider']) ? sanitize_key((string) $input['provider']) : 'openai';
    $provider = in_array($provider, $allowed_providers, true) ? $provider : 'openai';
    $selector_style = isset($input['selector_style']) && 'list' === $input['selector_style'] ? 'list' : 'dropdown';
    $max_segments = isset($input['max_segments_per_request']) ? absint($input['max_segments_per_request']) : 40;
    $max_segments = min(100, max(1, $max_segments));
    $daily_limit = isset($input['quota_daily_chars']) ? absint($input['quota_daily_chars']) : 0;
    $monthly_limit = isset($input['quota_monthly_chars']) ? absint($input['quota_monthly_chars']) : 0;
    $max_chars_per_request = isset($input['max_chars_per_request']) ? absint($input['max_chars_per_request']) : 0;
    $estimated_cost_limit = isset($input['estimated_cost_limit']) ? (float) str_replace(',', '.', sanitize_text_field((string) $input['estimated_cost_limit'])) : 0.0;
    $estimated_cost_limit = max(0, $estimated_cost_limit);
    $translation_temperature = isset($input['translation_temperature']) ? (float) str_replace(',', '.', sanitize_text_field((string) $input['translation_temperature'])) : 0.1;
    $translation_temperature = max(0, min(1, $translation_temperature));
    $quality_mode = isset($input['quality_mode']) ? sanitize_key((string) $input['quality_mode']) : 'cheap';
    $quality_mode = array_key_exists($quality_mode, aitmt_fallback_quality_mode_options()) ? $quality_mode : 'cheap';
    $deepl_plan = isset($input['deepl_plan']) && 'pro' === $input['deepl_plan'] ? 'pro' : 'free';
    $translation_mode = isset($input['translation_mode']) ? sanitize_key((string) $input['translation_mode']) : 'neutral';
    $translation_mode = array_key_exists($translation_mode, aitmt_fallback_translation_mode_options()) ? $translation_mode : 'neutral';
    $custom_instruction = empty($input['custom_translation_instruction']) ? '' : sanitize_textarea_field((string) $input['custom_translation_instruction']);
    $custom_instruction = function_exists('mb_substr') ? mb_substr($custom_instruction, 0, 500, 'UTF-8') : substr($custom_instruction, 0, 500);

    return array(
        'source_language' => $source_language,
        'enabled_languages' => array_values(array_unique($languages)),
        'admin_mode' => $admin_mode,
        'provider' => $provider,
        'openai_api_key' => empty($input['openai_api_key']) ? '' : sanitize_text_field((string) $input['openai_api_key']),
        'openai_model' => empty($input['openai_model']) ? 'gpt-4o-mini' : sanitize_text_field((string) $input['openai_model']),
        'gemini_api_key' => empty($input['gemini_api_key']) ? '' : sanitize_text_field((string) $input['gemini_api_key']),
        'gemini_model' => empty($input['gemini_model']) ? 'gemini-2.5-flash' : sanitize_text_field((string) $input['gemini_model']),
        'grok_api_key' => empty($input['grok_api_key']) ? '' : sanitize_text_field((string) $input['grok_api_key']),
        'grok_model' => empty($input['grok_model']) ? 'grok-3-mini' : sanitize_text_field((string) $input['grok_model']),
        'google_translate_api_key' => empty($input['google_translate_api_key']) ? '' : sanitize_text_field((string) $input['google_translate_api_key']),
        'deepl_api_key' => empty($input['deepl_api_key']) ? '' : sanitize_text_field((string) $input['deepl_api_key']),
        'deepl_plan' => $deepl_plan,
        'quota_daily_chars' => $daily_limit,
        'quota_monthly_chars' => $monthly_limit,
        'max_chars_per_request' => $max_chars_per_request,
        'estimated_cost_limit' => $estimated_cost_limit,
        'translation_temperature' => $translation_temperature,
        'quality_mode' => $quality_mode,
        'translation_mode' => $translation_mode,
        'custom_translation_instruction' => $custom_instruction,
        'url_mode' => $url_mode,
        'hide_default_language' => empty($input['hide_default_language']) ? '0' : '1',
        'auto_translate' => empty($input['auto_translate']) ? '0' : '1',
        'translate_on_page_load' => empty($input['translate_on_page_load']) ? '0' : '1',
        'queue_missing' => empty($input['queue_missing']) ? '0' : '1',
        'scan_on_save' => empty($input['scan_on_save']) ? '0' : '1',
        'cron_enabled' => empty($input['cron_enabled']) ? '0' : '1',
        'draft_mode' => empty($input['draft_mode']) ? '0' : '1',
        'translate_attributes' => empty($input['translate_attributes']) ? '0' : '1',
        'max_segments_per_request' => $max_segments,
        'selector_style' => $selector_style,
        'selector_show_flags' => empty($input['selector_show_flags']) ? '0' : '1',
        'selector_show_names' => empty($input['selector_show_names']) ? '0' : '1',
        'selector_show_codes' => empty($input['selector_show_codes']) ? '0' : '1',
        'selector_custom_css' => empty($input['selector_custom_css']) ? '' : substr(wp_strip_all_tags((string) $input['selector_custom_css']), 0, 5000),
        'selector_header' => empty($input['selector_header']) ? '0' : '1',
        'selector_footer' => empty($input['selector_footer']) ? '0' : '1',
        'frontend_editor' => empty($input['frontend_editor']) ? '0' : '1',
        'donation_coffee_url' => empty($input['donation_coffee_url']) ? '' : esc_url_raw((string) $input['donation_coffee_url']),
        'donation_paypal_url' => empty($input['donation_paypal_url']) ? '' : esc_url_raw((string) $input['donation_paypal_url']),
    );
}

function aitmt_fallback_languages()
{
    return array(
        'af' => 'Afrikaans',
        'sq' => 'Albanian',
        'am' => 'Amharic',
        'ar' => 'Arabic',
        'hy' => 'Armenian',
        'az' => 'Azerbaijani',
        'eu' => 'Basque',
        'be' => 'Belarusian',
        'bn' => 'Bengali',
        'bs' => 'Bosnian',
        'bg' => 'Bulgarian',
        'ca' => 'Catalan',
        'ceb' => 'Cebuano',
        'zh' => 'Chinese',
        'co' => 'Corsican',
        'hr' => 'Croatian',
        'cs' => 'Czech',
        'da' => 'Danish',
        'nl' => 'Dutch',
        'en' => 'English',
        'eo' => 'Esperanto',
        'et' => 'Estonian',
        'fi' => 'Finnish',
        'fr' => 'French',
        'fy' => 'Frisian',
        'gl' => 'Galician',
        'ka' => 'Georgian',
        'de' => 'German',
        'el' => 'Greek',
        'gu' => 'Gujarati',
        'ht' => 'Haitian Creole',
        'ha' => 'Hausa',
        'haw' => 'Hawaiian',
        'he' => 'Hebrew',
        'hi' => 'Hindi',
        'hmn' => 'Hmong',
        'hu' => 'Hungarian',
        'is' => 'Icelandic',
        'ig' => 'Igbo',
        'id' => 'Indonesian',
        'ga' => 'Irish',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'jv' => 'Javanese',
        'kn' => 'Kannada',
        'kk' => 'Kazakh',
        'km' => 'Khmer',
        'ko' => 'Korean',
        'ku' => 'Kurdish',
        'ky' => 'Kyrgyz',
        'lo' => 'Lao',
        'la' => 'Latin',
        'lv' => 'Latvian',
        'lt' => 'Lithuanian',
        'lb' => 'Luxembourgish',
        'mk' => 'Macedonian',
        'mg' => 'Malagasy',
        'ms' => 'Malay',
        'ml' => 'Malayalam',
        'mt' => 'Maltese',
        'mi' => 'Maori',
        'mr' => 'Marathi',
        'mn' => 'Mongolian',
        'my' => 'Myanmar',
        'ne' => 'Nepali',
        'no' => 'Norwegian',
        'ny' => 'Nyanja',
        'ps' => 'Pashto',
        'fa' => 'Persian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'pa' => 'Punjabi',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sm' => 'Samoan',
        'gd' => 'Scottish Gaelic',
        'sr' => 'Serbian',
        'st' => 'Sesotho',
        'sn' => 'Shona',
        'sd' => 'Sindhi',
        'si' => 'Sinhala',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'so' => 'Somali',
        'es' => 'Spanish',
        'su' => 'Sundanese',
        'sw' => 'Swahili',
        'sv' => 'Swedish',
        'tl' => 'Tagalog',
        'tg' => 'Tajik',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'ug' => 'Uyghur',
        'uz' => 'Uzbek',
        'vi' => 'Vietnamese',
        'cy' => 'Welsh',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'zu' => 'Zulu',
    );
}

function aitmt_fallback_default_options()
{
    return array(
        'source_language' => '',
        'enabled_languages' => array(),
        'admin_mode' => 'basic',
        'provider' => 'openai',
        'openai_api_key' => '',
        'openai_model' => 'gpt-4o-mini',
        'gemini_api_key' => '',
        'gemini_model' => 'gemini-2.5-flash',
        'grok_api_key' => '',
        'grok_model' => 'grok-3-mini',
        'google_translate_api_key' => '',
        'deepl_api_key' => '',
        'deepl_plan' => 'free',
        'quota_daily_chars' => 0,
        'quota_monthly_chars' => 0,
        'max_chars_per_request' => 0,
        'estimated_cost_limit' => 0,
        'translation_temperature' => 0.1,
        'quality_mode' => 'cheap',
        'translation_mode' => 'neutral',
        'custom_translation_instruction' => '',
        'url_mode' => 'directory',
        'hide_default_language' => '1',
        'auto_translate' => '1',
        'translate_on_page_load' => '0',
        'queue_missing' => '1',
        'scan_on_save' => '1',
        'cron_enabled' => '0',
        'draft_mode' => '0',
        'translate_attributes' => '1',
        'max_segments_per_request' => 40,
        'selector_style' => 'dropdown',
        'selector_show_flags' => '0',
        'selector_show_names' => '1',
        'selector_show_codes' => '0',
        'selector_custom_css' => '',
        'selector_header' => '0',
        'selector_footer' => '0',
        'frontend_editor' => '1',
        'donation_coffee_url' => '',
        'donation_paypal_url' => 'https://paypal.me/wpaitranslate',
    );
}

function aitmt_fallback_options()
{
    $options = get_option('aitmt_options', array());
    if (!is_array($options)) {
        $options = array();
    }

    $options = wp_parse_args($options, aitmt_fallback_default_options());

    if (!is_array($options['enabled_languages'])) {
        $options['enabled_languages'] = array_filter(array_map('trim', explode(',', (string) $options['enabled_languages'])));
    }

    return $options;
}

function aitmt_fallback_active_provider()
{
    $options = aitmt_fallback_options();
    $provider = isset($options['provider']) ? sanitize_key((string) $options['provider']) : 'openai';

    return in_array($provider, array('openai', 'gemini', 'grok', 'google_translate', 'deepl'), true) ? $provider : 'openai';
}

function aitmt_fallback_provider_label($provider = '')
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();

    if ('gemini' === $provider) {
        return 'Gemini';
    }

    if ('grok' === $provider) {
        return 'Grok / xAI';
    }

    if ('google_translate' === $provider) {
        return 'Google Translate';
    }

    if ('deepl' === $provider) {
        return 'DeepL';
    }

    return 'OpenAI';
}

function aitmt_fallback_provider_catalog()
{
    return array(
        'openai' => array(
            'label' => 'OpenAI',
            'status' => 'active',
            'badges' => array('Tone of Voice', 'SEO Mode', 'Fast', 'Cheap', 'HTML Aware', 'Batch Support'),
        ),
        'gemini' => array(
            'label' => 'Gemini',
            'status' => 'active',
            'badges' => array('Tone of Voice', 'SEO Mode', 'Flash', 'Cheap', 'HTML Aware', 'Batch Support'),
        ),
        'grok' => array(
            'label' => 'Grok / xAI',
            'status' => 'active',
            'badges' => array('Tone of Voice', 'SEO Mode', 'HTML Aware', 'Batch Support'),
        ),
        'google_translate' => array(
            'label' => 'Google Translate',
            'status' => 'active',
            'badges' => array('Fast', 'Batch Support'),
        ),
        'deepl' => array(
            'label' => 'DeepL',
            'status' => 'active',
            'badges' => array('Fast', 'Batch Support'),
        ),
        'claude' => array(
            'label' => 'Claude',
            'status' => 'planned',
            'badges' => array('Tone of Voice', 'SEO Mode', 'HTML Aware'),
        ),
        'yandex_translate' => array(
            'label' => 'Yandex Translate',
            'status' => 'planned',
            'badges' => array('Fast'),
        ),
        'yandexgpt' => array(
            'label' => 'YandexGPT / Alice',
            'status' => 'planned',
            'badges' => array('Tone of Voice', 'SEO Mode'),
        ),
        'more' => array(
            'label' => 'More providers',
            'status' => 'planned',
            'badges' => array('Mistral', 'DeepSeek', 'Qwen', 'Moonshot AI', 'Baidu ERNIE', 'Cohere', 'Together AI', 'OpenRouter'),
        ),
    );
}

function aitmt_fallback_provider_supports_tone($provider = '')
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();

    return in_array($provider, array('openai', 'gemini', 'grok'), true);
}

function aitmt_fallback_provider_key($provider = '')
{
    $options = aitmt_fallback_options();
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();

    if ('gemini' === $provider) {
        return defined('AITMT_GEMINI_API_KEY') && AITMT_GEMINI_API_KEY ? AITMT_GEMINI_API_KEY : $options['gemini_api_key'];
    }

    if ('grok' === $provider) {
        return defined('AITMT_GROK_API_KEY') && AITMT_GROK_API_KEY ? AITMT_GROK_API_KEY : $options['grok_api_key'];
    }

    if ('google_translate' === $provider) {
        return defined('AITMT_GOOGLE_TRANSLATE_API_KEY') && AITMT_GOOGLE_TRANSLATE_API_KEY ? AITMT_GOOGLE_TRANSLATE_API_KEY : $options['google_translate_api_key'];
    }

    if ('deepl' === $provider) {
        return defined('AITMT_DEEPL_API_KEY') && AITMT_DEEPL_API_KEY ? AITMT_DEEPL_API_KEY : $options['deepl_api_key'];
    }

    return defined('AITMT_OPENAI_API_KEY') && AITMT_OPENAI_API_KEY ? AITMT_OPENAI_API_KEY : $options['openai_api_key'];
}

function aitmt_fallback_provider_key_source($provider = '')
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();

    if ('gemini' === $provider && defined('AITMT_GEMINI_API_KEY') && AITMT_GEMINI_API_KEY) {
        return 'wp-config.php constant';
    }

    if ('grok' === $provider && defined('AITMT_GROK_API_KEY') && AITMT_GROK_API_KEY) {
        return 'wp-config.php constant';
    }

    if ('google_translate' === $provider && defined('AITMT_GOOGLE_TRANSLATE_API_KEY') && AITMT_GOOGLE_TRANSLATE_API_KEY) {
        return 'wp-config.php constant';
    }

    if ('deepl' === $provider && defined('AITMT_DEEPL_API_KEY') && AITMT_DEEPL_API_KEY) {
        return 'wp-config.php constant';
    }

    if ('openai' === $provider && defined('AITMT_OPENAI_API_KEY') && AITMT_OPENAI_API_KEY) {
        return 'wp-config.php constant';
    }

    return 'plugin settings';
}

function aitmt_fallback_provider_model($provider = '')
{
    $options = aitmt_fallback_options();
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();

    if ('gemini' === $provider) {
        return empty($options['gemini_model']) ? 'gemini-2.5-flash' : $options['gemini_model'];
    }

    if ('grok' === $provider) {
        return empty($options['grok_model']) ? 'grok-3-mini' : $options['grok_model'];
    }

    if ('google_translate' === $provider) {
        return 'Cloud Translation Basic v2';
    }

    if ('deepl' === $provider) {
        return 'DeepL API ' . ('pro' === $options['deepl_plan'] ? 'api.deepl.com' : 'api-free.deepl.com');
    }

    return empty($options['openai_model']) ? 'gpt-4o-mini' : $options['openai_model'];
}

function aitmt_fallback_quality_mode_options()
{
    return array(
        'cheap' => __('Cheap', 'ait-multilingual-translate'),
        'balanced' => __('Balanced', 'ait-multilingual-translate'),
        'premium' => __('Premium', 'ait-multilingual-translate'),
    );
}

function aitmt_fallback_quality_mode_label($mode = '')
{
    $options = aitmt_fallback_options();
    $mode = $mode ? sanitize_key($mode) : sanitize_key((string) $options['quality_mode']);
    $modes = aitmt_fallback_quality_mode_options();

    return isset($modes[$mode]) ? $modes[$mode] : $modes['cheap'];
}

function aitmt_fallback_recommended_provider_model($provider, $quality = '')
{
    $provider = sanitize_key((string) $provider);
    $quality = $quality ? sanitize_key((string) $quality) : sanitize_key((string) aitmt_fallback_options()['quality_mode']);

    $models = array(
        'openai' => array(
            'cheap' => 'gpt-4o-mini',
            'balanced' => 'gpt-4o-mini',
            'premium' => 'gpt-4o',
        ),
        'gemini' => array(
            'cheap' => 'gemini-2.5-flash',
            'balanced' => 'gemini-2.5-flash',
            'premium' => 'gemini-2.5-pro',
        ),
        'grok' => array(
            'cheap' => 'grok-3-mini',
            'balanced' => 'grok-3-mini',
            'premium' => 'grok-4',
        ),
    );

    return isset($models[$provider][$quality]) ? $models[$provider][$quality] : '';
}

function aitmt_fallback_translation_temperature()
{
    $options = aitmt_fallback_options();
    $temperature = isset($options['translation_temperature']) ? (float) $options['translation_temperature'] : 0.1;

    return max(0, min(1, $temperature));
}

function aitmt_fallback_translation_mode_options()
{
    return array(
        'neutral' => array(
            'label' => __('Neutral / Accurate', 'ait-multilingual-translate'),
            'instruction' => 'Translate accurately and preserve the original meaning, formatting and tone.',
        ),
        'seo' => array(
            'label' => __('SEO Optimized', 'ait-multilingual-translate'),
            'instruction' => 'Translate and adapt the text for SEO in the target language. Keep it natural, search-friendly and relevant. Do not add unrelated keywords.',
        ),
        'marketing' => array(
            'label' => __('Marketing', 'ait-multilingual-translate'),
            'instruction' => 'Translate the text in a persuasive marketing style. Keep the message natural, clear and conversion-oriented.',
        ),
        'ecommerce' => array(
            'label' => __('eCommerce', 'ait-multilingual-translate'),
            'instruction' => 'Translate the text for an eCommerce website. Keep product names, attributes, benefits and call-to-action phrases natural and commercially effective.',
        ),
        'formal' => array(
            'label' => __('Formal', 'ait-multilingual-translate'),
            'instruction' => 'Translate using a formal and professional tone.',
        ),
        'casual' => array(
            'label' => __('Casual', 'ait-multilingual-translate'),
            'instruction' => 'Translate using a natural and casual tone.',
        ),
        'technical' => array(
            'label' => __('Technical', 'ait-multilingual-translate'),
            'instruction' => 'Translate using precise technical language. Preserve technical terms and product names.',
        ),
        'legal' => array(
            'label' => __('Legal', 'ait-multilingual-translate'),
            'instruction' => 'Translate using a formal legal style. Preserve meaning carefully and avoid creative rewriting.',
        ),
        'luxury' => array(
            'label' => __('Luxury Brand', 'ait-multilingual-translate'),
            'instruction' => 'Translate using an elegant premium brand tone. Keep the language refined, polished and natural.',
        ),
        'friendly' => array(
            'label' => __('Friendly', 'ait-multilingual-translate'),
            'instruction' => 'Translate using a warm, friendly and approachable tone.',
        ),
        'custom' => array(
            'label' => __('Custom Prompt', 'ait-multilingual-translate'),
            'instruction' => '',
        ),
    );
}

function aitmt_fallback_translation_mode_label($mode = '')
{
    $options = aitmt_fallback_options();
    $mode = $mode ? sanitize_key($mode) : sanitize_key((string) $options['translation_mode']);
    $modes = aitmt_fallback_translation_mode_options();

    return isset($modes[$mode]) ? $modes[$mode]['label'] : $modes['neutral']['label'];
}

function aitmt_fallback_translation_instruction()
{
    $options = aitmt_fallback_options();
    $mode = sanitize_key((string) $options['translation_mode']);
    $modes = aitmt_fallback_translation_mode_options();

    if ('custom' === $mode && !empty($options['custom_translation_instruction'])) {
        $instruction = sanitize_textarea_field((string) $options['custom_translation_instruction']);

        return function_exists('mb_substr') ? mb_substr($instruction, 0, 500, 'UTF-8') : substr($instruction, 0, 500);
    }

    return isset($modes[$mode]) && !empty($modes[$mode]['instruction'])
        ? $modes[$mode]['instruction']
        : $modes['neutral']['instruction'];
}

function aitmt_fallback_translate_with_provider($segments, $source_language, $target_language)
{
    $provider = aitmt_fallback_active_provider();
    $deduped = aitmt_fallback_dedupe_segments((array) $segments);
    $segments_for_provider = $deduped['segments'];
    $cooldown = aitmt_fallback_provider_cooldown_remaining($provider);
    $quota_check = aitmt_fallback_provider_quota_check($provider, $segments_for_provider);

    if (empty($segments_for_provider)) {
        return array();
    }

    if ($cooldown > 0) {
        return new WP_Error(
            'aitmt_provider_cooldown',
            sprintf('Provider cooldown is active for %d more second(s) after a quota or rate-limit error.', $cooldown),
            array('status' => 429)
        );
    }

    if (is_wp_error($quota_check)) {
        return $quota_check;
    }

    if ('gemini' === $provider) {
        $result = aitmt_fallback_gemini_translate_batch($segments_for_provider, $source_language, $target_language);
    } elseif ('grok' === $provider) {
        $result = aitmt_fallback_grok_translate_batch($segments_for_provider, $source_language, $target_language);
    } elseif ('google_translate' === $provider) {
        $result = aitmt_fallback_google_translate_batch($segments_for_provider, $source_language, $target_language);
    } elseif ('deepl' === $provider) {
        $result = aitmt_fallback_deepl_translate_batch($segments_for_provider, $source_language, $target_language);
    } else {
        $result = aitmt_fallback_openai_translate_batch($segments_for_provider, $source_language, $target_language);
    }

    if (!is_wp_error($result)) {
        $result = aitmt_fallback_restore_deduped_translations((array) $result, $deduped['map']);
        aitmt_fallback_provider_quota_record($provider, $segments_for_provider);
        aitmt_fallback_provider_stats_record(
            $provider,
            array(
                'requests' => 1,
                'input_tokens' => aitmt_fallback_estimate_tokens_for_segments($segments_for_provider),
                'output_tokens' => aitmt_fallback_estimate_tokens_for_segments($result),
                'duplicate_skipped' => $deduped['duplicate_skipped'],
                'model' => aitmt_fallback_provider_model($provider),
            )
        );
    } else {
        aitmt_fallback_provider_stats_record(
            $provider,
            array(
                'requests' => 1,
                'input_tokens' => aitmt_fallback_estimate_tokens_for_segments($segments_for_provider),
                'output_tokens' => 0,
                'duplicate_skipped' => $deduped['duplicate_skipped'],
                'model' => aitmt_fallback_provider_model($provider),
            )
        );
    }

    return $result;
}

function aitmt_fallback_dedupe_segments($segments)
{
    $unique = array();
    $lookup = array();
    $map = array();
    $duplicate_skipped = 0;

    foreach ((array) $segments as $hash => $text) {
        $hash = (string) $hash;
        $text = (string) $text;
        $dedupe_key = md5($text);

        if (isset($lookup[$dedupe_key])) {
            $map[$hash] = $lookup[$dedupe_key];
            $duplicate_skipped++;
            continue;
        }

        $lookup[$dedupe_key] = $hash;
        $unique[$hash] = $text;
    }

    return array(
        'segments' => $unique,
        'map' => $map,
        'duplicate_skipped' => $duplicate_skipped,
    );
}

function aitmt_fallback_restore_deduped_translations($translations, $map)
{
    foreach ((array) $map as $hash => $source_hash) {
        if (isset($translations[$source_hash])) {
            $translations[$hash] = $translations[$source_hash];
        }
    }

    return $translations;
}

function aitmt_fallback_provider_char_count($segments)
{
    $total = 0;

    foreach ((array) $segments as $text) {
        $text = (string) $text;
        $total += function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    }

    return $total;
}

function aitmt_fallback_provider_usage()
{
    $usage = get_option('aitmt_provider_usage', array());

    return is_array($usage) ? $usage : array();
}

function aitmt_fallback_provider_stats()
{
    $stats = get_option('aitmt_provider_stats', array());

    return is_array($stats) ? $stats : array();
}

function aitmt_fallback_provider_stats_for($provider = '')
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();
    $stats = aitmt_fallback_provider_stats();

    return isset($stats[$provider]) && is_array($stats[$provider]) ? $stats[$provider] : array();
}

function aitmt_fallback_provider_stats_record($provider, $data)
{
    $provider = sanitize_key((string) $provider);
    if ('' === $provider) {
        return;
    }

    $stats = aitmt_fallback_provider_stats();
    $current = isset($stats[$provider]) && is_array($stats[$provider]) ? $stats[$provider] : array();
    $input_tokens = isset($data['input_tokens']) ? absint($data['input_tokens']) : 0;
    $output_tokens = isset($data['output_tokens']) ? absint($data['output_tokens']) : 0;
    $estimated_cost = aitmt_fallback_estimate_provider_cost($provider, isset($data['model']) ? (string) $data['model'] : '', $input_tokens, $output_tokens);

    $stats[$provider] = array(
        'requests' => absint($current['requests'] ?? 0) + absint($data['requests'] ?? 0),
        'input_tokens' => absint($current['input_tokens'] ?? 0) + $input_tokens,
        'output_tokens' => absint($current['output_tokens'] ?? 0) + $output_tokens,
        'estimated_cost' => (float) ($current['estimated_cost'] ?? 0) + $estimated_cost,
        'cache_hits' => absint($current['cache_hits'] ?? 0) + absint($data['cache_hits'] ?? 0),
        'duplicate_skipped' => absint($current['duplicate_skipped'] ?? 0) + absint($data['duplicate_skipped'] ?? 0),
        'last_provider' => $provider,
        'last_model' => isset($data['model']) ? sanitize_text_field((string) $data['model']) : (string) ($current['last_model'] ?? ''),
        'updated_at' => current_time('mysql'),
    );

    update_option('aitmt_provider_stats', $stats, false);
}

function aitmt_fallback_provider_stats_record_cache_hits($provider, $count)
{
    $count = absint($count);
    if ($count < 1) {
        return;
    }

    aitmt_fallback_provider_stats_record(
        $provider,
        array(
            'requests' => 0,
            'input_tokens' => 0,
            'output_tokens' => 0,
            'cache_hits' => $count,
            'duplicate_skipped' => 0,
            'model' => aitmt_fallback_provider_model($provider),
        )
    );
}

function aitmt_fallback_estimate_tokens_for_segments($segments)
{
    $chars = aitmt_fallback_provider_char_count((array) $segments);

    return (int) ceil($chars / 4);
}

function aitmt_fallback_estimate_provider_cost($provider, $model, $input_tokens, $output_tokens)
{
    $provider = sanitize_key((string) $provider);
    $model = strtolower((string) $model);
    $rates = array(
        'openai' => array('input' => 0.15, 'output' => 0.60),
        'gemini' => array('input' => 0.10, 'output' => 0.40),
        'grok' => array('input' => 0.30, 'output' => 0.50),
    );

    if ('openai' === $provider && false !== strpos($model, 'gpt-4o') && false === strpos($model, 'mini')) {
        $rates[$provider] = array('input' => 2.50, 'output' => 10.00);
    }

    if (!isset($rates[$provider])) {
        return 0.0;
    }

    return (($input_tokens / 1000000) * $rates[$provider]['input']) + (($output_tokens / 1000000) * $rates[$provider]['output']);
}

function aitmt_fallback_provider_usage_key($provider, $period)
{
    return sanitize_key($provider) . '_' . $period;
}

function aitmt_fallback_provider_chars_used($provider, $period)
{
    $usage = aitmt_fallback_provider_usage();
    $key = aitmt_fallback_provider_usage_key($provider, $period);

    return isset($usage[$key]) ? absint($usage[$key]) : 0;
}

function aitmt_fallback_provider_quota_check($provider, $segments)
{
    $options = aitmt_fallback_options();
    $chars = aitmt_fallback_provider_char_count($segments);
    $daily_limit = isset($options['quota_daily_chars']) ? absint($options['quota_daily_chars']) : 0;
    $monthly_limit = isset($options['quota_monthly_chars']) ? absint($options['quota_monthly_chars']) : 0;
    $request_char_limit = isset($options['max_chars_per_request']) ? absint($options['max_chars_per_request']) : 0;
    $cost_limit = isset($options['estimated_cost_limit']) ? (float) $options['estimated_cost_limit'] : 0.0;
    $day = gmdate('Ymd');
    $month = gmdate('Ym');

    if ($request_char_limit > 0 && $chars > $request_char_limit) {
        return new WP_Error(
            'aitmt_request_char_limit',
            sprintf('Local request character limit reached for %s. This batch has %d characters and the configured limit is %d.', aitmt_fallback_provider_label($provider), $chars, $request_char_limit),
            array('status' => 429)
        );
    }

    if ($cost_limit > 0) {
        $estimated_tokens = aitmt_fallback_estimate_tokens_for_segments($segments);
        $estimated_cost = aitmt_fallback_estimate_provider_cost($provider, aitmt_fallback_provider_model($provider), $estimated_tokens, $estimated_tokens);
        if ($estimated_cost > $cost_limit) {
            return new WP_Error(
                'aitmt_estimated_cost_limit',
                sprintf('Estimated cost limit reached for %s. Estimated request cost is %.6f and the configured limit is %.6f.', aitmt_fallback_provider_label($provider), $estimated_cost, $cost_limit),
                array('status' => 429)
            );
        }
    }

    if ($daily_limit > 0 && aitmt_fallback_provider_chars_used($provider, $day) + $chars > $daily_limit) {
        return new WP_Error(
            'aitmt_daily_quota',
            sprintf('Local daily character quota reached for %s. Used %d of %d characters.', aitmt_fallback_provider_label($provider), aitmt_fallback_provider_chars_used($provider, $day), $daily_limit),
            array('status' => 429)
        );
    }

    if ($monthly_limit > 0 && aitmt_fallback_provider_chars_used($provider, $month) + $chars > $monthly_limit) {
        return new WP_Error(
            'aitmt_monthly_quota',
            sprintf('Local monthly character quota reached for %s. Used %d of %d characters.', aitmt_fallback_provider_label($provider), aitmt_fallback_provider_chars_used($provider, $month), $monthly_limit),
            array('status' => 429)
        );
    }

    return true;
}

function aitmt_fallback_provider_quota_record($provider, $segments)
{
    $chars = aitmt_fallback_provider_char_count($segments);

    if ($chars <= 0) {
        return;
    }

    $usage = aitmt_fallback_provider_usage();
    $day_key = aitmt_fallback_provider_usage_key($provider, gmdate('Ymd'));
    $month_key = aitmt_fallback_provider_usage_key($provider, gmdate('Ym'));
    $usage[$day_key] = isset($usage[$day_key]) ? absint($usage[$day_key]) + $chars : $chars;
    $usage[$month_key] = isset($usage[$month_key]) ? absint($usage[$month_key]) + $chars : $chars;

    foreach ($usage as $key => $value) {
        if (false === strpos($key, sanitize_key($provider) . '_')) {
            continue;
        }

        $period = substr($key, strlen(sanitize_key($provider)) + 1);
        if (preg_match('/^\d{6}$/', $period) && $period < gmdate('Ym', strtotime('-13 months'))) {
            unset($usage[$key]);
        }

        if (preg_match('/^\d{8}$/', $period) && $period < gmdate('Ymd', strtotime('-45 days'))) {
            unset($usage[$key]);
        }
    }

    update_option('aitmt_provider_usage', $usage, false);
}

function aitmt_fallback_provider_cooldown_key($provider)
{
    return 'aitmt_provider_cooldown_' . sanitize_key($provider);
}

function aitmt_fallback_provider_cooldown_remaining($provider = '')
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();
    $until = (int) get_transient(aitmt_fallback_provider_cooldown_key($provider));

    return max(0, $until - time());
}

function aitmt_fallback_set_provider_cooldown($provider, $seconds)
{
    $provider = $provider ? sanitize_key($provider) : aitmt_fallback_active_provider();
    $seconds = max(60, min(DAY_IN_SECONDS, absint($seconds)));
    set_transient(aitmt_fallback_provider_cooldown_key($provider), time() + $seconds, $seconds);
}

function aitmt_fallback_normalize_language($language)
{
    $language = strtolower(str_replace('_', '-', trim((string) $language)));
    $parts = explode('-', $language);

    return sanitize_key(isset($parts[0]) ? $parts[0] : $language);
}

function aitmt_fallback_source_language()
{
    $options = aitmt_fallback_options();

    if (!empty($options['source_language'])) {
        return aitmt_fallback_normalize_language($options['source_language']);
    }

    $locale = function_exists('determine_locale') ? determine_locale() : get_locale();

    return aitmt_fallback_normalize_language($locale);
}

function aitmt_fallback_enabled_languages()
{
    $options = aitmt_fallback_options();
    $languages = array_merge(array(aitmt_fallback_source_language()), (array) $options['enabled_languages']);
    $languages = array_map('aitmt_fallback_normalize_language', $languages);

    return array_values(array_unique(array_filter($languages)));
}

function aitmt_fallback_gettext($translation, $text, $domain)
{
    if ('ait-multilingual-translate' !== $domain || '' === $text) {
        return $translation;
    }

    $language = aitmt_fallback_source_language();
    if ('en' === $language) {
        return $translation;
    }

    $dictionary = aitmt_fallback_plugin_dictionary($language);

    return isset($dictionary[$text]) ? $dictionary[$text] : $translation;
}

function aitmt_fallback_plugin_dictionary($language)
{
    $language = aitmt_fallback_normalize_language($language);
    $ru = array(
        'AI Translate' => 'AI Translate',
        'AIT Multilingual Translate' => 'AIT Multilingual Translate',
        'Dashboard' => 'Р СџР В°Р Р…Р ВµР В»РЎРЉ',
        'Translations' => 'Р СџР ВµРЎР‚Р ВµР Р†Р С•Р Т‘РЎвЂ№',
        'Scanner' => 'Р РЋР С”Р В°Р Р…Р ВµРЎР‚',
        'Debugger' => 'Р С›РЎвЂљР В»Р В°Р Т‘РЎвЂЎР С‘Р С”',
        'Settings' => 'Р СњР В°РЎРѓРЎвЂљРЎР‚Р С•Р в„–Р С”Р С‘',
        'Languages' => 'Р Р‡Р В·РЎвЂ№Р С”Р С‘',
        'AI Provider' => 'AI-Р С—РЎР‚Р С•Р Р†Р В°Р в„–Р Т‘Р ВµРЎР‚',
        'Switcher' => 'Р РЋР ВµР В»Р ВµР С”РЎвЂљР С•РЎР‚',
        'Language Switcher' => 'Р РЋР ВµР В»Р ВµР С”РЎвЂљР С•РЎР‚ РЎРЏР В·РЎвЂ№Р С”Р В°',
        'Source language' => 'Р С›РЎРѓР Р…Р С•Р Р†Р Р…Р С•Р в„– РЎРЏР В·РЎвЂ№Р С”',
        'Target languages' => 'Р Р‡Р В·РЎвЂ№Р С”Р С‘ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘Р В°',
        'Provider' => 'Р СџРЎР‚Р С•Р Р†Р В°Р в„–Р Т‘Р ВµРЎР‚',
        'OpenAI API key' => 'OpenAI API Р С”Р В»РЎР‹РЎвЂЎ',
        'OpenAI model' => 'Р СљР С•Р Т‘Р ВµР В»РЎРЉ OpenAI',
        'Gemini API key' => 'Gemini API Р С”Р В»РЎР‹РЎвЂЎ',
        'Gemini model' => 'Р СљР С•Р Т‘Р ВµР В»РЎРЉ Gemini',
        'Grok API key' => 'Grok API Р С”Р В»РЎР‹РЎвЂЎ',
        'Grok model' => 'Р СљР С•Р Т‘Р ВµР В»РЎРЉ Grok',
        'Google Translate API key' => 'Google Translate API Р С”Р В»РЎР‹РЎвЂЎ',
        'DeepL API key' => 'DeepL API Р С”Р В»РЎР‹РЎвЂЎ',
        'DeepL plan' => 'Р СћР В°РЎР‚Р С‘РЎвЂћ DeepL',
        'Quota control' => 'Р С™Р С•Р Р…РЎвЂљРЎР‚Р С•Р В»РЎРЉ Р С”Р Р†Р С•РЎвЂљРЎвЂ№',
        'Daily character limit' => 'Р вЂќР Р…Р ВµР Р†Р Р…Р С•Р в„– Р В»Р С‘Р СР С‘РЎвЂљ РЎРѓР С‘Р СР Р†Р С•Р В»Р С•Р Р†',
        'Monthly character limit' => 'Р СљР ВµРЎРѓРЎРЏРЎвЂЎР Р…РЎвЂ№Р в„– Р В»Р С‘Р СР С‘РЎвЂљ РЎРѓР С‘Р СР Р†Р С•Р В»Р С•Р Р†',
        'Translation behavior' => 'Р СџР С•Р Р†Р ВµР Т‘Р ВµР Р…Р С‘Р Вµ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘Р В°',
        'Batch size' => 'Р В Р В°Р В·Р СР ВµРЎР‚ Р С—Р В°Р С”Р ВµРЎвЂљР В°',
        'URLs and SEO' => 'URL Р С‘ SEO',
        'URL mode' => 'Р В Р ВµР В¶Р С‘Р С URL',
        'Style' => 'Р РЋРЎвЂљР С‘Р В»РЎРЉ',
        'Display parts' => 'Р В§РЎвЂљР С• Р С—Р С•Р С”Р В°Р В·РЎвЂ№Р Р†Р В°РЎвЂљРЎРЉ',
        'Automatic placement' => 'Р С’Р Р†РЎвЂљР С•Р СР В°РЎвЂљР С‘РЎвЂЎР ВµРЎРѓР С”Р С•Р Вµ РЎР‚Р В°Р В·Р СР ВµРЎвЂ°Р ВµР Р…Р С‘Р Вµ',
        'Frontend editor' => 'Р В Р ВµР Т‘Р В°Р С”РЎвЂљР С•РЎР‚ Р Р…Р В° РЎвЂћРЎР‚Р С•Р Р…РЎвЂљР ВµР Р…Р Т‘Р Вµ',
        'Selector custom CSS' => 'CSS РЎРѓР ВµР В»Р ВµР С”РЎвЂљР С•РЎР‚Р В°',
        'Save settings' => 'Р РЋР С•РЎвЂ¦РЎР‚Р В°Р Р…Р С‘РЎвЂљРЎРЉ Р Р…Р В°РЎРѓРЎвЂљРЎР‚Р С•Р в„–Р С”Р С‘',
        'String Scanner' => 'Р РЋР С”Р В°Р Р…Р ВµРЎР‚ РЎРѓРЎвЂљРЎР‚Р С•Р С”',
        'Translation Queue' => 'Р С›РЎвЂЎР ВµРЎР‚Р ВµР Т‘РЎРЉ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘Р В°',
        'Translations Matrix' => 'Р СљР В°РЎвЂљРЎР‚Р С‘РЎвЂ Р В° Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘Р С•Р Р†',
        'Scan new strings' => 'Р РЋР С”Р В°Р Р…Р С‘РЎР‚Р С•Р Р†Р В°РЎвЂљРЎРЉ Р Р…Р С•Р Р†РЎвЂ№Р Вµ РЎРѓРЎвЂљРЎР‚Р С•Р С”Р С‘',
        'Process queue' => 'Р С›Р В±РЎР‚Р В°Р В±Р С•РЎвЂљР В°РЎвЂљРЎРЉ Р С•РЎвЂЎР ВµРЎР‚Р ВµР Т‘РЎРЉ',
        'Per page' => 'Р СњР В° РЎРѓРЎвЂљРЎР‚Р В°Р Р…Р С‘РЎвЂ Р Вµ',
        'Previous' => 'Р СњР В°Р В·Р В°Р Т‘',
        'Next' => 'Р вЂ™Р С—Р ВµРЎР‚РЎвЂР Т‘',
        'Save visible translations' => 'Р РЋР С•РЎвЂ¦РЎР‚Р В°Р Р…Р С‘РЎвЂљРЎРЉ Р Р†Р С‘Р Т‘Р С‘Р СРЎвЂ№Р Вµ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘РЎвЂ№',
        'Export / Import' => 'Р В­Р С”РЎРѓР С—Р С•РЎР‚РЎвЂљ / Р С‘Р СР С—Р С•РЎР‚РЎвЂљ',
        'Export language' => 'Р Р‡Р В·РЎвЂ№Р С” РЎРЊР С”РЎРѓР С—Р С•РЎР‚РЎвЂљР В°',
        'Import language' => 'Р Р‡Р В·РЎвЂ№Р С” Р С‘Р СР С—Р С•РЎР‚РЎвЂљР В°',
        'Export translations' => 'Р В­Р С”РЎРѓР С—Р С•РЎР‚РЎвЂљР С‘РЎР‚Р С•Р Р†Р В°РЎвЂљРЎРЉ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘РЎвЂ№',
        'Import translations' => 'Р ВР СР С—Р С•РЎР‚РЎвЂљР С‘РЎР‚Р С•Р Р†Р В°РЎвЂљРЎРЉ Р С—Р ВµРЎР‚Р ВµР Р†Р С•Р Т‘РЎвЂ№',
        'Select language' => 'Р вЂ™РЎвЂ№Р В±Р ВµРЎР‚Р С‘РЎвЂљР Вµ РЎРЏР В·РЎвЂ№Р С”',
        'Dropdown' => 'Р вЂ™РЎвЂ№Р С—Р В°Р Т‘Р В°РЎР‹РЎвЂ°Р С‘Р в„– РЎРѓР С—Р С‘РЎРѓР С•Р С”',
        'List' => 'Р РЋР С—Р С‘РЎРѓР С•Р С”',
        'Flags' => 'Р В¤Р В»Р В°Р С–Р С‘',
        'Language names' => 'Р СњР В°Р В·Р Р†Р В°Р Р…Р С‘РЎРЏ РЎРЏР В·РЎвЂ№Р С”Р С•Р Р†',
        'Language codes' => 'Р С™Р С•Р Т‘РЎвЂ№ РЎРЏР В·РЎвЂ№Р С”Р С•Р Р†',
        'Status:' => 'Р РЋРЎвЂљР В°РЎвЂљРЎС“РЎРѓ:',
    );
    $de = array(
        'Dashboard' => 'Dashboard',
        'Translations' => 'Р“Сљbersetzungen',
        'Scanner' => 'Scanner',
        'Debugger' => 'Debugger',
        'Settings' => 'Einstellungen',
        'Languages' => 'Sprachen',
        'AI Provider' => 'KI-Anbieter',
        'Switcher' => 'Sprachumschalter',
        'Language Switcher' => 'Sprachumschalter',
        'Source language' => 'Ausgangssprache',
        'Target languages' => 'Zielsprachen',
        'Provider' => 'Anbieter',
        'Translation behavior' => 'Р“Сљbersetzungsverhalten',
        'Batch size' => 'Batch-GrР“В¶Р“Сџe',
        'URLs and SEO' => 'URLs und SEO',
        'URL mode' => 'URL-Modus',
        'Style' => 'Stil',
        'Display parts' => 'Anzeigeelemente',
        'Automatic placement' => 'Automatische Platzierung',
        'Frontend editor' => 'Frontend-Editor',
        'Selector custom CSS' => 'Eigenes CSS fР“Сr den Umschalter',
        'Save settings' => 'Einstellungen speichern',
        'String Scanner' => 'String-Scanner',
        'Translation Queue' => 'Р“Сљbersetzungswarteschlange',
        'Translations Matrix' => 'Р“Сљbersetzungsmatrix',
        'Scan new strings' => 'Neue Strings scannen',
        'Process queue' => 'Warteschlange verarbeiten',
        'Per page' => 'Pro Seite',
        'Previous' => 'ZurР“Сck',
        'Next' => 'Weiter',
        'Save visible translations' => 'Sichtbare Р“Сљbersetzungen speichern',
        'Export / Import' => 'Export / Import',
        'Export translations' => 'Р“Сљbersetzungen exportieren',
        'Import translations' => 'Р“Сљbersetzungen importieren',
        'Select language' => 'Sprache auswР“В¤hlen',
        'Dropdown' => 'Dropdown',
        'List' => 'Liste',
        'Flags' => 'Flaggen',
        'Language names' => 'Sprachnamen',
        'Language codes' => 'Sprachcodes',
        'Status:' => 'Status:',
    );
    $ka = array(
        'Dashboard' => 'Р±С“вЂєР±С“С’Р±С“В Р±С“вЂ”Р±С“вЂўР±С“ВР±С“РЋ Р±С“С›Р±С“С’Р±С“СљР±С“вЂќР±С“С™Р±С“В',
        'Translations' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“С’Р±С“СљР±С“вЂќР±С“вЂР±С“В',
        'Scanner' => 'Р±С“РЋР±С“в„ўР±С“С’Р±С“СљР±С“вЂќР±С“В Р±С“В',
        'Debugger' => 'Р±С“вЂ™Р±С“С’Р±С“вЂєР±С“С’Р±С“В Р±С“вЂ”Р±С“вЂўР±С“С’',
        'Settings' => 'Р±С“С›Р±С“С’Р±С“В Р±С“С’Р±С“вЂєР±С“вЂќР±С“СћР±С“В Р±С“вЂќР±С“вЂР±С“В',
        'Languages' => 'Р±С“вЂќР±С“СљР±С“вЂќР±С“вЂР±С“В',
        'AI Provider' => 'AI Р±С“С›Р±С“В Р±С“СњР±С“вЂўР±С“С’Р±С“ВР±С“вЂњР±С“вЂќР±С“В Р±С“В',
        'Switcher' => 'Р±С“вЂќР±С“СљР±С“ВР±С“РЋ Р±С“вЂ™Р±С“С’Р±С“вЂњР±С“С’Р±С“вЂєР±С“В Р±С“вЂ”Р±С“вЂўР±С“вЂќР±С“С™Р±С“В',
        'Language Switcher' => 'Р±С“вЂќР±С“СљР±С“ВР±С“РЋ Р±С“вЂ™Р±С“С’Р±С“вЂњР±С“С’Р±С“вЂєР±С“В Р±С“вЂ”Р±С“вЂўР±С“вЂќР±С“С™Р±С“В',
        'Source language' => 'Р±С“РЋР±С“С’Р±С“В¬Р±С“В§Р±С“ВР±С“РЋР±С“В Р±С“вЂќР±С“СљР±С“С’',
        'Target languages' => 'Р±С“РЋР±С“С’Р±С“вЂєР±С“ВР±С“вЂ“Р±С“СљР±С“вЂќ Р±С“вЂќР±С“СљР±С“вЂќР±С“вЂР±С“В',
        'Provider' => 'Р±С“С›Р±С“В Р±С“СњР±С“вЂўР±С“С’Р±С“ВР±С“вЂњР±С“вЂќР±С“В Р±С“В',
        'Translation behavior' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“СљР±С“ВР±С“РЋ Р±С“ТђР±С“Р„Р±С“вЂќР±С“вЂўР±С“С’',
        'Batch size' => 'Р±С“С›Р±С“С’Р±С“в„ўР±С“вЂќР±С“СћР±С“ВР±С“РЋ Р±С“вЂ“Р±С“СњР±С“вЂєР±С“С’',
        'URLs and SEO' => 'URL Р±С“вЂњР±С“С’ SEO',
        'URL mode' => 'URL Р±С“В Р±С“вЂќР±С“СџР±С“ВР±С“вЂєР±С“В',
        'Style' => 'Р±С“РЋР±С“СћР±С“ВР±С“С™Р±С“В',
        'Display parts' => 'Р±С“В©Р±С“вЂўР±С“вЂќР±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“СљР±С“С’Р±С“В¬Р±С“ВР±С“С™Р±С“вЂќР±С“вЂР±С“В',
        'Automatic placement' => 'Р±С“С’Р±С“вЂўР±С“СћР±С“СњР±С“вЂєР±С“С’Р±С“СћР±С“Р€Р±С“В Р±С“В Р±С“вЂ™Р±С“С’Р±С“СљР±С“вЂ”Р±С“С’Р±С“вЂўР±С“РЋР±С“вЂќР±С“вЂР±С“С’',
        'Frontend editor' => 'Р±С“В¤Р±С“В Р±С“СњР±С“СљР±С“СћР±С“вЂќР±С“СљР±С“вЂњР±С“ВР±С“РЋ Р±С“В Р±С“вЂќР±С“вЂњР±С“С’Р±С“ТђР±С“СћР±С“СњР±С“В Р±С“В',
        'Selector custom CSS' => 'Р±С“РЋР±С“вЂќР±С“С™Р±С“вЂќР±С“ТђР±С“СћР±С“СњР±С“В Р±С“ВР±С“РЋ CSS',
        'Save settings' => 'Р±С“С›Р±С“С’Р±С“В Р±С“С’Р±С“вЂєР±С“вЂќР±С“СћР±С“В Р±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“РЃР±С“вЂќР±С“СљР±С“С’Р±С“В®Р±С“вЂўР±С“С’',
        'String Scanner' => 'Р±С“РЋР±С“СћР±С“В Р±С“ВР±С“ТђР±С“СњР±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“РЋР±С“в„ўР±С“С’Р±С“СљР±С“вЂќР±С“В Р±С“В',
        'Translation Queue' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“СљР±С“ВР±С“РЋ Р±С“В Р±С“ВР±С“вЂ™Р±С“В',
        'Translations Matrix' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“С’Р±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“вЂєР±С“С’Р±С“СћР±С“В Р±С“ВР±С“Р„Р±С“С’',
        'Scan new strings' => 'Р±С“С’Р±С“В®Р±С“С’Р±С“С™Р±С“В Р±С“РЋР±С“СћР±С“В Р±С“ВР±С“ТђР±С“СњР±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“РЋР±С“в„ўР±С“С’Р±С“СљР±С“ВР±С“В Р±С“вЂќР±С“вЂР±С“С’',
        'Process queue' => 'Р±С“В Р±С“ВР±С“вЂ™Р±С“ВР±С“РЋ Р±С“вЂњР±С“С’Р±С“вЂєР±С“Р€Р±С“РЃР±С“С’Р±С“вЂўР±С“вЂќР±С“вЂР±С“С’',
        'Per page' => 'Р±С“вЂ™Р±С“вЂўР±С“вЂќР±С“В Р±С“вЂњР±С“вЂ“Р±С“вЂќ',
        'Previous' => 'Р±С“В¬Р±С“ВР±С“СљР±С“С’',
        'Next' => 'Р±С“РЃР±С“вЂќР±С“вЂєР±С“вЂњР±С“вЂќР±С“вЂ™Р±С“В',
        'Save visible translations' => 'Р±С“В®Р±С“ВР±С“С™Р±С“Р€Р±С“С™Р±С“В Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“С’Р±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“РЃР±С“вЂќР±С“СљР±С“С’Р±С“В®Р±С“вЂўР±С“С’',
        'Export / Import' => 'Р±С“вЂќР±С“ТђР±С“РЋР±С“С›Р±С“СњР±С“В Р±С“СћР±С“В / Р±С“ВР±С“вЂєР±С“С›Р±С“СњР±С“В Р±С“СћР±С“В',
        'Export translations' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“С’Р±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“вЂќР±С“ТђР±С“РЋР±С“С›Р±С“СњР±С“В Р±С“СћР±С“В',
        'Import translations' => 'Р±С“вЂ”Р±С“С’Р±С“В Р±С“вЂ™Р±С“вЂєР±С“С’Р±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“ВР±С“вЂєР±С“С›Р±С“СњР±С“В Р±С“СћР±С“В',
        'Select language' => 'Р±С“вЂќР±С“СљР±С“ВР±С“РЋ Р±С“С’Р±С“В Р±С“В©Р±С“вЂќР±С“вЂўР±С“С’',
        'Dropdown' => 'Р±С“В©Р±С“С’Р±С“вЂєР±С“СњР±С“РЋР±С“С’Р±С“РЃР±С“С™Р±С“вЂќР±С“С™Р±С“В',
        'List' => 'Р±С“РЋР±С“ВР±С“С’',
        'Flags' => 'Р±С“вЂњР±С“В Р±С“СњР±С“РЃР±С“вЂќР±С“вЂР±С“В',
        'Language names' => 'Р±С“вЂќР±С“СљР±С“вЂќР±С“вЂР±С“ВР±С“РЋ Р±С“РЋР±С“С’Р±С“В®Р±С“вЂќР±С“С™Р±С“вЂќР±С“вЂР±С“В',
        'Language codes' => 'Р±С“вЂќР±С“СљР±С“ВР±С“РЋ Р±С“в„ўР±С“СњР±С“вЂњР±С“вЂќР±С“вЂР±С“В',
        'Status:' => 'Р±С“РЋР±С“СћР±С“С’Р±С“СћР±С“Р€Р±С“РЋР±С“В:',
    );

    if ('ru' === $language) {
        return $ru;
    }

    if ('de' === $language) {
        return $de;
    }

    if ('ka' === $language) {
        return $ka;
    }

    return array();
}

function aitmt_fallback_current_language()
{
    $enabled = aitmt_fallback_enabled_languages();
    $source = aitmt_fallback_source_language();

    $requested_language = aitmt_fallback_requested_language();
    if ($requested_language && in_array($requested_language, $enabled, true)) {
        return $requested_language;
    }

    if (!empty($_COOKIE['aitmt_language'])) {
        $cookie_language = aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_COOKIE['aitmt_language'])));
        if ($cookie_language && in_array($cookie_language, $enabled, true)) {
            return $cookie_language;
        }
    }

    return $source;
}

function aitmt_fallback_requested_language()
{
    $enabled = aitmt_fallback_enabled_languages();

    // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Public language switching is a read-only GET action.
    if (isset($_GET['lang'])) {
        $language = aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_GET['lang'])));
        if (in_array($language, $enabled, true)) {
            return $language;
        }
    }
    // phpcs:enable WordPress.Security.NonceVerification.Recommended

    if (!empty($GLOBALS['aitmt_request_language'])) {
        $language = aitmt_fallback_normalize_language((string) $GLOBALS['aitmt_request_language']);
        if (in_array($language, $enabled, true)) {
            return $language;
        }
    }

    $rewrite_language = get_query_var('aitmt_lang');
    if ($rewrite_language) {
        $language = aitmt_fallback_normalize_language((string) $rewrite_language);
        if (in_array($language, $enabled, true)) {
            return $language;
        }
    }

    $path_language = aitmt_fallback_language_from_path();
    if ($path_language && in_array($path_language, $enabled, true)) {
        return $path_language;
    }

    return '';
}

function aitmt_fallback_remember_language()
{
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    $language = aitmt_fallback_requested_language();
    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        return;
    }

    $path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
    $domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
    setcookie('aitmt_language', $language, time() + MONTH_IN_SECONDS, $path, $domain, is_ssl(), true);
    $_COOKIE['aitmt_language'] = $language;
}

function aitmt_fallback_language_from_path()
{
    if (!empty($GLOBALS['aitmt_path_language'])) {
        return aitmt_fallback_normalize_language((string) $GLOBALS['aitmt_path_language']);
    }

    $request_uri = aitmt_fallback_request_uri(true);
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = aitmt_fallback_strip_home_path($path, $home_path);
    $segments = array_values(array_filter(explode('/', trim($relative, '/'))));
    $language = isset($segments[0]) ? aitmt_fallback_normalize_language($segments[0]) : '';

    return $language && in_array($language, aitmt_fallback_enabled_languages(), true) ? $language : '';
}

function aitmt_fallback_language_url($language)
{
    $language = aitmt_fallback_normalize_language($language);
    $options = aitmt_fallback_options();
    $source = aitmt_fallback_source_language();
    $current = aitmt_fallback_current_language();
    $request_uri = aitmt_fallback_request_uri(true);
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $query = (string) wp_parse_url($request_uri, PHP_URL_QUERY);
    $query_args = array();

    if ('' !== $query) {
        wp_parse_str($query, $query_args);
    }

    unset($query_args['lang']);

    if ('query' === $options['url_mode'] || !file_exists(AITMT_PLUGIN_DIR . 'includes/class-aitmt-activator.php')) {
        $relative = trim(aitmt_fallback_strip_home_path($path, (string) wp_parse_url(home_url('/'), PHP_URL_PATH)), '/');
        $segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $relative))));
        $base_url = home_url(empty($segments) ? '/' : trailingslashit(implode('/', $segments)));
        if ($language !== $source || '1' !== $options['hide_default_language'] || $current !== $source) {
            $query_args['lang'] = $language;
        }

        return add_query_arg($query_args, $base_url);
    }

    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');
    $segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $relative))));

    if (empty($segments)) {
        if ($language !== $source || '1' !== $options['hide_default_language'] || $current !== $source) {
            $query_args['lang'] = $language;
        }

        return add_query_arg($query_args, home_url('/'));
    }

    if ($language !== $source || '1' !== $options['hide_default_language']) {
        array_unshift($segments, $language);
    } elseif ($current !== $source) {
        $query_args['lang'] = $language;
    }

    $new_path = implode('/', $segments);
    $url = home_url($new_path ? trailingslashit($new_path) : '/');

    return add_query_arg($query_args, $url);
}

function aitmt_fallback_strip_language_segment($segments)
{
    $segments = array_values((array) $segments);
    $all_languages = aitmt_fallback_enabled_languages();

    while (isset($segments[0]) && in_array(aitmt_fallback_normalize_language($segments[0]), $all_languages, true)) {
        array_shift($segments);
        $segments = array_values($segments);
    }

    return array_values($segments);
}

function aitmt_fallback_apply_language_to_url($url, $language)
{
    $url = trim(html_entity_decode((string) $url, ENT_QUOTES, 'UTF-8'));

    if ('' === $url || '#' === $url || 0 === strpos($url, '#')) {
        return $url;
    }

    if (preg_match('/^(mailto|tel|sms|javascript|data):/i', $url)) {
        return $url;
    }

    $language = aitmt_fallback_normalize_language($language);
    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        return $url;
    }

    $parts = wp_parse_url($url);
    if (false === $parts) {
        return $url;
    }

    $home_parts = wp_parse_url(home_url('/'));
    $home_host = isset($home_parts['host']) ? strtolower($home_parts['host']) : '';
    $url_host = isset($parts['host']) ? strtolower($parts['host']) : '';

    if ($url_host && $home_host && $url_host !== $home_host) {
        return $url;
    }

    $path = isset($parts['path']) ? $parts['path'] : '/';
    if (isset($url[0]) && '?' === $url[0]) {
        $path = (string) wp_parse_url(aitmt_fallback_request_uri(true), PHP_URL_PATH);
    }
    if ('' === $path) {
        $path = '/';
    }

    if (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), array('http', 'https'), true)) {
        return $url;
    }

    $home_path = isset($home_parts['path']) ? $home_parts['path'] : '/';
    $relative = trim(aitmt_fallback_strip_home_path($path, $home_path), '/');

    if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|css|js|json|xml|pdf|zip|rar|7z|mp4|mp3|woff|woff2|ttf|eot)$/i', $relative)) {
        return $url;
    }

    if (0 === strpos($relative, 'wp-admin') || 0 === strpos($relative, 'wp-login.php') || 0 === strpos($relative, 'wp-content') || 0 === strpos($relative, 'wp-includes')) {
        return $url;
    }

    $query_args = array();
    if (!empty($parts['query'])) {
        wp_parse_str($parts['query'], $query_args);
    }
    unset($query_args['lang']);

    $fragment = empty($parts['fragment']) ? '' : '#' . $parts['fragment'];
    $options = aitmt_fallback_options();
    $source = aitmt_fallback_source_language();
    $segments = aitmt_fallback_strip_language_segment(array_values(array_filter(explode('/', $relative))));

    if ('query' === $options['url_mode'] || empty($segments)) {
        $base_url = home_url(empty($segments) ? '/' : trailingslashit(implode('/', $segments)));
        if ($language !== $source || '1' !== $options['hide_default_language']) {
            $query_args['lang'] = $language;
        }

        return add_query_arg($query_args, $base_url) . $fragment;
    }

    if ($language !== $source || '1' !== $options['hide_default_language']) {
        array_unshift($segments, $language);
    }

    $translated_url = home_url(trailingslashit(implode('/', $segments)));

    return add_query_arg($query_args, $translated_url) . $fragment;
}

function aitmt_fallback_strip_home_path($path, $home_path)
{
    $home_path = '/' . trim((string) $home_path, '/');

    if ('/' === $home_path) {
        return '/' . ltrim((string) $path, '/');
    }

    if (0 === strpos((string) $path, $home_path)) {
        return '/' . ltrim(substr((string) $path, strlen($home_path)), '/');
    }

    return '/' . ltrim((string) $path, '/');
}

function aitmt_fallback_shortcode($atts = array())
{
    $options = aitmt_fallback_options();
    $atts = shortcode_atts(
        array(
            'style' => $options['selector_style'],
        ),
        is_array($atts) ? $atts : array(),
        'aitmt_language_switcher'
    );

    return aitmt_fallback_render_switcher((string) $atts['style']);
}

function aitmt_fallback_render_header_switcher()
{
    $options = aitmt_fallback_options();
    if ('1' !== $options['selector_header']) {
        return;
    }

    echo '<div class="aitmt-fallback-switcher-wrap aitmt-fallback-switcher-header notranslate" data-aitmt-no-translate="1" translate="no">';
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
    echo wp_kses(aitmt_fallback_render_switcher($options['selector_style']), aitmt_fallback_allowed_switcher_html());
    echo '</div>';
}

function aitmt_fallback_render_footer_switcher()
{
    $options = aitmt_fallback_options();
    if ('1' !== $options['selector_footer']) {
        return;
    }

    echo '<div class="aitmt-fallback-switcher-wrap aitmt-fallback-switcher-footer notranslate" data-aitmt-no-translate="1" translate="no">';
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
    echo wp_kses(aitmt_fallback_render_switcher($options['selector_style']), aitmt_fallback_allowed_switcher_html());
    echo '</div>';
}

function aitmt_fallback_boolish($value, $default = false)
{
    if (null === $value) {
        return (bool) $default;
    }

    if (is_bool($value)) {
        return $value;
    }

    $value = strtolower(trim((string) $value));
    if (in_array($value, array('1', 'true', 'yes', 'on'), true)) {
        return true;
    }

    if (in_array($value, array('0', 'false', 'no', 'off'), true)) {
        return false;
    }

    return (bool) $default;
}

function aitmt_fallback_switcher_default_display_args()
{
    $options = aitmt_fallback_options();

    return array(
        'show_flags' => '1' === (string) $options['selector_show_flags'],
        'show_names' => '1' === (string) $options['selector_show_names'],
        'show_codes' => '1' === (string) $options['selector_show_codes'],
        'show_current' => true,
        'hide_current' => false,
        'orientation' => 'horizontal',
        'open_current_page' => true,
    );
}

function aitmt_fallback_normalize_switcher_display_args($args = array())
{
    $args = is_array($args) ? $args : array();
    $display = wp_parse_args($args, aitmt_fallback_switcher_default_display_args());

    $display['show_flags'] = aitmt_fallback_boolish($display['show_flags']);
    $display['show_names'] = aitmt_fallback_boolish($display['show_names']);
    $display['show_codes'] = aitmt_fallback_boolish($display['show_codes']);
    $display['show_current'] = aitmt_fallback_boolish($display['show_current'], true);
    $display['hide_current'] = aitmt_fallback_boolish($display['hide_current']);
    $display['open_current_page'] = aitmt_fallback_boolish($display['open_current_page'], true);
    $display['orientation'] = in_array((string) $display['orientation'], array('horizontal', 'vertical'), true) ? (string) $display['orientation'] : 'horizontal';

    if (!$display['show_flags'] && !$display['show_names'] && !$display['show_codes']) {
        $display['show_codes'] = true;
    }

    return $display;
}

function aitmt_fallback_switcher_display_for_layout($layout, $display)
{
    $display = aitmt_fallback_normalize_switcher_display_args($display);

    if ('flags_only' === $layout) {
        $display['show_flags'] = true;
        $display['show_names'] = false;
        $display['show_codes'] = false;
    } elseif ('flags_name' === $layout) {
        $display['show_flags'] = true;
        $display['show_names'] = true;
    } elseif ('name_only' === $layout) {
        $display['show_flags'] = false;
        $display['show_names'] = true;
        $display['show_codes'] = false;
    }

    return $display;
}

function aitmt_fallback_render_switcher($style = '', $display_args = array())
{
    $languages = aitmt_fallback_enabled_languages();
    if (count($languages) < 2) {
        return '';
    }

    $options = aitmt_fallback_options();
    $style = sanitize_key($style ? $style : $options['selector_style']);
    $style = in_array($style, array('dropdown', 'list', 'buttons', 'flags_only', 'flags_name', 'name_only'), true) ? $style : 'dropdown';
    $display_args = aitmt_fallback_switcher_display_for_layout($style, $display_args);
    $current = aitmt_fallback_current_language();
    $visible_languages = array();

    foreach ($languages as $language) {
        if ($language === $current && ($display_args['hide_current'] || !$display_args['show_current'])) {
            continue;
        }

        $visible_languages[] = $language;
    }

    if (empty($visible_languages) && in_array($current, $languages, true)) {
        $visible_languages[] = $current;
    }

    if ('dropdown' !== $style) {
        $classes = array(
            'aitmt-fallback-switcher',
            'aitmt-fallback-switcher-list',
            'aitmt-switcher-layout-' . $style,
            'aitmt-switcher-orientation-' . $display_args['orientation'],
        );
        $classes[] = 'notranslate';
        $output = '<nav class="' . esc_attr(implode(' ', $classes)) . '" aria-label="' . esc_attr__('Language switcher', 'ait-multilingual-translate') . '" data-aitmt-no-translate="1" translate="no">';
        foreach ($visible_languages as $language) {
            $classes = array('aitmt-fallback-switcher-link');
            if ($language === $current) {
                $classes[] = 'is-current';
            }

            $output .= sprintf(
                '<a class="%1$s" href="%2$s" hreflang="%3$s" lang="%3$s" data-aitmt-no-translate="1" translate="no" %4$s aria-label="%5$s">%6$s</a>',
                esc_attr(implode(' ', $classes)),
                esc_url(aitmt_fallback_language_url($language)),
                esc_attr($language),
                $language === $current ? 'aria-current="true"' : '',
                esc_attr(aitmt_fallback_language_accessible_label($language)),
                aitmt_fallback_language_html_for_display($language, $display_args)
            );
        }
        $output .= '</nav>';

        return $output;
    }

    $id = 'aitmt-fallback-switcher-' . wp_rand(1000, 999999);
    $output = '<details class="aitmt-fallback-switcher aitmt-fallback-switcher-dropdown aitmt-switcher-layout-dropdown aitmt-custom-dropdown notranslate" data-aitmt-no-translate="1" translate="no">';
    $output .= '<summary class="aitmt-fallback-switcher-dropdown-control" aria-label="' . esc_attr__('Select language', 'ait-multilingual-translate') . '">';
    $output .= aitmt_fallback_language_html_for_display($current, $display_args);
    $output .= '</summary>';
    $output .= '<div id="' . esc_attr($id) . '" class="aitmt-fallback-switcher-dropdown-menu" role="listbox">';

    foreach ($visible_languages as $language) {
        $classes = array('aitmt-fallback-switcher-dropdown-link');
        if ($language === $current) {
            $classes[] = 'is-current';
        }

        $output .= sprintf(
            '<a class="%1$s" href="%2$s" hreflang="%3$s" lang="%3$s" data-aitmt-no-translate="1" translate="no" %4$s aria-label="%5$s">%6$s</a>',
            esc_attr(implode(' ', $classes)),
            esc_url(aitmt_fallback_language_url($language)),
            esc_attr($language),
            $language === $current ? 'aria-current="true"' : '',
            esc_attr(aitmt_fallback_language_accessible_label($language)),
            aitmt_fallback_language_html_for_display($language, $display_args)
        );
    }

    $output .= '</div></details>';

    return $output;
}

function aitmt_fallback_register_widget()
{
    if (class_exists('WP_Widget') && class_exists('AITMT_Fallback_Switcher_Widget')) {
        register_widget('AITMT_Fallback_Switcher_Widget');
    }
}

if (class_exists('WP_Widget') && !class_exists('AITMT_Fallback_Switcher_Widget')) {
    class AITMT_Fallback_Switcher_Widget extends WP_Widget
    {
        public function __construct()
        {
            parent::__construct(
                'aitmt_fallback_switcher',
                __('AIT Multilingual Translate Switcher', 'ait-multilingual-translate'),
                array('description' => __('Display the AIT Multilingual Translate language switcher.', 'ait-multilingual-translate'))
            );
        }

        public function widget($args, $instance)
        {
            $style = isset($instance['style']) && 'list' === $instance['style'] ? 'list' : 'dropdown';
            $switcher = aitmt_fallback_render_switcher($style);

            if ('' === $switcher) {
                return;
            }

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget wrapper is provided by WordPress/theme sidebars.
            echo isset($args['before_widget']) ? wp_kses_post($args['before_widget']) : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
            echo $switcher;
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget wrapper is provided by WordPress/theme sidebars.
            echo isset($args['after_widget']) ? wp_kses_post($args['after_widget']) : '';
        }

        public function form($instance)
        {
            $style = isset($instance['style']) && 'list' === $instance['style'] ? 'list' : 'dropdown';
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Style', 'ait-multilingual-translate'); ?></label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                    <option value="dropdown" <?php selected($style, 'dropdown'); ?>><?php esc_html_e('Dropdown', 'ait-multilingual-translate'); ?></option>
                    <option value="list" <?php selected($style, 'list'); ?>><?php esc_html_e('List', 'ait-multilingual-translate'); ?></option>
                </select>
            </p>
            <?php
        }

        public function update($new_instance, $old_instance)
        {
            return array(
                'style' => isset($new_instance['style']) && 'list' === $new_instance['style'] ? 'list' : 'dropdown',
            );
        }
    }
}

function aitmt_fallback_register_elementor_widget_legacy()
{
    if (!did_action('elementor/widgets/register') && class_exists('\Elementor\Plugin')) {
        aitmt_fallback_register_elementor_widget(\Elementor\Plugin::instance()->widgets_manager);
    }
}

function aitmt_fallback_register_elementor_widget($widgets_manager)
{
    if (!$widgets_manager || !class_exists('\Elementor\Widget_Base') || !class_exists('\Elementor\Controls_Manager')) {
        return;
    }

    if (!class_exists('AITMT_Fallback_Elementor_Switcher_Widget')) {
        class AITMT_Fallback_Elementor_Switcher_Widget extends \Elementor\Widget_Base
        {
            public function get_name()
            {
                return 'aitmt_language_switcher';
            }

            public function get_title()
            {
                return __('AI Translate Language Switcher', 'ait-multilingual-translate');
            }

            public function get_icon()
            {
                return 'eicon-globe';
            }

            public function get_categories()
            {
                return array('general');
            }

            public function get_keywords()
            {
                return array('language', 'translate', 'translation', 'ai', 'wp ai');
            }

            protected function register_controls()
            {
                $this->start_controls_section(
                    'aitmt_content',
                    array('label' => __('Language Switcher', 'ait-multilingual-translate'))
                );

                $this->add_control(
                    'layout_type',
                    array(
                        'label' => __('Layout type', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'dropdown',
                        'options' => array(
                            'list' => __('List', 'ait-multilingual-translate'),
                            'dropdown' => __('Dropdown', 'ait-multilingual-translate'),
                            'buttons' => __('Buttons', 'ait-multilingual-translate'),
                            'flags_only' => __('Flags only', 'ait-multilingual-translate'),
                            'flags_name' => __('Flags + language name', 'ait-multilingual-translate'),
                            'name_only' => __('Language name only', 'ait-multilingual-translate'),
                        ),
                    )
                );

                $this->add_control(
                    'show_flags',
                    array(
                        'label' => __('Show flags', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => '',
                    )
                );

                $this->add_control(
                    'show_language_name',
                    array(
                        'label' => __('Show language name', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => 'yes',
                    )
                );

                $this->add_control(
                    'show_language_code',
                    array(
                        'label' => __('Show language code', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => '',
                    )
                );

                $this->add_control(
                    'show_current_language',
                    array(
                        'label' => __('Show current language', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => 'yes',
                    )
                );

                $this->add_control(
                    'hide_current_language',
                    array(
                        'label' => __('Hide current language', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => '',
                    )
                );

                $this->add_control(
                    'open_current_page',
                    array(
                        'label' => __('Open links in current page', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __('Yes', 'ait-multilingual-translate'),
                        'label_off' => __('No', 'ait-multilingual-translate'),
                        'return_value' => 'yes',
                        'default' => 'yes',
                    )
                );

                $this->add_control(
                    'orientation',
                    array(
                        'label' => __('Orientation', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'horizontal',
                        'options' => array(
                            'horizontal' => __('Horizontal', 'ait-multilingual-translate'),
                            'vertical' => __('Vertical', 'ait-multilingual-translate'),
                        ),
                    )
                );

                $this->add_responsive_control(
                    'align',
                    array(
                        'label' => __('Alignment', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::CHOOSE,
                        'default' => 'left',
                        'options' => array(
                            'left' => array('title' => __('Left', 'ait-multilingual-translate'), 'icon' => 'eicon-text-align-left'),
                            'center' => array('title' => __('Center', 'ait-multilingual-translate'), 'icon' => 'eicon-text-align-center'),
                            'right' => array('title' => __('Right', 'ait-multilingual-translate'), 'icon' => 'eicon-text-align-right'),
                        ),
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-elementor-switcher' => 'text-align: {{VALUE}};',
                        ),
                    )
                );

                $this->end_controls_section();

                $this->start_controls_section(
                    'aitmt_style',
                    array(
                        'label' => __('Switcher Style', 'ait-multilingual-translate'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    )
                );

                if (class_exists('\Elementor\Group_Control_Typography')) {
                    $this->add_group_control(
                        \Elementor\Group_Control_Typography::get_type(),
                        array(
                            'name' => 'typography',
                            'selector' => '{{WRAPPER}} .aitmt-fallback-switcher, {{WRAPPER}} .aitmt-fallback-switcher-link',
                        )
                    );
                }

                $this->add_control(
                    'text_color',
                    array(
                        'label' => __('Text color', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher, {{WRAPPER}} .aitmt-fallback-switcher-link' => 'color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_control(
                    'hover_color',
                    array(
                        'label' => __('Hover color', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-link:hover, {{WRAPPER}} .aitmt-fallback-switcher-link:focus, {{WRAPPER}} .aitmt-fallback-switcher-dropdown:hover, {{WRAPPER}} .aitmt-fallback-switcher-dropdown:focus' => 'color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_control(
                    'active_language_color',
                    array(
                        'label' => __('Active language color', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-link.is-current' => 'color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_control(
                    'background_color',
                    array(
                        'label' => __('Background color', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown, {{WRAPPER}} .aitmt-fallback-switcher-link' => 'background-color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_control(
                    'border_color',
                    array(
                        'label' => __('Border color', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown, {{WRAPPER}} .aitmt-fallback-switcher-link' => 'border-color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_control(
                    'dropdown_background',
                    array(
                        'label' => __('Dropdown background', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown' => 'background-color: {{VALUE}};',
                        ),
                    )
                );

                $this->add_responsive_control(
                    'border_radius',
                    array(
                        'label' => __('Border radius', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => array('px', '%'),
                        'range' => array(
                            'px' => array('min' => 0, 'max' => 40),
                            '%' => array('min' => 0, 'max' => 50),
                        ),
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown, {{WRAPPER}} .aitmt-fallback-switcher-link' => 'border-radius: {{SIZE}}{{UNIT}};',
                        ),
                    )
                );

                $this->add_responsive_control(
                    'padding',
                    array(
                        'label' => __('Padding', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => array('px', 'em', '%'),
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown, {{WRAPPER}} .aitmt-fallback-switcher-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                    )
                );

                $this->add_responsive_control(
                    'gap',
                    array(
                        'label' => __('Gap between items', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => array('px'),
                        'range' => array('px' => array('min' => 0, 'max' => 40)),
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-list' => 'gap: {{SIZE}}{{UNIT}};',
                        ),
                    )
                );

                $this->add_responsive_control(
                    'dropdown_width',
                    array(
                        'label' => __('Dropdown width', 'ait-multilingual-translate'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => array('px', '%', 'em'),
                        'range' => array(
                            'px' => array('min' => 80, 'max' => 520),
                            '%' => array('min' => 10, 'max' => 100),
                            'em' => array('min' => 4, 'max' => 40),
                        ),
                        'selectors' => array(
                            '{{WRAPPER}} .aitmt-fallback-switcher-dropdown' => 'width: {{SIZE}}{{UNIT}};',
                        ),
                    )
                );

                if (class_exists('\Elementor\Group_Control_Box_Shadow')) {
                    $this->add_group_control(
                        \Elementor\Group_Control_Box_Shadow::get_type(),
                        array(
                            'name' => 'dropdown_shadow',
                            'label' => __('Dropdown shadow', 'ait-multilingual-translate'),
                            'selector' => '{{WRAPPER}} .aitmt-fallback-switcher-dropdown',
                        )
                    );
                }

                $this->end_controls_section();
            }

            protected function render()
            {
                $settings = $this->get_settings_for_display();
                $layout = isset($settings['layout_type']) ? sanitize_key((string) $settings['layout_type']) : (isset($settings['style']) ? sanitize_key((string) $settings['style']) : 'dropdown');
                $orientation = isset($settings['orientation']) && 'vertical' === $settings['orientation'] ? 'vertical' : 'horizontal';
                $align = isset($settings['align']) ? sanitize_key((string) $settings['align']) : 'left';

                $display = array(
                    'show_flags' => isset($settings['show_flags']) && 'yes' === $settings['show_flags'],
                    'show_names' => !isset($settings['show_language_name']) || 'yes' === $settings['show_language_name'],
                    'show_codes' => isset($settings['show_language_code']) && 'yes' === $settings['show_language_code'],
                    'show_current' => !isset($settings['show_current_language']) || 'yes' === $settings['show_current_language'],
                    'hide_current' => isset($settings['hide_current_language']) && 'yes' === $settings['hide_current_language'],
                    'open_current_page' => !isset($settings['open_current_page']) || 'yes' === $settings['open_current_page'],
                    'orientation' => $orientation,
                );

                echo '<div class="aitmt-elementor-switcher aitmt-elementor-align-' . esc_attr($align) . ' notranslate" data-aitmt-no-translate="1" translate="no">';
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
                echo wp_kses(aitmt_fallback_render_switcher($layout, $display), aitmt_fallback_allowed_switcher_html());
                echo '</div>';
            }
        }
    }

    if (method_exists($widgets_manager, 'register')) {
        $widgets_manager->register(new AITMT_Fallback_Elementor_Switcher_Widget());
    } elseif (method_exists($widgets_manager, 'register_widget_type')) {
        $widgets_manager->register_widget_type(new AITMT_Fallback_Elementor_Switcher_Widget());
    }
}

function aitmt_fallback_add_nav_menu_metabox()
{
    static $added = false;

    if ($added || !current_user_can('edit_theme_options')) {
        return;
    }

    $added = true;

    add_meta_box(
        'aitmt-language-switcher-menu',
        __('Language Switcher', 'ait-multilingual-translate'),
        'aitmt_fallback_nav_menu_metabox',
        'nav-menus',
        'side',
        'high'
    );
}

function aitmt_fallback_nav_menu_metabox()
{
    $languages = aitmt_fallback_languages();
    $enabled = aitmt_fallback_enabled_languages();
    ?>
    <div id="aitmt-language-switcher-menu-content" class="posttypediv aitmt-menu-metabox">
        <p class="description"><?php esc_html_e('Add the language switcher to a WordPress menu.', 'ait-multilingual-translate'); ?></p>
        <div class="aitmt-menu-metabox-options">
            <label><input type="checkbox" data-aitmt-menu-option="show-flag"> <?php esc_html_e('Show flag', 'ait-multilingual-translate'); ?></label>
            <label><input type="checkbox" data-aitmt-menu-option="show-name" checked> <?php esc_html_e('Show language name', 'ait-multilingual-translate'); ?></label>
            <label><input type="checkbox" data-aitmt-menu-option="show-code"> <?php esc_html_e('Show language code', 'ait-multilingual-translate'); ?></label>
            <label><input type="checkbox" data-aitmt-menu-option="hide-current"> <?php esc_html_e('Hide current language', 'ait-multilingual-translate'); ?></label>
            <fieldset>
                <legend class="screen-reader-text"><?php esc_html_e('Menu display mode', 'ait-multilingual-translate'); ?></legend>
                <label><input type="radio" name="aitmt_menu_display_mode" data-aitmt-menu-display value="list" checked> <?php esc_html_e('List mode', 'ait-multilingual-translate'); ?></label>
                <label><input type="radio" name="aitmt_menu_display_mode" data-aitmt-menu-display value="dropdown"> <?php esc_html_e('Dropdown mode', 'ait-multilingual-translate'); ?></label>
            </fieldset>
        </div>
        <div id="tabs-panel-aitmt-language-switcher" class="tabs-panel tabs-panel-active">
            <ul id="aitmt-language-switcher-checklist" class="categorychecklist form-no-clear">
                <?php aitmt_fallback_nav_menu_metabox_item(-91001, 'aitmt-current', __('Current language switcher', 'ait-multilingual-translate'), 'aitmt-menu-mode-current'); ?>
                <?php aitmt_fallback_nav_menu_metabox_item(-91002, 'aitmt-all', __('All enabled languages', 'ait-multilingual-translate'), 'aitmt-menu-mode-all'); ?>
                <?php foreach ($enabled as $index => $language) : ?>
                    <?php
                    $label = isset($languages[$language]) ? $languages[$language] : strtoupper($language);
                    /* translators: %s: Language name. */
                    aitmt_fallback_nav_menu_metabox_item(-91100 - (int) $index, 'aitmt-language-' . $language, sprintf(__('Specific language: %s', 'ait-multilingual-translate'), $label), 'aitmt-menu-mode-specific aitmt-menu-language-' . sanitize_html_class($language));
                    ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu', 'ait-multilingual-translate'); ?>" name="add-aitmt-language-switcher-menu-item" id="submit-aitmt-language-switcher-menu">
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

function aitmt_fallback_nav_menu_metabox_item($id, $object_id, $title, $mode_class)
{
    $classes = 'aitmt-menu-switcher ' . $mode_class . ' aitmt-menu-display-list aitmt-menu-show-name';
    ?>
    <li>
        <label class="menu-item-title">
            <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-object-id]" value="<?php echo esc_attr($object_id); ?>">
            <?php echo esc_html($title); ?>
        </label>
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-db-id]" value="0">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-object]" value="custom">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-parent-id]" value="0">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-type]" value="custom">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-status]" value="publish">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-title]" value="<?php echo esc_attr($title); ?>">
        <input type="hidden" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-url]" value="#aitmt-language-switcher">
        <input type="hidden" class="aitmt-menu-item-classes" data-aitmt-base-classes="<?php echo esc_attr('aitmt-menu-switcher ' . $mode_class); ?>" name="menu-item[<?php echo esc_attr((string) $id); ?>][menu-item-classes]" value="<?php echo esc_attr($classes); ?>">
    </li>
    <?php
}

function aitmt_fallback_nav_menu_objects($items, $args)
{
    $items = is_array($items) ? $items : array();
    $expanded = array();
    $current = aitmt_fallback_current_language();
    $enabled = aitmt_fallback_enabled_languages();
    $sequence = 1;

    foreach ($items as $item) {
        $classes = array_filter(array_map('strval', (array) $item->classes));

        if (!in_array('aitmt-menu-switcher', $classes, true)) {
            $expanded[] = $item;
            continue;
        }

        $display = aitmt_fallback_nav_menu_display_from_classes($classes);
        $mode = aitmt_fallback_nav_menu_mode_from_classes($classes);
        $languages = array();

        if ('current' === $mode) {
            $languages = $enabled;
            $mode = 'all';
        } elseif ('specific' === $mode) {
            $specific = aitmt_fallback_nav_menu_language_from_classes($classes);
            $languages = $specific && in_array($specific, $enabled, true) ? array($specific) : array();
        } else {
            $languages = $enabled;
        }

        if (empty($languages)) {
            continue;
        }

        if ('all' === $mode && 'dropdown' === $display['menu_display']) {
            $parent = clone $item;
            $parent->title = aitmt_fallback_language_text_for_display($current, $display);
            $parent->url = '#';
            $parent->classes = array_values(array_unique(array_merge($classes, array('menu-item-has-children', 'aitmt-menu-switcher-dropdown-toggle'))));
            $expanded[] = $parent;

            foreach ($languages as $language) {
                if ($language === $current && $display['hide_current']) {
                    continue;
                }

                $expanded[] = aitmt_fallback_nav_menu_language_item($item, $language, $display, $sequence, (int) $parent->ID);
                $sequence++;
            }

            continue;
        }

        foreach ($languages as $language) {
            if ($language === $current && $display['hide_current'] && 'current' !== $mode) {
                continue;
            }

            $expanded[] = aitmt_fallback_nav_menu_language_item($item, $language, $display, $sequence, (int) $item->menu_item_parent);
            $sequence++;
        }
    }

    return $expanded;
}

function aitmt_fallback_nav_menu_display_from_classes($classes)
{
    $display = array(
        'show_flags' => in_array('aitmt-menu-show-flag', $classes, true),
        'show_names' => in_array('aitmt-menu-show-name', $classes, true),
        'show_codes' => in_array('aitmt-menu-show-code', $classes, true),
        'hide_current' => in_array('aitmt-menu-hide-current', $classes, true),
        'show_current' => !in_array('aitmt-menu-hide-current', $classes, true),
        'orientation' => 'horizontal',
        'open_current_page' => true,
        'menu_display' => in_array('aitmt-menu-display-dropdown', $classes, true) ? 'dropdown' : 'list',
    );

    if (!$display['show_flags'] && !$display['show_names'] && !$display['show_codes']) {
        $display['show_names'] = true;
    }

    return $display;
}

function aitmt_fallback_nav_menu_mode_from_classes($classes)
{
    if (in_array('aitmt-menu-mode-current', $classes, true)) {
        return 'current';
    }

    if (in_array('aitmt-menu-mode-specific', $classes, true)) {
        return 'specific';
    }

    return 'all';
}

function aitmt_fallback_nav_menu_language_from_classes($classes)
{
    foreach ($classes as $class) {
        if ('aitmt-menu-language-item' === (string) $class) {
            continue;
        }

        if (0 === strpos((string) $class, 'aitmt-menu-language-')) {
            return aitmt_fallback_normalize_language(substr((string) $class, strlen('aitmt-menu-language-')));
        }
    }

    return '';
}

function aitmt_fallback_nav_menu_language_item($source_item, $language, $display, $sequence, $parent_id)
{
    $item = clone $source_item;
    $current = aitmt_fallback_current_language();
    $classes = array_diff(array_map('strval', (array) $source_item->classes), array('aitmt-menu-switcher', 'aitmt-menu-mode-current', 'aitmt-menu-mode-all', 'aitmt-menu-mode-specific', 'aitmt-menu-display-list', 'aitmt-menu-display-dropdown'));

    $item->ID = (int) $source_item->ID + 900000 + (int) $sequence;
    $item->db_id = 0;
    $item->object_id = 0;
    $item->menu_item_parent = (int) $parent_id;
    $item->title = aitmt_fallback_language_text_for_display($language, $display);
    $item->url = aitmt_fallback_language_url($language);
    $item->target = '';
    $item->attr_title = '';
    $item->description = '';
    $item->classes = array_values(array_unique(array_merge($classes, array('aitmt-menu-language-item', 'aitmt-menu-language-' . sanitize_html_class($language)))));

    if ($language === $current) {
        $item->classes[] = 'aitmt-menu-current-language';
        $item->classes[] = 'current-menu-item';
    }

    return $item;
}

function aitmt_fallback_nav_menu_item_title($title, $item, $args = null, $depth = 0)
{
    if (!is_object($item)) {
        return $title;
    }

    $classes = array_filter(array_map('strval', isset($item->classes) ? (array) $item->classes : array()));
    $is_language_item = in_array('aitmt-menu-language-item', $classes, true);
    $is_dropdown_toggle = in_array('aitmt-menu-switcher-dropdown-toggle', $classes, true);

    if (!$is_language_item && !$is_dropdown_toggle) {
        return $title;
    }

    $display = aitmt_fallback_nav_menu_display_from_classes($classes);
    $language = $is_dropdown_toggle ? aitmt_fallback_current_language() : aitmt_fallback_nav_menu_language_from_classes($classes);

    if (!$language) {
        return $title;
    }

    return aitmt_fallback_language_html_for_display($language, $display);
}

function aitmt_fallback_nav_menu_link_attributes($atts, $item, $args = null, $depth = 0)
{
    if (!is_object($item)) {
        return $atts;
    }

    $classes = array_filter(array_map('strval', isset($item->classes) ? (array) $item->classes : array()));
    $is_language_item = in_array('aitmt-menu-language-item', $classes, true);
    $is_dropdown_toggle = in_array('aitmt-menu-switcher-dropdown-toggle', $classes, true);

    if (!$is_language_item && !$is_dropdown_toggle) {
        return $atts;
    }

    $existing_classes = isset($atts['class']) ? preg_split('/\s+/', trim((string) $atts['class'])) : array();
    $existing_classes = is_array($existing_classes) ? array_filter($existing_classes) : array();
    $existing_classes[] = $is_language_item ? 'aitmt-menu-language-link' : 'aitmt-menu-language-toggle';

    $atts['class'] = implode(' ', array_values(array_unique($existing_classes)));
    $atts['data-aitmt-language-switcher'] = 'menu';
    $atts['data-aitmt-no-translate'] = '1';
    $atts['translate'] = 'no';

    if ($is_language_item) {
        $language = aitmt_fallback_nav_menu_language_from_classes($classes);
        if ($language) {
            $atts['data-aitmt-language'] = $language;
            $atts['aria-label'] = aitmt_fallback_language_accessible_label($language);
        }
    } elseif ($is_dropdown_toggle) {
        $atts['aria-label'] = aitmt_fallback_language_accessible_label(aitmt_fallback_current_language());
        $atts['aria-haspopup'] = 'true';
    }

    return $atts;
}

function aitmt_fallback_language_text($language)
{
    return aitmt_fallback_language_text_for_display($language);
}

function aitmt_fallback_language_text_for_display($language, $display_args = array())
{
    $display_args = aitmt_fallback_normalize_switcher_display_args($display_args);
    $parts = array();

    if ($display_args['show_flags']) {
        $parts[] = aitmt_fallback_flag($language);
    }

    if ($display_args['show_names']) {
        $languages = aitmt_fallback_languages();
        $parts[] = isset($languages[$language]) ? $languages[$language] : strtoupper($language);
    }

    if ($display_args['show_codes']) {
        $parts[] = strtoupper($language);
    }

    if (empty($parts)) {
        $parts[] = strtoupper($language);
    }

    return trim(implode(' ', $parts));
}

function aitmt_fallback_language_html($language)
{
    return aitmt_fallback_language_html_for_display($language);
}

function aitmt_fallback_language_html_for_display($language, $display_args = array())
{
    $display_args = aitmt_fallback_normalize_switcher_display_args($display_args);
    $parts = array();

    if ($display_args['show_flags']) {
        $parts[] = aitmt_fallback_flag_html($language);
    }

    if ($display_args['show_names']) {
        $languages = aitmt_fallback_languages();
        $label = isset($languages[$language]) ? $languages[$language] : strtoupper($language);
        $parts[] = '<span class="aitmt-fallback-language-name">' . esc_html($label) . '</span>';
    }

    if ($display_args['show_codes']) {
        $parts[] = '<span class="aitmt-fallback-language-code">' . esc_html(strtoupper($language)) . '</span>';
    }

    if (empty($parts)) {
        $parts[] = '<span class="aitmt-fallback-language-code">' . esc_html(strtoupper($language)) . '</span>';
    }

    return implode(' ', $parts);
}

function aitmt_fallback_language_accessible_label($language)
{
    $language = aitmt_fallback_normalize_language($language);
    $languages = aitmt_fallback_languages();
    $label = isset($languages[$language]) ? $languages[$language] : strtoupper($language);

    return $label . ' (' . strtoupper($language) . ')';
}

function aitmt_fallback_flag_html($language)
{
    $url = aitmt_fallback_flag_url($language);

    if (!$url) {
        return '<span class="aitmt-fallback-flag aitmt-flag" aria-hidden="true"></span>';
    }

    return '<img class="aitmt-fallback-flag aitmt-flag" src="' . esc_url($url) . '" alt="" aria-hidden="true" loading="lazy" decoding="async">';
}

function aitmt_fallback_flag_url($language)
{
    $country = strtolower(aitmt_fallback_flag_country($language));
    $relative = 'assets/flags/flag-icons/4x3/' . sanitize_file_name($country) . '.svg';

    if (!file_exists(AITMT_PLUGIN_DIR . $relative)) {
        return '';
    }

    return AITMT_PLUGIN_URL . $relative;
}

function aitmt_fallback_flag($language)
{
    $country = aitmt_fallback_flag_country($language);
    $entity = '';

    foreach (str_split(strtoupper($country)) as $letter) {
        $entity .= '&#' . (127397 + ord($letter)) . ';';
    }

    return html_entity_decode($entity, ENT_QUOTES, 'UTF-8');
}

function aitmt_fallback_flag_country($language)
{
    $language = aitmt_fallback_normalize_language($language);
    $map = array(
        'af' => 'ZA',
        'am' => 'ET',
        'en' => 'US',
        'ka' => 'GE',
        'ru' => 'RU',
        'de' => 'DE',
        'fr' => 'FR',
        'es' => 'ES',
        'it' => 'IT',
        'pt' => 'PT',
        'zh' => 'CN',
        'ja' => 'JP',
        'ko' => 'KR',
        'uk' => 'UA',
        'tr' => 'TR',
        'ar' => 'SA',
        'he' => 'IL',
        'sq' => 'AL',
        'hy' => 'AM',
        'az' => 'AZ',
        'eu' => 'ES',
        'be' => 'BY',
        'bn' => 'BD',
        'bs' => 'BA',
        'bg' => 'BG',
        'ca' => 'ES',
        'ceb' => 'PH',
        'co' => 'FR',
        'hr' => 'HR',
        'cs' => 'CZ',
        'da' => 'DK',
        'nl' => 'NL',
        'eo' => 'UN',
        'et' => 'EE',
        'fi' => 'FI',
        'fy' => 'NL',
        'gl' => 'ES',
        'el' => 'GR',
        'gu' => 'IN',
        'ht' => 'HT',
        'ha' => 'NG',
        'haw' => 'US',
        'hi' => 'IN',
        'hmn' => 'CN',
        'hu' => 'HU',
        'is' => 'IS',
        'ig' => 'NG',
        'id' => 'ID',
        'ga' => 'IE',
        'jv' => 'ID',
        'kn' => 'IN',
        'kk' => 'KZ',
        'km' => 'KH',
        'ku' => 'IQ',
        'ky' => 'KG',
        'lo' => 'LA',
        'la' => 'VA',
        'lv' => 'LV',
        'lt' => 'LT',
        'lb' => 'LU',
        'mk' => 'MK',
        'mg' => 'MG',
        'ms' => 'MY',
        'ml' => 'IN',
        'mt' => 'MT',
        'mi' => 'NZ',
        'mr' => 'IN',
        'mn' => 'MN',
        'my' => 'MM',
        'ne' => 'NP',
        'no' => 'NO',
        'ny' => 'MW',
        'ps' => 'AF',
        'fa' => 'IR',
        'pa' => 'IN',
        'ro' => 'RO',
        'sm' => 'WS',
        'gd' => 'GB-SCT',
        'sr' => 'RS',
        'st' => 'LS',
        'sn' => 'ZW',
        'sd' => 'PK',
        'si' => 'LK',
        'sk' => 'SK',
        'sl' => 'SI',
        'so' => 'SO',
        'su' => 'ID',
        'sw' => 'TZ',
        'sv' => 'SE',
        'tl' => 'PH',
        'tg' => 'TJ',
        'ta' => 'IN',
        'te' => 'IN',
        'th' => 'TH',
        'ur' => 'PK',
        'ug' => 'CN',
        'uz' => 'UZ',
        'vi' => 'VN',
        'cy' => 'GB-WLS',
        'xh' => 'ZA',
        'yi' => 'IL',
        'yo' => 'NG',
        'zu' => 'ZA',
    );

    return isset($map[$language]) ? $map[$language] : strtoupper(substr($language, 0, 2));
}

function aitmt_fallback_inline_styles()
{
    // Kept for backward compatibility with older builds. Assets are enqueued through wp_enqueue_scripts.
}

function aitmt_fallback_start_translation()
{
    if (!aitmt_fallback_should_translate_request()) {
        return;
    }

    if (!empty($GLOBALS['aitmt_fallback_translation_buffer_level'])) {
        return;
    }

    $GLOBALS['aitmt_fallback_translation_buffer_level'] = ob_get_level() + 1;
    ob_start('aitmt_fallback_translate_html');
    add_action('shutdown', 'aitmt_fallback_flush_translation_buffer', 0);
}

function aitmt_fallback_flush_translation_buffer()
{
    $level = isset($GLOBALS['aitmt_fallback_translation_buffer_level']) ? (int) $GLOBALS['aitmt_fallback_translation_buffer_level'] : 0;
    if ($level <= 0) {
        return;
    }

    while (ob_get_level() >= $level) {
        ob_end_flush();
    }

    unset($GLOBALS['aitmt_fallback_translation_buffer_level']);
}

function aitmt_fallback_should_translate_request()
{
    $options = aitmt_fallback_options();

    if (is_admin() || wp_doing_ajax() || is_feed() || is_robots() || is_trackback()) {
        return false;
    }

    if (defined('REST_REQUEST') && REST_REQUEST) {
        return false;
    }

    if ('1' !== $options['auto_translate'] && !aitmt_fallback_frontend_editor_enabled()) {
        return false;
    }

    return aitmt_fallback_current_language() !== aitmt_fallback_source_language();
}

function aitmt_fallback_translate_html($html)
{
    if ('' === trim((string) $html) || !class_exists('DOMDocument')) {
        return $html;
    }

    $target_language = aitmt_fallback_current_language();
    $source_language = aitmt_fallback_source_language();

    if ($target_language === $source_language) {
        return $html;
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    if (!$loaded) {
        return $html;
    }

    foreach ($dom->childNodes as $child) {
        if (XML_PI_NODE === $child->nodeType) {
            $dom->removeChild($child);
            break;
        }
    }

    $text_nodes = array();
    $attribute_nodes = array();
    aitmt_fallback_collect_translation_nodes($dom, $text_nodes, $attribute_nodes);

    $segments = array();
    foreach ($text_nodes as $item) {
        $segments[$item['hash']] = $item['source'];
    }

    $options = aitmt_fallback_options();
    if ('1' === $options['translate_attributes']) {
        foreach ($attribute_nodes as $item) {
            $segments[$item['hash']] = $item['source'];
        }
    }

    if (empty($segments)) {
        aitmt_fallback_rewrite_dom_links($dom, $target_language);
        return $dom->saveHTML();
    }

    $translations = aitmt_fallback_translate_segments($segments, $source_language, $target_language);
    $editor_mode = aitmt_fallback_frontend_editor_enabled();
    if (empty($translations) && !$editor_mode) {
        aitmt_fallback_rewrite_dom_links($dom, $target_language);
        return $dom->saveHTML();
    }

    foreach ($text_nodes as $item) {
        if (!$item['node']->parentNode) {
            continue;
        }

        if (!isset($translations[$item['hash']]) && !$editor_mode) {
            continue;
        }

        $replacement = isset($translations[$item['hash']]) ? $translations[$item['hash']] : $item['source'];

        aitmt_fallback_replace_text_node(
            $dom,
            $item['node'],
            $item['original'],
            $item['source'],
            $replacement,
            $item['hash']
        );
    }

    if ('1' === $options['translate_attributes']) {
        foreach ($attribute_nodes as $item) {
            if (!isset($translations[$item['hash']])) {
                continue;
            }

            $item['node']->setAttribute($item['attribute'], $translations[$item['hash']]);
        }
    }

    aitmt_fallback_rewrite_dom_links($dom, $target_language);

    return $dom->saveHTML();
}

function aitmt_fallback_rewrite_dom_links($dom, $target_language)
{
    if (!$dom instanceof DOMDocument) {
        return;
    }

    $target_language = aitmt_fallback_normalize_language($target_language);
    if (!$target_language || $target_language === aitmt_fallback_source_language()) {
        return;
    }

    $url_attributes = array('href', 'action', 'data-href', 'data-url', 'data-link', 'data-product-url', 'data-product-permalink', 'data-permalink', 'data-product_permalink');

    foreach ($dom->getElementsByTagName('*') as $element) {
        if (!$element instanceof DOMElement) {
            continue;
        }

        $class = ' ' . $element->getAttribute('class') . ' ';
        if (
            $element->hasAttribute('data-aitmt-no-translate') ||
            $element->hasAttribute('data-aitmt-language-switcher') ||
            false !== strpos($class, ' aitmt-fallback-switcher-link ')
            || false !== strpos($class, ' aitmt-fallback-switcher-dropdown-link ')
            || false !== strpos($class, ' aitmt-switcher-dropdown-link ')
            || false !== strpos($class, ' aitmt-switcher-link ')
            || false !== strpos($class, ' aitmt-editor-toolbar ')
            || false !== strpos($class, ' notranslate ')
            || aitmt_fallback_dom_has_class_in_ancestry($element, array('aitmt-menu-language-item', 'aitmt-menu-current-language', 'aitmt-menu-switcher-dropdown-toggle'))
        ) {
            continue;
        }

        foreach ($url_attributes as $attribute) {
            if (!$element->hasAttribute($attribute)) {
                continue;
            }

            $url = $element->getAttribute($attribute);
            $translated_url = aitmt_fallback_apply_language_to_url($url, $target_language);

            if ($translated_url !== $url) {
                $element->setAttribute($attribute, $translated_url);
            }
        }
    }
}

function aitmt_fallback_dom_has_class_in_ancestry($element, $classes)
{
    if (!$element instanceof DOMElement) {
        return false;
    }

    $classes = array_filter(array_map('strval', (array) $classes));
    if (empty($classes)) {
        return false;
    }

    $node = $element;
    while ($node instanceof DOMElement) {
        $node_classes = ' ' . $node->getAttribute('class') . ' ';
        foreach ($classes as $class) {
            if (false !== strpos($node_classes, ' ' . $class . ' ')) {
                return true;
            }
        }

        $node = $node->parentNode;
    }

    return false;
}

function aitmt_fallback_replace_text_node($dom, $node, $original, $source, $translation, $hash)
{
    if (!$node->parentNode) {
        return;
    }

    $translation = aitmt_fallback_apply_original_spacing($original, $translation);

    if (aitmt_fallback_frontend_editor_enabled() && aitmt_fallback_can_wrap_for_editor($node)) {
        $span = $dom->createElement('span');
        $span->setAttribute('class', 'aitmt-editable');
        $span->setAttribute('data-aitmt-source-hash', $hash);
        $span->setAttribute('data-aitmt-source', base64_encode($source));
        $span->appendChild($dom->createTextNode($translation));
        $node->parentNode->replaceChild($span, $node);

        return;
    }

    $node->nodeValue = $translation;
}

function aitmt_fallback_frontend_editor_enabled()
{
    $options = aitmt_fallback_options();

    return '1' === $options['frontend_editor']
        && current_user_can('manage_options')
        && '' !== aitmt_fallback_frontend_editor_target_language();
}

function aitmt_fallback_frontend_editor_target_language()
{
    $source_language = aitmt_fallback_source_language();
    $current_language = aitmt_fallback_current_language();

    if ($current_language && $current_language !== $source_language) {
        return $current_language;
    }

    return '';
}

function aitmt_fallback_can_wrap_for_editor($node)
{
    $current = $node->parentNode;

    while ($current instanceof DOMElement) {
        $tag = strtolower($current->tagName);

        if (in_array($tag, array('head', 'title', 'option', 'select', 'script', 'style'), true)) {
            return false;
        }

        $class = ' ' . $current->getAttribute('class') . ' ';
        if (false !== strpos($class, ' aitmt-editable ') || false !== strpos($class, ' aitmt-editor-toolbar ')) {
            return false;
        }

        $current = $current->parentNode;
    }

    return true;
}

function aitmt_fallback_enqueue_frontend_editor()
{
    wp_enqueue_style('aitmt-frontend', AITMT_PLUGIN_URL . 'assets/css/frontend.css', array(), AITMT_VERSION);
    $frontend_css = aitmt_fallback_asset_contents('assets/css/frontend.css');
    wp_add_inline_style('aitmt-frontend', $frontend_css ? $frontend_css : aitmt_fallback_frontend_inline_css());

    $options = aitmt_fallback_options();
    if (!empty($options['selector_custom_css'])) {
        wp_add_inline_style('aitmt-frontend', wp_strip_all_tags((string) $options['selector_custom_css']));
    }

    if (!aitmt_fallback_frontend_editor_enabled()) {
        return;
    }

    wp_enqueue_script('aitmt-frontend-editor', AITMT_PLUGIN_URL . 'assets/js/frontend-editor.js', array(), AITMT_VERSION, true);
    wp_localize_script('aitmt-frontend-editor', 'AITMT_EDITOR', aitmt_fallback_frontend_editor_config());
    $frontend_editor_js = aitmt_fallback_asset_contents('assets/js/frontend-editor.js');
    wp_add_inline_script('aitmt-frontend-editor', $frontend_editor_js ? $frontend_editor_js : aitmt_fallback_frontend_editor_inline_script(), 'after');
}

function aitmt_fallback_admin_bar_editor_node($wp_admin_bar)
{
    if (!aitmt_fallback_frontend_editor_enabled() || !is_admin_bar_showing()) {
        return;
    }

    $wp_admin_bar->add_node(array(
        'id' => 'aitmt-frontend-editor',
        'title' => __('AI Translate Edit', 'ait-multilingual-translate'),
        'href' => '#',
        'meta' => array(
            'class' => 'aitmt-adminbar-editor',
            'onclick' => 'window.AITMT_INLINE_TOGGLE && window.AITMT_INLINE_TOGGLE(); return false;',
        ),
    ));
}

function aitmt_fallback_render_frontend_editor_inline()
{
    // Deprecated. Frontend editor data is localized into assets/js/frontend-editor.js.
}

function aitmt_fallback_frontend_editor_config()
{
    $target_language = aitmt_fallback_frontend_editor_target_language();

    return array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aitmt_frontend_editor'),
        'sourceLanguage' => aitmt_fallback_source_language(),
        'targetLanguage' => $target_language,
        'editLabel' => __('AI Translate edit', 'ait-multilingual-translate'),
        'activeLabel' => __('Editing on', 'ait-multilingual-translate'),
        'promptLabel' => __('Edit translation', 'ait-multilingual-translate'),
        'saveLabel' => __('Save', 'ait-multilingual-translate'),
        'cancelLabel' => __('Cancel', 'ait-multilingual-translate'),
        'autoTranslateLabel' => __('Auto Translate', 'ait-multilingual-translate'),
        'translatingLabel' => __('AI translating...', 'ait-multilingual-translate'),
        'translationReadyLabel' => __('AI Translation Ready', 'ait-multilingual-translate'),
        'translateFailedLabel' => __('Translation failed. Please try again.', 'ait-multilingual-translate'),
        'savingLabel' => __('Saving...', 'ait-multilingual-translate'),
        'savedLabel' => __('Saved', 'ait-multilingual-translate'),
        'errorLabel' => __('Could not save translation', 'ait-multilingual-translate'),
        'emptyLabel' => __('No editable translation text found yet. Scan/process the queue, or click source text that is highlighted for admins.', 'ait-multilingual-translate'),
        /* translators: %s: Target language code. */
        'targetNotice' => $target_language ? sprintf(__('Target: %s', 'ait-multilingual-translate'), strtoupper($target_language)) : '',
        'logoUrl' => aitmt_fallback_logo_url(),
        'brandLabel' => sprintf('AI Translate %s | %s | sotter IT Design | info@itdesign.biz', AITMT_VERSION, aitmt_fallback_edition_label()),
        'siteUrl' => 'https://wp-ai.itdesign.biz',
    );
}

function aitmt_fallback_frontend_editor_inline_script()
{
    return <<<'JS'
(function(){if(!window.AITMT_EDITOR||window.AITMT_INLINE_EDITOR_READY){return;}window.AITMT_INLINE_EDITOR_READY=true;var c=window.AITMT_EDITOR||{},active=false,current=null,toolbar=document.createElement('div'),brand=document.createElement('a'),img=document.createElement('img'),brandText=document.createElement('span'),button=document.createElement('button'),status=document.createElement('span'),modal=document.createElement('div'),dialog=document.createElement('div'),title=document.createElement('h2'),textarea=document.createElement('textarea'),actions=document.createElement('div'),save=document.createElement('button'),cancel=document.createElement('button');toolbar.className='aitmt-inline-editor-toolbar';toolbar.setAttribute('data-aitmt-no-translate','1');brand.className='aitmt-inline-editor-brand';brand.href=c.siteUrl||'#';brand.target='_blank';brand.rel='noopener noreferrer';if(c.logoUrl){img.src=c.logoUrl;img.alt='';brand.appendChild(img);}brandText.textContent=c.brandLabel||'AI Translate';brand.appendChild(brandText);button.type='button';button.textContent=c.editLabel||'AI Translate edit';status.className='aitmt-inline-editor-status';status.textContent=c.targetNotice||'';toolbar.appendChild(brand);toolbar.appendChild(button);toolbar.appendChild(status);modal.className='aitmt-inline-editor-modal';modal.setAttribute('data-aitmt-no-translate','1');modal.setAttribute('aria-hidden','true');dialog.className='aitmt-inline-editor-dialog';dialog.setAttribute('role','dialog');dialog.setAttribute('aria-modal','true');title.textContent=c.promptLabel||'Edit translation';textarea.className='aitmt-inline-editor-textarea';actions.className='aitmt-inline-editor-dialog-actions';save.type='button';save.textContent=c.saveLabel||'Save';cancel.type='button';cancel.textContent=c.cancelLabel||'Cancel';cancel.className='is-secondary';actions.appendChild(cancel);actions.appendChild(save);dialog.appendChild(title);dialog.appendChild(textarea);dialog.appendChild(actions);modal.appendChild(dialog);function mount(){if(document.body&&!document.querySelector('.aitmt-inline-editor-toolbar')){document.body.appendChild(toolbar);document.body.appendChild(modal);}}function setActive(next){active=typeof next==='boolean'?next:!active;document.body.classList.toggle('aitmt-editor-active',active);button.classList.toggle('is-active',active);status.textContent=active?(c.activeLabel||'Editing on'):(c.targetNotice||'');if(active&&!document.querySelector('.aitmt-editable')){status.textContent=c.emptyLabel||'No editable text found yet.';}}window.AITMT_INLINE_TOGGLE=function(){setActive();};button.addEventListener('click',function(){setActive();});document.addEventListener('click',function(e){var target=e.target.closest?e.target.closest('.aitmt-editable'):null;if(!active||!target){return;}e.preventDefault();e.stopPropagation();current=target;textarea.value=target.textContent;modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');setTimeout(function(){textarea.focus();textarea.select();},20);},true);function close(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');current=null;textarea.value='';save.disabled=false;}modal.addEventListener('click',function(e){if(e.target===modal){close();}});cancel.addEventListener('click',close);document.addEventListener('keydown',function(e){if(e.key==='Escape'&&modal.classList.contains('is-open')){close();}});function decode(v){try{var b=window.atob(v),out='',i;for(i=0;i<b.length;i++){out+='%'+('00'+b.charCodeAt(i).toString(16)).slice(-2);}return decodeURIComponent(out);}catch(e){return '';}}save.addEventListener('click',function(){if(!current){close();return;}var next=textarea.value;if(next===current.textContent){close();return;}var body=new URLSearchParams();body.set('action','aitmt_save_translation');body.set('nonce',c.nonce||'');body.set('sourceLanguage',c.sourceLanguage||'');body.set('targetLanguage',c.targetLanguage||'');body.set('sourceText',decode(current.getAttribute('data-aitmt-source')||''));body.set('translatedText',next);status.textContent='...';save.disabled=true;fetch(c.ajaxUrl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString()}).then(function(r){if(!r.ok){throw new Error('Save failed');}return r.json();}).then(function(p){if(!p||!p.success){throw new Error('Save failed');}current.textContent=next;close();status.textContent=c.savedLabel||'Saved';setTimeout(function(){if(active){status.textContent=c.activeLabel||'Editing on';}},1600);}).catch(function(){status.textContent=c.errorLabel||'Error';save.disabled=false;});});if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',mount);}else{mount();}})();
JS;
}

function aitmt_fallback_collect_translation_nodes($node, &$text_nodes, &$attribute_nodes)
{
    if ($node instanceof DOMElement) {
        $tag = strtolower($node->tagName);
        $skip_tags = array('script', 'style', 'noscript', 'code', 'pre', 'textarea', 'svg', 'canvas', 'iframe', 'object');

        if (in_array($tag, $skip_tags, true)) {
            return;
        }

        $class = ' ' . $node->getAttribute('class') . ' ';
        if (
            'wpadminbar' === $node->getAttribute('id')
            || false !== strpos($class, ' aitmt-fallback-switcher')
            || false !== strpos($class, ' aitmt-switcher')
            || false !== strpos($class, ' aitmt-menu-language-item')
            || false !== strpos($class, ' aitmt-menu-current-language')
            || false !== strpos($class, ' aitmt-menu-switcher-dropdown-toggle')
            || false !== strpos($class, ' aitmt-editor-toolbar')
            || false !== strpos($class, ' aitmt-editable')
            || false !== strpos($class, ' notranslate ')
        ) {
            return;
        }

        if ($node->hasAttribute('data-aitmt-no-translate') || $node->hasAttribute('data-aitmt-language-switcher') || ($node->hasAttribute('translate') && 'no' === strtolower($node->getAttribute('translate')))) {
            return;
        }

        aitmt_fallback_collect_translation_attributes($node, $attribute_nodes);
    }

    if (XML_TEXT_NODE === $node->nodeType) {
        $original = isset($node->nodeValue) ? $node->nodeValue : '';
        $source = aitmt_fallback_normalize_text((string) $original);

        if (aitmt_fallback_is_translatable_text($source)) {
            $text_nodes[] = array(
                'node' => $node,
                'original' => (string) $original,
                'source' => $source,
                'hash' => aitmt_fallback_translation_hash($source),
            );
        }
    }

    if (!$node->hasChildNodes()) {
        return;
    }

    foreach (iterator_to_array($node->childNodes) as $child) {
        aitmt_fallback_collect_translation_nodes($child, $text_nodes, $attribute_nodes);
    }
}

function aitmt_fallback_collect_translation_attributes($node, &$attribute_nodes)
{
    $attributes = array('alt', 'title', 'placeholder', 'aria-label');
    $tag = strtolower($node->tagName);

    if ('input' === $tag || 'button' === $tag) {
        $attributes[] = 'value';
    }

    if ('meta' === $tag && $node->hasAttribute('content')) {
        $name = strtolower($node->getAttribute('name') ? $node->getAttribute('name') : $node->getAttribute('property'));
        if (in_array($name, array('description', 'og:title', 'og:description', 'twitter:title', 'twitter:description'), true)) {
            $attributes[] = 'content';
        }
    }

    foreach (array_unique($attributes) as $attribute) {
        if (!$node->hasAttribute($attribute)) {
            continue;
        }

        $source = aitmt_fallback_normalize_text($node->getAttribute($attribute));
        if (!aitmt_fallback_is_translatable_text($source)) {
            continue;
        }

        $attribute_nodes[] = array(
            'node' => $node,
            'attribute' => $attribute,
            'source' => $source,
            'hash' => aitmt_fallback_translation_hash($source),
        );
    }
}

function aitmt_fallback_translate_segments($segments, $source_language, $target_language)
{
    $clean_segments = array();
    foreach ($segments as $hash => $text) {
        $text = aitmt_fallback_normalize_text((string) $text);
        if (aitmt_fallback_is_translatable_text($text)) {
            $clean_segments[$hash] = $text;
        }
    }

    if (empty($clean_segments)) {
        return array();
    }

    $existing = aitmt_fallback_get_existing_translations($clean_segments, $source_language, $target_language);
    if (!empty($existing)) {
        aitmt_fallback_provider_stats_record_cache_hits(aitmt_fallback_active_provider(), count($existing));
    }
    $missing = array_diff_key($clean_segments, $existing);

    if (empty($missing)) {
        return $existing;
    }

    $options = aitmt_fallback_options();
    $limit = isset($options['max_segments_per_request']) ? absint($options['max_segments_per_request']) : 40;
    $missing = array_slice($missing, 0, max(1, min(100, $limit)), true);

    if ('1' === $options['queue_missing']) {
        aitmt_fallback_enqueue_translation_batch($missing, $source_language, $target_language);
    }

    if ('1' !== $options['translate_on_page_load']) {
        return $existing;
    }

    $translated = aitmt_fallback_translate_with_provider($missing, $source_language, $target_language);

    if (!is_wp_error($translated) && is_array($translated)) {
        $status = '1' === $options['draft_mode'] ? 'draft' : 'published';
        aitmt_fallback_save_translation_batch($missing, $translated, $source_language, $target_language, $status);

        if ('draft' !== $status || current_user_can('manage_options')) {
            $existing = array_merge($existing, $translated);
        }
    } elseif (is_wp_error($translated)) {
        aitmt_fallback_log($translated->get_error_message());
    }

    return $existing;
}

function aitmt_fallback_openai_translate_batch($segments, $source_language, $target_language)
{
    $api_key = aitmt_fallback_provider_key('openai');

    if (empty($api_key)) {
        return new WP_Error('aitmt_missing_openai_key', 'OpenAI API key is missing.');
    }

    $languages = aitmt_fallback_languages();
    $source_name = isset($languages[$source_language]) ? $languages[$source_language] : strtoupper($source_language);
    $target_name = isset($languages[$target_language]) ? $languages[$target_language] : strtoupper($target_language);
    $translation_instruction = aitmt_fallback_translation_instruction();
    $items = array();

    foreach ($segments as $hash => $text) {
        $items[] = array(
            'id' => $hash,
            'text' => $text,
        );
    }

    $schema = array(
        'type' => 'object',
        'additionalProperties' => false,
        'properties' => array(
            'translations' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => array(
                        'id' => array('type' => 'string'),
                        'text' => array('type' => 'string'),
                    ),
                    'required' => array('id', 'text'),
                ),
            ),
        ),
        'required' => array('translations'),
    );

    $body = array(
        'model' => aitmt_fallback_provider_model('openai'),
        'temperature' => aitmt_fallback_translation_temperature(),
        'instructions' => sprintf(
            'Translate to %s (%s). Preserve formatting. Return only translated text for each segment. %s Preserve placeholders, numbers, emails, URLs, shortcodes, HTML entities, and brand names. Return only valid JSON that matches the schema.',
            $target_name,
            strtoupper($target_language),
            $translation_instruction
        ),
        'input' => wp_json_encode(array('segments' => $items), JSON_UNESCAPED_UNICODE),
        'text' => array(
            'format' => array(
                'type' => 'json_schema',
                'name' => 'aitmt_fallback_translate_batch',
                'schema' => $schema,
                'strict' => true,
            ),
        ),
    );

    $response = wp_remote_post(
        'https://api.openai.com/v1/responses',
        array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($body),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $raw_body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300) {
        $message = isset($data['error']['message']) ? $data['error']['message'] : 'OpenAI request failed.';
        if (429 === $status) {
            aitmt_fallback_set_provider_cooldown('openai', HOUR_IN_SECONDS);
        }

        return new WP_Error('aitmt_openai_error', $message, array('status' => $status));
    }

    $output_text = aitmt_fallback_extract_openai_output_text(is_array($data) ? $data : array());
    $decoded = json_decode($output_text, true);

    if (!is_array($decoded)) {
        $decoded = json_decode(aitmt_fallback_strip_json_fence($output_text), true);
    }

    if (!is_array($decoded) || empty($decoded['translations']) || !is_array($decoded['translations'])) {
        return new WP_Error('aitmt_openai_parse_error', 'OpenAI returned an unexpected translation payload.');
    }

    $translations = array();
    foreach ($decoded['translations'] as $item) {
        if (empty($item['id']) || !array_key_exists('text', $item)) {
            continue;
        }

        $translations[(string) $item['id']] = (string) $item['text'];
    }

    return $translations;
}

function aitmt_fallback_extract_openai_output_text($data)
{
    if (isset($data['output_text']) && is_string($data['output_text'])) {
        return $data['output_text'];
    }

    if (empty($data['output']) || !is_array($data['output'])) {
        return '';
    }

    foreach ($data['output'] as $output) {
        if (!isset($output['content']) || !is_array($output['content'])) {
            continue;
        }

        foreach ($output['content'] as $content) {
            if (isset($content['text']) && is_string($content['text'])) {
                return $content['text'];
            }
        }
    }

    return '';
}

function aitmt_fallback_strip_json_fence($text)
{
    $text = trim((string) $text);
    $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
    $text = preg_replace('/\s*```$/', '', (string) $text);

    return trim((string) $text);
}

function aitmt_fallback_extract_error_message($data, $fallback)
{
    if (isset($data['error']['message']) && is_string($data['error']['message'])) {
        return $data['error']['message'];
    }

    if (isset($data['message']) && is_string($data['message'])) {
        return $data['message'];
    }

    if (isset($data['error']) && is_string($data['error'])) {
        return $data['error'];
    }

    return $fallback;
}

function aitmt_fallback_gemini_translate_batch($segments, $source_language, $target_language)
{
    $api_key = aitmt_fallback_provider_key('gemini');

    if (empty($api_key)) {
        return new WP_Error('aitmt_missing_gemini_key', 'Gemini API key is missing.');
    }

    $languages = aitmt_fallback_languages();
    $source_name = isset($languages[$source_language]) ? $languages[$source_language] : strtoupper($source_language);
    $target_name = isset($languages[$target_language]) ? $languages[$target_language] : strtoupper($target_language);
    $translation_instruction = aitmt_fallback_translation_instruction();
    $items = array();

    foreach ($segments as $hash => $text) {
        $items[] = array(
            'id' => $hash,
            'text' => $text,
        );
    }

    $prompt = sprintf(
        "Translate to %s (%s). Preserve formatting. Return only translated text for each segment.\n%s\nPreserve placeholders, numbers, emails, URLs, shortcodes, HTML entities, and brand names.\nReturn only JSON in this exact shape: {\"translations\":[{\"id\":\"same id\",\"text\":\"translated text\"}]}.\n\nSegments:\n%s",
        $target_name,
        strtoupper($target_language),
        $translation_instruction,
        wp_json_encode($items, JSON_UNESCAPED_UNICODE)
    );

    $body = array(
        'contents' => array(
            array(
                'role' => 'user',
                'parts' => array(
                    array('text' => $prompt),
                ),
            ),
        ),
        'generationConfig' => array(
            'temperature' => aitmt_fallback_translation_temperature(),
            'responseMimeType' => 'application/json',
        ),
    );

    $model = aitmt_fallback_provider_model('gemini');
    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';
    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 60,
            'headers' => array(
                'x-goog-api-key' => $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($body),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $raw_body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300) {
        $message = isset($data['error']['message']) ? $data['error']['message'] : 'Gemini request failed.';
        if (429 === $status) {
            aitmt_fallback_set_provider_cooldown('gemini', HOUR_IN_SECONDS);
        }

        return new WP_Error('aitmt_gemini_error', $message, array('status' => $status));
    }

    $output_text = aitmt_fallback_extract_gemini_output_text(is_array($data) ? $data : array());
    $decoded = json_decode($output_text, true);

    if (!is_array($decoded)) {
        $decoded = json_decode(aitmt_fallback_strip_json_fence($output_text), true);
    }

    if (!is_array($decoded) || empty($decoded['translations']) || !is_array($decoded['translations'])) {
        return new WP_Error('aitmt_gemini_parse_error', 'Gemini returned an unexpected translation payload.');
    }

    $translations = array();
    foreach ($decoded['translations'] as $item) {
        if (empty($item['id']) || !array_key_exists('text', $item)) {
            continue;
        }

        $translations[(string) $item['id']] = (string) $item['text'];
    }

    return $translations;
}

function aitmt_fallback_extract_gemini_output_text($data)
{
    if (empty($data['candidates']) || !is_array($data['candidates'])) {
        return '';
    }

    foreach ($data['candidates'] as $candidate) {
        if (empty($candidate['content']['parts']) || !is_array($candidate['content']['parts'])) {
            continue;
        }

        foreach ($candidate['content']['parts'] as $part) {
            if (isset($part['text']) && is_string($part['text'])) {
                return $part['text'];
            }
        }
    }

    return '';
}

function aitmt_fallback_google_translate_batch($segments, $source_language, $target_language)
{
    $api_key = aitmt_fallback_provider_key('google_translate');

    if (empty($api_key)) {
        return new WP_Error('aitmt_missing_google_translate_key', 'Google Translate API key is missing.');
    }

    $hashes = array_keys($segments);
    $texts = array_values($segments);
    $endpoint = add_query_arg('key', $api_key, 'https://translation.googleapis.com/language/translate/v2');
    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 45,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Referer' => home_url('/'),
            ),
            'body' => wp_json_encode(array(
                'q' => $texts,
                'source' => $source_language,
                'target' => $target_language,
                'format' => 'text',
            )),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $raw_body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300) {
        $message = aitmt_fallback_extract_error_message(is_array($data) ? $data : array(), 'Google Translate request failed.');
        if (429 === $status) {
            aitmt_fallback_set_provider_cooldown('google_translate', HOUR_IN_SECONDS);
        }

        return new WP_Error('aitmt_google_translate_error', $message, array('status' => $status));
    }

    if (empty($data['data']['translations']) || !is_array($data['data']['translations'])) {
        return new WP_Error('aitmt_google_translate_parse_error', 'Google Translate returned an unexpected translation payload.');
    }

    $translations = array();
    foreach ($data['data']['translations'] as $index => $item) {
        if (!isset($hashes[$index]) || !isset($item['translatedText'])) {
            continue;
        }

        $translations[$hashes[$index]] = html_entity_decode((string) $item['translatedText'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $translations;
}

function aitmt_fallback_deepl_translate_batch($segments, $source_language, $target_language)
{
    $api_key = aitmt_fallback_provider_key('deepl');

    if (empty($api_key)) {
        return new WP_Error('aitmt_missing_deepl_key', 'DeepL API key is missing.');
    }

    $options = aitmt_fallback_options();
    $hashes = array_keys($segments);
    $texts = array_values($segments);
    $endpoint = ('pro' === $options['deepl_plan'] ? 'https://api.deepl.com' : 'https://api-free.deepl.com') . '/v2/translate';
    $body_parts = array();
    foreach ($texts as $text) {
        $body_parts[] = 'text=' . rawurlencode((string) $text);
    }
    $body_parts[] = 'target_lang=' . rawurlencode(aitmt_fallback_deepl_language_code($target_language, false));
    $source_code = aitmt_fallback_deepl_language_code($source_language, true);

    if ($source_code) {
        $body_parts[] = 'source_lang=' . rawurlencode($source_code);
    }

    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 45,
            'headers' => array(
                'Authorization' => 'DeepL-Auth-Key ' . $api_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => implode('&', $body_parts),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $raw_body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300) {
        $message = aitmt_fallback_extract_error_message(is_array($data) ? $data : array(), 'DeepL request failed.');
        if (429 === $status || 456 === $status) {
            aitmt_fallback_set_provider_cooldown('deepl', HOUR_IN_SECONDS);
        }

        return new WP_Error('aitmt_deepl_error', $message, array('status' => $status));
    }

    if (empty($data['translations']) || !is_array($data['translations'])) {
        return new WP_Error('aitmt_deepl_parse_error', 'DeepL returned an unexpected translation payload.');
    }

    $translations = array();
    foreach ($data['translations'] as $index => $item) {
        if (!isset($hashes[$index]) || !isset($item['text'])) {
            continue;
        }

        $translations[$hashes[$index]] = (string) $item['text'];
    }

    return $translations;
}

function aitmt_fallback_deepl_language_code($language, $is_source = false)
{
    $language = strtoupper(aitmt_fallback_normalize_language($language));

    if ('EN' === $language && !$is_source) {
        return 'EN-US';
    }

    return $language;
}

function aitmt_fallback_grok_translate_batch($segments, $source_language, $target_language)
{
    $api_key = aitmt_fallback_provider_key('grok');

    if (empty($api_key)) {
        return new WP_Error('aitmt_missing_grok_key', 'Grok API key is missing.');
    }

    $languages = aitmt_fallback_languages();
    $source_name = isset($languages[$source_language]) ? $languages[$source_language] : strtoupper($source_language);
    $target_name = isset($languages[$target_language]) ? $languages[$target_language] : strtoupper($target_language);
    $translation_instruction = aitmt_fallback_translation_instruction();
    $items = array();

    foreach ($segments as $hash => $text) {
        $items[] = array(
            'id' => $hash,
            'text' => $text,
        );
    }

    $body = array(
        'model' => aitmt_fallback_provider_model('grok'),
        'temperature' => aitmt_fallback_translation_temperature(),
        'input' => array(
            array(
                'role' => 'system',
                'content' => sprintf(
                    'Translate to %s (%s). Preserve formatting. Return only translated text for each segment. %s Preserve placeholders, numbers, emails, URLs, shortcodes, HTML entities, and brand names. Return only valid JSON.',
                    $target_name,
                    strtoupper($target_language),
                    $translation_instruction
                ),
            ),
            array(
                'role' => 'user',
                'content' => 'Return exactly this JSON shape: {"translations":[{"id":"same id","text":"translated text"}]}. Segments: ' . wp_json_encode($items, JSON_UNESCAPED_UNICODE),
            ),
        ),
    );

    $response = wp_remote_post(
        'https://api.x.ai/v1/responses',
        array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($body),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $raw_body = (string) wp_remote_retrieve_body($response);
    $data = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300) {
        $message = aitmt_fallback_extract_error_message(is_array($data) ? $data : array(), 'Grok request failed.');
        if (429 === $status) {
            aitmt_fallback_set_provider_cooldown('grok', HOUR_IN_SECONDS);
        }

        return new WP_Error('aitmt_grok_error', $message, array('status' => $status));
    }

    $output_text = aitmt_fallback_extract_openai_output_text(is_array($data) ? $data : array());
    $decoded = json_decode($output_text, true);

    if (!is_array($decoded)) {
        $decoded = json_decode(aitmt_fallback_strip_json_fence($output_text), true);
    }

    if (!is_array($decoded) || empty($decoded['translations']) || !is_array($decoded['translations'])) {
        return new WP_Error('aitmt_grok_parse_error', 'Grok returned an unexpected translation payload.');
    }

    $translations = array();
    foreach ($decoded['translations'] as $item) {
        if (empty($item['id']) || !array_key_exists('text', $item)) {
            continue;
        }

        $translations[(string) $item['id']] = (string) $item['text'];
    }

    return $translations;
}

function aitmt_fallback_translation_table()
{
    global $wpdb;

    return $wpdb->prefix . 'aitmt_translations';
}

function aitmt_fallback_translation_table_sql()
{
    $table = aitmt_fallback_translation_table();

    if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
        return '';
    }

    return esc_sql($table);
}

function aitmt_fallback_maybe_create_translation_table()
{
    if (get_option('aitmt_fallback_table_ready') && aitmt_fallback_translation_table_exists()) {
        return;
    }

    global $wpdb;

    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        source_hash char(64) NOT NULL,
        source_language varchar(16) NOT NULL,
        target_language varchar(16) NOT NULL,
        context varchar(80) NOT NULL DEFAULT 'html',
        source_text longtext NOT NULL,
        translated_text longtext NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'published',
        provider varchar(40) NOT NULL DEFAULT 'openai',
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY aitmt_lookup (source_hash, source_language, target_language, context),
        KEY target_language (target_language),
        KEY status (status)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('aitmt_fallback_table_ready', 1, false);
}

function aitmt_fallback_get_existing_translations($segments, $source_language, $target_language)
{
    global $wpdb;

    if (empty($segments)) {
        return array();
    }

    aitmt_fallback_maybe_create_translation_table();

    $hashes = array_keys($segments);
    $placeholders = implode(',', array_fill(0, count($hashes), '%s'));
    $args = array_merge($hashes, array($source_language, $target_language, 'html'));
    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return array();
    }

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Dynamic IN placeholders and table name are controlled above; all values are prepared.
    $prepared = $wpdb->prepare(
        "SELECT source_hash, translated_text FROM {$table} WHERE source_hash IN ({$placeholders}) AND source_language = %s AND target_language = %s AND context = %s AND status IN ('published', 'manual', 'import')",
        ...$args
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $rows = $wpdb->get_results($prepared, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Prepared immediately above.
    $map = array();

    foreach ((array) $rows as $row) {
        $map[$row['source_hash']] = $row['translated_text'];
    }

    return $map;
}

function aitmt_fallback_save_translation_batch($source_segments, $translated_segments, $source_language, $target_language, $status)
{
    foreach ($translated_segments as $hash => $translation) {
        if (!isset($source_segments[$hash])) {
            continue;
        }

        aitmt_fallback_save_translation($source_segments[$hash], (string) $translation, $source_language, $target_language, $status);
    }
}

function aitmt_fallback_save_translation($source_text, $translated_text, $source_language, $target_language, $status)
{
    global $wpdb;
    $options = aitmt_fallback_options();

    aitmt_fallback_maybe_create_translation_table();

    $source_text = aitmt_fallback_normalize_text($source_text);
    $translated_text = trim((string) $translated_text);

    if ('' === $source_text || '' === $translated_text) {
        return false;
    }

    $now = current_time('mysql');
    $result = $wpdb->replace(
        aitmt_fallback_translation_table(),
        array(
            'source_hash' => aitmt_fallback_translation_hash($source_text),
            'source_language' => aitmt_fallback_normalize_language($source_language),
            'target_language' => aitmt_fallback_normalize_language($target_language),
            'context' => 'html',
            'source_text' => $source_text,
            'translated_text' => $translated_text,
            'status' => sanitize_key($status),
            'provider' => aitmt_fallback_active_provider(),
            'created_at' => $now,
            'updated_at' => $now,
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    return false !== $result;
}

function aitmt_fallback_enqueue_translation_batch($source_segments, $source_language, $target_language)
{
    foreach ($source_segments as $hash => $source_text) {
        aitmt_fallback_enqueue_translation($source_text, $source_language, $target_language);
    }
}

function aitmt_fallback_enqueue_translation($source_text, $source_language, $target_language)
{
    global $wpdb;

    aitmt_fallback_maybe_create_translation_table();

    $source_text = aitmt_fallback_normalize_text($source_text);
    if ('' === $source_text) {
        return false;
    }

    $now = current_time('mysql');
    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return false;
    }
    $hash = aitmt_fallback_translation_hash($source_text);

    $existing = $wpdb->get_var( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); values are prepared below.
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE source_hash = %s AND source_language = %s AND target_language = %s AND context = %s LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            $hash,
            aitmt_fallback_normalize_language($source_language),
            aitmt_fallback_normalize_language($target_language),
            'html'
        )
    );

    if ($existing) {
        return false;
    }

    $result = $wpdb->insert(
        $table,
        array(
            'source_hash' => $hash,
            'source_language' => aitmt_fallback_normalize_language($source_language),
            'target_language' => aitmt_fallback_normalize_language($target_language),
            'context' => 'html',
            'source_text' => $source_text,
            'translated_text' => '',
            'status' => 'queued',
            'provider' => aitmt_fallback_active_provider(),
            'created_at' => $now,
            'updated_at' => $now,
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    return false !== $result;
}

function aitmt_fallback_queued_count()
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return 0;
    }

    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return 0;
    }

    return (int) $wpdb->get_var( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); value is prepared below.
        $wpdb->prepare(
            'SELECT COUNT(*) FROM ' . $table . ' WHERE status = %s', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            'queued'
        )
    );
}

function aitmt_fallback_process_queue($limit = 25)
{
    global $wpdb;

    aitmt_fallback_maybe_create_translation_table();

    $limit = max(1, min(100, absint($limit)));
    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return array(
            'ok' => false,
            'message' => 'Translation table is not available.',
            'processed' => 0,
        );
    }
    $rows = $wpdb->get_results( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); values are prepared below.
        $wpdb->prepare(
            "SELECT id, source_hash, source_text, source_language, target_language FROM {$table} WHERE status = %s ORDER BY updated_at ASC, id ASC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            'queued',
            $limit
        ),
        ARRAY_A
    );

    if (empty($rows)) {
        return array(
            'ok' => true,
            'message' => 'Queue is empty.',
            'processed' => 0,
        );
    }

    $groups = array();
    foreach ($rows as $row) {
        $key = $row['source_language'] . '|' . $row['target_language'];

        if (!isset($groups[$key])) {
            $groups[$key] = array(
                'source_language' => $row['source_language'],
                'target_language' => $row['target_language'],
                'rows' => array(),
                'segments' => array(),
            );
        }

        $groups[$key]['rows'][] = $row;
        $groups[$key]['segments'][$row['source_hash']] = $row['source_text'];
    }

    $options = aitmt_fallback_options();
    $processed = 0;
    $routes = array();
    $now = current_time('mysql');
    $status = '1' === $options['draft_mode'] ? 'draft' : 'published';

    foreach ($groups as $group) {
        $source_language = $group['source_language'];
        $target_language = $group['target_language'];
        $routes[] = $source_language . ' -> ' . $target_language;
        $translated = aitmt_fallback_translate_with_provider($group['segments'], $source_language, $target_language);

        if (is_wp_error($translated)) {
            aitmt_fallback_log($translated->get_error_message());

            return array(
                'ok' => false,
                'message' => $translated->get_error_message(),
                'processed' => $processed,
                'source_language' => $source_language,
                'target_language' => $target_language,
                'routes' => implode(', ', $routes),
            );
        }

        foreach ($group['rows'] as $row) {
            if (!isset($translated[$row['source_hash']])) {
                continue;
            }

            $updated = $wpdb->update( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql().
                $table,
                array(
                    'translated_text' => (string) $translated[$row['source_hash']],
                    'status' => $status,
                    'provider' => aitmt_fallback_active_provider(),
                    'updated_at' => $now,
                ),
                array('id' => (int) $row['id']),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );

            if (false !== $updated) {
                $processed++;
            }
        }
    }

    return array(
        'ok' => true,
        'message' => sprintf('Processed %d queued translation(s) across %d route(s).', $processed, count($groups)),
        'processed' => $processed,
        'routes' => implode(', ', $routes),
    );
}

function aitmt_fallback_enqueue_post_strings($post_id)
{
    $post_id = absint($post_id);
    $post = get_post($post_id);

    if (!$post || 'publish' !== $post->post_status) {
        return array(
            'strings' => 0,
            'queued' => 0,
        );
    }

    $source_language = aitmt_fallback_source_language();
    $target_languages = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));
    $strings = array();

    aitmt_fallback_add_scan_text($strings, get_the_title($post_id));
    aitmt_fallback_add_scan_text($strings, get_post_field('post_excerpt', $post_id));

    foreach (aitmt_fallback_extract_strings_from_html(get_post_field('post_content', $post_id)) as $text) {
        $strings[aitmt_fallback_translation_hash($text)] = $text;
    }

    foreach (aitmt_fallback_scan_post_meta($post_id) as $text) {
        $strings[aitmt_fallback_translation_hash($text)] = $text;
    }

    $queued = 0;
    foreach ($strings as $text) {
        foreach ($target_languages as $target_language) {
            if (aitmt_fallback_enqueue_translation($text, $source_language, $target_language)) {
                $queued++;
            }
        }
    }

    return array(
        'strings' => count($strings),
        'queued' => $queued,
    );
}

function aitmt_fallback_scan_saved_post($post_id, $post, $update)
{
    $options = aitmt_fallback_options();

    if ('1' !== $options['scan_on_save']) {
        return;
    }

    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    if (!$post || 'publish' !== $post->post_status) {
        return;
    }

    $post_type = get_post_type($post_id);
    $post_types = get_post_types(array('public' => true), 'names');

    if (!in_array($post_type, $post_types, true) || 'attachment' === $post_type) {
        return;
    }

    $result = aitmt_fallback_enqueue_post_strings($post_id);
    update_option('aitmt_last_auto_scan', array(
        'post_id' => absint($post_id),
        'post_type' => $post_type,
        'strings' => (int) $result['strings'],
        'queued' => (int) $result['queued'],
        'created_at' => current_time('mysql'),
    ), false);
}

function aitmt_fallback_cron_schedules($schedules)
{
    if (!isset($schedules['aitmt_five_minutes'])) {
        $schedules['aitmt_five_minutes'] = array(
            'interval' => 5 * MINUTE_IN_SECONDS,
            'display' => __('Every 5 minutes', 'ait-multilingual-translate'),
        );
    }

    return $schedules;
}

function aitmt_fallback_sync_cron_schedule()
{
    $options = aitmt_fallback_options();
    $hook = 'aitmt_fallback_process_queue_event';
    $scheduled = wp_next_scheduled($hook);

    if ('1' === $options['cron_enabled']) {
        if (!$scheduled) {
            $result = wp_schedule_event(time() + 120, 'aitmt_five_minutes', $hook, array(), true);
            if (is_wp_error($result)) {
                aitmt_fallback_set_last_error($result->get_error_message());
                aitmt_fallback_log_debug_event('cron', 'Could not schedule translation queue.', array(
                    'code' => $result->get_error_code(),
                    'message' => $result->get_error_message(),
                ));
            }
        }

        return;
    }

    if ($scheduled) {
        wp_clear_scheduled_hook($hook);
    }
}

function aitmt_fallback_cron_process_queue()
{
    $options = aitmt_fallback_options();

    if ('1' !== $options['cron_enabled']) {
        return;
    }

    $limit = isset($options['max_segments_per_request']) ? absint($options['max_segments_per_request']) : 10;
    $limit = max(1, min(25, $limit));
    $result = aitmt_fallback_process_queue($limit);
    update_option('aitmt_cron_queue_result', array_merge($result, array('created_at' => current_time('mysql'))), false);
}

function aitmt_fallback_normalize_text($text)
{
    $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', trim($text));

    return null === $text ? '' : $text;
}

function aitmt_fallback_translation_hash($text)
{
    return hash('sha256', aitmt_fallback_normalize_text($text));
}

function aitmt_fallback_is_translatable_text($text)
{
    $text = trim((string) $text);
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

function aitmt_fallback_apply_original_spacing($original, $translation)
{
    preg_match('/^\s*/u', (string) $original, $leading);
    preg_match('/\s*$/u', (string) $original, $trailing);

    return (isset($leading[0]) ? $leading[0] : '') . trim((string) $translation) . (isset($trailing[0]) ? $trailing[0] : '');
}

function aitmt_fallback_log($message)
{
    update_option('aitmt_last_error', current_time('mysql') . ' - ' . (string) $message, false);
    aitmt_fallback_log_event('error', (string) $message);

    do_action('aitmt_logged_error', (string) $message);
}

function aitmt_fallback_log_event($type, $message, $context = array())
{
    $extended = aitmt_fallback_debug_file_enabled();
    $events = get_option('aitmt_debug_events', array());
    $events = is_array($events) ? $events : array();
    $context = is_array($context) ? $context : array();
    $event = array(
        'created_at' => current_time('mysql'),
        'type' => sanitize_key((string) $type),
        'message' => aitmt_fallback_mask_log_text(sanitize_text_field((string) $message)),
        'context' => aitmt_fallback_shrink_log_context($context),
    );

    if ($extended || 'error' === $event['type']) {
        array_unshift($events, $event);
        $events = array_slice($events, 0, 80);
        update_option('aitmt_debug_events', $events, false);
    }

    if ($extended) {
        aitmt_fallback_write_debug_log_file($event);
    }
}

function aitmt_fallback_shrink_log_context($context)
{
    $clean = array();

    foreach ((array) $context as $key => $value) {
        $key = sanitize_key((string) $key);

        if (is_array($value)) {
            $value = wp_json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_scalar($value)) {
            $value = (string) $value;
        } else {
            continue;
        }

        $value = aitmt_fallback_mask_log_text((string) $value);
        $clean[$key] = function_exists('mb_substr') ? mb_substr($value, 0, 900, 'UTF-8') : substr($value, 0, 900);
    }

    return $clean;
}

function aitmt_fallback_debug_file_enabled()
{
    return '1' === get_option('aitmt_debug_file_enabled', '0');
}

function aitmt_fallback_debug_log_dir()
{
    $uploads = wp_upload_dir(null, false);
    if (empty($uploads['basedir']) || !empty($uploads['error'])) {
        return '';
    }

    return trailingslashit($uploads['basedir']) . AITMT_PUBLIC_SLUG;
}

function aitmt_fallback_debug_log_path()
{
    $dir = aitmt_fallback_debug_log_dir();

    return $dir ? trailingslashit($dir) . 'debug.log' : '';
}

function aitmt_fallback_ensure_debug_log_dir()
{
    $dir = aitmt_fallback_debug_log_dir();

    if ('' === $dir || !wp_mkdir_p($dir)) {
        return false;
    }

    $index = trailingslashit($dir) . 'index.php';
    if (!file_exists($index)) {
        aitmt_fallback_write_local_file($index, "<?php\n// Silence is golden.\n");
    }

    $htaccess = trailingslashit($dir) . '.htaccess';
    if (!file_exists($htaccess)) {
        aitmt_fallback_write_local_file($htaccess, "Deny from all\n");
    }

    return true;
}

function aitmt_fallback_write_debug_log_file($event)
{
    if (!aitmt_fallback_ensure_debug_log_dir()) {
        return false;
    }

    $line = wp_json_encode($event);
    if (!$line) {
        return false;
    }

    $path = aitmt_fallback_debug_log_path();
    $existing = aitmt_fallback_read_local_file($path);
    if (strlen($existing) > 1048576) {
        $existing = substr($existing, -524288);
    }

    return aitmt_fallback_write_local_file($path, $existing . $line . PHP_EOL);
}

function aitmt_fallback_mask_log_text($text)
{
    $text = aitmt_fallback_redact_sensitive_text((string) $text);
    $patterns = array(
        '/sk-[A-Za-z0-9_\-]{12,}/',
        '/xai-[A-Za-z0-9_\-]{12,}/',
        '/AIzaSy[A-Za-z0-9_\-]{12,}/',
        '/sk-ant-[A-Za-z0-9_\-]{12,}/',
    );

    return preg_replace($patterns, '[masked-api-key]', $text);
}

function aitmt_fallback_ajax_save_translation()
{
    check_ajax_referer('aitmt_frontend_editor', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'ait-multilingual-translate')), 403);
    }

    $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
    $translated_text = isset($_POST['translatedText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['translatedText'])) : '';
    $source_language = isset($_POST['sourceLanguage']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : aitmt_fallback_source_language();
    $target_language = isset($_POST['targetLanguage']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : aitmt_fallback_current_language();

    if ('' === $source_text || '' === $translated_text || '' === $target_language) {
        wp_send_json_error(array('message' => __('Missing translation data.', 'ait-multilingual-translate')), 400);
    }

    $saved = aitmt_fallback_save_translation($source_text, $translated_text, $source_language, $target_language, 'manual');

    if (!$saved) {
        wp_send_json_error(array('message' => __('Translation was not saved.', 'ait-multilingual-translate')), 500);
    }

    wp_send_json_success(array('message' => __('Translation saved.', 'ait-multilingual-translate')));
}

function aitmt_fallback_ajax_auto_translate_frontend()
{
    check_ajax_referer('aitmt_frontend_editor', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'ait-multilingual-translate')), 403);
    }

    $source_text = isset($_POST['sourceText']) ? sanitize_textarea_field(wp_unslash((string) $_POST['sourceText'])) : '';
    $source_language = isset($_POST['sourceLanguage']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['sourceLanguage']))) : aitmt_fallback_source_language();
    $target_language = isset($_POST['targetLanguage']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['targetLanguage']))) : aitmt_fallback_current_language();

    if ('' === $source_text || '' === $target_language) {
        wp_send_json_error(array('message' => __('Missing translation data.', 'ait-multilingual-translate')), 400);
    }

    if ($source_language === $target_language) {
        wp_send_json_error(array('message' => __('Source and target languages are the same.', 'ait-multilingual-translate')), 400);
    }

    $provider = aitmt_fallback_active_provider();
    if ('' === aitmt_fallback_provider_key($provider)) {
        wp_send_json_error(array('message' => __('No translation provider configured.', 'ait-multilingual-translate')), 400);
    }

    $hash = aitmt_fallback_translation_hash($source_text);
    $memory = aitmt_fallback_get_existing_translations(array($hash => $source_text), $source_language, $target_language);
    if (!empty($memory[$hash])) {
        aitmt_fallback_provider_stats_record_cache_hits($provider, 1);
        wp_send_json_success(
            array(
                'translation' => (string) $memory[$hash],
                'message' => __('Translation memory hit', 'ait-multilingual-translate'),
                'provider' => aitmt_fallback_provider_label($provider),
            )
        );
    }

    $translated = aitmt_fallback_translate_with_provider(array($hash => $source_text), $source_language, $target_language);

    if (is_wp_error($translated)) {
        $error_data = $translated->get_error_data();
        $status = is_array($error_data) && !empty($error_data['status']) ? absint($error_data['status']) : 500;
        $message = aitmt_fallback_provider_error_message_for_editor($translated, $status);
        aitmt_fallback_log($translated->get_error_message());

        wp_send_json_error(array('message' => $message), $status);
    }

    if (!is_array($translated) || !array_key_exists($hash, $translated) || '' === trim((string) $translated[$hash])) {
        wp_send_json_error(array('message' => __('Translation failed. Please try again.', 'ait-multilingual-translate')), 500);
    }

    wp_send_json_success(
        array(
            'translation' => (string) $translated[$hash],
            /* translators: %s: Active translation provider label. */
            'message' => sprintf(__('Translated via %s', 'ait-multilingual-translate'), aitmt_fallback_provider_label($provider)),
            'provider' => aitmt_fallback_provider_label($provider),
        )
    );
}

function aitmt_fallback_provider_error_message_for_editor($error, $status = 500)
{
    if (!$error instanceof WP_Error) {
        return __('Translation failed. Please try again.', 'ait-multilingual-translate');
    }

    $code = $error->get_error_code();
    $message = strtolower($error->get_error_message());

    if (false !== strpos($code, 'missing') || false !== strpos($message, 'api key is missing')) {
        return __('No translation provider configured.', 'ait-multilingual-translate');
    }

    if (429 === (int) $status || false !== strpos($code, 'quota') || false !== strpos($code, 'cooldown') || false !== strpos($message, 'rate limit') || false !== strpos($message, 'quota')) {
        return __('Provider rate limit reached.', 'ait-multilingual-translate');
    }

    return __('Translation failed. Please try again.', 'ait-multilingual-translate');
}

function aitmt_fallback_scan_site_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_scan_site');

    $result = aitmt_fallback_scan_site();
    update_option('aitmt_scan_result', array_merge($result, array('created_at' => current_time('mysql'))), false);

    wp_safe_redirect(aitmt_fallback_admin_redirect_url_from_post('ait-multilingual-translate-scanner'));
    exit;
}

function aitmt_fallback_scan_site()
{
    $source_language = aitmt_fallback_source_language();
    $target_languages = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));

    if (empty($target_languages)) {
        return array(
            'ok' => false,
            'message' => 'No target languages are selected.',
            'strings' => 0,
            'queued' => 0,
        );
    }

    $strings = array();
    aitmt_fallback_add_scan_text($strings, get_bloginfo('name'));
    aitmt_fallback_add_scan_text($strings, get_bloginfo('description'));

    $post_types = get_post_types(array('public' => true), 'names');
    unset($post_types['attachment']);

    $posts = get_posts(array(
        'post_type' => array_values($post_types),
        'post_status' => array('publish'),
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    foreach ((array) $posts as $post_id) {
        aitmt_fallback_add_scan_text($strings, get_the_title($post_id));
        aitmt_fallback_add_scan_text($strings, get_post_field('post_excerpt', $post_id));

        foreach (aitmt_fallback_extract_strings_from_html(get_post_field('post_content', $post_id)) as $text) {
            $strings[aitmt_fallback_translation_hash($text)] = $text;
        }

        foreach (aitmt_fallback_scan_post_meta($post_id) as $text) {
            $strings[aitmt_fallback_translation_hash($text)] = $text;
        }
    }

    $taxonomies = get_taxonomies(array('public' => true), 'names');
    foreach ((array) $taxonomies as $taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

        if (is_wp_error($terms)) {
            continue;
        }

        foreach ((array) $terms as $term) {
            aitmt_fallback_add_scan_text($strings, $term->name);
            foreach (aitmt_fallback_extract_strings_from_html($term->description) as $text) {
                $strings[aitmt_fallback_translation_hash($text)] = $text;
            }
        }
    }

    foreach ((array) wp_get_nav_menus() as $menu) {
        $items = wp_get_nav_menu_items($menu->term_id);

        foreach ((array) $items as $item) {
            aitmt_fallback_add_scan_text($strings, $item->title);
            aitmt_fallback_add_scan_text($strings, $item->attr_title);
            aitmt_fallback_add_scan_text($strings, $item->description);
        }
    }

    foreach (aitmt_fallback_scan_widget_options() as $text) {
        $strings[aitmt_fallback_translation_hash($text)] = $text;
    }

    $queued = 0;
    foreach ($strings as $text) {
        foreach ($target_languages as $target_language) {
            if (aitmt_fallback_enqueue_translation($text, $source_language, $target_language)) {
                $queued++;
            }
        }
    }

    return array(
        'ok' => true,
        'message' => sprintf('Scan finished. Found %d unique string(s), added %d new queue item(s).', count($strings), $queued),
        'strings' => count($strings),
        'queued' => $queued,
        'targets' => implode(', ', $target_languages),
    );
}

function aitmt_fallback_scan_post_meta($post_id)
{
    $allowed_private_keys = array(
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_aioseo_title',
        '_aioseo_description',
        'rank_math_title',
        'rank_math_description',
        '_elementor_data',
        '_elementor_page_settings',
    );
    $meta = get_post_meta($post_id);
    $strings = array();

    foreach ((array) $meta as $key => $values) {
        if (0 === strpos((string) $key, '_') && !in_array($key, $allowed_private_keys, true)) {
            continue;
        }

        foreach ((array) $values as $value) {
            if (!is_scalar($value) || is_serialized($value)) {
                continue;
            }

            $raw = (string) $value;
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                aitmt_fallback_collect_scalar_scan_strings($strings, $decoded);
            } else {
                foreach (aitmt_fallback_extract_strings_from_html($raw) as $text) {
                    $strings[aitmt_fallback_translation_hash($text)] = $text;
                }
            }
        }
    }

    return array_values($strings);
}

function aitmt_fallback_scan_widget_options()
{
    global $wpdb;

    $strings = array();
    $options_table = esc_sql($wpdb->options);
    $rows = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT option_value FROM {$options_table} WHERE option_name LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Core options table name is controlled by WordPress.
            $wpdb->esc_like('widget_') . '%'
        )
    );

    foreach ((array) $rows as $raw_value) {
        $value = maybe_unserialize($raw_value);
        aitmt_fallback_collect_scalar_scan_strings($strings, $value);
    }

    return array_values($strings);
}

function aitmt_fallback_collect_scalar_scan_strings(&$strings, $value, $depth = 0)
{
    if ($depth > 5 || null === $value || is_bool($value)) {
        return;
    }

    if (is_array($value) || is_object($value)) {
        foreach ((array) $value as $child) {
            aitmt_fallback_collect_scalar_scan_strings($strings, $child, $depth + 1);
        }

        return;
    }

    if (!is_scalar($value)) {
        return;
    }

    foreach (aitmt_fallback_extract_strings_from_html((string) $value) as $text) {
        $strings[aitmt_fallback_translation_hash($text)] = $text;
    }
}

function aitmt_fallback_extract_strings_from_html($html)
{
    $html = (string) $html;
    $html = strip_shortcodes($html);
    $html = preg_replace('/<!--.*?-->/s', ' ', $html);
    $html = preg_replace('/<(script|style|noscript)[^>]*>.*?<\/\1>/is', ' ', (string) $html);
    $html = preg_replace('/<\/(p|div|li|h[1-6]|td|th|blockquote|figcaption|button|a)>/i', "</$1>\n", (string) $html);
    $text = wp_strip_all_tags((string) $html, true);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $lines = preg_split('/[\r\n]+/', (string) $text);
    $strings = array();

    foreach ((array) $lines as $line) {
        $line = aitmt_fallback_normalize_text($line);

        if ('' === $line) {
            continue;
        }

        $length = function_exists('mb_strlen') ? mb_strlen($line, 'UTF-8') : strlen($line);
        $parts = $length > 320 ? preg_split('/(?<=[.!?;:])\s+/u', $line) : array($line);

        foreach ((array) $parts as $part) {
            aitmt_fallback_add_scan_text($strings, $part);
        }
    }

    return array_values($strings);
}

function aitmt_fallback_add_scan_text(&$strings, $text)
{
    $text = aitmt_fallback_normalize_text($text);

    if (!aitmt_fallback_is_translatable_text($text)) {
        return;
    }

    if (preg_match('/^(https?:)?\/\//i', $text) || false !== strpos($text, '@')) {
        return;
    }

    $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    if ($length > 900) {
        return;
    }

    $strings[aitmt_fallback_translation_hash($text)] = $text;
}

function aitmt_fallback_save_manual_translation_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_save_manual_translation');

    $id = isset($_POST['translation_id']) ? absint($_POST['translation_id']) : 0;
    $translated_text = isset($_POST['translated_text']) ? sanitize_textarea_field(wp_unslash((string) $_POST['translated_text'])) : '';
    $source_text = isset($_POST['source_text']) ? sanitize_textarea_field(wp_unslash((string) $_POST['source_text'])) : '';
    $source_language = isset($_POST['source_language']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['source_language']))) : aitmt_fallback_source_language();
    $target_language = isset($_POST['target_language']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['target_language']))) : '';

    if ($id && '' !== trim($translated_text) && aitmt_fallback_translation_table_exists()) {
        global $wpdb;
        $wpdb->update(
            aitmt_fallback_translation_table(),
            array(
                'translated_text' => trim($translated_text),
                'status' => 'manual',
                'provider' => 'manual',
                'updated_at' => current_time('mysql'),
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );
    } elseif ('' !== trim($source_text) && '' !== trim($translated_text) && '' !== $target_language) {
        aitmt_fallback_save_translation($source_text, $translated_text, $source_language, $target_language, 'manual');
    }

    wp_safe_redirect(aitmt_fallback_admin_redirect_url_from_post('ait-multilingual-translate-translations'));
    exit;
}

function aitmt_fallback_save_translation_matrix_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_save_translation_matrix');

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Matrix fields are sanitized individually below before saving.
    $items = isset($_POST['aitmt_matrix']) && is_array($_POST['aitmt_matrix']) ? wp_unslash($_POST['aitmt_matrix']) : array();
    $saved = 0;

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $id = isset($item['translation_id']) ? absint($item['translation_id']) : 0;
        $translated_text = isset($item['translated_text']) ? sanitize_textarea_field((string) $item['translated_text']) : '';
        $source_text = isset($item['source_text']) ? sanitize_textarea_field((string) $item['source_text']) : '';
        $source_language = isset($item['source_language']) ? aitmt_fallback_normalize_language((string) $item['source_language']) : aitmt_fallback_source_language();
        $target_language = isset($item['target_language']) ? aitmt_fallback_normalize_language((string) $item['target_language']) : '';

        if ('' === trim($translated_text)) {
            continue;
        }

        if ($id && aitmt_fallback_translation_table_exists()) {
            global $wpdb;
            $updated = $wpdb->update(
                aitmt_fallback_translation_table(),
                array(
                    'translated_text' => trim($translated_text),
                    'status' => 'manual',
                    'provider' => 'manual',
                    'updated_at' => current_time('mysql'),
                ),
                array('id' => $id),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );

            if (false !== $updated) {
                $saved++;
            }
        } elseif ('' !== trim($source_text) && '' !== $target_language && aitmt_fallback_save_translation($source_text, $translated_text, $source_language, $target_language, 'manual')) {
            $saved++;
        }
    }

    update_option('aitmt_matrix_save_result', array(
        'saved' => $saved,
        'created_at' => current_time('mysql'),
    ), false);

    $per_page = isset($_POST['per_page']) ? max(25, min(100, absint($_POST['per_page']))) : 25;
    $paged = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;
    wp_safe_redirect(add_query_arg(array(
        'page' => 'ait-multilingual-translate-translations',
        'per_page' => $per_page,
        'paged' => $paged,
    ), admin_url('admin.php')));
    exit;
}

function aitmt_fallback_export_translations_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_export_translations');

    $language = isset($_POST['target_language']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['target_language']))) : '';
    $format = isset($_POST['export_format']) ? sanitize_key(wp_unslash((string) $_POST['export_format'])) : 'csv';
    $format = in_array($format, array('csv', 'po', 'mo'), true) ? $format : 'csv';

    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        wp_die(esc_html__('Invalid target language.', 'ait-multilingual-translate'));
    }

    $entries = aitmt_fallback_export_entries($language);
    $filename = 'ait-multilingual-translate-' . aitmt_fallback_source_language() . '-' . $language . '.' . $format;
    nocache_headers();

    if ('csv' === $format) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo aitmt_fallback_csv_line(array('source_language', 'target_language', 'source_hash', 'source_text', 'translated_text', 'status', 'provider')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Export endpoint intentionally streams CSV after nonce/capability checks.
        foreach ($entries as $entry) {
            echo aitmt_fallback_csv_line(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Export endpoint intentionally streams CSV after nonce/capability checks.
                $entry['source_language'],
                $entry['target_language'],
                $entry['source_hash'],
                $entry['source_text'],
                $entry['translated_text'],
                $entry['status'],
                $entry['provider'],
            ));
        }
        exit;
    }

    if ('mo' === $format) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Export endpoint intentionally streams binary MO file content after nonce/capability checks.
        echo aitmt_fallback_build_mo($entries, $language);
        exit;
    }

    header('Content-Type: text/x-gettext-translation; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Export endpoint intentionally streams PO file content after nonce/capability checks.
    echo aitmt_fallback_build_po($entries, $language);
    exit;
}

function aitmt_fallback_import_translations_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_import_translations');

    $uploaded_tmp_name = isset($_FILES['translation_file']['tmp_name']) ? sanitize_text_field(wp_unslash((string) $_FILES['translation_file']['tmp_name'])) : '';
    if ('' === $uploaded_tmp_name || !is_uploaded_file($uploaded_tmp_name)) {
        wp_die(esc_html__('No import file uploaded.', 'ait-multilingual-translate'));
    }

    $language = isset($_POST['target_language']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['target_language']))) : '';
    $format = isset($_POST['import_format']) ? sanitize_key(wp_unslash((string) $_POST['import_format'])) : 'csv';
    $format = in_array($format, array('csv', 'po', 'mo'), true) ? $format : 'csv';

    if (!$language || !in_array($language, aitmt_fallback_enabled_languages(), true)) {
        wp_die(esc_html__('Invalid target language.', 'ait-multilingual-translate'));
    }

    $path = $uploaded_tmp_name;
    $source_language = aitmt_fallback_source_language();

    if ('csv' === $format) {
        $entries = aitmt_fallback_parse_csv_import($path, $source_language, $language);
    } elseif ('mo' === $format) {
        $entries = aitmt_fallback_parse_mo(aitmt_fallback_read_local_file($path), $source_language, $language);
    } else {
        $entries = aitmt_fallback_parse_po(aitmt_fallback_read_local_file($path), $source_language, $language);
    }

    $saved = 0;
    foreach ($entries as $entry) {
        if (empty($entry['source_text']) || !array_key_exists('translated_text', $entry) || '' === trim((string) $entry['translated_text'])) {
            continue;
        }

        if (aitmt_fallback_save_translation($entry['source_text'], $entry['translated_text'], $source_language, $language, 'import')) {
            $saved++;
        }
    }

    update_option('aitmt_matrix_save_result', array(
        'saved' => $saved,
        'created_at' => current_time('mysql'),
    ), false);

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-translations'));
    exit;
}

function aitmt_fallback_export_entries($target_language)
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return array();
    }

    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return array();
    }

    return (array) $wpdb->get_results( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); values are prepared below.
        $wpdb->prepare(
            'SELECT source_hash, source_language, target_language, source_text, translated_text, status, provider FROM ' . $table . ' WHERE source_language = %s AND target_language = %s ORDER BY source_text ASC', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            aitmt_fallback_source_language(),
            $target_language
        ),
        ARRAY_A
    );
}

function aitmt_fallback_csv_line($fields)
{
    $escaped = array();

    foreach ((array) $fields as $field) {
        $escaped[] = '"' . str_replace('"', '""', (string) $field) . '"';
    }

    return implode(',', $escaped) . "\r\n";
}

function aitmt_fallback_build_po($entries, $target_language)
{
    $lines = array(
        'msgid ""',
        'msgstr ""',
        '"Project-Id-Version: AIT Multilingual Translate ' . AITMT_VERSION . '\n"',
        '"Language: ' . $target_language . '\n"',
        '"Content-Type: text/plain; charset=UTF-8\n"',
        '"Content-Transfer-Encoding: 8bit\n"',
        '',
    );

    foreach ($entries as $entry) {
        $lines[] = '#. source_hash: ' . $entry['source_hash'];
        $lines[] = '#. status: ' . $entry['status'] . ', provider: ' . $entry['provider'];
        $lines[] = 'msgid ' . aitmt_fallback_po_quote($entry['source_text']);
        $lines[] = 'msgstr ' . aitmt_fallback_po_quote($entry['translated_text']);
        $lines[] = '';
    }

    return implode("\n", $lines);
}

function aitmt_fallback_po_quote($text)
{
    $text = str_replace(array('\\', '"', "\t", "\r", "\n"), array('\\\\', '\"', '\t', '', '\n'), (string) $text);

    return '"' . $text . '"';
}

function aitmt_fallback_build_mo($entries, $target_language)
{
    $pairs = array('' => "Project-Id-Version: AIT Multilingual Translate " . AITMT_VERSION . "\nLanguage: " . $target_language . "\nContent-Type: text/plain; charset=UTF-8\n");

    foreach ($entries as $entry) {
        $pairs[(string) $entry['source_text']] = (string) $entry['translated_text'];
    }

    ksort($pairs, SORT_STRING);
    $count = count($pairs);
    $header_size = 28;
    $original_table_offset = $header_size;
    $translation_table_offset = $original_table_offset + ($count * 8);
    $string_offset = $translation_table_offset + ($count * 8);
    $original_table = '';
    $translation_table = '';
    $strings = '';

    foreach ($pairs as $original => $translation) {
        $original_table .= pack('VV', strlen($original), $string_offset + strlen($strings));
        $strings .= $original . "\0";
    }

    foreach ($pairs as $original => $translation) {
        $translation_table .= pack('VV', strlen($translation), $string_offset + strlen($strings));
        $strings .= $translation . "\0";
    }

    return pack('V*', 0x950412de, 0, $count, $original_table_offset, $translation_table_offset, 0, 0) . $original_table . $translation_table . $strings;
}

function aitmt_fallback_parse_csv_import($path, $source_language, $target_language)
{
    $entries = array();
    $content = aitmt_fallback_read_local_file($path);

    if ('' === $content) {
        return $entries;
    }

    $lines = preg_split('/\r\n|\r|\n/', $content);
    if (empty($lines)) {
        return $entries;
    }

    $headers = str_getcsv((string) array_shift($lines));
    if (empty($headers)) {
        return $entries;
    }

    $headers = array_map('sanitize_key', $headers);
    foreach ($lines as $line) {
        if ('' === trim((string) $line)) {
            continue;
        }

        $row = str_getcsv((string) $line);
        $item = array_combine($headers, array_pad($row, count($headers), ''));
        if (!$item) {
            continue;
        }

        $entries[] = array(
            'source_language' => isset($item['source_language']) ? aitmt_fallback_normalize_language($item['source_language']) : $source_language,
            'target_language' => $target_language,
            'source_text' => isset($item['source_text']) ? (string) $item['source_text'] : '',
            'translated_text' => isset($item['translated_text']) ? (string) $item['translated_text'] : '',
        );
    }

    return $entries;
}

function aitmt_fallback_parse_po($content, $source_language, $target_language)
{
    $entries = array();
    $lines = preg_split('/\r\n|\r|\n/', (string) $content);
    $current = array('msgid' => '', 'msgstr' => '');
    $field = '';

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ('' === $trimmed) {
            if ('' !== $current['msgid'] && '' !== $current['msgstr']) {
                $entries[] = array(
                    'source_language' => $source_language,
                    'target_language' => $target_language,
                    'source_text' => $current['msgid'],
                    'translated_text' => $current['msgstr'],
                );
            }
            $current = array('msgid' => '', 'msgstr' => '');
            $field = '';
            continue;
        }

        if (0 === strpos($trimmed, 'msgid ')) {
            $field = 'msgid';
            $current[$field] = aitmt_fallback_po_unquote(substr($trimmed, 6));
            continue;
        }

        if (0 === strpos($trimmed, 'msgstr ')) {
            $field = 'msgstr';
            $current[$field] = aitmt_fallback_po_unquote(substr($trimmed, 7));
            continue;
        }

        if ($field && isset($trimmed[0]) && '"' === $trimmed[0]) {
            $current[$field] .= aitmt_fallback_po_unquote($trimmed);
        }
    }

    if ('' !== $current['msgid'] && '' !== $current['msgstr']) {
        $entries[] = array(
            'source_language' => $source_language,
            'target_language' => $target_language,
            'source_text' => $current['msgid'],
            'translated_text' => $current['msgstr'],
        );
    }

    return $entries;
}

function aitmt_fallback_po_unquote($text)
{
    $text = trim((string) $text);
    if (strlen($text) >= 2 && '"' === $text[0] && '"' === substr($text, -1)) {
        $text = substr($text, 1, -1);
    }

    return stripcslashes($text);
}

function aitmt_fallback_parse_mo($content, $source_language, $target_language)
{
    $entries = array();
    $content = (string) $content;

    if (strlen($content) < 28) {
        return $entries;
    }

    $magic = unpack('V', substr($content, 0, 4));
    $little_endian = isset($magic[1]) && 0x950412de === $magic[1];
    $big_endian = isset($magic[1]) && 0xde120495 === $magic[1];

    if (!$little_endian && !$big_endian) {
        return $entries;
    }

    $read = function ($offset) use ($content, $little_endian) {
        $value = unpack($little_endian ? 'V' : 'N', substr($content, $offset, 4));
        return isset($value[1]) ? (int) $value[1] : 0;
    };
    $count = $read(8);
    $original_table_offset = $read(12);
    $translation_table_offset = $read(16);

    for ($i = 0; $i < $count; $i++) {
        $original_length = $read($original_table_offset + ($i * 8));
        $original_offset = $read($original_table_offset + ($i * 8) + 4);
        $translation_length = $read($translation_table_offset + ($i * 8));
        $translation_offset = $read($translation_table_offset + ($i * 8) + 4);
        $source = substr($content, $original_offset, $original_length);
        $translation = substr($content, $translation_offset, $translation_length);

        if ('' === $source || '' === $translation) {
            continue;
        }

        $entries[] = array(
            'source_language' => $source_language,
            'target_language' => $target_language,
            'source_text' => $source,
            'translated_text' => $translation,
        );
    }

    return $entries;
}

function aitmt_fallback_recent_translations($limit = 20)
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return array();
    }

    $limit = max(1, min(100, absint($limit)));
    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return array();
    }

    return (array) $wpdb->get_results( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); value is prepared below.
        $wpdb->prepare(
            'SELECT id, source_language, target_language, source_text, translated_text, status, provider, updated_at FROM ' . $table . ' ORDER BY updated_at DESC, id DESC LIMIT %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            $limit
        ),
        ARRAY_A
    );
}

function aitmt_fallback_translation_matrix($limit = 60, $offset = 0, $search = '', $status_filter = 'all')
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return array();
    }

    $source_language = aitmt_fallback_source_language();
    $targets = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));
    $table = aitmt_fallback_translation_table_sql();

    if (empty($targets) || '' === $table) {
        return array();
    }

    $limit = max(1, min(100, absint($limit)));
    $offset = max(0, absint($offset));
    $search = trim((string) $search);
    $status_filter = in_array($status_filter, array('all', 'translated', 'untranslated'), true) ? $status_filter : 'all';
    $where = 'source_language = %s';
    $args = array($source_language);

    if ('' !== $search) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= ' AND (source_text LIKE %s OR source_hash LIKE %s)';
        $args[] = $like;
        $args[] = $like;
    }

    $having = '';
    if ('all' !== $status_filter) {
        $target_placeholders = implode(',', array_fill(0, count($targets), '%s'));
        $complete_expression = "COUNT(DISTINCT CASE WHEN target_language IN ({$target_placeholders}) AND translated_text <> '' AND status IN ('published', 'manual', 'import') THEN target_language END)";
        $having = ' HAVING ' . $complete_expression . ('translated' === $status_filter ? ' >= %d' : ' < %d');
        $args = array_merge($args, $targets, array(count($targets)));
    }

    $args = array_merge($args, array($limit, $offset));
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Table name is validated; dynamic WHERE/HAVING fragments only contain fixed clauses with placeholders.
    $prepared = $wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by aitmt_fallback_translation_table_sql(); WHERE/HAVING fragments use fixed clauses and prepared placeholders.
        'SELECT source_hash, source_language, MIN(source_text) AS source_text, MAX(updated_at) AS updated_at FROM ' . $table . " WHERE {$where} GROUP BY source_hash, source_language{$having} ORDER BY updated_at DESC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated and values are prepared.
        ...$args
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $source_rows = (array) $wpdb->get_results( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Prepared immediately above.
        $prepared, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared immediately above.
        ARRAY_A
    );
    $matrix = array();
    $hashes = array();

    foreach ($source_rows as $row) {
        $key = $row['source_language'] . '|' . $row['source_hash'];
        $hashes[] = $row['source_hash'];
        $matrix[$key] = array(
            'source_hash' => $row['source_hash'],
            'source_language' => $row['source_language'],
            'source_text' => $row['source_text'],
            'updated_at' => $row['updated_at'],
            'targets' => array(),
        );
    }

    if (empty($hashes)) {
        return array();
    }

    $hash_placeholders = implode(',', array_fill(0, count($hashes), '%s'));
    $target_placeholders = implode(',', array_fill(0, count($targets), '%s'));
    $args = array_merge($hashes, array($source_language), $targets);
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Dynamic IN placeholders and table name are controlled above; all values are prepared.
    $prepared = $wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and placeholder fragments are controlled; all values are prepared.
        'SELECT id, source_hash, source_language, target_language, source_text, translated_text, status, provider, updated_at FROM ' . $table . " WHERE source_hash IN ({$hash_placeholders}) AND source_language = %s AND target_language IN ({$target_placeholders})", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and IN placeholders are controlled and values are prepared.
        ...$args
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $rows = (array) $wpdb->get_results( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Prepared immediately above.
        $prepared, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared immediately above.
        ARRAY_A
    );

    foreach ($rows as $row) {
        $key = $row['source_language'] . '|' . $row['source_hash'];
        if (isset($matrix[$key])) {
            $matrix[$key]['targets'][$row['target_language']] = $row;
        }
    }

    return array_values($matrix);
}

function aitmt_fallback_translation_matrix_total($search = '', $status_filter = 'all')
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return 0;
    }

    $source_language = aitmt_fallback_source_language();
    $targets = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));
    $table = aitmt_fallback_translation_table_sql();

    if (empty($targets) || '' === $table) {
        return 0;
    }

    $search = trim((string) $search);
    $status_filter = in_array($status_filter, array('all', 'translated', 'untranslated'), true) ? $status_filter : 'all';
    $where = 'source_language = %s';
    $args = array($source_language);

    if ('' !== $search) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= ' AND (source_text LIKE %s OR source_hash LIKE %s)';
        $args[] = $like;
        $args[] = $like;
    }

    $having = '';
    if ('all' !== $status_filter) {
        $target_placeholders = implode(',', array_fill(0, count($targets), '%s'));
        $complete_expression = "COUNT(DISTINCT CASE WHEN target_language IN ({$target_placeholders}) AND translated_text <> '' AND status IN ('published', 'manual', 'import') THEN target_language END)";
        $having = ' HAVING ' . $complete_expression . ('translated' === $status_filter ? ' >= %d' : ' < %d');
        $args = array_merge($args, $targets, array(count($targets)));
    }

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Dynamic HAVING placeholders are built from a controlled target language list.
    $prepared = $wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by aitmt_fallback_translation_table_sql(); WHERE/HAVING fragments use fixed clauses and prepared placeholders.
        'SELECT COUNT(*) FROM (SELECT source_hash FROM ' . $table . " WHERE {$where} GROUP BY source_hash, source_language{$having}) aitmt_sources", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated and values are prepared.
        ...$args
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare

    return (int) $wpdb->get_var($prepared); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Prepared immediately above.
}

function aitmt_fallback_language_stats()
{
    global $wpdb;

    $stats = array();
    $source_language = aitmt_fallback_source_language();
    $targets = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));

    foreach ($targets as $target) {
        $stats[$target] = array(
            'queued' => 0,
            'published' => 0,
            'manual' => 0,
            'import' => 0,
            'draft' => 0,
        );
    }

    $table = aitmt_fallback_translation_table_sql();

    if (!aitmt_fallback_translation_table_exists() || empty($targets) || '' === $table) {
        return $stats;
    }

    $placeholders = implode(',', array_fill(0, count($targets), '%s'));
    $args = array_merge(array($source_language), $targets);
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Dynamic IN placeholders and table name are controlled above; all values are prepared.
    $prepared = $wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and placeholder fragments are controlled; all values are prepared.
        'SELECT target_language, status, COUNT(*) AS total FROM ' . $table . " WHERE source_language = %s AND target_language IN ({$placeholders}) GROUP BY target_language, status", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and IN placeholders are controlled and values are prepared.
        ...$args
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $rows = $wpdb->get_results($prepared, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Prepared immediately above.

    foreach ((array) $rows as $row) {
        $target = $row['target_language'];
        $status = $row['status'];

        if (!isset($stats[$target])) {
            $stats[$target] = array();
        }

        $stats[$target][$status] = (int) $row['total'];
    }

    return $stats;
}

function aitmt_fallback_admin_page_start($title, $description = '')
{
    ?>
    <div class="wrap aitmt-admin-page aitmt-mode-<?php echo esc_attr(aitmt_fallback_admin_mode()); ?>">
        <div class="aitmt-admin-title">
            <img src="<?php echo esc_attr(aitmt_fallback_logo_url()); ?>" alt="" width="34" height="34">
            <div>
                <h1><?php echo esc_html($title); ?></h1>
                <?php if ($description) : ?>
                    <p><?php echo esc_html($description); ?></p>
                <?php endif; ?>
                <p class="aitmt-admin-meta">
                    <?php echo esc_html(sprintf('AI Translate %s | %s | Developer: sotter IT Design | ', AITMT_VERSION, aitmt_fallback_edition_label())); ?>
                    <a href="https://wp-ai.itdesign.biz" target="_blank" rel="noopener noreferrer">wp-ai.itdesign.biz</a>
                    <?php echo esc_html(' | info@itdesign.biz'); ?>
                </p>
            </div>
        </div>
    <?php
}

function aitmt_fallback_admin_page_end()
{
    echo '</div>';
}

function aitmt_fallback_sent_notice($success_message)
{
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin notice status is read-only and sanitized.
    $status = isset($_GET['aitmt_sent']) ? sanitize_key(wp_unslash((string) $_GET['aitmt_sent'])) : '';

    if ('' === $status) {
        return;
    }

    $class = 'notice-success';
    $message = $success_message;

    if ('0' === $status) {
        $class = 'notice-error';
        $message = __('The message could not be sent. Please check WordPress email delivery on this site.', 'ait-multilingual-translate');
    } elseif ('consent' === $status) {
        $class = 'notice-warning';
        $message = __('Please confirm consent before sending diagnostic information.', 'ait-multilingual-translate');
    }

    echo '<div class="notice ' . esc_attr($class) . ' inline"><p>' . esc_html($message) . '</p></div>';
}

function aitmt_fallback_report_bug_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    aitmt_fallback_admin_page_start(
        __('AI Translate - Report Bug', 'ait-multilingual-translate'),
        __('Send a safe technical report for Public Beta troubleshooting.', 'ait-multilingual-translate')
    );
    aitmt_fallback_sent_notice(__('Bug report sent. Thank you for helping test AIT Multilingual Translate.', 'ait-multilingual-translate'));
    aitmt_fallback_public_beta_notice();
    aitmt_fallback_support_development_block(true);
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Report Bug', 'ait-multilingual-translate'); ?></h2>
        <p><?php esc_html_e('The technical report includes WordPress, WooCommerce, PHP, plugin, language, provider, queue, and route status. Sensitive values are redacted before sending.', 'ait-multilingual-translate'); ?></p>
        <form class="aitmt-public-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_submit_bug_report'); ?>
            <input type="hidden" name="action" value="aitmt_submit_bug_report">
            <div class="aitmt-form-grid">
                <label>
                    <span><?php esc_html_e('Name', 'ait-multilingual-translate'); ?></span>
                    <input type="text" name="name" class="regular-text" autocomplete="name">
                </label>
                <label>
                    <span><?php esc_html_e('Email', 'ait-multilingual-translate'); ?></span>
                    <input type="email" name="email" class="regular-text" autocomplete="email">
                </label>
                <label>
                    <span><?php esc_html_e('Website', 'ait-multilingual-translate'); ?></span>
                    <input type="url" name="website" class="regular-text" value="<?php echo esc_attr(home_url('/')); ?>">
                </label>
                <label>
                    <span><?php esc_html_e('Problem type', 'ait-multilingual-translate'); ?></span>
                    <select name="problem_type">
                        <option value="translation"><?php esc_html_e('Translation issue', 'ait-multilingual-translate'); ?></option>
                        <option value="woocommerce"><?php esc_html_e('WooCommerce issue', 'ait-multilingual-translate'); ?></option>
                        <option value="elementor"><?php esc_html_e('Elementor issue', 'ait-multilingual-translate'); ?></option>
                        <option value="frontend-editor"><?php esc_html_e('Frontend editor issue', 'ait-multilingual-translate'); ?></option>
                        <option value="provider"><?php esc_html_e('Provider / API issue', 'ait-multilingual-translate'); ?></option>
                        <option value="update"><?php esc_html_e('Install / update issue', 'ait-multilingual-translate'); ?></option>
                        <option value="other"><?php esc_html_e('Other', 'ait-multilingual-translate'); ?></option>
                    </select>
                </label>
            </div>
            <label class="aitmt-field-block">
                <span><?php esc_html_e('Short description', 'ait-multilingual-translate'); ?></span>
                <textarea name="short_description" rows="4" class="large-text" required></textarea>
            </label>
            <label class="aitmt-field-block">
                <span><?php esc_html_e('Steps to reproduce', 'ait-multilingual-translate'); ?></span>
                <textarea name="steps" rows="7" class="large-text" placeholder="<?php esc_attr_e('1. Open page...\n2. Switch language...\n3. Expected...\n4. Actual...', 'ait-multilingual-translate'); ?>"></textarea>
            </label>
            <label class="aitmt-checkbox-row">
                <input type="checkbox" name="attach_log" value="1">
                <span><?php esc_html_e('Attach technical log', 'ait-multilingual-translate'); ?></span>
            </label>
            <label class="aitmt-checkbox-row">
                <input type="checkbox" name="aitmt_consent" value="1" required>
                <span><?php esc_html_e('I agree to send this report and redacted technical diagnostics to info@itdesign.biz.', 'ait-multilingual-translate'); ?></span>
            </label>
            <?php submit_button(__('Send bug report', 'ait-multilingual-translate')); ?>
        </form>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_feedback_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    aitmt_fallback_admin_page_start(
        __('AI Translate - Feedback', 'ait-multilingual-translate'),
        __('Share Public Beta feedback, feature requests, and translation notes.', 'ait-multilingual-translate')
    );
    aitmt_fallback_sent_notice(__('Feedback sent. Thank you for shaping AIT Multilingual Translate.', 'ait-multilingual-translate'));
    aitmt_fallback_public_beta_notice();
    aitmt_fallback_support_development_block(true);
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Feedback / Feature Request', 'ait-multilingual-translate'); ?></h2>
        <form class="aitmt-public-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_submit_feedback'); ?>
            <input type="hidden" name="action" value="aitmt_submit_feedback">
            <div class="aitmt-form-grid">
                <label>
                    <span><?php esc_html_e('Name', 'ait-multilingual-translate'); ?></span>
                    <input type="text" name="name" class="regular-text" autocomplete="name">
                </label>
                <label>
                    <span><?php esc_html_e('Email', 'ait-multilingual-translate'); ?></span>
                    <input type="email" name="email" class="regular-text" autocomplete="email">
                </label>
                <label>
                    <span><?php esc_html_e('Type', 'ait-multilingual-translate'); ?></span>
                    <select name="feedback_type">
                        <option value="bug"><?php esc_html_e('Bug', 'ait-multilingual-translate'); ?></option>
                        <option value="feature-request"><?php esc_html_e('Feature request', 'ait-multilingual-translate'); ?></option>
                        <option value="translation-issue"><?php esc_html_e('Translation issue', 'ait-multilingual-translate'); ?></option>
                        <option value="woocommerce-issue"><?php esc_html_e('WooCommerce issue', 'ait-multilingual-translate'); ?></option>
                        <option value="elementor-issue"><?php esc_html_e('Elementor issue', 'ait-multilingual-translate'); ?></option>
                        <option value="other"><?php esc_html_e('Other', 'ait-multilingual-translate'); ?></option>
                    </select>
                </label>
            </div>
            <label class="aitmt-field-block">
                <span><?php esc_html_e('Message', 'ait-multilingual-translate'); ?></span>
                <textarea name="message" rows="8" class="large-text" required></textarea>
            </label>
            <label class="aitmt-checkbox-row">
                <input type="checkbox" name="aitmt_consent" value="1" required>
                <span><?php esc_html_e('I agree to send this feedback to info@itdesign.biz.', 'ait-multilingual-translate'); ?></span>
            </label>
            <?php submit_button(__('Send feedback', 'ait-multilingual-translate')); ?>
        </form>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_support_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    aitmt_fallback_admin_page_start(
        __('AI Translate - Support', 'ait-multilingual-translate'),
        __('Support the free Public Beta and help prioritize the roadmap.', 'ait-multilingual-translate')
    );
    aitmt_fallback_public_beta_notice();
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Support AIT Multilingual Translate', 'ait-multilingual-translate'); ?></h2>
        <div class="aitmt-support-page-grid">
            <section class="aitmt-support-page-card">
                <h3><?php esc_html_e('Support AIT Multilingual Translate', 'ait-multilingual-translate'); ?></h3>
                <p><?php esc_html_e('AIT Multilingual Translate is currently in Public Beta and includes temporary full feature access while the platform is actively tested and improved.', 'ait-multilingual-translate'); ?></p>
                <p><?php esc_html_e('Users who support the project with a donation during the Public Beta period may receive a significant discount or special early-supporter offer for the future commercial release of AIT Multilingual Translate.', 'ait-multilingual-translate'); ?></p>
                <p><?php esc_html_e('Your support helps improve the plugin, optimize AI providers, expand language support, and accelerate development.', 'ait-multilingual-translate'); ?></p>
                <p class="aitmt-support-buttons">
                    <?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Button HTML is built from escaped attributes and translated text.
                    echo wp_kses_post(aitmt_fallback_support_development_button('button button-primary aitmt-support-donation-button'));
                    ?>
                </p>
            </section>
            <section class="aitmt-support-page-card">
                <h3><?php esc_html_e('Need help?', 'ait-multilingual-translate'); ?></h3>
                <p class="aitmt-support-buttons">
                    <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-report-bug')); ?>"><?php esc_html_e('Report Bug', 'ait-multilingual-translate'); ?></a>
                    <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-feedback')); ?>"><?php esc_html_e('Send Feedback', 'ait-multilingual-translate'); ?></a>
                    <a class="button" href="https://wp-ai.itdesign.biz/documentation/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Documentation', 'ait-multilingual-translate'); ?></a>
                    <a class="button" href="https://wp-ai.itdesign.biz" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Plugin Website', 'ait-multilingual-translate'); ?></a>
                </p>
            </section>
            <section class="aitmt-support-page-card">
                <h3><?php esc_html_e('Public Beta Notice', 'ait-multilingual-translate'); ?></h3>
                <p><?php esc_html_e('This Public Beta includes temporary unrestricted access for testing and feedback purposes. Commercial licensing and additional editions may be introduced in future releases.', 'ait-multilingual-translate'); ?></p>
            </section>
            <section class="aitmt-support-page-card">
                <h3><?php esc_html_e('Support contact', 'ait-multilingual-translate'); ?></h3>
                <p><a href="mailto:info@itdesign.biz">info@itdesign.biz</a></p>
                <p><a href="https://wp-ai.itdesign.biz" target="_blank" rel="noopener noreferrer">https://wp-ai.itdesign.biz</a></p>
            </section>
        </div>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_onboarding_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = aitmt_fallback_options();
    $languages = aitmt_fallback_languages();
    $source_language = (string) $options['source_language'];
    $scan_result = get_option('aitmt_scan_result', array());
    $scan_result = is_array($scan_result) ? $scan_result : array();
    $queue_result = get_option('aitmt_queue_result', array());
    $queue_result = is_array($queue_result) ? $queue_result : array();

    aitmt_fallback_admin_page_start(
        __('AI Translate - Onboarding', 'ait-multilingual-translate'),
        __('Set up languages, provider keys, scanning, queue translation, and frontend editing.', 'ait-multilingual-translate')
    );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin saved notice flag is read-only.
    if (!empty($_GET['aitmt_saved'])) {
        echo '<div class="notice notice-success inline"><p>' . esc_html__('Onboarding settings saved.', 'ait-multilingual-translate') . '</p></div>';
    }
    aitmt_fallback_public_beta_notice();
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Setup Wizard', 'ait-multilingual-translate'); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_onboarding_save'); ?>
            <input type="hidden" name="action" value="aitmt_onboarding_save">
            <div class="aitmt-onboarding-steps">
                <section class="aitmt-onboarding-step">
                    <span class="aitmt-step-number">1</span>
                    <h3><?php esc_html_e('Welcome', 'ait-multilingual-translate'); ?></h3>
                    <p><?php esc_html_e('AIT Multilingual Translate adds AI translation, WooCommerce support, frontend editing, SEO-friendly language URLs, and saved translation memory. This is a Public Beta, so make a backup before bulk translating production websites.', 'ait-multilingual-translate'); ?></p>
                </section>
                <section class="aitmt-onboarding-step">
                    <span class="aitmt-step-number">2</span>
                    <h3><?php esc_html_e('Languages', 'ait-multilingual-translate'); ?></h3>
                    <label>
                        <span><?php esc_html_e('Source language', 'ait-multilingual-translate'); ?></span>
                        <select name="aitmt_options[source_language]">
                            <option value=""><?php esc_html_e('Auto: WordPress site language', 'ait-multilingual-translate'); ?></option>
                            <?php foreach ($languages as $code => $label) : ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($source_language, $code); ?>><?php echo esc_html($label . ' (' . strtoupper($code) . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <input type="search" class="aitmt-fallback-language-search" placeholder="<?php esc_attr_e('Search languages...', 'ait-multilingual-translate'); ?>">
                    <div class="aitmt-fallback-language-grid">
                        <?php foreach ($languages as $code => $label) : ?>
                            <label>
                                <input type="checkbox" name="aitmt_options[enabled_languages][]" value="<?php echo esc_attr($code); ?>" <?php checked(in_array($code, $options['enabled_languages'], true)); ?>>
                                <span><?php echo esc_html($label); ?></span>
                                <code><?php echo esc_html(strtoupper($code)); ?></code>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="aitmt-onboarding-step">
                    <span class="aitmt-step-number">3</span>
                    <h3><?php esc_html_e('Provider', 'ait-multilingual-translate'); ?></h3>
                    <select name="aitmt_options[provider]">
                        <option value="google_translate" <?php selected($options['provider'], 'google_translate'); ?>><?php esc_html_e('Google Translate', 'ait-multilingual-translate'); ?></option>
                        <option value="openai" <?php selected($options['provider'], 'openai'); ?>><?php esc_html_e('OpenAI / ChatGPT', 'ait-multilingual-translate'); ?></option>
                        <option value="deepl" <?php selected($options['provider'], 'deepl'); ?>><?php esc_html_e('DeepL', 'ait-multilingual-translate'); ?></option>
                        <option value="gemini" <?php selected($options['provider'], 'gemini'); ?>><?php esc_html_e('Google Gemini', 'ait-multilingual-translate'); ?></option>
                        <option value="grok" <?php selected($options['provider'], 'grok'); ?>><?php esc_html_e('Grok / xAI', 'ait-multilingual-translate'); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e('Saved translations are reused. The provider is used only for queue items that still need translation.', 'ait-multilingual-translate'); ?></p>
                </section>
                <section class="aitmt-onboarding-step">
                    <span class="aitmt-step-number">4</span>
                    <h3><?php esc_html_e('API Keys', 'ait-multilingual-translate'); ?></h3>
                    <div class="aitmt-form-grid">
                        <label><span><?php esc_html_e('Google Translate API key', 'ait-multilingual-translate'); ?></span><input type="password" class="regular-text" name="aitmt_options[google_translate_api_key]" value="<?php echo esc_attr($options['google_translate_api_key']); ?>"></label>
                        <label><span><?php esc_html_e('OpenAI API key', 'ait-multilingual-translate'); ?></span><input type="password" class="regular-text" name="aitmt_options[openai_api_key]" value="<?php echo esc_attr($options['openai_api_key']); ?>"></label>
                        <label><span><?php esc_html_e('DeepL API key', 'ait-multilingual-translate'); ?></span><input type="password" class="regular-text" name="aitmt_options[deepl_api_key]" value="<?php echo esc_attr($options['deepl_api_key']); ?>"></label>
                        <label><span><?php esc_html_e('Gemini API key', 'ait-multilingual-translate'); ?></span><input type="password" class="regular-text" name="aitmt_options[gemini_api_key]" value="<?php echo esc_attr($options['gemini_api_key']); ?>"></label>
                        <label><span><?php esc_html_e('Grok API key', 'ait-multilingual-translate'); ?></span><input type="password" class="regular-text" name="aitmt_options[grok_api_key]" value="<?php echo esc_attr($options['grok_api_key']); ?>"></label>
                    </div>
                </section>
                <section class="aitmt-onboarding-step">
                    <span class="aitmt-step-number">7</span>
                    <h3><?php esc_html_e('Frontend editor', 'ait-multilingual-translate'); ?></h3>
                    <input type="hidden" name="aitmt_options[frontend_editor]" value="0">
                    <label class="aitmt-checkbox-row">
                        <input type="checkbox" name="aitmt_options[frontend_editor]" value="1" <?php checked($options['frontend_editor'], '1'); ?>>
                        <span><?php esc_html_e('Allow administrators to edit translations from the frontend', 'ait-multilingual-translate'); ?></span>
                    </label>
                </section>
            </div>
            <?php submit_button(__('Save setup', 'ait-multilingual-translate')); ?>
        </form>
    </div>

    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Scan and Translate', 'ait-multilingual-translate'); ?></h2>
        <div class="aitmt-onboarding-actions">
            <section>
                <span class="aitmt-step-number">5</span>
                <h3><?php esc_html_e('Scan', 'ait-multilingual-translate'); ?></h3>
                <p><?php esc_html_e('Collect site strings into the saved translation queue.', 'ait-multilingual-translate'); ?></p>
                <?php if (!empty($scan_result['message'])) : ?>
                    <p><code><?php echo esc_html((string) $scan_result['message']); ?></code></p>
                <?php endif; ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_scan_site'); ?>
                    <input type="hidden" name="action" value="aitmt_scan_site">
                    <input type="hidden" name="redirect_page" value="ait-multilingual-translate-onboarding">
                    <?php submit_button(__('Scan site strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
                </form>
            </section>
            <section>
                <span class="aitmt-step-number">6</span>
                <h3><?php esc_html_e('Translate', 'ait-multilingual-translate'); ?></h3>
                <p><?php esc_html_e('Start one translation queue batch. Repeat or enable background processing later in Advanced mode.', 'ait-multilingual-translate'); ?></p>
                <?php if (!empty($queue_result['message'])) : ?>
                    <p><code><?php echo esc_html((string) $queue_result['message']); ?></code></p>
                <?php endif; ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_process_queue'); ?>
                    <input type="hidden" name="action" value="aitmt_process_queue">
                    <input type="hidden" name="redirect_page" value="ait-multilingual-translate-onboarding">
                    <?php submit_button(__('Start translation queue', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
                </form>
            </section>
        </div>
        <form class="aitmt-finish-onboarding" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_onboarding_finish'); ?>
            <input type="hidden" name="action" value="aitmt_onboarding_finish">
            <?php submit_button(__('Finish setup', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=' . AITMT_PUBLIC_SLUG)); ?>"><?php esc_html_e('Open dashboard', 'ait-multilingual-translate'); ?></a>
        </form>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_render_matrix_pagination($total, $paged, $total_pages, $per_page, $include_save = false, $filters = array())
{
    $total = max(0, (int) $total);
    $paged = max(1, (int) $paged);
    $total_pages = max(1, (int) $total_pages);
    $per_page = in_array((int) $per_page, array(25, 50, 100), true) ? (int) $per_page : 25;
    $base_args = array('page' => 'ait-multilingual-translate-translations', 'per_page' => $per_page);

    if (!empty($filters['matrix_search'])) {
        $base_args['matrix_search'] = (string) $filters['matrix_search'];
    }

    if (!empty($filters['matrix_status']) && 'all' !== $filters['matrix_status']) {
        $base_args['matrix_status'] = (string) $filters['matrix_status'];
    }

    $first_url = add_query_arg(array_merge($base_args, array('paged' => 1)), admin_url('admin.php'));
    $prev_url = add_query_arg(array_merge($base_args, array('paged' => max(1, $paged - 1))), admin_url('admin.php'));
    $next_url = add_query_arg(array_merge($base_args, array('paged' => min($total_pages, $paged + 1))), admin_url('admin.php'));
    $last_url = add_query_arg(array_merge($base_args, array('paged' => $total_pages)), admin_url('admin.php'));
    $window_start = max(1, min($paged - 4, max(1, $total_pages - 8)));
    $window_end = min($total_pages, $window_start + 8);
    ?>
    <div class="aitmt-matrix-pagination">
        <?php if ($include_save) : ?>
            <button type="submit" class="button button-primary"><?php esc_html_e('Save visible translations', 'ait-multilingual-translate'); ?></button>
        <?php endif; ?>
        <span><?php echo esc_html(sprintf('%d item(s), page %d of %d', $total, $paged, $total_pages)); ?></span>
        <a class="button" href="<?php echo esc_url($first_url); ?>" aria-disabled="<?php echo 1 === $paged ? 'true' : 'false'; ?>"><?php esc_html_e('First', 'ait-multilingual-translate'); ?></a>
        <a class="button" href="<?php echo esc_url($prev_url); ?>" aria-disabled="<?php echo 1 === $paged ? 'true' : 'false'; ?>"><?php esc_html_e('Previous', 'ait-multilingual-translate'); ?></a>
        <?php if ($window_start > 1) : ?>
            <a class="button aitmt-page-number" href="<?php echo esc_url($first_url); ?>">1</a>
            <?php if ($window_start > 2) : ?>
                <span class="aitmt-pagination-dots">...</span>
            <?php endif; ?>
        <?php endif; ?>
        <?php for ($page = $window_start; $page <= $window_end; $page++) : ?>
            <?php $page_url = add_query_arg(array_merge($base_args, array('paged' => $page)), admin_url('admin.php')); ?>
            <?php if ($page === $paged) : ?>
                <span class="button aitmt-page-number is-current" aria-current="page"><?php echo esc_html((string) $page); ?></span>
            <?php else : ?>
                <a class="button aitmt-page-number" href="<?php echo esc_url($page_url); ?>"><?php echo esc_html((string) $page); ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($window_end < $total_pages) : ?>
            <?php if ($window_end < $total_pages - 1) : ?>
                <span class="aitmt-pagination-dots">...</span>
            <?php endif; ?>
            <a class="button aitmt-page-number" href="<?php echo esc_url($last_url); ?>"><?php echo esc_html((string) $total_pages); ?></a>
        <?php endif; ?>
        <a class="button" href="<?php echo esc_url($next_url); ?>" aria-disabled="<?php echo $paged >= $total_pages ? 'true' : 'false'; ?>"><?php esc_html_e('Next', 'ait-multilingual-translate'); ?></a>
        <a class="button" href="<?php echo esc_url($last_url); ?>" aria-disabled="<?php echo $paged >= $total_pages ? 'true' : 'false'; ?>"><?php esc_html_e('Last', 'ait-multilingual-translate'); ?></a>
    </div>
    <?php
}

function aitmt_fallback_translations_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $languages = aitmt_fallback_languages();
    $source_language = aitmt_fallback_source_language();
    $targets = array_values(array_diff(aitmt_fallback_enabled_languages(), array($source_language)));
    // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Translation matrix filters and pagination are read-only GET parameters.
    $per_page = isset($_GET['per_page']) ? absint($_GET['per_page']) : 25;
    $per_page = in_array($per_page, array(25, 50, 100), true) ? $per_page : 25;
    $matrix_search = isset($_GET['matrix_search']) ? sanitize_text_field(wp_unslash((string) $_GET['matrix_search'])) : '';
    $matrix_status = isset($_GET['matrix_status']) ? sanitize_key(wp_unslash((string) $_GET['matrix_status'])) : 'all';
    $matrix_status = in_array($matrix_status, array('all', 'translated', 'untranslated'), true) ? $matrix_status : 'all';
    $matrix_filters = array(
        'matrix_search' => $matrix_search,
        'matrix_status' => $matrix_status,
    );
    $paged = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
    // phpcs:enable WordPress.Security.NonceVerification.Recommended
    $total = aitmt_fallback_translation_matrix_total($matrix_search, $matrix_status);
    $total_pages = max(1, (int) ceil($total / $per_page));
    $paged = min($paged, $total_pages);
    $offset = ($paged - 1) * $per_page;
    $matrix = aitmt_fallback_translation_matrix($per_page, $offset, $matrix_search, $matrix_status);
    $save_result = get_option('aitmt_matrix_save_result', array());
    $save_result = is_array($save_result) ? $save_result : array();

    aitmt_fallback_admin_page_start(
        __('AI Translate - Translations', 'ait-multilingual-translate'),
        __('Edit each source string per target language. Empty fields can be filled manually or translated from the queue.', 'ait-multilingual-translate')
    );
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Translations Matrix', 'ait-multilingual-translate'); ?></h2>
        <div class="aitmt-matrix-actions">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_scan_site'); ?>
                <input type="hidden" name="action" value="aitmt_scan_site">
                <input type="hidden" name="redirect_page" value="ait-multilingual-translate-translations">
                <input type="hidden" name="per_page" value="<?php echo esc_attr((string) $per_page); ?>">
                <input type="hidden" name="paged" value="<?php echo esc_attr((string) $paged); ?>">
                <input type="hidden" name="matrix_search" value="<?php echo esc_attr($matrix_search); ?>">
                <input type="hidden" name="matrix_status" value="<?php echo esc_attr($matrix_status); ?>">
                <?php submit_button(__('Scan new strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_process_queue'); ?>
                <input type="hidden" name="action" value="aitmt_process_queue">
                <input type="hidden" name="redirect_page" value="ait-multilingual-translate-translations">
                <input type="hidden" name="per_page" value="<?php echo esc_attr((string) $per_page); ?>">
                <input type="hidden" name="paged" value="<?php echo esc_attr((string) $paged); ?>">
                <input type="hidden" name="matrix_search" value="<?php echo esc_attr($matrix_search); ?>">
                <input type="hidden" name="matrix_status" value="<?php echo esc_attr($matrix_status); ?>">
                <?php submit_button(__('Process queue', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
            </form>
            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
                <input type="hidden" name="page" value="ait-multilingual-translate-translations">
                <input type="hidden" name="matrix_search" value="<?php echo esc_attr($matrix_search); ?>">
                <input type="hidden" name="matrix_status" value="<?php echo esc_attr($matrix_status); ?>">
                <label>
                    <?php esc_html_e('Per page', 'ait-multilingual-translate'); ?>
                    <select name="per_page" onchange="this.form.submit()">
                        <option value="25" <?php selected($per_page, 25); ?>>25</option>
                        <option value="50" <?php selected($per_page, 50); ?>>50</option>
                        <option value="100" <?php selected($per_page, 100); ?>>100</option>
                    </select>
                </label>
            </form>
        </div>
        <form class="aitmt-matrix-filters" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="ait-multilingual-translate-translations">
            <input type="hidden" name="per_page" value="<?php echo esc_attr((string) $per_page); ?>">
            <label>
                <?php esc_html_e('Search', 'ait-multilingual-translate'); ?>
                <input type="search" name="matrix_search" value="<?php echo esc_attr($matrix_search); ?>" placeholder="<?php esc_attr_e('Source text or hash...', 'ait-multilingual-translate'); ?>">
            </label>
            <label>
                <?php esc_html_e('Status', 'ait-multilingual-translate'); ?>
                <select name="matrix_status">
                    <option value="all" <?php selected($matrix_status, 'all'); ?>><?php esc_html_e('All strings', 'ait-multilingual-translate'); ?></option>
                    <option value="untranslated" <?php selected($matrix_status, 'untranslated'); ?>><?php esc_html_e('Needs translation', 'ait-multilingual-translate'); ?></option>
                    <option value="translated" <?php selected($matrix_status, 'translated'); ?>><?php esc_html_e('Translated', 'ait-multilingual-translate'); ?></option>
                </select>
            </label>
            <?php submit_button(__('Filter', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-translations&per_page=' . (int) $per_page)); ?>"><?php esc_html_e('Reset', 'ait-multilingual-translate'); ?></a>
        </form>
        <?php if (!empty($save_result)) : ?>
            <div class="aitmt-debug-result is-good">
                <p><strong><?php esc_html_e('Last bulk save:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($save_result['created_at']) ? $save_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(sprintf('Saved %d translation(s).', isset($save_result['saved']) ? (int) $save_result['saved'] : 0)); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($targets)) : ?>
            <details class="aitmt-export-import">
                <summary><?php esc_html_e('Export / Import', 'ait-multilingual-translate'); ?></summary>
                <div class="aitmt-export-import-grid">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('aitmt_export_translations'); ?>
                        <input type="hidden" name="action" value="aitmt_export_translations">
                        <h3><?php esc_html_e('Export translations', 'ait-multilingual-translate'); ?></h3>
                        <label>
                            <?php esc_html_e('Export language', 'ait-multilingual-translate'); ?>
                            <select name="target_language">
                                <?php foreach ($targets as $target) : ?>
                                    <option value="<?php echo esc_attr($target); ?>"><?php echo esc_html(isset($languages[$target]) ? $languages[$target] : strtoupper($target)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <?php esc_html_e('Format', 'ait-multilingual-translate'); ?>
                            <select name="export_format">
                                <option value="csv">CSV</option>
                                <option value="po">PO</option>
                                <option value="mo">MO</option>
                            </select>
                        </label>
                        <?php submit_button(__('Export translations', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
                    </form>
                    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('aitmt_import_translations'); ?>
                        <input type="hidden" name="action" value="aitmt_import_translations">
                        <h3><?php esc_html_e('Import translations', 'ait-multilingual-translate'); ?></h3>
                        <label>
                            <?php esc_html_e('Import language', 'ait-multilingual-translate'); ?>
                            <select name="target_language">
                                <?php foreach ($targets as $target) : ?>
                                    <option value="<?php echo esc_attr($target); ?>"><?php echo esc_html(isset($languages[$target]) ? $languages[$target] : strtoupper($target)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <?php esc_html_e('Format', 'ait-multilingual-translate'); ?>
                            <select name="import_format">
                                <option value="csv">CSV</option>
                                <option value="po">PO</option>
                                <option value="mo">MO</option>
                            </select>
                        </label>
                        <input type="file" name="translation_file" accept=".csv,.po,.mo">
                        <?php submit_button(__('Import translations', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
                        <p class="description"><?php esc_html_e('CSV columns: source_text and translated_text. PO/MO import uses msgid as the original string and msgstr as the saved translation.', 'ait-multilingual-translate'); ?></p>
                    </form>
                </div>
            </details>
        <?php endif; ?>
        <?php if (empty($targets)) : ?>
            <p><?php esc_html_e('Select target languages first.', 'ait-multilingual-translate'); ?></p>
        <?php elseif (empty($matrix)) : ?>
            <p><?php esc_html_e('No strings collected yet. Run the scanner or open translated pages once to collect frontend strings.', 'ait-multilingual-translate'); ?></p>
        <?php else : ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_save_translation_matrix'); ?>
                <input type="hidden" name="action" value="aitmt_save_translation_matrix">
                <input type="hidden" name="per_page" value="<?php echo esc_attr((string) $per_page); ?>">
                <input type="hidden" name="paged" value="<?php echo esc_attr((string) $paged); ?>">
                <input type="hidden" name="matrix_search" value="<?php echo esc_attr($matrix_search); ?>">
                <input type="hidden" name="matrix_status" value="<?php echo esc_attr($matrix_status); ?>">
                <?php aitmt_fallback_render_matrix_pagination($total, $paged, $total_pages, $per_page, true, $matrix_filters); ?>
            <table class="widefat striped aitmt-matrix-table">
                <thead>
                    <tr>
                        <th class="source-cell"><?php esc_html_e('Original', 'ait-multilingual-translate'); ?> <code><?php echo esc_html(strtoupper($source_language)); ?></code></th>
                        <?php foreach ($targets as $target) : ?>
                            <th><?php echo esc_html(isset($languages[$target]) ? $languages[$target] : strtoupper($target)); ?> <code><?php echo esc_html(strtoupper($target)); ?></code></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $field_index = 0; ?>
                    <?php foreach ($matrix as $item) : ?>
                        <tr>
                            <td class="source-cell">
                                <?php echo esc_html($item['source_text']); ?>
                                <p class="description"><code><?php echo esc_html($item['source_hash']); ?></code></p>
                            </td>
                            <?php foreach ($targets as $target) : ?>
                                <?php $row = isset($item['targets'][$target]) ? $item['targets'][$target] : array(); ?>
                                <td>
                                    <input type="hidden" name="aitmt_matrix[<?php echo esc_attr((string) $field_index); ?>][translation_id]" value="<?php echo esc_attr(isset($row['id']) ? (string) $row['id'] : '0'); ?>">
                                    <input type="hidden" name="aitmt_matrix[<?php echo esc_attr((string) $field_index); ?>][source_language]" value="<?php echo esc_attr($source_language); ?>">
                                    <input type="hidden" name="aitmt_matrix[<?php echo esc_attr((string) $field_index); ?>][target_language]" value="<?php echo esc_attr($target); ?>">
                                    <input type="hidden" name="aitmt_matrix[<?php echo esc_attr((string) $field_index); ?>][source_text]" value="<?php echo esc_attr($item['source_text']); ?>">
                                    <textarea name="aitmt_matrix[<?php echo esc_attr((string) $field_index); ?>][translated_text]"><?php echo esc_textarea(isset($row['translated_text']) ? $row['translated_text'] : ''); ?></textarea>
                                    <p class="description">
                                        <?php esc_html_e('Status:', 'ait-multilingual-translate'); ?>
                                        <code><?php echo esc_html(isset($row['status']) ? $row['status'] : 'missing'); ?></code>
                                        <?php if (!empty($row['provider'])) : ?>
                                            <code><?php echo esc_html($row['provider']); ?></code>
                                        <?php endif; ?>
                                    </p>
                                </td>
                                <?php $field_index++; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                <?php aitmt_fallback_render_matrix_pagination($total, $paged, $total_pages, $per_page, true, $matrix_filters); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_scanner_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $scan_result = get_option('aitmt_scan_result', array());
    $scan_result = is_array($scan_result) ? $scan_result : array();
    $queue_result = get_option('aitmt_queue_result', array());
    $queue_result = is_array($queue_result) ? $queue_result : array();
    $cron_result = get_option('aitmt_cron_queue_result', array());
    $cron_result = is_array($cron_result) ? $cron_result : array();
    $auto_scan_result = get_option('aitmt_last_auto_scan', array());
    $auto_scan_result = is_array($auto_scan_result) ? $auto_scan_result : array();
    $stats = aitmt_fallback_language_stats();
    $options = aitmt_fallback_options();
    $provider_stats = aitmt_fallback_provider_stats_for(aitmt_fallback_active_provider());

    aitmt_fallback_admin_page_start(
        __('AI Translate - Scanner', 'ait-multilingual-translate'),
        __('Collect site strings into the queue, then translate queued strings in controlled batches.', 'ait-multilingual-translate')
    );
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Scanner and Queue', 'ait-multilingual-translate'); ?></h2>
        <p>
            <?php esc_html_e('Auto scan on save:', 'ait-multilingual-translate'); ?>
            <code><?php echo esc_html('1' === $options['scan_on_save'] ? 'enabled' : 'disabled'); ?></code>
            <?php esc_html_e('Background processing:', 'ait-multilingual-translate'); ?>
            <code><?php echo esc_html('1' === $options['cron_enabled'] ? 'enabled' : 'disabled'); ?></code>
            <?php esc_html_e('Translation Mode:', 'ait-multilingual-translate'); ?>
            <code><?php echo esc_html(aitmt_fallback_translation_mode_label()); ?></code>
            <?php esc_html_e('Quality:', 'ait-multilingual-translate'); ?>
            <code><?php echo esc_html(aitmt_fallback_quality_mode_label()); ?></code>
        </p>
        <p class="description">
            <?php esc_html_e('Cost optimization:', 'ait-multilingual-translate'); ?>
            <code><?php echo esc_html(sprintf('requests %d / input tokens %d / output tokens %d / cost %.6f / cache hits %d / duplicates skipped %d', absint($provider_stats['requests'] ?? 0), absint($provider_stats['input_tokens'] ?? 0), absint($provider_stats['output_tokens'] ?? 0), (float) ($provider_stats['estimated_cost'] ?? 0), absint($provider_stats['cache_hits'] ?? 0), absint($provider_stats['duplicate_skipped'] ?? 0))); ?></code>
        </p>
        <div class="aitmt-mini-stats">
            <?php foreach ($stats as $target => $row) : ?>
                <div class="aitmt-mini-stat">
                    <span><?php echo esc_html(strtoupper($target)); ?></span>
                    <strong><?php echo esc_html((string) ((int) $row['published'] + (int) $row['manual'] + (int) $row['import'])); ?></strong>
                    <small><?php echo esc_html(sprintf('queued %d / draft %d', (int) $row['queued'], (int) $row['draft'])); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($scan_result)) : ?>
            <div class="aitmt-debug-result <?php echo !empty($scan_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                <p><strong><?php esc_html_e('Last scan:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($scan_result['created_at']) ? $scan_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(isset($scan_result['message']) ? $scan_result['message'] : ''); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($auto_scan_result)) : ?>
            <div class="aitmt-debug-result is-good">
                <p><strong><?php esc_html_e('Last automatic scan:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($auto_scan_result['created_at']) ? $auto_scan_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(sprintf('Post #%d: found %d string(s), queued %d new item(s).', isset($auto_scan_result['post_id']) ? (int) $auto_scan_result['post_id'] : 0, isset($auto_scan_result['strings']) ? (int) $auto_scan_result['strings'] : 0, isset($auto_scan_result['queued']) ? (int) $auto_scan_result['queued'] : 0)); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($queue_result)) : ?>
            <div class="aitmt-debug-result <?php echo !empty($queue_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                <p><strong><?php esc_html_e('Last queue run:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($queue_result['created_at']) ? $queue_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(isset($queue_result['message']) ? $queue_result['message'] : ''); ?></p>
                <?php if (!empty($queue_result['routes'])) : ?>
                    <p><?php esc_html_e('Routes:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html($queue_result['routes']); ?></code></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cron_result)) : ?>
            <div class="aitmt-debug-result <?php echo !empty($cron_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                <p><strong><?php esc_html_e('Last background run:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($cron_result['created_at']) ? $cron_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(isset($cron_result['message']) ? $cron_result['message'] : ''); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
            <?php wp_nonce_field('aitmt_scan_site'); ?>
            <input type="hidden" name="action" value="aitmt_scan_site">
            <?php submit_button(__('Scan site strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
        </form>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
            <?php wp_nonce_field('aitmt_process_queue'); ?>
            <input type="hidden" name="action" value="aitmt_process_queue">
            <?php submit_button(__('Process translation queue', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
        </form>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
            <?php wp_nonce_field('aitmt_translate_all_queue'); ?>
            <input type="hidden" name="action" value="aitmt_translate_all_queue">
            <?php submit_button(__('Translate All', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
        </form>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block">
            <?php wp_nonce_field('aitmt_clear_queue'); ?>
            <input type="hidden" name="action" value="aitmt_clear_queue">
            <?php submit_button(__('Clear queued strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
        </form>
        <p class="description">
            <?php esc_html_e('Translate All uses the active provider and can consume API quota quickly. It runs in safe batches and may stop if provider quota, cooldown, or server time limits are reached.', 'ait-multilingual-translate'); ?>
        </p>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_debugger_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $last_error = get_option('aitmt_last_error', '');
    $debug_result = get_option('aitmt_debug_result', array());
    $debug_result = is_array($debug_result) ? $debug_result : array();
    $route_debug = get_option('aitmt_last_route_debug', array());
    $route_debug = is_array($route_debug) ? $route_debug : array();
    $debug_events = get_option('aitmt_debug_events', array());
    $debug_events = is_array($debug_events) ? array_slice($debug_events, 0, 20) : array();
    $debug_file_enabled = aitmt_fallback_debug_file_enabled();
    $debug_log_path = aitmt_fallback_debug_log_path();
    $debug_log_size = file_exists($debug_log_path) ? size_format((int) filesize($debug_log_path)) : '0 B';
    $provider = aitmt_fallback_active_provider();
    $api_key = aitmt_fallback_provider_key($provider);

    aitmt_fallback_admin_page_start(
        __('AI Translate - Debugger', 'ait-multilingual-translate'),
        __('Check provider access, current route, translation table, and the latest API error.', 'ait-multilingual-translate')
    );
    ?>
    <div class="aitmt-wide-card">
        <h2><?php esc_html_e('Debugger', 'ait-multilingual-translate'); ?></h2>
        <table class="form-table aitmt-debug-table" role="presentation">
            <?php
            aitmt_fallback_render_debug_value('Plugin version', AITMT_VERSION, true);
            aitmt_fallback_render_debug_value('Build status', aitmt_fallback_edition_label(), true);
            aitmt_fallback_render_debug_value('Plugin mode', 'single-file queued engine', true);
            aitmt_fallback_render_debug_value('Plugin folder', aitmt_fallback_plugin_folder(), aitmt_fallback_is_update_safe_folder());
            aitmt_fallback_render_debug_value('Upload update safe', aitmt_fallback_is_update_safe_folder() ? 'yes' : 'no - install once into ait-multilingual-translate folder', aitmt_fallback_is_update_safe_folder());
            aitmt_fallback_render_debug_value('PHP version', PHP_VERSION, version_compare(PHP_VERSION, '7.0', '>='));
            aitmt_fallback_render_debug_value('DOMDocument', class_exists('DOMDocument') ? 'available' : 'missing', class_exists('DOMDocument'));
            aitmt_fallback_render_debug_value('WP HTTP API', function_exists('wp_remote_post') ? 'available' : 'missing', function_exists('wp_remote_post'));
            aitmt_fallback_render_debug_value('Source language', aitmt_fallback_source_language(), !empty(aitmt_fallback_source_language()));
            aitmt_fallback_render_debug_value('Enabled languages', implode(', ', aitmt_fallback_enabled_languages()), count(aitmt_fallback_enabled_languages()) > 1);
            aitmt_fallback_render_debug_value('Current language', aitmt_fallback_current_language(), true);
            aitmt_fallback_render_debug_value('Provider', aitmt_fallback_provider_label($provider), true);
            aitmt_fallback_render_debug_value('Provider key', aitmt_fallback_mask_secret($api_key) . ' (' . aitmt_fallback_provider_key_source($provider) . ')', !empty($api_key));
            aitmt_fallback_render_debug_value('Provider model', aitmt_fallback_provider_model($provider), true);
            aitmt_fallback_render_debug_value('Translation Mode', aitmt_fallback_translation_mode_label(), true);
            aitmt_fallback_render_debug_value('Quality mode', aitmt_fallback_quality_mode_label(), true);
            aitmt_fallback_render_debug_value('Provider cooldown', aitmt_fallback_provider_cooldown_remaining($provider) ? aitmt_fallback_provider_cooldown_remaining($provider) . ' seconds' : 'none', !aitmt_fallback_provider_cooldown_remaining($provider));
            aitmt_fallback_render_debug_value('Provider daily characters', (string) aitmt_fallback_provider_chars_used($provider, gmdate('Ymd')), true);
            aitmt_fallback_render_debug_value('Provider monthly characters', (string) aitmt_fallback_provider_chars_used($provider, gmdate('Ym')), true);
            $provider_stats = aitmt_fallback_provider_stats_for($provider);
            aitmt_fallback_render_debug_value('Provider API requests', (string) absint($provider_stats['requests'] ?? 0), true);
            aitmt_fallback_render_debug_value('Estimated input tokens', (string) absint($provider_stats['input_tokens'] ?? 0), true);
            aitmt_fallback_render_debug_value('Estimated output tokens', (string) absint($provider_stats['output_tokens'] ?? 0), true);
            aitmt_fallback_render_debug_value('Estimated cost', number_format((float) ($provider_stats['estimated_cost'] ?? 0), 6), true);
            aitmt_fallback_render_debug_value('Translation memory cache hits', (string) absint($provider_stats['cache_hits'] ?? 0), true);
            aitmt_fallback_render_debug_value('Duplicate strings skipped', (string) absint($provider_stats['duplicate_skipped'] ?? 0), true);
            aitmt_fallback_render_debug_value('Translation table', aitmt_fallback_translation_table_exists() ? aitmt_fallback_translation_table() : 'missing', aitmt_fallback_translation_table_exists());
            aitmt_fallback_render_debug_value('Queued strings', (string) aitmt_fallback_queued_count(), true);
            aitmt_fallback_render_debug_value('Saved translations', (string) aitmt_fallback_translation_count(), true);
            aitmt_fallback_render_debug_value('Last error', $last_error ? $last_error : 'none', !$last_error);
            aitmt_fallback_render_debug_value('Extended file logging', $debug_file_enabled ? 'enabled' : 'disabled', !$debug_file_enabled ? null : true);
            aitmt_fallback_render_debug_value('Debug log file', $debug_log_path . ' (' . $debug_log_size . ')', $debug_file_enabled ? file_exists(dirname($debug_log_path)) : null);
            ?>
        </table>

        <form class="aitmt-debug-settings" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_debug_settings'); ?>
            <input type="hidden" name="action" value="aitmt_debug_settings">
            <label>
                <input type="checkbox" name="aitmt_debug_file_enabled" value="1" <?php checked($debug_file_enabled); ?>>
                <?php esc_html_e('Write extended debug log to a separate file', 'ait-multilingual-translate'); ?>
            </label>
            <?php submit_button(__('Save debug settings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            <p class="description"><?php esc_html_e('Use this only while testing. The file stores route/API events as JSON lines and is cleared by Clear debug log.', 'ait-multilingual-translate'); ?></p>
        </form>

        <?php if (!empty($route_debug)) : ?>
            <div class="aitmt-debug-result">
                <p><strong><?php esc_html_e('Last frontend route:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($route_debug['created_at']) ? $route_debug['created_at'] : ''); ?></p>
                <table class="form-table aitmt-debug-table" role="presentation">
                    <?php
                    aitmt_fallback_render_debug_value('Original request URI', isset($route_debug['original_request_uri']) ? $route_debug['original_request_uri'] : '', true);
                    aitmt_fallback_render_debug_value('Routed request URI', isset($route_debug['routed_request_uri']) ? $route_debug['routed_request_uri'] : '', true);
                    aitmt_fallback_render_debug_value('Path language', isset($route_debug['path_language']) ? $route_debug['path_language'] : '', true);
                    aitmt_fallback_render_debug_value('Requested language', isset($route_debug['requested_language']) ? $route_debug['requested_language'] : '', true);
                    aitmt_fallback_render_debug_value('Current language', isset($route_debug['current_language']) ? $route_debug['current_language'] : '', true);
                    aitmt_fallback_render_debug_value('Matched rule', isset($route_debug['matched_rule']) ? $route_debug['matched_rule'] : '', null);
                    aitmt_fallback_render_debug_value('Matched query', isset($route_debug['matched_query']) ? $route_debug['matched_query'] : '', null);
                    aitmt_fallback_render_debug_value('Query vars', !empty($route_debug['query_vars']) ? wp_json_encode($route_debug['query_vars']) : 'none', null);
                    aitmt_fallback_render_debug_value('Queried object ID', isset($route_debug['queried_object_id']) ? (string) $route_debug['queried_object_id'] : '0', null);
                    aitmt_fallback_render_debug_value('is_404', isset($route_debug['is_404']) ? $route_debug['is_404'] : 'no', empty($route_debug['is_404']) || 'no' === $route_debug['is_404']);
                    aitmt_fallback_render_debug_value('is_product', isset($route_debug['is_product']) ? $route_debug['is_product'] : 'no', null);
                    aitmt_fallback_render_debug_value('is_shop', isset($route_debug['is_shop']) ? $route_debug['is_shop'] : 'no', null);
                    aitmt_fallback_render_debug_value('is_product_category', isset($route_debug['is_product_category']) ? $route_debug['is_product_category'] : 'no', null);
                    ?>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($debug_result)) : ?>
            <div class="aitmt-debug-result <?php echo !empty($debug_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                <p><strong><?php esc_html_e('Last provider test:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($debug_result['created_at']) ? $debug_result['created_at'] : ''); ?></p>
                <p><?php echo esc_html(isset($debug_result['message']) ? $debug_result['message'] : ''); ?></p>
                <?php if (!empty($debug_result['http_status'])) : ?>
                    <p><?php esc_html_e('HTTP status:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((string) $debug_result['http_status']); ?></code></p>
                <?php endif; ?>
                <?php if (!empty($debug_result['source_language']) || !empty($debug_result['target_language'])) : ?>
                    <p><?php esc_html_e('Route:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((isset($debug_result['source_language']) ? $debug_result['source_language'] : '') . ' -> ' . (isset($debug_result['target_language']) ? $debug_result['target_language'] : '')); ?></code></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($debug_events)) : ?>
            <div class="aitmt-debug-result">
                <p><strong><?php esc_html_e('Debug log', 'ait-multilingual-translate'); ?></strong> <?php esc_html_e('Latest 20 events', 'ait-multilingual-translate'); ?></p>
                <table class="widefat striped aitmt-log-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Time', 'ait-multilingual-translate'); ?></th>
                            <th><?php esc_html_e('Type', 'ait-multilingual-translate'); ?></th>
                            <th><?php esc_html_e('Message', 'ait-multilingual-translate'); ?></th>
                            <th><?php esc_html_e('Context', 'ait-multilingual-translate'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($debug_events as $event) : ?>
                            <tr>
                                <td><?php echo esc_html(isset($event['created_at']) ? $event['created_at'] : ''); ?></td>
                                <td><code><?php echo esc_html(isset($event['type']) ? $event['type'] : ''); ?></code></td>
                                <td><?php echo esc_html(isset($event['message']) ? $event['message'] : ''); ?></td>
                                <td><code><?php echo esc_html(!empty($event['context']) ? wp_json_encode($event['context']) : ''); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="aitmt-debug-actions">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_debug_test'); ?>
                <input type="hidden" name="action" value="aitmt_debug_test">
                <select name="target_language">
                    <?php foreach (array_diff(aitmt_fallback_enabled_languages(), array(aitmt_fallback_source_language())) as $language) : ?>
                        <option value="<?php echo esc_attr($language); ?>"><?php echo esc_html(strtoupper($language)); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php submit_button(__('Test provider translation', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_debug_download'); ?>
                <input type="hidden" name="action" value="aitmt_debug_download">
                <?php submit_button(__('Download log file', 'ait-multilingual-translate'), 'secondary', 'submit', false, file_exists($debug_log_path) ? array() : array('disabled' => 'disabled')); ?>
            </form>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_debug_clear'); ?>
                <input type="hidden" name="action" value="aitmt_debug_clear">
                <?php submit_button(__('Clear debug log', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_clear_provider_cooldown'); ?>
                <input type="hidden" name="action" value="aitmt_clear_provider_cooldown">
                <?php submit_button(__('Clear provider cooldown', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
        </div>
    </div>
    <?php
    aitmt_fallback_admin_page_end();
}

function aitmt_fallback_debug_test_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_debug_test');

    $result = aitmt_fallback_run_openai_debug_test();
    update_option('aitmt_debug_result', $result, false);

    if (!empty($result['ok'])) {
        update_option('aitmt_last_error', '', false);
    } elseif (!empty($result['message'])) {
        update_option('aitmt_last_error', current_time('mysql') . ' - ' . $result['message'], false);
    }

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-debugger'));
    exit;
}

function aitmt_fallback_debug_settings_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_debug_settings');

    $enabled = empty($_POST['aitmt_debug_file_enabled']) ? '0' : '1';
    update_option('aitmt_debug_file_enabled', $enabled, false);

    if ('1' === $enabled) {
        aitmt_fallback_ensure_debug_log_dir();
        aitmt_fallback_log_event('settings', 'Extended file logging enabled.', array(
            'path' => aitmt_fallback_debug_log_path(),
        ));
    } else {
        aitmt_fallback_log_event('settings', 'Extended file logging disabled.');
    }

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-debugger'));
    exit;
}

function aitmt_fallback_debug_clear_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_debug_clear');
    delete_option('aitmt_debug_result');
    delete_option('aitmt_last_error');
    delete_option('aitmt_last_route_debug');
    delete_option('aitmt_debug_events');
    $log_path = aitmt_fallback_debug_log_path();
    if (file_exists($log_path)) {
        aitmt_fallback_write_local_file($log_path, '');
    }

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-debugger'));
    exit;
}

function aitmt_fallback_debug_download_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_debug_download');

    $log_path = aitmt_fallback_debug_log_path();
    if (!file_exists($log_path) || !is_readable($log_path)) {
        wp_die(esc_html__('Debug log file was not found.', 'ait-multilingual-translate'));
    }

    while (ob_get_level()) {
        ob_end_clean();
    }

    nocache_headers();
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="ait-multilingual-translate-debug-' . gmdate('Ymd-His') . '.log"');
    $contents = aitmt_fallback_read_local_file($log_path);
    header('Content-Length: ' . (string) strlen($contents));
    echo aitmt_fallback_redact_sensitive_text($contents); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Plain-text log download is sanitized/redacted above after nonce/capability checks.
    exit;
}

function aitmt_fallback_clear_provider_cooldown_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_clear_provider_cooldown');
    $provider = aitmt_fallback_active_provider();
    delete_transient(aitmt_fallback_provider_cooldown_key($provider));
    update_option('aitmt_debug_result', array(
        'ok' => true,
        'message' => sprintf('Provider cooldown cleared for %s.', aitmt_fallback_provider_label($provider)),
        'provider' => aitmt_fallback_provider_label($provider),
        'model' => aitmt_fallback_provider_model($provider),
        'created_at' => current_time('mysql'),
    ), false);

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-debugger'));
    exit;
}

function aitmt_fallback_process_queue_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_process_queue');
    $options = aitmt_fallback_options();
    $limit = isset($options['max_segments_per_request']) ? absint($options['max_segments_per_request']) : 40;
    $result = aitmt_fallback_process_queue($limit);
    update_option('aitmt_queue_result', array_merge($result, array('created_at' => current_time('mysql'))), false);

    if (empty($result['ok']) && !empty($result['message'])) {
        update_option('aitmt_last_error', current_time('mysql') . ' - ' . $result['message'], false);
    }

    wp_safe_redirect(aitmt_fallback_admin_redirect_url_from_post('ait-multilingual-translate-scanner'));
    exit;
}

function aitmt_fallback_translate_all_queue_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_translate_all_queue');

    $options = aitmt_fallback_options();
    $limit = isset($options['max_segments_per_request']) ? absint($options['max_segments_per_request']) : 40;
    $limit = max(1, min(100, $limit));
    $queued_before = aitmt_fallback_queued_count();
    $max_batches = (int) apply_filters('aitmt_translate_all_max_batches', 10);
    $max_batches = max(1, min(25, $max_batches));
    $deadline = time() + (int) apply_filters('aitmt_translate_all_time_budget', 25);
    $processed = 0;
    $batches = 0;
    $routes = array();
    $last_result = array(
        'ok' => true,
        'message' => 'Queue is empty.',
        'processed' => 0,
    );

    while ($queued_before > 0 && $batches < $max_batches && time() < $deadline) {
        $result = aitmt_fallback_process_queue($limit);
        $last_result = is_array($result) ? $result : $last_result;
        $batches++;

        if (!empty($last_result['routes'])) {
            $routes[] = (string) $last_result['routes'];
        }

        $batch_processed = isset($last_result['processed']) ? absint($last_result['processed']) : 0;
        $processed += $batch_processed;

        if (empty($last_result['ok']) || $batch_processed < 1 || aitmt_fallback_queued_count() < 1) {
            break;
        }
    }

    $remaining = aitmt_fallback_queued_count();
    $ok = !empty($last_result['ok']);

    if ($queued_before < 1) {
        $message = __('Queue is empty.', 'ait-multilingual-translate');
    } elseif (!$ok) {
        $message = sprintf(
            /* translators: 1: processed count, 2: original queued count, 3: error message. */
            __('Translate All stopped after processing %1$d of %2$d queued translation(s): %3$s', 'ait-multilingual-translate'),
            $processed,
            $queued_before,
            isset($last_result['message']) ? (string) $last_result['message'] : __('Provider error.', 'ait-multilingual-translate')
        );
    } elseif ($remaining > 0) {
        $message = sprintf(
            /* translators: 1: processed count, 2: original queued count, 3: remaining count, 4: batch count. */
            __('Translate All processed %1$d of %2$d queued translation(s) in %4$d safe batch(es). %3$d item(s) remain. Run it again or enable background processing; provider quota or server time limits may stop long runs.', 'ait-multilingual-translate'),
            $processed,
            $queued_before,
            $remaining,
            $batches
        );
    } else {
        $message = sprintf(
            /* translators: 1: processed count, 2: batch count. */
            __('Translate All finished. Processed %1$d queued translation(s) in %2$d safe batch(es).', 'ait-multilingual-translate'),
            $processed,
            $batches
        );
    }

    $routes = array_values(array_unique(array_filter($routes)));
    $queue_result = array_merge($last_result, array(
        'ok' => $ok,
        'message' => $message,
        'processed' => $processed,
        'routes' => implode(', ', $routes),
        'queued_before' => $queued_before,
        'queued_remaining' => $remaining,
        'batches' => $batches,
        'translate_all' => true,
        'created_at' => current_time('mysql'),
    ));

    update_option('aitmt_queue_result', $queue_result, false);

    if (!$ok && !empty($message)) {
        update_option('aitmt_last_error', current_time('mysql') . ' - ' . wp_strip_all_tags($message), false);
    }

    wp_safe_redirect(aitmt_fallback_admin_redirect_url_from_post('ait-multilingual-translate-scanner'));
    exit;
}

function aitmt_fallback_clear_queue_handler()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'ait-multilingual-translate'));
    }

    check_admin_referer('aitmt_clear_queue');

    global $wpdb;
    if (aitmt_fallback_translation_table_exists()) {
        $wpdb->delete(aitmt_fallback_translation_table(), array('status' => 'queued'), array('%s'));
    }
    update_option('aitmt_queue_result', array(
        'ok' => true,
        'message' => 'Queued items cleared.',
        'processed' => 0,
        'created_at' => current_time('mysql'),
    ), false);

    wp_safe_redirect(admin_url('admin.php?page=ait-multilingual-translate-scanner'));
    exit;
}

function aitmt_fallback_run_openai_debug_test()
{
    $started = microtime(true);
    $provider = aitmt_fallback_active_provider();
    $api_key = aitmt_fallback_provider_key($provider);
    $provider_label = aitmt_fallback_provider_label($provider);
    $model = aitmt_fallback_provider_model($provider);
    $source_language = aitmt_fallback_source_language();
    $enabled = aitmt_fallback_enabled_languages();
    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- The debug handler verifies aitmt_debug_test before calling this helper.
    $target_language = isset($_POST['target_language']) ? aitmt_fallback_normalize_language(sanitize_key(wp_unslash((string) $_POST['target_language']))) : '';

    if ($target_language === $source_language || !in_array($target_language, $enabled, true)) {
        $target_language = '';
    }

    foreach ($enabled as $language) {
        if ('' === $target_language && $language !== $source_language) {
            $target_language = $language;
            break;
        }
    }

    if (empty($api_key)) {
        return array(
            'ok' => false,
            'message' => $provider_label . ' API key is missing.',
            'provider' => $provider_label,
            'created_at' => current_time('mysql'),
        );
    }

    if (empty($target_language)) {
        return array(
            'ok' => false,
            'message' => 'No target language is selected.',
            'created_at' => current_time('mysql'),
        );
    }

    $sample = 'Sample website text for translation.';
    $hash = aitmt_fallback_translation_hash($sample);
    $translations = aitmt_fallback_translate_with_provider(array($hash => $sample), $source_language, $target_language);
    $duration = round(microtime(true) - $started, 3);

    if (is_wp_error($translations)) {
        $data = $translations->get_error_data();

        return array(
            'ok' => false,
            'message' => $translations->get_error_message(),
            'code' => $translations->get_error_code(),
            'http_status' => is_array($data) && isset($data['status']) ? (int) $data['status'] : '',
            'provider' => $provider_label,
            'model' => $model,
            'source_language' => $source_language,
            'target_language' => $target_language,
            'duration' => $duration,
            'created_at' => current_time('mysql'),
        );
    }

    return array(
        'ok' => true,
        'message' => $provider_label . ' test translation succeeded.',
        'provider' => $provider_label,
        'model' => $model,
        'source_language' => $source_language,
        'target_language' => $target_language,
        'sample_source' => $sample,
        'sample_translation' => isset($translations[$hash]) ? $translations[$hash] : '',
        'duration' => $duration,
        'created_at' => current_time('mysql'),
    );
}

function aitmt_fallback_translation_table_exists()
{
    global $wpdb;

    $table = aitmt_fallback_translation_table();
    $found = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));

    return $found === $table;
}

function aitmt_fallback_translation_count()
{
    global $wpdb;

    if (!aitmt_fallback_translation_table_exists()) {
        return 0;
    }

    $table = aitmt_fallback_translation_table_sql();
    if ('' === $table) {
        return 0;
    }

    return (int) $wpdb->get_var( // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated by aitmt_fallback_translation_table_sql(); values are prepared below.
        $wpdb->prepare(
            'SELECT COUNT(*) FROM ' . $table . ' WHERE status IN (%s, %s)', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is controlled by aitmt_fallback_translation_table_sql().
            'published',
            'manual'
        )
    );
}

function aitmt_fallback_mask_secret($secret)
{
    $secret = (string) $secret;
    if ('' === $secret) {
        return 'missing';
    }

    if (strlen($secret) <= 10) {
        return 'saved';
    }

    return substr($secret, 0, 6) . '...' . substr($secret, -4);
}

function aitmt_fallback_render_debug_value($label, $value, $good = null)
{
    $class = '';
    if (true === $good) {
        $class = ' is-good';
    } elseif (false === $good) {
        $class = ' is-bad';
    }

    echo '<tr class="' . esc_attr($class) . '"><th scope="row">' . esc_html($label) . '</th><td><code>' . esc_html((string) $value) . '</code></td></tr>';
}

function aitmt_fallback_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('aitmt_options', array());
    if (!is_array($options)) {
        $options = array();
    }

    $defaults = aitmt_fallback_default_options();
    $options = wp_parse_args($options, $defaults);

    if (!is_array($options['enabled_languages'])) {
        $options['enabled_languages'] = array_filter(array_map('trim', explode(',', (string) $options['enabled_languages'])));
    }

    $languages = aitmt_fallback_languages();
    $source_language = (string) $options['source_language'];
    $admin_mode = 'advanced' === (string) $options['admin_mode'] ? 'advanced' : 'basic';
    $last_error = get_option('aitmt_last_error', '');
    $debug_result = get_option('aitmt_debug_result', array());
    $debug_result = is_array($debug_result) ? $debug_result : array();
    $queue_result = get_option('aitmt_queue_result', array());
    $queue_result = is_array($queue_result) ? $queue_result : array();
    $cron_result = get_option('aitmt_cron_queue_result', array());
    $cron_result = is_array($cron_result) ? $cron_result : array();
    $auto_scan_result = get_option('aitmt_last_auto_scan', array());
    $auto_scan_result = is_array($auto_scan_result) ? $auto_scan_result : array();
    $scan_result = get_option('aitmt_scan_result', array());
    $scan_result = is_array($scan_result) ? $scan_result : array();
    $recent_translations = aitmt_fallback_recent_translations(20);
    $active_provider = aitmt_fallback_active_provider();
    $api_key = aitmt_fallback_provider_key($active_provider);
    $api_key_source = aitmt_fallback_provider_key_source($active_provider);
    ?>
    <div class="wrap aitmt-admin-page aitmt-mode-<?php echo esc_attr($admin_mode); ?>">
        <div class="aitmt-admin-title is-dashboard">
            <img src="<?php echo esc_attr(aitmt_fallback_logo_url()); ?>" alt="" width="150" height="150">
            <div>
                <h1><?php esc_html_e('AI Translate', 'ait-multilingual-translate'); ?></h1>
                <p><?php esc_html_e('Scan strings, translate them in batches, then edit saved translations on the frontend.', 'ait-multilingual-translate'); ?></p>
                <p class="aitmt-admin-meta">
                    <?php echo esc_html(sprintf('AI Translate %s | %s | Developer: sotter IT Design | ', AITMT_VERSION, aitmt_fallback_edition_label())); ?>
                    <a href="https://wp-ai.itdesign.biz" target="_blank" rel="noopener noreferrer">wp-ai.itdesign.biz</a>
                    <?php echo esc_html(' | info@itdesign.biz'); ?>
                </p>
            </div>
        </div>
        <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WordPress settings API notice flag is read-only. ?>
        <?php if (!empty($_GET['settings-updated'])) : ?>
            <div class="notice notice-success inline">
                <p><?php esc_html_e('Settings saved.', 'ait-multilingual-translate'); ?></p>
            </div>
        <?php endif; ?>

        <?php aitmt_fallback_public_beta_notice(); ?>

        <div class="notice notice-warning inline aitmt-advanced-only">
            <p>
                <?php esc_html_e('AIT Multilingual Translate is using the single-file queued translation engine. Pages use saved translations first; new strings are collected into the queue for batch translation.', 'ait-multilingual-translate'); ?>
            </p>
        </div>
        <?php if (!empty($last_error)) : ?>
            <div class="notice notice-error inline">
                <p><strong><?php esc_html_e('Last translation error:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html($last_error); ?></p>
            </div>
        <?php endif; ?>

        <nav class="aitmt-admin-tabs" aria-label="<?php esc_attr_e('AIT Multilingual Translate sections', 'ait-multilingual-translate'); ?>">
            <a href="#aitmt-general"><?php esc_html_e('General', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-languages"><?php esc_html_e('Languages', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-provider"><?php esc_html_e('Providers', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-switcher"><?php esc_html_e('Frontend Editor', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-urls"><?php esc_html_e('SEO & URLs', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-scan"><?php esc_html_e('Scanner', 'ait-multilingual-translate'); ?></a>
            <a class="aitmt-advanced-only" href="#aitmt-queue"><?php esc_html_e('Queue', 'ait-multilingual-translate'); ?></a>
            <a class="aitmt-advanced-only" href="#aitmt-debug"><?php esc_html_e('Debugger', 'ait-multilingual-translate'); ?></a>
            <a href="#aitmt-support"><?php esc_html_e('Support', 'ait-multilingual-translate'); ?></a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-translations')); ?>"><?php esc_html_e('Translations', 'ait-multilingual-translate'); ?></a>
        </nav>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('aitmt_save_settings'); ?>
            <input type="hidden" name="action" value="aitmt_save_settings">

            <div class="aitmt-fallback-card" id="aitmt-general">
                <h2><?php esc_html_e('General', 'ait-multilingual-translate'); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e('Interface mode', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <fieldset class="aitmt-segmented">
                                <label><input type="radio" name="aitmt_options[admin_mode]" value="basic" <?php checked($admin_mode, 'basic'); ?>> <span><?php esc_html_e('Basic', 'ait-multilingual-translate'); ?></span></label>
                                <label><input type="radio" name="aitmt_options[admin_mode]" value="advanced" <?php checked($admin_mode, 'advanced'); ?>> <span><?php esc_html_e('Advanced', 'ait-multilingual-translate'); ?></span></label>
                            </fieldset>
                            <p class="description"><?php esc_html_e('Basic mode shows the setup needed for everyday translation. Advanced mode reveals queue diagnostics, provider internals, scanner details, routes, and logs.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Build status', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <p><strong><?php echo esc_html(aitmt_fallback_edition_label()); ?></strong> <?php esc_html_e('Public Beta preparation build.', 'ait-multilingual-translate'); ?></p>
                            <p class="description"><?php esc_html_e('Feature flag helpers are prepared for future package variants. All current beta features remain enabled.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="aitmt-fallback-card" id="aitmt-languages">
                <h2><?php esc_html_e('Languages', 'ait-multilingual-translate'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="aitmt-source-language"><?php esc_html_e('Source language', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <select id="aitmt-source-language" name="aitmt_options[source_language]">
                            <option value=""><?php esc_html_e('Auto: WordPress site language', 'ait-multilingual-translate'); ?></option>
                            <?php foreach ($languages as $code => $label) : ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($source_language, $code); ?>>
                                    <?php echo esc_html($label . ' (' . strtoupper($code) . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('This is the language your original site content is written in. Auto uses the WordPress site language.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-enabled-languages"><?php esc_html_e('Target languages', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input type="search" class="aitmt-fallback-language-search" placeholder="<?php esc_attr_e('Search languages...', 'ait-multilingual-translate'); ?>">
                        <div class="aitmt-fallback-language-grid" id="aitmt-enabled-languages">
                            <?php foreach ($languages as $code => $label) : ?>
                                <label>
                                    <input type="checkbox" name="aitmt_options[enabled_languages][]" value="<?php echo esc_attr($code); ?>" <?php checked(in_array($code, $options['enabled_languages'], true)); ?>>
                                    <span><?php echo esc_html($label); ?></span>
                                    <code><?php echo esc_html(strtoupper($code)); ?></code>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="description"><?php esc_html_e('Select only the languages you want visitors to use. Every selected target language creates its own saved translation rows.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
            </table>
            </div>

            <div class="aitmt-fallback-card" id="aitmt-provider">
                <h2><?php esc_html_e('AI Provider', 'ait-multilingual-translate'); ?></h2>
                <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Provider', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <select name="aitmt_options[provider]">
                            <option value="openai" <?php selected($options['provider'], 'openai'); ?>><?php esc_html_e('OpenAI / ChatGPT', 'ait-multilingual-translate'); ?></option>
                            <option value="gemini" <?php selected($options['provider'], 'gemini'); ?>><?php esc_html_e('Google Gemini', 'ait-multilingual-translate'); ?></option>
                            <option value="grok" <?php selected($options['provider'], 'grok'); ?>><?php esc_html_e('Grok / xAI', 'ait-multilingual-translate'); ?></option>
                            <option value="google_translate" <?php selected($options['provider'], 'google_translate'); ?>><?php esc_html_e('Google Translate', 'ait-multilingual-translate'); ?></option>
                            <option value="deepl" <?php selected($options['provider'], 'deepl'); ?>><?php esc_html_e('DeepL', 'ait-multilingual-translate'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('The provider used for new queue translations. Already saved manual/published translations are reused and are not sent again.', 'ait-multilingual-translate'); ?></p>
                        <div class="aitmt-provider-cards" aria-label="<?php esc_attr_e('Provider capabilities', 'ait-multilingual-translate'); ?>">
                            <?php foreach (aitmt_fallback_provider_catalog() as $provider_id => $provider_data) : ?>
                                <div class="aitmt-provider-card is-<?php echo esc_attr($provider_data['status']); ?> <?php echo esc_attr($provider_id === $options['provider'] ? 'is-selected' : ''); ?>">
                                    <div class="aitmt-provider-card-head">
                                        <strong><?php echo esc_html($provider_data['label']); ?></strong>
                                        <span><?php echo 'active' === $provider_data['status'] ? esc_html__('Active', 'ait-multilingual-translate') : esc_html__('Planned', 'ait-multilingual-translate'); ?></span>
                                    </div>
                                    <div class="aitmt-provider-badges">
                                        <?php foreach ($provider_data['badges'] as $badge) : ?>
                                            <code><?php echo esc_html($badge); ?></code>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="description"><?php esc_html_e('Provider cards show active integrations plus planned architecture targets. Planned providers are documented but are not enabled until their API layer is added in a future release.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-openai-api-key"><?php esc_html_e('OpenAI API key', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-openai-api-key" class="regular-text" type="password" name="aitmt_options[openai_api_key]" value="<?php echo esc_attr($options['openai_api_key']); ?>">
                        <p class="description">
                            <?php esc_html_e('Create a key in the OpenAI dashboard, then paste it here.', 'ait-multilingual-translate'); ?>
                            <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Open API keys page', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-openai-model"><?php esc_html_e('OpenAI model', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-openai-model" class="regular-text" type="text" name="aitmt_options[openai_model]" value="<?php echo esc_attr($options['openai_model']); ?>">
                        <p class="description"><?php esc_html_e('Use a model available to your OpenAI account. If quota/billing is missing, the provider returns 429.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-gemini-api-key"><?php esc_html_e('Gemini API key', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-gemini-api-key" class="regular-text" type="password" name="aitmt_options[gemini_api_key]" value="<?php echo esc_attr($options['gemini_api_key']); ?>">
                        <p class="description">
                            <?php esc_html_e('Create a Gemini key in Google AI Studio, then paste it here.', 'ait-multilingual-translate'); ?>
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Open Google AI Studio API keys', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-gemini-model"><?php esc_html_e('Gemini model', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-gemini-model" class="regular-text" type="text" name="aitmt_options[gemini_model]" value="<?php echo esc_attr($options['gemini_model']); ?>">
                        <p class="description"><?php esc_html_e('For testing, use a small batch size because Gemini free tier limits can be very low.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-grok-api-key"><?php esc_html_e('Grok API key', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-grok-api-key" class="regular-text" type="password" name="aitmt_options[grok_api_key]" value="<?php echo esc_attr($options['grok_api_key']); ?>">
                        <p class="description">
                            <?php esc_html_e('Create an xAI API key, then paste it here.', 'ait-multilingual-translate'); ?>
                            <a href="https://console.x.ai/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Open xAI Console', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-grok-model"><?php esc_html_e('Grok model', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-grok-model" class="regular-text" type="text" name="aitmt_options[grok_model]" value="<?php echo esc_attr($options['grok_model']); ?>">
                        <p class="description"><?php esc_html_e('Use a model enabled for your xAI account. A 403 usually means the key cannot access the selected model or billing is not ready.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-google-translate-api-key"><?php esc_html_e('Google Translate API key', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-google-translate-api-key" class="regular-text" type="password" name="aitmt_options[google_translate_api_key]" value="<?php echo esc_attr($options['google_translate_api_key']); ?>">
                        <p class="description">
                            <?php esc_html_e('Uses Google Cloud Translation Basic v2. Enable Cloud Translation API in Google Cloud and use an API key with billing/quota configured.', 'ait-multilingual-translate'); ?>
                            <a href="https://cloud.google.com/translate/docs/basic/translating-text" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Google docs', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-deepl-api-key"><?php esc_html_e('DeepL API key', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-deepl-api-key" class="regular-text" type="password" name="aitmt_options[deepl_api_key]" value="<?php echo esc_attr($options['deepl_api_key']); ?>">
                        <p class="description">
                            <?php esc_html_e('DeepL has separate api-free.deepl.com and api.deepl.com endpoints. Pick the endpoint that matches your key.', 'ait-multilingual-translate'); ?>
                            <a href="https://developers.deepl.com/api-reference/translate" target="_blank" rel="noopener noreferrer"><?php esc_html_e('DeepL docs', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('DeepL plan', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <select name="aitmt_options[deepl_plan]">
                            <option value="free" <?php selected($options['deepl_plan'], 'free'); ?>><?php esc_html_e('api-free.deepl.com endpoint', 'ait-multilingual-translate'); ?></option>
                            <option value="pro" <?php selected($options['deepl_plan'], 'pro'); ?>><?php esc_html_e('api.deepl.com endpoint', 'ait-multilingual-translate'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Match this setting to the endpoint assigned to your DeepL API key.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" scope="row">
                        <h3 class="aitmt-section-heading"><?php esc_html_e('Translation Style / Tone of Voice', 'ait-multilingual-translate'); ?></h3>
                        <p class="description"><?php esc_html_e('Set the global translation style for prompt-based AI providers. Per content type modes for pages, posts, WooCommerce products, SEO meta, buttons, and short strings are planned for a future version.', 'ait-multilingual-translate'); ?></p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-translation-mode"><?php esc_html_e('Global Translation Mode', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <select id="aitmt-translation-mode" name="aitmt_options[translation_mode]">
                            <?php foreach (aitmt_fallback_translation_mode_options() as $mode_key => $mode_data) : ?>
                                <option value="<?php echo esc_attr($mode_key); ?>" <?php selected($options['translation_mode'], $mode_key); ?>><?php echo esc_html($mode_data['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('Tone of Voice affects AI-based translations only. Use Neutral mode for accurate translation, SEO mode for search-oriented content, and eCommerce mode for product pages.', 'ait-multilingual-translate'); ?></p>
                        <p class="description"><?php esc_html_e('Tone of Voice is applied only to prompt-based AI providers. In this Public Beta that means OpenAI, Gemini, and Grok/xAI; planned Claude, Mistral, DeepSeek, and similar AI providers will use the same setting when their API layers are added. Google Translate and DeepL ignore this setting.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-custom-translation-instruction"><?php esc_html_e('Custom translation instruction', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <textarea id="aitmt-custom-translation-instruction" class="large-text" rows="3" maxlength="500" name="aitmt_options[custom_translation_instruction]" placeholder="<?php esc_attr_e('Example: Translate naturally for Georgian customers, keep product names unchanged, use friendly but professional tone.', 'ait-multilingual-translate'); ?>"><?php echo esc_textarea($options['custom_translation_instruction']); ?></textarea>
                        <p class="description"><?php esc_html_e('Used only when Translation Mode is Custom Prompt. Maximum 500 characters.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" scope="row">
                        <h3 class="aitmt-section-heading"><?php esc_html_e('AI Cost Optimization', 'ait-multilingual-translate'); ?></h3>
                        <p class="description"><?php esc_html_e('Use cheap models by default, deduplicate strings before provider calls, reuse translation memory, and keep safety limits enabled while testing provider quota.', 'ait-multilingual-translate'); ?></p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aitmt-quality-mode"><?php esc_html_e('Translation quality mode', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <select id="aitmt-quality-mode" name="aitmt_options[quality_mode]">
                            <?php foreach (aitmt_fallback_quality_mode_options() as $quality_key => $quality_label) : ?>
                                <option value="<?php echo esc_attr($quality_key); ?>" <?php selected($options['quality_mode'], $quality_key); ?>><?php echo esc_html($quality_label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('Cheap is recommended for Public Beta testing. Balanced and Premium are guidance modes for choosing provider models; custom model fields still remain under your control.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr class="aitmt-advanced-only">
                    <th scope="row"><?php esc_html_e('Recommended default models', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <div class="aitmt-model-recommendations">
                            <code><?php esc_html_e('OpenAI Cheap: gpt-4o-mini', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('OpenAI Premium: gpt-4o', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('Gemini Cheap: Flash model', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('Gemini Premium: Pro model', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('Grok Cheap: lightweight/mini model where available', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('Claude Cheap: Haiku model where available', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('Mistral Cheap: small/fast model', 'ait-multilingual-translate'); ?></code>
                            <code><?php esc_html_e('DeepSeek Cheap: chat/standard model', 'ait-multilingual-translate'); ?></code>
                        </div>
                        <p class="description"><?php esc_html_e('The active Public Beta providers keep using the model field you set above. Planned provider model names are shown as release-planning guidance only.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr class="aitmt-advanced-only">
                    <th scope="row">
                        <label for="aitmt-translation-temperature"><?php esc_html_e('Temperature', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-translation-temperature" type="number" min="0" max="1" step="0.1" name="aitmt_options[translation_temperature]" value="<?php echo esc_attr((string) $options['translation_temperature']); ?>">
                        <p class="description"><?php esc_html_e('Default 0.1 keeps translations stable and predictable for AI providers.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr class="aitmt-advanced-only">
                    <th scope="row"><?php esc_html_e('Quota control', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <label for="aitmt-quota-daily"><?php esc_html_e('Daily character limit', 'ait-multilingual-translate'); ?></label><br>
                        <input id="aitmt-quota-daily" type="number" min="0" step="1000" name="aitmt_options[quota_daily_chars]" value="<?php echo esc_attr((string) $options['quota_daily_chars']); ?>">
                        <p class="description"><?php esc_html_e('Local safety limit for the active provider. 0 means no plugin-side daily stop.', 'ait-multilingual-translate'); ?></p>
                        <label for="aitmt-quota-monthly"><?php esc_html_e('Monthly character limit', 'ait-multilingual-translate'); ?></label><br>
                        <input id="aitmt-quota-monthly" type="number" min="0" step="1000" name="aitmt_options[quota_monthly_chars]" value="<?php echo esc_attr((string) $options['quota_monthly_chars']); ?>">
                        <p class="description"><?php esc_html_e('Local safety limit for the active provider. Provider billing limits still need to be configured in OpenAI/Google/DeepL/xAI accounts.', 'ait-multilingual-translate'); ?></p>
                        <label for="aitmt-max-chars-per-request"><?php esc_html_e('Max characters per request', 'ait-multilingual-translate'); ?></label><br>
                        <input id="aitmt-max-chars-per-request" type="number" min="0" step="500" name="aitmt_options[max_chars_per_request]" value="<?php echo esc_attr((string) $options['max_chars_per_request']); ?>">
                        <p class="description"><?php esc_html_e('0 means no plugin-side per-request character stop. Set a limit to avoid accidentally sending very large batches.', 'ait-multilingual-translate'); ?></p>
                        <label for="aitmt-estimated-cost-limit"><?php esc_html_e('Estimated cost limit per request', 'ait-multilingual-translate'); ?></label><br>
                        <input id="aitmt-estimated-cost-limit" type="number" min="0" step="0.0001" name="aitmt_options[estimated_cost_limit]" value="<?php echo esc_attr((string) $options['estimated_cost_limit']); ?>">
                        <p class="description"><?php esc_html_e('0 means no estimated-cost stop. Estimates are approximate and provider billing remains authoritative.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr class="aitmt-advanced-only">
                    <th scope="row"><?php esc_html_e('Translation behavior', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <label><input type="checkbox" name="aitmt_options[auto_translate]" value="1" <?php checked($options['auto_translate'], '1'); ?>> <?php esc_html_e('Enable frontend translation output', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[queue_missing]" value="1" <?php checked($options['queue_missing'], '1'); ?>> <?php esc_html_e('Collect missing strings into the translation queue', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[scan_on_save]" value="1" <?php checked($options['scan_on_save'], '1'); ?>> <?php esc_html_e('Automatically scan saved/updated content for new strings', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[cron_enabled]" value="1" <?php checked($options['cron_enabled'], '1'); ?>> <?php esc_html_e('Automatically process the translation queue in the background', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[translate_on_page_load]" value="1" <?php checked($options['translate_on_page_load'], '1'); ?>> <?php esc_html_e('Translate missing strings during page load (slower, use only for testing)', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[draft_mode]" value="1" <?php checked($options['draft_mode'], '1'); ?>> <?php esc_html_e('Save new AI translations as drafts', 'ait-multilingual-translate'); ?></label><br>
                        <label><input type="checkbox" name="aitmt_options[translate_attributes]" value="1" <?php checked($options['translate_attributes'], '1'); ?>> <?php esc_html_e('Translate alt, title, placeholder, aria-label, and SEO meta attributes', 'ait-multilingual-translate'); ?></label>
                        <p class="description"><?php esc_html_e('Recommended production mode: frontend output on, queue missing strings on, scan on save on, page-load translation off. Turn background processing on only when your API quota/billing is ready.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                <tr class="aitmt-advanced-only">
                    <th scope="row">
                        <label for="aitmt-max-segments"><?php esc_html_e('Batch size', 'ait-multilingual-translate'); ?></label>
                    </th>
                    <td>
                        <input id="aitmt-max-segments" type="number" min="1" max="100" name="aitmt_options[max_segments_per_request]" value="<?php echo esc_attr($options['max_segments_per_request']); ?>">
                        <p class="description"><?php esc_html_e('How many strings are sent in one API request. Use 3-5 for free/test keys, 10-25 for normal paid keys, higher only when quota is confirmed.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
                </table>
            </div>

            <div class="aitmt-fallback-card" id="aitmt-urls">
                <h2><?php esc_html_e('URLs and SEO', 'ait-multilingual-translate'); ?></h2>
                <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('URL mode', 'ait-multilingual-translate'); ?></th>
                    <td>
                        <label>
                            <input type="radio" name="aitmt_options[url_mode]" value="directory" <?php checked($options['url_mode'], 'directory'); ?>>
                            <?php esc_html_e('Directory URLs: /ka/about/', 'ait-multilingual-translate'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="aitmt_options[url_mode]" value="query" <?php checked($options['url_mode'], 'query'); ?>>
                            <?php esc_html_e('Query URLs: /about/?lang=ka', 'ait-multilingual-translate'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Directory URLs are better for SEO, but require WordPress permalinks and rewrite rules. Query URLs are simpler and useful while testing.', 'ait-multilingual-translate'); ?></p>
                        <br><br>
                        <label><input type="checkbox" name="aitmt_options[hide_default_language]" value="1" <?php checked($options['hide_default_language'], '1'); ?>> <?php esc_html_e('Keep the source language without a language prefix', 'ait-multilingual-translate'); ?></label>
                        <p class="description"><?php esc_html_e('When enabled, the original language stays on normal URLs while only translated languages use a language code.', 'ait-multilingual-translate'); ?></p>
                    </td>
                </tr>
            </table>
            </div>

            <div class="aitmt-fallback-card" id="aitmt-switcher">
                <h2><?php esc_html_e('Language Switcher', 'ait-multilingual-translate'); ?></h2>
                <div class="aitmt-fallback-shortcodes">
                    <p><?php esc_html_e('Shortcode:', 'ait-multilingual-translate'); ?></p>
                    <code>[aitmt_language_switcher]</code>
                </div>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e('Style', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <select name="aitmt_options[selector_style]">
                                <option value="dropdown" <?php selected($options['selector_style'], 'dropdown'); ?>><?php esc_html_e('Dropdown', 'ait-multilingual-translate'); ?></option>
                                <option value="list" <?php selected($options['selector_style'], 'list'); ?>><?php esc_html_e('List', 'ait-multilingual-translate'); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e('Dropdown is compact for headers. List is useful in footers, sidebars, and menu-like layouts.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Display parts', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <label><input type="checkbox" name="aitmt_options[selector_show_flags]" value="1" <?php checked($options['selector_show_flags'], '1'); ?>> <?php esc_html_e('Flags', 'ait-multilingual-translate'); ?></label><br>
                            <label><input type="checkbox" name="aitmt_options[selector_show_names]" value="1" <?php checked($options['selector_show_names'], '1'); ?>> <?php esc_html_e('Language names', 'ait-multilingual-translate'); ?></label><br>
                            <label><input type="checkbox" name="aitmt_options[selector_show_codes]" value="1" <?php checked($options['selector_show_codes'], '1'); ?>> <?php esc_html_e('Language codes', 'ait-multilingual-translate'); ?></label>
                            <p class="description"><?php esc_html_e('Choose what visitors see inside the selector, for example Georgian, KA, or both.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Automatic placement', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <label><input type="checkbox" name="aitmt_options[selector_header]" value="1" <?php checked($options['selector_header'], '1'); ?>> <?php esc_html_e('Try to show in header', 'ait-multilingual-translate'); ?></label><br>
                            <label><input type="checkbox" name="aitmt_options[selector_footer]" value="1" <?php checked($options['selector_footer'], '1'); ?>> <?php esc_html_e('Show in footer', 'ait-multilingual-translate'); ?></label>
                            <p class="description"><?php esc_html_e('Automatic placement is optional. You can also place the selector with a shortcode, widget, or theme/menu area.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="aitmt-selector-custom-css"><?php esc_html_e('Selector custom CSS', 'ait-multilingual-translate'); ?></label>
                        </th>
                        <td>
                            <textarea id="aitmt-selector-custom-css" class="large-text code" rows="8" name="aitmt_options[selector_custom_css]" placeholder=".aitmt-fallback-switcher-dropdown { border-radius: 4px; }"><?php echo esc_textarea($options['selector_custom_css']); ?></textarea>
                            <p class="description"><?php esc_html_e('Optional CSS loaded on the frontend only for styling the language selector.', 'ait-multilingual-translate'); ?></p>
                            <p class="description">
                                <?php esc_html_e('Useful classes:', 'ait-multilingual-translate'); ?>
                                <code>.aitmt-fallback-switcher-wrap</code>
                                <code>.aitmt-fallback-switcher-dropdown</code>
                                <code>.aitmt-fallback-switcher-list</code>
                                <code>.aitmt-fallback-switcher-link</code>
                                <code>.is-current</code>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Frontend editor', 'ait-multilingual-translate'); ?></th>
                        <td>
                            <label><input type="checkbox" name="aitmt_options[frontend_editor]" value="1" <?php checked($options['frontend_editor'], '1'); ?>> <?php esc_html_e('Allow administrators to edit translations from the frontend', 'ait-multilingual-translate'); ?></label>
                            <p class="description"><?php esc_html_e('When enabled, logged-in administrators see the AI Translate button in the admin bar on translated pages and can save manual corrections.', 'ait-multilingual-translate'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="aitmt-fallback-card" id="aitmt-support">
                <h2><?php esc_html_e('Support', 'ait-multilingual-translate'); ?></h2>
                <div class="aitmt-support-grid">
                    <div>
                        <h3><?php esc_html_e('Support AIT Multilingual Translate', 'ait-multilingual-translate'); ?></h3>
                        <p><?php esc_html_e('AIT Multilingual Translate is currently in Public Beta and includes temporary full feature access while the platform is actively tested and improved.', 'ait-multilingual-translate'); ?></p>
                        <p><?php esc_html_e('Users who support the project with a donation during the Public Beta period may receive a significant discount or special early-supporter offer for the future commercial release of AIT Multilingual Translate.', 'ait-multilingual-translate'); ?></p>
                        <p><?php esc_html_e('Your support helps improve the plugin, optimize AI providers, expand language support, and accelerate development.', 'ait-multilingual-translate'); ?></p>
                        <p class="aitmt-support-buttons">
                            <?php
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Button HTML is built from escaped attributes and translated text.
                            echo wp_kses_post(aitmt_fallback_support_development_button('button button-primary aitmt-support-donation-button'));
                            ?>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-feedback')); ?>"><?php esc_html_e('Send Feedback', 'ait-multilingual-translate'); ?></a>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=ait-multilingual-translate-report-bug')); ?>"><?php esc_html_e('Report Bug', 'ait-multilingual-translate'); ?></a>
                        </p>
                    </div>
                    <table class="form-table aitmt-advanced-only" role="presentation">
                        <tr>
                            <th scope="row"><label for="aitmt-donation-coffee-url"><?php esc_html_e('Buy Me a Coffee URL', 'ait-multilingual-translate'); ?></label></th>
                            <td><input id="aitmt-donation-coffee-url" class="regular-text" type="url" name="aitmt_options[donation_coffee_url]" value="<?php echo esc_attr($options['donation_coffee_url']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="aitmt-donation-paypal-url"><?php esc_html_e('PayPal donation URL', 'ait-multilingual-translate'); ?></label></th>
                            <td><input id="aitmt-donation-paypal-url" class="regular-text" type="url" name="aitmt_options[donation_paypal_url]" value="<?php echo esc_attr($options['donation_paypal_url']); ?>"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php submit_button(__('Save settings', 'ait-multilingual-translate')); ?>
        </form>

        <div class="aitmt-fallback-card" id="aitmt-scan">
            <h2><?php esc_html_e('String Scanner', 'ait-multilingual-translate'); ?></h2>
            <p><?php esc_html_e('Scan posts, pages, products, menus, widgets, taxonomy terms, SEO meta, and public custom fields. New strings are added to the queue once and are translated from there.', 'ait-multilingual-translate'); ?></p>

            <?php if (!empty($scan_result)) : ?>
                <div class="aitmt-debug-result <?php echo !empty($scan_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                    <p><strong><?php esc_html_e('Last scan:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($scan_result['created_at']) ? $scan_result['created_at'] : ''); ?></p>
                    <p><?php echo esc_html(isset($scan_result['message']) ? $scan_result['message'] : ''); ?></p>
                    <?php if (!empty($scan_result['targets'])) : ?>
                        <p><?php esc_html_e('Targets:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html($scan_result['targets']); ?></code></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="aitmt-mini-stats">
                <div class="aitmt-mini-stat">
                    <span><?php esc_html_e('Queued strings', 'ait-multilingual-translate'); ?></span>
                    <strong><?php echo esc_html((string) aitmt_fallback_queued_count()); ?></strong>
                </div>
                <div class="aitmt-mini-stat">
                    <span><?php esc_html_e('Saved translations', 'ait-multilingual-translate'); ?></span>
                    <strong><?php echo esc_html((string) aitmt_fallback_translation_count()); ?></strong>
                </div>
            </div>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aitmt_scan_site'); ?>
                <input type="hidden" name="action" value="aitmt_scan_site">
                <?php submit_button(__('Scan site strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
        </div>

        <div class="aitmt-fallback-card" id="aitmt-queue">
            <h2><?php esc_html_e('Translation Queue', 'ait-multilingual-translate'); ?></h2>
            <p><?php esc_html_e('Frontend pages now use saved translations only. New strings are collected here and translated in batches, so visitors do not wait for the AI provider.', 'ait-multilingual-translate'); ?></p>
            <table class="form-table aitmt-debug-table aitmt-advanced-only" role="presentation">
                <?php
                aitmt_fallback_render_debug_value('Queued strings', (string) aitmt_fallback_queued_count(), true);
                aitmt_fallback_render_debug_value('Saved translations', (string) aitmt_fallback_translation_count(), true);
                aitmt_fallback_render_debug_value('Batch size', (string) $options['max_segments_per_request'], true);
                aitmt_fallback_render_debug_value('Auto scan on save', '1' === $options['scan_on_save'] ? 'enabled' : 'disabled', '1' === $options['scan_on_save']);
                aitmt_fallback_render_debug_value('Background queue processing', '1' === $options['cron_enabled'] ? 'enabled' : 'disabled', null);
                ?>
            </table>

            <?php if (!empty($auto_scan_result)) : ?>
                <div class="aitmt-debug-result is-good">
                    <p><strong><?php esc_html_e('Last automatic scan:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($auto_scan_result['created_at']) ? $auto_scan_result['created_at'] : ''); ?></p>
                    <p><?php echo esc_html(sprintf('Post #%d: found %d string(s), queued %d new item(s).', isset($auto_scan_result['post_id']) ? (int) $auto_scan_result['post_id'] : 0, isset($auto_scan_result['strings']) ? (int) $auto_scan_result['strings'] : 0, isset($auto_scan_result['queued']) ? (int) $auto_scan_result['queued'] : 0)); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($queue_result)) : ?>
                <div class="aitmt-debug-result <?php echo !empty($queue_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                    <p><strong><?php esc_html_e('Last queue run:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($queue_result['created_at']) ? $queue_result['created_at'] : ''); ?></p>
                    <p><?php echo esc_html(isset($queue_result['message']) ? $queue_result['message'] : ''); ?></p>
                    <?php if (!empty($queue_result['routes'])) : ?>
                        <p><?php esc_html_e('Routes:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html($queue_result['routes']); ?></code></p>
                    <?php elseif (!empty($queue_result['source_language']) || !empty($queue_result['target_language'])) : ?>
                        <p><?php esc_html_e('Route:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((isset($queue_result['source_language']) ? $queue_result['source_language'] : '') . ' -> ' . (isset($queue_result['target_language']) ? $queue_result['target_language'] : '')); ?></code></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($cron_result)) : ?>
                <div class="aitmt-debug-result <?php echo !empty($cron_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                    <p><strong><?php esc_html_e('Last background run:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($cron_result['created_at']) ? $cron_result['created_at'] : ''); ?></p>
                    <p><?php echo esc_html(isset($cron_result['message']) ? $cron_result['message'] : ''); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                <?php wp_nonce_field('aitmt_process_queue'); ?>
                <input type="hidden" name="action" value="aitmt_process_queue">
                <?php submit_button(__('Process translation queue', 'ait-multilingual-translate'), 'primary', 'submit', false); ?>
            </form>
            <form class="aitmt-advanced-only" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block">
                <?php wp_nonce_field('aitmt_clear_queue'); ?>
                <input type="hidden" name="action" value="aitmt_clear_queue">
                <?php submit_button(__('Clear queued strings', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
            </form>
        </div>

        <div class="aitmt-fallback-card aitmt-advanced-only" id="aitmt-debug">
            <h2><?php esc_html_e('Debugger', 'ait-multilingual-translate'); ?></h2>
            <table class="form-table aitmt-debug-table" role="presentation">
                <?php
                aitmt_fallback_render_debug_value('Plugin mode', 'single-file queued engine', true);
                aitmt_fallback_render_debug_value('Plugin folder', aitmt_fallback_plugin_folder(), aitmt_fallback_is_update_safe_folder());
                aitmt_fallback_render_debug_value('Upload update safe', aitmt_fallback_is_update_safe_folder() ? 'yes' : 'no - install once into ait-multilingual-translate folder', aitmt_fallback_is_update_safe_folder());
                aitmt_fallback_render_debug_value('Legacy includes folder', AITMT_PLUGIN_DIR . 'includes', file_exists(AITMT_PLUGIN_DIR . 'includes/class-aitmt-activator.php') ? true : null);
                aitmt_fallback_render_debug_value('PHP version', PHP_VERSION, version_compare(PHP_VERSION, '7.0', '>='));
                aitmt_fallback_render_debug_value('DOMDocument', class_exists('DOMDocument') ? 'available' : 'missing', class_exists('DOMDocument'));
                aitmt_fallback_render_debug_value('WP HTTP API', function_exists('wp_remote_post') ? 'available' : 'missing', function_exists('wp_remote_post'));
                aitmt_fallback_render_debug_value('Source language', aitmt_fallback_source_language(), !empty(aitmt_fallback_source_language()));
                aitmt_fallback_render_debug_value('Enabled languages', implode(', ', aitmt_fallback_enabled_languages()), count(aitmt_fallback_enabled_languages()) > 1);
                aitmt_fallback_render_debug_value('Current language', aitmt_fallback_current_language(), true);
                aitmt_fallback_render_debug_value('Provider', aitmt_fallback_provider_label($active_provider), true);
                aitmt_fallback_render_debug_value('Provider key', aitmt_fallback_mask_secret($api_key) . ' (' . $api_key_source . ')', !empty($api_key));
                aitmt_fallback_render_debug_value('Provider model', aitmt_fallback_provider_model($active_provider), true);
                aitmt_fallback_render_debug_value('Translation Mode', aitmt_fallback_translation_mode_label(), true);
                aitmt_fallback_render_debug_value('Quality mode', aitmt_fallback_quality_mode_label(), true);
                aitmt_fallback_render_debug_value('Provider cooldown', aitmt_fallback_provider_cooldown_remaining($active_provider) ? aitmt_fallback_provider_cooldown_remaining($active_provider) . ' seconds' : 'none', !aitmt_fallback_provider_cooldown_remaining($active_provider));
                aitmt_fallback_render_debug_value('Provider daily characters', (string) aitmt_fallback_provider_chars_used($active_provider, gmdate('Ymd')), true);
                aitmt_fallback_render_debug_value('Provider monthly characters', (string) aitmt_fallback_provider_chars_used($active_provider, gmdate('Ym')), true);
                $provider_stats = aitmt_fallback_provider_stats_for($active_provider);
                aitmt_fallback_render_debug_value('Provider API requests', (string) absint($provider_stats['requests'] ?? 0), true);
                aitmt_fallback_render_debug_value('Estimated input tokens', (string) absint($provider_stats['input_tokens'] ?? 0), true);
                aitmt_fallback_render_debug_value('Estimated output tokens', (string) absint($provider_stats['output_tokens'] ?? 0), true);
                aitmt_fallback_render_debug_value('Estimated cost', number_format((float) ($provider_stats['estimated_cost'] ?? 0), 6), true);
                aitmt_fallback_render_debug_value('Translation memory cache hits', (string) absint($provider_stats['cache_hits'] ?? 0), true);
                aitmt_fallback_render_debug_value('Duplicate strings skipped', (string) absint($provider_stats['duplicate_skipped'] ?? 0), true);
                aitmt_fallback_render_debug_value('Translation table', aitmt_fallback_translation_table_exists() ? aitmt_fallback_translation_table() : 'missing', aitmt_fallback_translation_table_exists());
                aitmt_fallback_render_debug_value('Queued strings', (string) aitmt_fallback_queued_count(), true);
                aitmt_fallback_render_debug_value('Saved translations', (string) aitmt_fallback_translation_count(), true);
                aitmt_fallback_render_debug_value('Last error', $last_error ? $last_error : 'none', !$last_error);
                ?>
            </table>

            <?php if (!empty($debug_result)) : ?>
                <div class="aitmt-debug-result <?php echo !empty($debug_result['ok']) ? 'is-good' : 'is-bad'; ?>">
                    <p><strong><?php esc_html_e('Last provider test:', 'ait-multilingual-translate'); ?></strong> <?php echo esc_html(isset($debug_result['created_at']) ? $debug_result['created_at'] : ''); ?></p>
                    <p><?php echo esc_html(isset($debug_result['message']) ? $debug_result['message'] : ''); ?></p>
                    <?php if (!empty($debug_result['provider']) || !empty($debug_result['model'])) : ?>
                        <p><?php esc_html_e('Provider:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((isset($debug_result['provider']) ? $debug_result['provider'] : '') . ' / ' . (isset($debug_result['model']) ? $debug_result['model'] : '')); ?></code></p>
                    <?php endif; ?>
                    <?php if (!empty($debug_result['http_status'])) : ?>
                        <p><?php esc_html_e('HTTP status:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((string) $debug_result['http_status']); ?></code></p>
                    <?php endif; ?>
                    <?php if (!empty($debug_result['sample_source']) || !empty($debug_result['sample_translation'])) : ?>
                        <p><?php esc_html_e('Sample:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html(isset($debug_result['sample_source']) ? $debug_result['sample_source'] : ''); ?></code> &rarr; <code><?php echo esc_html(isset($debug_result['sample_translation']) ? $debug_result['sample_translation'] : ''); ?></code></p>
                    <?php endif; ?>
                    <?php if (!empty($debug_result['source_language']) || !empty($debug_result['target_language'])) : ?>
                        <p><?php esc_html_e('Route:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((isset($debug_result['source_language']) ? $debug_result['source_language'] : '') . ' -> ' . (isset($debug_result['target_language']) ? $debug_result['target_language'] : '')); ?></code></p>
                    <?php endif; ?>
                    <?php if (!empty($debug_result['duration'])) : ?>
                        <p><?php esc_html_e('Duration:', 'ait-multilingual-translate'); ?> <code><?php echo esc_html((string) $debug_result['duration']); ?>s</code></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="aitmt-debug-actions">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_debug_test'); ?>
                    <input type="hidden" name="action" value="aitmt_debug_test">
                    <?php submit_button(__('Test provider translation', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_debug_download'); ?>
                    <input type="hidden" name="action" value="aitmt_debug_download">
                    <?php submit_button(__('Download log file', 'ait-multilingual-translate'), 'secondary', 'submit', false, file_exists(aitmt_fallback_debug_log_path()) ? array() : array('disabled' => 'disabled')); ?>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_debug_clear'); ?>
                    <input type="hidden" name="action" value="aitmt_debug_clear">
                    <?php submit_button(__('Clear debug log', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('aitmt_clear_provider_cooldown'); ?>
                    <input type="hidden" name="action" value="aitmt_clear_provider_cooldown">
                    <?php submit_button(__('Clear provider cooldown', 'ait-multilingual-translate'), 'secondary', 'submit', false); ?>
                </form>
            </div>
        </div>

    </div>
    <?php
}

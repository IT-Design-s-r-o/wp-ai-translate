<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AITMT_Plugin
{
    private static $instance = null;

    public static function init(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        AITMT_Settings::init();
        AITMT_Router::init();
        AITMT_Switcher::init();
        AITMT_Frontend_Editor::init();
        AITMT_Output_Buffer::init();

        add_action('widgets_init', array($this, 'register_widgets'));
        add_filter('plugin_action_links_' . plugin_basename(AITMT_PLUGIN_FILE), array($this, 'settings_link'));
    }

    public function register_widgets() {
        if (!class_exists('WP_Widget')) {
            return;
        }

        require_once AITMT_PLUGIN_DIR . 'includes/class-aitmt-widget.php';
        register_widget('AITMT_Widget');
    }

    public function settings_link(array $links): array
    {
        $url = admin_url('admin.php?page=' . AITMT_PUBLIC_SLUG);
        array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'ait-multilingual-translate') . '</a>');

        return $links;
    }
}

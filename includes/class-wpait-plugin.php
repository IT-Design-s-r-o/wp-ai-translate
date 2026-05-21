<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Plugin
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
        WPAIT_Settings::init();
        WPAIT_Router::init();
        WPAIT_Switcher::init();
        WPAIT_Frontend_Editor::init();
        WPAIT_Output_Buffer::init();

        add_action('widgets_init', array($this, 'register_widgets'));
        add_filter('plugin_action_links_' . plugin_basename(WPAIT_PLUGIN_FILE), array($this, 'settings_link'));
    }

    public function register_widgets() {
        if (!class_exists('WP_Widget')) {
            return;
        }

        require_once WPAIT_PLUGIN_DIR . 'includes/class-wpait-widget.php';
        register_widget('WPAIT_Widget');
    }

    public function settings_link(array $links): array
    {
        $url = admin_url('admin.php?page=' . WPAIT_PUBLIC_SLUG);
        array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'ai-translate-woocommerce-elementor') . '</a>');

        return $links;
    }
}

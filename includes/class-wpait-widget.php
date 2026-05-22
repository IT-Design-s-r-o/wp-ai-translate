<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'wpait_language_switcher',
            __('WPAIT – AI Translate for WooCommerce & Elementor Switcher', 'wpait-ai-translate-for-woocommerce-elementor'),
            array('description' => __('Language switcher for WPAIT – AI Translate for WooCommerce & Elementor.', 'wpait-ai-translate-for-woocommerce-elementor'))
        );
    }

    public function widget($args, $instance)
    {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget wrappers are provided by WordPress/theme sidebars.
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget title wrappers are provided by WordPress/theme sidebars; title text is escaped.
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
        echo WPAIT_Switcher::render();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget wrappers are provided by WordPress/theme sidebars.
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? (string) $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'wpait-ai-translate-for-woocommerce-elementor'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        return array(
            'title' => isset($new_instance['title']) ? sanitize_text_field((string) $new_instance['title']) : '',
        );
    }
}

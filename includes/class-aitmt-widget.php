<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AITMT_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'aitmt_language_switcher',
            __('AIT Multilingual Translate Switcher', 'ait-multilingual-translate'),
            array('description' => __('Language switcher for AIT Multilingual Translate.', 'ait-multilingual-translate'))
        );
    }

    public function widget($args, $instance)
    {
        echo isset($args['before_widget']) ? wp_kses_post($args['before_widget']) : '';

        if (!empty($instance['title'])) {
            echo (isset($args['before_title']) ? wp_kses_post($args['before_title']) : '') . esc_html($instance['title']) . (isset($args['after_title']) ? wp_kses_post($args['after_title']) : '');
        }

        echo wp_kses(AITMT_Switcher::render(), AITMT_Switcher::allowed_html());
        echo isset($args['after_widget']) ? wp_kses_post($args['after_widget']) : '';
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? (string) $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'ait-multilingual-translate'); ?></label>
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

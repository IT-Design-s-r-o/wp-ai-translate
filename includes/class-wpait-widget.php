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
            __('WP AI Translate Switcher', 'wp-ai-translate'),
            array('description' => __('Language switcher for WP AI Translate.', 'wp-ai-translate'))
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        echo WPAIT_Switcher::render();
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? (string) $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'wp-ai-translate'); ?></label>
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

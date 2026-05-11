<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Settings
{
    const OPTION = 'wpait_options';

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_init', array(__CLASS__, 'maybe_flush_rewrites'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }

    public static function defaults(): array
    {
        return array(
            'source_language' => '',
            'enabled_languages' => array(),
            'url_mode' => 'directory',
            'hide_default_language' => '1',
            'provider' => 'openai',
            'openai_api_key' => '',
            'openai_model' => 'gpt-5.2',
            'auto_translate' => '1',
            'draft_mode' => '0',
            'translate_attributes' => '1',
            'max_segments_per_request' => 40,
            'selector_style' => 'dropdown',
            'selector_show_flags' => '0',
            'selector_show_names' => '1',
            'selector_show_codes' => '0',
            'selector_header' => '0',
            'selector_footer' => '0',
            'frontend_editor' => '1',
        );
    }

    public static function options(): array
    {
        $saved = get_option(self::OPTION, array());

        if (!is_array($saved)) {
            $saved = array();
        }

        $options = wp_parse_args($saved, self::defaults());

        if (!is_array($options['enabled_languages'])) {
            $options['enabled_languages'] = array_filter(array_map('trim', explode(',', (string) $options['enabled_languages'])));
        }

        return $options;
    }

    public static function get(string $key, $default = null)
    {
        $options = self::options();

        return array_key_exists($key, $options) ? $options[$key] : $default;
    }

    public static function source_language(): string
    {
        $configured = self::get('source_language', '');

        if (!empty($configured)) {
            return WPAIT_Languages::normalize_code((string) $configured);
        }

        return WPAIT_Languages::site_default();
    }

    public static function openai_api_key(): string
    {
        if (defined('WPAIT_OPENAI_API_KEY') && WPAIT_OPENAI_API_KEY) {
            return (string) WPAIT_OPENAI_API_KEY;
        }

        return (string) self::get('openai_api_key', '');
    }

    public static function admin_menu() {
        add_options_page(
            __('WP AI Translate', 'wp-ai-translate'),
            __('WP AI Translate', 'wp-ai-translate'),
            'manage_options',
            'wp-ai-translate',
            array(__CLASS__, 'render_page')
        );
    }

    public static function register_settings() {
        register_setting(
            'wpait_settings',
            self::OPTION,
            array(
                'type' => 'array',
                'sanitize_callback' => array(__CLASS__, 'sanitize'),
                'default' => self::defaults(),
            )
        );
    }

    public static function sanitize($input): array
    {
        $old = self::options();
        $input = is_array($input) ? $input : array();
        $all_codes = array_keys(WPAIT_Languages::all());

        $source_language = isset($input['source_language']) ? WPAIT_Languages::normalize_code((string) $input['source_language']) : '';
        if (!in_array($source_language, $all_codes, true)) {
            $source_language = '';
        }
        $actual_source_language = $source_language ?: WPAIT_Languages::site_default();

        $enabled_languages = array();
        if (isset($input['enabled_languages']) && is_array($input['enabled_languages'])) {
            foreach ($input['enabled_languages'] as $language) {
                $language = WPAIT_Languages::normalize_code((string) $language);
                if (in_array($language, $all_codes, true) && $language !== $actual_source_language) {
                    $enabled_languages[] = $language;
                }
            }
        }
        $enabled_languages = array_values(array_unique($enabled_languages));

        $url_mode = isset($input['url_mode']) && in_array($input['url_mode'], array('directory', 'query'), true)
            ? $input['url_mode']
            : 'directory';

        $selector_style = isset($input['selector_style']) && in_array($input['selector_style'], array('dropdown', 'list'), true)
            ? $input['selector_style']
            : 'dropdown';

        $max_segments = isset($input['max_segments_per_request']) ? absint($input['max_segments_per_request']) : 40;
        $max_segments = min(100, max(1, $max_segments));

        $sanitized = array(
            'source_language' => $source_language,
            'enabled_languages' => $enabled_languages,
            'url_mode' => $url_mode,
            'hide_default_language' => empty($input['hide_default_language']) ? '0' : '1',
            'provider' => 'openai',
            'openai_api_key' => isset($input['openai_api_key']) ? sanitize_text_field((string) $input['openai_api_key']) : '',
            'openai_model' => isset($input['openai_model']) ? sanitize_text_field((string) $input['openai_model']) : 'gpt-5.2',
            'auto_translate' => empty($input['auto_translate']) ? '0' : '1',
            'draft_mode' => empty($input['draft_mode']) ? '0' : '1',
            'translate_attributes' => empty($input['translate_attributes']) ? '0' : '1',
            'max_segments_per_request' => $max_segments,
            'selector_style' => $selector_style,
            'selector_show_flags' => empty($input['selector_show_flags']) ? '0' : '1',
            'selector_show_names' => empty($input['selector_show_names']) ? '0' : '1',
            'selector_show_codes' => empty($input['selector_show_codes']) ? '0' : '1',
            'selector_header' => empty($input['selector_header']) ? '0' : '1',
            'selector_footer' => empty($input['selector_footer']) ? '0' : '1',
            'frontend_editor' => empty($input['frontend_editor']) ? '0' : '1',
        );

        if ($old['enabled_languages'] !== $sanitized['enabled_languages'] || $old['url_mode'] !== $sanitized['url_mode'] || $old['hide_default_language'] !== $sanitized['hide_default_language']) {
            update_option('wpait_rewrite_needs_flush', 1, false);
        }

        return $sanitized;
    }

    public static function maybe_flush_rewrites() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (get_option('wpait_rewrite_needs_flush')) {
            flush_rewrite_rules(false);
            delete_option('wpait_rewrite_needs_flush');
        }
    }

    public static function enqueue_admin_assets(string $hook) {
        if ('settings_page_wp-ai-translate' !== $hook) {
            return;
        }

        wp_enqueue_style('wpait-admin', WPAIT_PLUGIN_URL . 'assets/css/admin.css', array(), WPAIT_VERSION);
        wp_enqueue_script('wpait-admin', WPAIT_PLUGIN_URL . 'assets/js/admin.js', array(), WPAIT_VERSION, true);
    }

    public static function render_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $options = self::options();
        $languages = WPAIT_Languages::all();
        $source = self::source_language();
        ?>
        <div class="wrap wpait-admin">
            <h1><?php esc_html_e('WP AI Translate', 'wp-ai-translate'); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields('wpait_settings'); ?>

                <div class="wpait-admin-grid">
                    <section class="wpait-panel">
                        <h2><?php esc_html_e('Languages', 'wp-ai-translate'); ?></h2>

                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">
                                    <label for="wpait-source-language"><?php esc_html_e('Source language', 'wp-ai-translate'); ?></label>
                                </th>
                                <td>
                                    <select id="wpait-source-language" name="<?php echo esc_attr(self::OPTION); ?>[source_language]">
                                        <option value=""><?php echo esc_html(sprintf(__('Auto: site language (%s)', 'wp-ai-translate'), strtoupper($source))); ?></option>
                                        <?php foreach ($languages as $code => $label) : ?>
                                            <option value="<?php echo esc_attr($code); ?>" <?php selected($options['source_language'], $code); ?>>
                                                <?php echo esc_html($label . ' (' . strtoupper($code) . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php esc_html_e('Leave on Auto to follow the WordPress site language.', 'wp-ai-translate'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Target languages', 'wp-ai-translate'); ?></th>
                                <td>
                                    <input type="search" class="wpait-language-search" placeholder="<?php esc_attr_e('Search languages...', 'wp-ai-translate'); ?>">
                                    <div class="wpait-language-list">
                                        <?php foreach ($languages as $code => $label) : ?>
                                            <label class="wpait-language-option">
                                                <input
                                                    type="checkbox"
                                                    name="<?php echo esc_attr(self::OPTION); ?>[enabled_languages][]"
                                                    value="<?php echo esc_attr($code); ?>"
                                                    <?php checked(in_array($code, (array) $options['enabled_languages'], true)); ?>
                                                    <?php disabled($code, $source); ?>
                                                >
                                                <span><?php echo esc_html($label); ?></span>
                                                <code><?php echo esc_html(strtoupper($code)); ?></code>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <section class="wpait-panel">
                        <h2><?php esc_html_e('AI Provider', 'wp-ai-translate'); ?></h2>

                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php esc_html_e('Provider', 'wp-ai-translate'); ?></th>
                                <td>
                                    <select name="<?php echo esc_attr(self::OPTION); ?>[provider]">
                                        <option value="openai" selected><?php esc_html_e('OpenAI / ChatGPT', 'wp-ai-translate'); ?></option>
                                    </select>
                                    <p class="description"><?php esc_html_e('Claude, Gemini, and Grok can be added as providers after this first version.', 'wp-ai-translate'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="wpait-openai-api-key"><?php esc_html_e('OpenAI API key', 'wp-ai-translate'); ?></label>
                                </th>
                                <td>
                                    <input id="wpait-openai-api-key" type="password" class="regular-text" autocomplete="off" name="<?php echo esc_attr(self::OPTION); ?>[openai_api_key]" value="<?php echo esc_attr($options['openai_api_key']); ?>">
                                    <p class="description"><?php esc_html_e('You can also define WPAIT_OPENAI_API_KEY in wp-config.php.', 'wp-ai-translate'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="wpait-openai-model"><?php esc_html_e('OpenAI model', 'wp-ai-translate'); ?></label>
                                </th>
                                <td>
                                    <input id="wpait-openai-model" type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION); ?>[openai_model]" value="<?php echo esc_attr($options['openai_model']); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Translation behavior', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[auto_translate]" value="1" <?php checked($options['auto_translate'], '1'); ?>>
                                        <?php esc_html_e('Automatically translate missing strings on page view', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[draft_mode]" value="1" <?php checked($options['draft_mode'], '1'); ?>>
                                        <?php esc_html_e('Save new AI translations as drafts', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[translate_attributes]" value="1" <?php checked($options['translate_attributes'], '1'); ?>>
                                        <?php esc_html_e('Translate alt, title, placeholder, aria-label, and SEO meta attributes', 'wp-ai-translate'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="wpait-max-segments"><?php esc_html_e('Batch size', 'wp-ai-translate'); ?></label>
                                </th>
                                <td>
                                    <input id="wpait-max-segments" type="number" min="1" max="100" name="<?php echo esc_attr(self::OPTION); ?>[max_segments_per_request]" value="<?php echo esc_attr((string) $options['max_segments_per_request']); ?>">
                                    <p class="description"><?php esc_html_e('Limits how many new strings are sent to AI from one page render.', 'wp-ai-translate'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <section class="wpait-panel">
                        <h2><?php esc_html_e('URLs and SEO', 'wp-ai-translate'); ?></h2>

                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php esc_html_e('Language URL mode', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="radio" name="<?php echo esc_attr(self::OPTION); ?>[url_mode]" value="directory" <?php checked($options['url_mode'], 'directory'); ?>>
                                        <?php esc_html_e('Directory URLs: /en/about/', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="radio" name="<?php echo esc_attr(self::OPTION); ?>[url_mode]" value="query" <?php checked($options['url_mode'], 'query'); ?>>
                                        <?php esc_html_e('Query URLs: /about/?lang=en', 'wp-ai-translate'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Default language URL', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[hide_default_language]" value="1" <?php checked($options['hide_default_language'], '1'); ?>>
                                        <?php esc_html_e('Keep the source language without a language prefix', 'wp-ai-translate'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <section class="wpait-panel">
                        <h2><?php esc_html_e('Language Switcher', 'wp-ai-translate'); ?></h2>

                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php esc_html_e('Style', 'wp-ai-translate'); ?></th>
                                <td>
                                    <select name="<?php echo esc_attr(self::OPTION); ?>[selector_style]">
                                        <option value="dropdown" <?php selected($options['selector_style'], 'dropdown'); ?>><?php esc_html_e('Dropdown', 'wp-ai-translate'); ?></option>
                                        <option value="list" <?php selected($options['selector_style'], 'list'); ?>><?php esc_html_e('List', 'wp-ai-translate'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Display parts', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[selector_show_flags]" value="1" <?php checked($options['selector_show_flags'], '1'); ?>>
                                        <?php esc_html_e('Flags', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[selector_show_names]" value="1" <?php checked($options['selector_show_names'], '1'); ?>>
                                        <?php esc_html_e('Language names', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[selector_show_codes]" value="1" <?php checked($options['selector_show_codes'], '1'); ?>>
                                        <?php esc_html_e('Language codes', 'wp-ai-translate'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Automatic placement', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[selector_header]" value="1" <?php checked($options['selector_header'], '1'); ?>>
                                        <?php esc_html_e('Try to show in header via wp_body_open', 'wp-ai-translate'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[selector_footer]" value="1" <?php checked($options['selector_footer'], '1'); ?>>
                                        <?php esc_html_e('Show in footer', 'wp-ai-translate'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('Shortcodes: [wp_ai_translate_switcher] or [ai_language_switcher].', 'wp-ai-translate'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Frontend editor', 'wp-ai-translate'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[frontend_editor]" value="1" <?php checked($options['frontend_editor'], '1'); ?>>
                                        <?php esc_html_e('Allow administrators to edit translated text from the frontend', 'wp-ai-translate'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </section>
                </div>

                <?php submit_button(__('Save settings', 'wp-ai-translate')); ?>
            </form>
        </div>
        <?php
    }
}

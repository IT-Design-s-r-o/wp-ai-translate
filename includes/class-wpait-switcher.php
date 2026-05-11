<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Switcher
{
    public static function init() {
        add_shortcode('wp_ai_translate_switcher', array(__CLASS__, 'shortcode'));
        add_shortcode('ai_language_switcher', array(__CLASS__, 'shortcode'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        add_action('wp_body_open', array(__CLASS__, 'render_header_switcher'));
        add_action('wp_footer', array(__CLASS__, 'render_footer_switcher'));
    }

    public static function enqueue_assets() {
        wp_enqueue_style('wpait-frontend', WPAIT_PLUGIN_URL . 'assets/css/frontend.css', array(), WPAIT_VERSION);
    }

    public static function shortcode($atts = array()): string
    {
        $atts = shortcode_atts(
            array(
                'style' => WPAIT_Settings::get('selector_style', 'dropdown'),
            ),
            is_array($atts) ? $atts : array(),
            'wp_ai_translate_switcher'
        );

        return self::render((string) $atts['style']);
    }

    public static function render_header_switcher() {
        if ('1' !== WPAIT_Settings::get('selector_header', '0')) {
            return;
        }

        echo '<div class="wpait-switcher-wrap wpait-switcher-header">';
        echo self::render();
        echo '</div>';
    }

    public static function render_footer_switcher() {
        if ('1' !== WPAIT_Settings::get('selector_footer', '0')) {
            return;
        }

        echo '<div class="wpait-switcher-wrap wpait-switcher-footer">';
        echo self::render();
        echo '</div>';
    }

    public static function render(string $style = ''): string
    {
        $languages = WPAIT_Languages::enabled_with_source();

        if (count($languages) < 2) {
            return '';
        }

        $style = $style ?: (string) WPAIT_Settings::get('selector_style', 'dropdown');
        $current = WPAIT_Router::current_language();

        if ('list' === $style) {
            return self::render_list($languages, $current);
        }

        return self::render_dropdown($languages, $current);
    }

    private static function render_dropdown(array $languages, string $current): string
    {
        $id = 'wpait-switcher-' . wp_rand(1000, 999999);
        $output = '<label class="screen-reader-text" for="' . esc_attr($id) . '">' . esc_html__('Select language', 'wp-ai-translate') . '</label>';
        $output .= '<select id="' . esc_attr($id) . '" class="wpait-switcher wpait-switcher-dropdown" onchange="if(this.value){window.location.href=this.value;}">';

        foreach ($languages as $language) {
            $output .= sprintf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_url(WPAIT_Router::language_url($language)),
                selected($current, $language, false),
                esc_html(self::language_text($language))
            );
        }

        $output .= '</select>';

        return $output;
    }

    private static function render_list(array $languages, string $current): string
    {
        $output = '<nav class="wpait-switcher wpait-switcher-list" aria-label="' . esc_attr__('Language switcher', 'wp-ai-translate') . '">';

        foreach ($languages as $language) {
            $classes = array('wpait-switcher-link');
            if ($language === $current) {
                $classes[] = 'is-current';
            }

            $output .= sprintf(
                '<a class="%1$s" href="%2$s" hreflang="%3$s">%4$s</a>',
                esc_attr(implode(' ', $classes)),
                esc_url(WPAIT_Router::language_url($language)),
                esc_attr($language),
                wp_kses_post(self::language_html($language))
            );
        }

        $output .= '</nav>';

        return $output;
    }

    private static function language_text(string $language): string
    {
        $parts = array();

        if ('1' === WPAIT_Settings::get('selector_show_flags', '0')) {
            $parts[] = self::flag_entity($language);
        }

        if ('1' === WPAIT_Settings::get('selector_show_names', '1')) {
            $parts[] = WPAIT_Languages::label($language);
        }

        if ('1' === WPAIT_Settings::get('selector_show_codes', '0')) {
            $parts[] = strtoupper($language);
        }

        if (empty($parts)) {
            $parts[] = strtoupper($language);
        }

        return trim(implode(' ', $parts));
    }

    private static function language_html(string $language): string
    {
        $parts = array();

        if ('1' === WPAIT_Settings::get('selector_show_flags', '0')) {
            $parts[] = '<span class="wpait-flag" aria-hidden="true">' . self::flag_entity($language) . '</span>';
        }

        if ('1' === WPAIT_Settings::get('selector_show_names', '1')) {
            $parts[] = '<span class="wpait-language-name">' . esc_html(WPAIT_Languages::label($language)) . '</span>';
        }

        if ('1' === WPAIT_Settings::get('selector_show_codes', '0')) {
            $parts[] = '<span class="wpait-language-code">' . esc_html(strtoupper($language)) . '</span>';
        }

        if (empty($parts)) {
            $parts[] = '<span class="wpait-language-code">' . esc_html(strtoupper($language)) . '</span>';
        }

        return implode(' ', $parts);
    }

    private static function flag_entity(string $language): string
    {
        $country = self::language_country($language);
        $entity = '';

        foreach (str_split(strtoupper($country)) as $letter) {
            $entity .= '&#' . (127397 + ord($letter)) . ';';
        }

        return html_entity_decode($entity, ENT_QUOTES, 'UTF-8');
    }

    private static function language_country(string $language): string
    {
        $map = array(
            'af' => 'ZA',
            'ar' => 'SA',
            'az' => 'AZ',
            'be' => 'BY',
            'bg' => 'BG',
            'bn' => 'BD',
            'bs' => 'BA',
            'ca' => 'ES',
            'cs' => 'CZ',
            'cy' => 'GB',
            'da' => 'DK',
            'de' => 'DE',
            'el' => 'GR',
            'en' => 'US',
            'es' => 'ES',
            'et' => 'EE',
            'eu' => 'ES',
            'fa' => 'IR',
            'fi' => 'FI',
            'fr' => 'FR',
            'ga' => 'IE',
            'gd' => 'GB',
            'gl' => 'ES',
            'he' => 'IL',
            'hi' => 'IN',
            'hr' => 'HR',
            'hu' => 'HU',
            'hy' => 'AM',
            'id' => 'ID',
            'is' => 'IS',
            'it' => 'IT',
            'ja' => 'JP',
            'ka' => 'GE',
            'kk' => 'KZ',
            'ko' => 'KR',
            'ky' => 'KG',
            'lt' => 'LT',
            'lv' => 'LV',
            'mk' => 'MK',
            'ms' => 'MY',
            'nb' => 'NO',
            'nl' => 'NL',
            'pl' => 'PL',
            'pt' => 'PT',
            'ro' => 'RO',
            'ru' => 'RU',
            'sk' => 'SK',
            'sl' => 'SI',
            'sq' => 'AL',
            'sr' => 'RS',
            'sv' => 'SE',
            'th' => 'TH',
            'tr' => 'TR',
            'uk' => 'UA',
            'ur' => 'PK',
            'uz' => 'UZ',
            'vi' => 'VN',
            'zh' => 'CN',
        );

        return $map[WPAIT_Languages::normalize_code($language)] ?? strtoupper(substr($language, 0, 2));
    }
}

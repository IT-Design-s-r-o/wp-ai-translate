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

        echo '<div class="wpait-switcher-wrap wpait-switcher-header notranslate" data-wpait-no-translate="1" translate="no">';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
        echo self::render();
        echo '</div>';
    }

    public static function render_footer_switcher() {
        if ('1' !== WPAIT_Settings::get('selector_footer', '0')) {
            return;
        }

        echo '<div class="wpait-switcher-wrap wpait-switcher-footer notranslate" data-wpait-no-translate="1" translate="no">';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Switcher HTML is generated internally with escaped URLs, labels, and attributes.
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
        $output = '<details class="wpait-switcher wpait-switcher-dropdown wpait-custom-dropdown notranslate" data-wpait-no-translate="1" translate="no">';
        $output .= '<summary class="wpait-switcher-dropdown-control" aria-label="' . esc_attr__('Select language', 'ai-translate-woocommerce-elementor') . '">';
        $output .= self::language_html($current);
        $output .= '</summary>';
        $output .= '<div id="' . esc_attr($id) . '" class="wpait-switcher-dropdown-menu" role="listbox">';

        foreach ($languages as $language) {
            $classes = array('wpait-switcher-dropdown-link');
            if ($language === $current) {
                $classes[] = 'is-current';
            }

            $output .= sprintf(
                '<a class="%1$s" href="%2$s" hreflang="%3$s" lang="%3$s" data-wpait-no-translate="1" translate="no" %4$s aria-label="%5$s">%6$s</a>',
                esc_attr(implode(' ', $classes)),
                esc_url(WPAIT_Router::language_url($language)),
                esc_attr($language),
                $language === $current ? 'aria-current="true"' : '',
                esc_attr(self::language_accessible_label($language)),
                self::language_html($language)
            );
        }

        $output .= '</div></details>';

        return $output;
    }

    private static function render_list(array $languages, string $current): string
    {
        $output = '<nav class="wpait-switcher wpait-switcher-list notranslate" aria-label="' . esc_attr__('Language switcher', 'ai-translate-woocommerce-elementor') . '" data-wpait-no-translate="1" translate="no">';

        foreach ($languages as $language) {
            $classes = array('wpait-switcher-link');
            if ($language === $current) {
                $classes[] = 'is-current';
            }

            $output .= sprintf(
                '<a class="%1$s" href="%2$s" hreflang="%3$s" lang="%3$s" data-wpait-no-translate="1" translate="no" %4$s aria-label="%5$s">%6$s</a>',
                esc_attr(implode(' ', $classes)),
                esc_url(WPAIT_Router::language_url($language)),
                esc_attr($language),
                $language === $current ? 'aria-current="true"' : '',
                esc_attr(self::language_accessible_label($language)),
                self::language_html($language)
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
            $parts[] = self::flag_html($language);
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

    private static function language_accessible_label(string $language): string
    {
        return WPAIT_Languages::label($language) . ' (' . strtoupper($language) . ')';
    }

    private static function flag_html(string $language): string
    {
        $url = self::flag_url($language);

        if ('' === $url) {
            return '<span class="wpait-flag" aria-hidden="true"></span>';
        }

        return '<img class="wpait-flag" src="' . esc_url($url) . '" alt="" aria-hidden="true" loading="lazy" decoding="async">';
    }

    private static function flag_url(string $language): string
    {
        $country = strtolower(self::language_country($language));
        $relative = 'assets/flags/flag-icons/4x3/' . sanitize_file_name($country) . '.svg';

        if (!file_exists(WPAIT_PLUGIN_DIR . $relative)) {
            return '';
        }

        return WPAIT_PLUGIN_URL . $relative;
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
            'am' => 'ET',
            'ar' => 'SA',
            'az' => 'AZ',
            'be' => 'BY',
            'bg' => 'BG',
            'bn' => 'BD',
            'bs' => 'BA',
            'ca' => 'ES',
            'ceb' => 'PH',
            'co' => 'FR',
            'cs' => 'CZ',
            'cy' => 'GB',
            'da' => 'DK',
            'de' => 'DE',
            'el' => 'GR',
            'en' => 'US',
            'es' => 'ES',
            'eo' => 'UN',
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
            'ht' => 'HT',
            'haw' => 'US',
            'hmn' => 'CN',
            'ha' => 'NG',
            'ig' => 'NG',
            'id' => 'ID',
            'is' => 'IS',
            'it' => 'IT',
            'ja' => 'JP',
            'jv' => 'ID',
            'ka' => 'GE',
            'km' => 'KH',
            'kk' => 'KZ',
            'ko' => 'KR',
            'kn' => 'IN',
            'ku' => 'IQ',
            'ky' => 'KG',
            'la' => 'VA',
            'lo' => 'LA',
            'lb' => 'LU',
            'lt' => 'LT',
            'lv' => 'LV',
            'mk' => 'MK',
            'mg' => 'MG',
            'ml' => 'IN',
            'mn' => 'MN',
            'mr' => 'IN',
            'ms' => 'MY',
            'mt' => 'MT',
            'mi' => 'NZ',
            'my' => 'MM',
            'ne' => 'NP',
            'no' => 'NO',
            'ny' => 'MW',
            'ps' => 'AF',
            'nb' => 'NO',
            'nl' => 'NL',
            'pl' => 'PL',
            'pt' => 'PT',
            'pa' => 'IN',
            'ro' => 'RO',
            'ru' => 'RU',
            'sd' => 'PK',
            'si' => 'LK',
            'sk' => 'SK',
            'sl' => 'SI',
            'sm' => 'WS',
            'sn' => 'ZW',
            'so' => 'SO',
            'sq' => 'AL',
            'sr' => 'RS',
            'st' => 'LS',
            'su' => 'ID',
            'sv' => 'SE',
            'sw' => 'TZ',
            'ta' => 'IN',
            'te' => 'IN',
            'tg' => 'TJ',
            'th' => 'TH',
            'tl' => 'PH',
            'tr' => 'TR',
            'uk' => 'UA',
            'ug' => 'CN',
            'ur' => 'PK',
            'uz' => 'UZ',
            'vi' => 'VN',
            'xh' => 'ZA',
            'yi' => 'IL',
            'yo' => 'NG',
            'zh' => 'CN',
            'zu' => 'ZA',
        );

        return $map[WPAIT_Languages::normalize_code($language)] ?? strtoupper(substr($language, 0, 2));
    }
}

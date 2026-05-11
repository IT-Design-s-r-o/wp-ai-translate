<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_Router
{
    public static function init() {
        add_action('init', array(__CLASS__, 'register_rewrites'));
        add_filter('query_vars', array(__CLASS__, 'query_vars'));
        add_filter('request', array(__CLASS__, 'filter_request'));
        add_action('wp_head', array(__CLASS__, 'render_hreflang'), 2);
    }

    public static function register_rewrites() {
        add_rewrite_tag('%wpait_lang%', '([^&]+)');

        if ('directory' !== WPAIT_Settings::get('url_mode', 'directory')) {
            return;
        }

        $languages = WPAIT_Languages::enabled_with_source();
        if (empty($languages)) {
            return;
        }

        $pattern = implode('|', array_map('preg_quote', $languages));
        add_rewrite_rule('^(' . $pattern . ')/?$', 'index.php?wpait_lang=$matches[1]', 'top');
        add_rewrite_rule('^(' . $pattern . ')/(.+?)/?$', 'index.php?wpait_lang=$matches[1]&pagename=$matches[2]', 'top');
    }

    public static function query_vars(array $vars): array
    {
        $vars[] = 'wpait_lang';

        return $vars;
    }

    public static function filter_request(array $query_vars): array
    {
        if ('directory' !== WPAIT_Settings::get('url_mode', 'directory')) {
            return $query_vars;
        }

        $language = self::language_from_path();
        $enabled = WPAIT_Languages::enabled_with_source();

        if (!$language || !in_array($language, $enabled, true)) {
            return $query_vars;
        }

        $path = self::path_without_language();
        $query_vars['wpait_lang'] = $language;

        if ('' === $path) {
            return $query_vars;
        }

        $post_types = get_post_types(array('public' => true), 'names');
        $post = get_page_by_path($path, OBJECT, $post_types);

        if ($post instanceof WP_Post) {
            unset($query_vars['pagename'], $query_vars['name'], $query_vars['page_id']);

            if ('page' === $post->post_type) {
                $query_vars['pagename'] = $path;
            } else {
                $query_vars['name'] = $post->post_name;
                $query_vars['post_type'] = $post->post_type;
            }

            return $query_vars;
        }

        if (isset($query_vars['pagename'])) {
            $parts = explode('/', (string) $query_vars['pagename']);
            if (isset($parts[0]) && WPAIT_Languages::normalize_code($parts[0]) === $language) {
                array_shift($parts);
                $query_vars['pagename'] = implode('/', $parts);
            }
        }

        return $query_vars;
    }

    public static function current_language(): string
    {
        $source = WPAIT_Settings::source_language();
        $enabled = WPAIT_Languages::enabled_with_source();

        $query_var = get_query_var('wpait_lang');
        if ($query_var) {
            $language = WPAIT_Languages::normalize_code((string) $query_var);
            if (in_array($language, $enabled, true)) {
                return $language;
            }
        }

        if ('query' === WPAIT_Settings::get('url_mode', 'directory') && isset($_GET['lang'])) {
            $language = WPAIT_Languages::normalize_code(wp_unslash((string) $_GET['lang']));
            if (in_array($language, $enabled, true)) {
                return $language;
            }
        }

        if ('directory' === WPAIT_Settings::get('url_mode', 'directory')) {
            $language = self::language_from_path();
            if ($language && in_array($language, $enabled, true)) {
                return $language;
            }
        }

        return $source;
    }

    public static function language_from_path(): string
    {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '/';
        $path = (string) parse_url($request_uri, PHP_URL_PATH);
        $home_path = (string) parse_url(home_url('/'), PHP_URL_PATH);
        $relative = self::strip_home_path($path, $home_path);
        $segments = array_values(array_filter(explode('/', trim($relative, '/'))));

        return isset($segments[0]) ? WPAIT_Languages::normalize_code($segments[0]) : '';
    }

    public static function path_without_language(): string
    {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '/';
        $path = (string) parse_url($request_uri, PHP_URL_PATH);
        $home_path = (string) parse_url(home_url('/'), PHP_URL_PATH);
        $relative = self::strip_home_path($path, $home_path);
        $segments = array_values(array_filter(explode('/', trim($relative, '/'))));
        $enabled = WPAIT_Languages::enabled_with_source();

        if (isset($segments[0]) && in_array(WPAIT_Languages::normalize_code($segments[0]), $enabled, true)) {
            array_shift($segments);
        }

        return implode('/', $segments);
    }

    public static function language_url(string $language): string
    {
        $language = WPAIT_Languages::normalize_code($language);
        $source = WPAIT_Settings::source_language();
        $url_mode = WPAIT_Settings::get('url_mode', 'directory');
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '/';
        $path = (string) parse_url($request_uri, PHP_URL_PATH);
        $query = (string) parse_url($request_uri, PHP_URL_QUERY);
        $query_args = array();

        if ('' !== $query) {
            wp_parse_str($query, $query_args);
        }

        unset($query_args['lang']);

        if ('query' === $url_mode) {
            $base_url = home_url(self::strip_home_path($path, (string) parse_url(home_url('/'), PHP_URL_PATH)));
            if ($language !== $source || '1' !== WPAIT_Settings::get('hide_default_language', '1')) {
                $query_args['lang'] = $language;
            }

            return add_query_arg($query_args, $base_url);
        }

        $home_path = (string) parse_url(home_url('/'), PHP_URL_PATH);
        $relative = trim(self::strip_home_path($path, $home_path), '/');
        $segments = array_values(array_filter(explode('/', $relative)));
        $all_languages = WPAIT_Languages::enabled_with_source();

        if (isset($segments[0]) && in_array(WPAIT_Languages::normalize_code($segments[0]), $all_languages, true)) {
            array_shift($segments);
        }

        if ($language !== $source || '1' !== WPAIT_Settings::get('hide_default_language', '1')) {
            array_unshift($segments, $language);
        }

        $new_path = implode('/', $segments);
        $url = home_url($new_path ? trailingslashit($new_path) : '/');

        return add_query_arg($query_args, $url);
    }

    public static function render_hreflang() {
        if (is_admin() || is_feed()) {
            return;
        }

        foreach (WPAIT_Languages::enabled_with_source() as $language) {
            printf(
                '<link rel="alternate" hreflang="%1$s" href="%2$s" />' . "\n",
                esc_attr($language),
                esc_url(self::language_url($language))
            );
        }
    }

    private static function strip_home_path(string $path, string $home_path): string
    {
        $home_path = '/' . trim($home_path, '/');

        if ('/' === $home_path) {
            return '/' . ltrim($path, '/');
        }

        if (0 === strpos($path, $home_path)) {
            return '/' . ltrim(substr($path, strlen($home_path)), '/');
        }

        return '/' . ltrim($path, '/');
    }
}

<?php

if (!defined('ABSPATH')) {
    exit;
}

final class AITMT_Output_Buffer
{
    const SKIP_TAGS = array(
        'script',
        'style',
        'noscript',
        'code',
        'pre',
        'textarea',
        'svg',
        'canvas',
        'iframe',
        'object',
    );

    private static $buffer_level = 0;

    public static function init() {
        add_action('template_redirect', array(__CLASS__, 'start'), 0);
        add_action('shutdown', array(__CLASS__, 'flush'), 0);
    }

    public static function start() {
        if (!self::should_translate_request()) {
            return;
        }

        if (self::$buffer_level > 0) {
            return;
        }

        self::$buffer_level = ob_get_level() + 1;
        ob_start(array(__CLASS__, 'translate_html'));
    }

    public static function flush() {
        if (self::$buffer_level <= 0) {
            return;
        }

        while (ob_get_level() >= self::$buffer_level) {
            ob_end_flush();
        }

        self::$buffer_level = 0;
    }

    public static function translate_html(string $html): string
    {
        if ('' === trim($html) || !class_exists('DOMDocument')) {
            return $html;
        }

        $target_language = AITMT_Router::current_language();
        $source_language = AITMT_Settings::source_language();

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
        self::collect_nodes($dom, $text_nodes, $attribute_nodes);

        $segments = array();
        foreach ($text_nodes as $item) {
            $segments[$item['hash']] = $item['source'];
        }

        if ('1' === AITMT_Settings::get('translate_attributes', '1')) {
            foreach ($attribute_nodes as $item) {
                $segments[$item['hash']] = $item['source'];
            }
        }

        if (empty($segments)) {
            return $html;
        }

        $translations = AITMT_Translator::translate_segments($segments, $target_language, 'html');

        if (empty($translations)) {
            return $html;
        }

        foreach ($text_nodes as $item) {
            if (!isset($translations[$item['hash']])) {
                continue;
            }

            self::replace_text_node($dom, $item['node'], $item['original'], $item['source'], $translations[$item['hash']], $item['hash']);
        }

        if ('1' === AITMT_Settings::get('translate_attributes', '1')) {
            foreach ($attribute_nodes as $item) {
                if (!isset($translations[$item['hash']])) {
                    continue;
                }

                $item['node']->setAttribute($item['attribute'], $translations[$item['hash']]);
            }
        }

        return $dom->saveHTML();
    }

    private static function should_translate_request(): bool
    {
        if (is_admin() || wp_doing_ajax() || is_feed() || is_robots() || is_trackback()) {
            return false;
        }

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return false;
        }

        if (AITMT_Router::current_language() === AITMT_Settings::source_language()) {
            return false;
        }

        return true;
    }

    private static function collect_nodes(DOMNode $node, array &$text_nodes, array &$attribute_nodes) {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);

            if (in_array($tag, self::SKIP_TAGS, true)) {
                return;
            }

            $class = ' ' . $node->getAttribute('class') . ' ';
            if (
                'wpadminbar' === $node->getAttribute('id')
                || false !== strpos($class, ' aitmt-switcher')
                || false !== strpos($class, ' aitmt-fallback-switcher')
                || false !== strpos($class, ' aitmt-menu-language-item')
                || false !== strpos($class, ' aitmt-menu-current-language')
                || false !== strpos($class, ' aitmt-menu-switcher-dropdown-toggle')
                || false !== strpos($class, ' notranslate ')
            ) {
                return;
            }

            if ($node->hasAttribute('data-aitmt-no-translate') || $node->hasAttribute('data-aitmt-language-switcher') || $node->hasAttribute('translate') && 'no' === strtolower($node->getAttribute('translate'))) {
                return;
            }

            self::collect_attributes($node, $attribute_nodes);
        }

        if (XML_TEXT_NODE === $node->nodeType) {
            $original = $node->nodeValue ?? '';
            $source = AITMT_Translations::normalize_text((string) $original);

            if (AITMT_Translator::is_translatable_text($source)) {
                $text_nodes[] = array(
                    'node' => $node,
                    'original' => (string) $original,
                    'source' => $source,
                    'hash' => AITMT_Translations::hash($source),
                );
            }
        }

        if (!$node->hasChildNodes()) {
            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::collect_nodes($child, $text_nodes, $attribute_nodes);
        }
    }

    private static function collect_attributes(DOMElement $node, array &$attribute_nodes) {
        $attributes = array('alt', 'title', 'placeholder', 'aria-label');
        $tag = strtolower($node->tagName);

        if ('input' === $tag || 'button' === $tag) {
            $attributes[] = 'value';
        }

        if ('meta' === $tag && $node->hasAttribute('content')) {
            $name = strtolower($node->getAttribute('name') ?: $node->getAttribute('property'));
            if (in_array($name, array('description', 'og:title', 'og:description', 'twitter:title', 'twitter:description'), true)) {
                $attributes[] = 'content';
            }
        }

        foreach (array_unique($attributes) as $attribute) {
            if (!$node->hasAttribute($attribute)) {
                continue;
            }

            $source = AITMT_Translations::normalize_text($node->getAttribute($attribute));
            if (!AITMT_Translator::is_translatable_text($source)) {
                continue;
            }

            $attribute_nodes[] = array(
                'node' => $node,
                'attribute' => $attribute,
                'source' => $source,
                'hash' => AITMT_Translations::hash($source),
            );
        }
    }

    private static function replace_text_node(DOMDocument $dom, DOMNode $node, string $original, string $source, string $translation, string $hash) {
        if (!$node->parentNode) {
            return;
        }

        $translation = self::apply_original_spacing($original, $translation);

        if (AITMT_Frontend_Editor::enabled() && self::can_wrap_for_editor($node)) {
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

    private static function can_wrap_for_editor(DOMNode $node): bool
    {
        $current = $node->parentNode;

        while ($current instanceof DOMElement) {
            $tag = strtolower($current->tagName);
            if (in_array($tag, array('head', 'title', 'option', 'select'), true)) {
                return false;
            }

            $current = $current->parentNode;
        }

        return true;
    }

    private static function apply_original_spacing(string $original, string $translation): string
    {
        preg_match('/^\s*/u', $original, $leading);
        preg_match('/\s*$/u', $original, $trailing);

        return ($leading[0] ?? '') . trim($translation) . ($trailing[0] ?? '');
    }
}

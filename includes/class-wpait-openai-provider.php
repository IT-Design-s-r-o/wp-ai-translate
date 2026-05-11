<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WPAIT_OpenAI_Provider
{
    public function translate_batch(array $segments, string $source_language, string $target_language)
    {
        $api_key = WPAIT_Settings::openai_api_key();

        if (empty($api_key)) {
            return new WP_Error('wpait_missing_openai_key', __('OpenAI API key is missing.', 'wp-ai-translate'));
        }

        if (empty($segments)) {
            return array();
        }

        $model = (string) WPAIT_Settings::get('openai_model', 'gpt-5.2');
        $source_name = WPAIT_Languages::label($source_language);
        $target_name = WPAIT_Languages::label($target_language);

        $items = array();
        foreach ($segments as $hash => $text) {
            $items[] = array(
                'id' => $hash,
                'text' => $text,
            );
        }

        $schema = array(
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => array(
                'translations' => array(
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => array(
                            'id' => array('type' => 'string'),
                            'text' => array('type' => 'string'),
                        ),
                        'required' => array('id', 'text'),
                    ),
                ),
            ),
            'required' => array('translations'),
        );

        $body = array(
            'model' => $model,
            'instructions' => sprintf(
                'You are a professional website localization engine. Translate from %s (%s) to %s (%s). Preserve placeholders, brand names, numbers, emails, URLs, shortcodes, HTML entities, and inline formatting. Return only valid JSON that matches the provided schema.',
                $source_name,
                strtoupper($source_language),
                $target_name,
                strtoupper($target_language)
            ),
            'input' => wp_json_encode(array('segments' => $items), JSON_UNESCAPED_UNICODE),
            'text' => array(
                'format' => array(
                    'type' => 'json_schema',
                    'name' => 'wp_ai_translate_batch',
                    'schema' => $schema,
                    'strict' => true,
                ),
            ),
        );

        $response = wp_remote_post(
            'https://api.openai.com/v1/responses',
            array(
                'timeout' => 60,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode($body),
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $raw_body = (string) wp_remote_retrieve_body($response);
        $data = json_decode($raw_body, true);

        if ($status < 200 || $status >= 300) {
            $message = $data['error']['message'] ?? __('OpenAI request failed.', 'wp-ai-translate');

            return new WP_Error('wpait_openai_error', $message, array('status' => $status));
        }

        $output_text = $this->extract_output_text(is_array($data) ? $data : array());
        $decoded = json_decode($output_text, true);

        if (!is_array($decoded)) {
            $decoded = json_decode($this->strip_json_fence($output_text), true);
        }

        if (!is_array($decoded) || empty($decoded['translations']) || !is_array($decoded['translations'])) {
            return new WP_Error('wpait_openai_parse_error', __('OpenAI returned an unexpected translation payload.', 'wp-ai-translate'));
        }

        $translations = array();
        foreach ($decoded['translations'] as $item) {
            if (empty($item['id']) || !array_key_exists('text', $item)) {
                continue;
            }

            $translations[(string) $item['id']] = (string) $item['text'];
        }

        return $translations;
    }

    private function extract_output_text(array $data): string
    {
        if (isset($data['output_text']) && is_string($data['output_text'])) {
            return $data['output_text'];
        }

        if (empty($data['output']) || !is_array($data['output'])) {
            return '';
        }

        foreach ($data['output'] as $output) {
            if (($output['type'] ?? '') !== 'message' || empty($output['content']) || !is_array($output['content'])) {
                continue;
            }

            foreach ($output['content'] as $content) {
                if (isset($content['text']) && is_string($content['text'])) {
                    return $content['text'];
                }
            }
        }

        return '';
    }

    private function strip_json_fence(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', (string) $text);

        return trim((string) $text);
    }
}


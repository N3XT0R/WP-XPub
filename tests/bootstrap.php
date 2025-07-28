<?php
require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value) { return $value; }
}
if (!function_exists('do_action')) {
    function do_action($tag, ...$args) {}
}
if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($text) { return strip_tags($text); }
}
if (!function_exists('wp_remote_post')) {
    function wp_remote_post($url, $args = []) { return ['response' => ['code' => 200]]; }
}
if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) { return false; }
}
if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response) { return $response['response']['code'] ?? 500; }
}
if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) { return ''; }
}
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data) { return json_encode($data); }
}
if (!function_exists('__')) {
    function __($text, $domain = 'default') { return $text; }
}
if (!function_exists('add_action')) { function add_action() {} }
if (!function_exists('add_meta_box')) { function add_meta_box() {} }
if (!function_exists('update_option')) { function update_option() {} }
if (!function_exists('get_post_meta')) { function get_post_meta() { return ''; } }
if (!function_exists('sanitize_textarea_field')) { function sanitize_textarea_field($text) { return $text; } }
if (!function_exists('wp_verify_nonce')) { function wp_verify_nonce() { return true; } }
if (!function_exists('current_user_can')) { function current_user_can() { return true; } }
if (!function_exists('update_post_meta')) { function update_post_meta() {} }
if (!function_exists('is_admin')) { function is_admin() { return true; } }
if (!function_exists('wp_get_post_tags')) { function wp_get_post_tags() { return []; } }
if (!function_exists('wp_reset_postdata')) { function wp_reset_postdata() {} }
if (!function_exists('wp_kses_post')) { function wp_kses_post($content) { return $content; } }

if (!function_exists('get_permalink')) {
    function get_permalink($post): string { return ''; }
}
if (!function_exists('setup_postdata')) { function setup_postdata($post) {} }

if (!class_exists('WP_Post')) {
    class WP_Post
    {
        public int $ID;
        public string $post_title = '';
        public string $post_content = '';
        public string $post_excerpt = '';
        public string $post_status = '';

        public function __construct(array $data = [])
        {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}

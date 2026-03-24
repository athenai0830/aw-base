<?php
// AW-Base LLMs.txt output
if ( ! defined( 'ABSPATH' ) ) exit;

// Catch requests to random_blog_url/llms.txt and output our custom file
function awbase_llms_txt_endpoint() {
    $options = get_option('awbase_settings', awbase_get_default_settings());
    if ( $options['llms_txt_enable'] !== '1' ) return;

    // Check if the request is for llms.txt
    if ( $_SERVER['REQUEST_URI'] === '/llms.txt' ) {
        // We ensure no WP HTML headers are processed
        header('Content-Type: text/plain; charset=utf-8');
        echo $options['llms_txt_content'];
        exit;
    }
}
// We hook very early
add_action('parse_request', 'awbase_llms_txt_endpoint', 0);

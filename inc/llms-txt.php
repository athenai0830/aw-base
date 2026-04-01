<?php
// AW-Base LLMs.txt output
if ( ! defined( 'ABSPATH' ) ) exit;

// Register rewrite rule for llms.txt
function awbase_llms_txt_rewrite() {
    add_rewrite_rule( '^llms\.txt$', 'index.php?awbase_llms_txt=1', 'top' );
}
add_action( 'init', 'awbase_llms_txt_rewrite' );

// Register query var
function awbase_llms_txt_query_var( $vars ) {
    $vars[] = 'awbase_llms_txt';
    return $vars;
}
add_filter( 'query_vars', 'awbase_llms_txt_query_var' );

// Output llms.txt
function awbase_llms_txt_output() {
    if ( get_query_var( 'awbase_llms_txt' ) !== '1' ) return;

    $options = get_option( 'awbase_settings', awbase_get_default_settings() );
    if ( empty( $options['llms_txt_enable'] ) || $options['llms_txt_enable'] !== '1' ) {
        status_header( 404 );
        exit;
    }

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo $options['llms_txt_content'];
    exit;
}
add_action( 'template_redirect', 'awbase_llms_txt_output' );

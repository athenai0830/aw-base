<?php
// AW-Base LLMs.txt / LLMs-full.txt / ai-index.md output
if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------------------------------------------------------------------
// Rewrite rules
// ---------------------------------------------------------------------------
function awbase_llms_txt_rewrite() {
    add_rewrite_rule( '^llms\.txt$',      'index.php?awbase_llms_txt=1',      'top' );
    add_rewrite_rule( '^llms-full\.txt$', 'index.php?awbase_llms_full_txt=1', 'top' );
    add_rewrite_rule( '^ai-index\.md$',   'index.php?awbase_ai_index_md=1',   'top' );
}
add_action( 'init', 'awbase_llms_txt_rewrite' );

// テーマ有効化時にrewriteルールを自動フラッシュ（新規インストール時に手動操作不要）
function awbase_llms_txt_flush_rewrite() {
    awbase_llms_txt_rewrite();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'awbase_llms_txt_flush_rewrite' );

// ---------------------------------------------------------------------------
// Query vars
// ---------------------------------------------------------------------------
function awbase_llms_txt_query_var( $vars ) {
    $vars[] = 'awbase_llms_txt';
    $vars[] = 'awbase_llms_full_txt';
    $vars[] = 'awbase_ai_index_md';
    return $vars;
}
add_filter( 'query_vars', 'awbase_llms_txt_query_var' );

// ---------------------------------------------------------------------------
// File access counter（オプションテーブルで軽量管理）
// ---------------------------------------------------------------------------
function awbase_increment_file_access( $file_key ) {
    $counts = get_option( 'awbase_file_access_counts', [] );
    $counts[ $file_key ] = ( isset( $counts[ $file_key ] ) ? (int) $counts[ $file_key ] : 0 ) + 1;
    update_option( 'awbase_file_access_counts', $counts, false );
}

// ---------------------------------------------------------------------------
// Output handlers
// ---------------------------------------------------------------------------
function awbase_llms_txt_output() {
    $options = get_option( 'awbase_settings', awbase_get_default_settings() );

    // llms.txt
    if ( get_query_var( 'awbase_llms_txt' ) === '1' ) {
        if ( empty( $options['llms_txt_enable'] ) || $options['llms_txt_enable'] !== '1' ) {
            status_header( 404 ); exit;
        }
        awbase_increment_file_access( 'llms_txt' );
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo $options['llms_txt_content'];
        exit;
    }

    // llms-full.txt
    if ( get_query_var( 'awbase_llms_full_txt' ) === '1' ) {
        if ( empty( $options['llms_full_txt_enable'] ) || $options['llms_full_txt_enable'] !== '1' ) {
            status_header( 404 ); exit;
        }
        awbase_increment_file_access( 'llms_full_txt' );
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo $options['llms_full_txt_content'];
        exit;
    }

    // ai-index.md
    if ( get_query_var( 'awbase_ai_index_md' ) === '1' ) {
        if ( empty( $options['ai_index_md_enable'] ) || $options['ai_index_md_enable'] !== '1' ) {
            status_header( 404 ); exit;
        }
        awbase_increment_file_access( 'ai_index_md' );
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo awbase_generate_ai_index_content();
        exit;
    }
}
add_action( 'template_redirect', 'awbase_llms_txt_output' );

// ---------------------------------------------------------------------------
// ai-index.md コンテンツ動的生成
// ---------------------------------------------------------------------------
function awbase_generate_ai_index_content() {
    $site_name = get_bloginfo( 'name' );
    $site_desc = get_bloginfo( 'description' );
    $site_url  = home_url( '/' );

    $out  = "# サイトタイトル: {$site_name}\n\n";
    $out .= "## TOP\n\n";
    $out .= "### {$site_name}\n";
    $out .= "- Source URL (primary reference): {$site_url}\n";
    $out .= "- Key concept: {$site_desc}\n";
    $out .= "- Definition: {$site_desc}\n";
    $out .= "- Cite when:\n";
    $out .= "  - サイト概要（site overview）\n";
    $out .= "  - 運営者情報（author profile）\n";
    $out .= "- Original term: {$site_name}\n\n";
    $out .= "## 記事\n\n";

    $posts = get_posts( [
        'post_type'      => [ 'post', 'page' ],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [ [ 'key' => 'awbase_ai_index_enable', 'value' => '1' ] ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    if ( $posts ) {
        foreach ( $posts as $post ) {
            $title       = get_the_title( $post );
            $url         = get_permalink( $post );
            $key_concept = get_post_meta( $post->ID, 'awbase_ai_index_key_concept', true );
            $definition  = get_post_meta( $post->ID, 'awbase_ai_index_definition', true );
            $cite_when   = get_post_meta( $post->ID, 'awbase_ai_index_cite_when', true );
            $orig_term   = get_post_meta( $post->ID, 'awbase_ai_index_original_term', true );

            $out .= "### {$title}\n";
            $out .= "- Source URL (primary reference): {$url}\n";
            if ( $key_concept ) $out .= "- Key concept: {$key_concept}\n";
            if ( $definition )  $out .= "- Definition: {$definition}\n";
            if ( $cite_when ) {
                $out .= "- Cite when:\n";
                foreach ( array_filter( array_map( 'trim', explode( "\n", $cite_when ) ) ) as $line ) {
                    $out .= "  - {$line}\n";
                }
            }
            if ( $orig_term ) $out .= "- Original term: {$orig_term}\n";
            $out .= "\n";
        }
    }

    return $out;
}

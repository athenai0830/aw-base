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
// File access counter（サービス別・ファイル別）
// 構造: [ 'llms_txt' => [ 'ChatGPT' => 3, 'Claude' => 1 ], ... ]
// ---------------------------------------------------------------------------
function awbase_increment_file_access( $file_key ) {
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $service    = 'Unknown';
    if ( $user_agent && function_exists( 'awbase_get_bot_map' ) ) {
        foreach ( awbase_get_bot_map() as $bot => $info ) {
            if ( stripos( $user_agent, $bot ) !== false ) {
                $service = $info[0];
                break;
            }
        }
    }

    $counts = get_option( 'awbase_file_access_counts', [] );
    if ( ! isset( $counts[ $file_key ] ) )             $counts[ $file_key ] = [];
    if ( ! isset( $counts[ $file_key ][ $service ] ) ) $counts[ $file_key ][ $service ] = 0;
    $counts[ $file_key ][ $service ]++;
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
        echo awbase_generate_llms_full_content();
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

    $llms_full_url = home_url( '/llms-full.txt' );

    $out  = "# サイトタイトル: {$site_name}\n\n";
    $out .= "> Full content available at: {$llms_full_url}\n";
    $out .= "> For complete article text, fetch the above URL directly.\n\n";
    // TOPセクション — 固定フロントページのメタボックスデータを優先使用
    $front_page_id  = (int) get_option( 'page_on_front' );
    $top_key        = $front_page_id ? get_post_meta( $front_page_id, 'awbase_ai_index_key_concept',   true ) : '';
    $top_definition = $front_page_id ? get_post_meta( $front_page_id, 'awbase_ai_index_definition',    true ) : '';
    $top_cite       = $front_page_id ? get_post_meta( $front_page_id, 'awbase_ai_index_cite_when',     true ) : '';
    $top_orig       = $front_page_id ? get_post_meta( $front_page_id, 'awbase_ai_index_original_term', true ) : '';

    $out .= "## TOP\n\n";
    $out .= "### {$site_name}\n";
    $out .= "- Source URL (primary reference): {$site_url}\n";
    $out .= "- Key concept: " . ( $top_key        ?: $site_desc   ) . "\n";
    $out .= "- Definition: "  . ( $top_definition  ?: $site_desc   ) . "\n";
    if ( $top_cite ) {
        $out .= "- Cite when:\n";
        foreach ( array_filter( array_map( 'trim', explode( "\n", $top_cite ) ) ) as $line ) {
            $out .= "  - {$line}\n";
        }
    } else {
        $out .= "- Cite when:\n";
        $out .= "  - サイト概要（site overview）\n";
        $out .= "  - 運営者情報（author profile）\n";
    }
    $out .= "- Original term: " . ( $top_orig ?: $site_name ) . "\n\n";
    $out .= "## 記事\n\n";

    // フロントページは記事セクションから除外
    $exclude_ids = $front_page_id ? [ $front_page_id ] : [];

    $posts = get_posts( [
        'post_type'      => [ 'post', 'page' ],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__not_in'   => $exclude_ids,
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

// ---------------------------------------------------------------------------
// llms-full.txt キャッシュ再生成（クリア → 即座に再生成してキャッシュを常に維持）
// 投稿保存・削除・設定更新時にバックグラウンドで実行。
// ボットアクセス時にキャッシュが存在しない状態を防ぐ。
// ---------------------------------------------------------------------------
function awbase_refresh_llms_full_cache( $post_id = 0 ) {
    // 自動保存・リビジョンはスキップ
    if ( $post_id && ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) ) return;
    delete_transient( 'awbase_llms_full_txt' );
    awbase_generate_llms_full_content(); // 即座に再生成してキャッシュ保存
}
// priority 20 — 投稿・設定が完全に保存されてから実行
add_action( 'save_post',    'awbase_refresh_llms_full_cache', 20 );
add_action( 'deleted_post', 'awbase_refresh_llms_full_cache', 20 );
add_action( 'update_option_awbase_settings', 'awbase_refresh_llms_full_cache', 20 );

// テーマ有効化時に初回キャッシュを生成
add_action( 'after_switch_theme', 'awbase_generate_llms_full_content' );

// ---------------------------------------------------------------------------
// llms-full.txt 動的生成（公開済み投稿・固定ページの本文をそのまま出力）
// ---------------------------------------------------------------------------
function awbase_generate_llms_full_content() {
    $cached = get_transient( 'awbase_llms_full_txt' );
    if ( $cached !== false ) return $cached;

    $site_name = get_bloginfo( 'name' );
    $site_desc = get_bloginfo( 'description' );
    $site_url  = home_url( '/' );

    $out  = "# {$site_name}\n\n";
    $out .= "> {$site_desc}\n\n";
    $out .= "Source: {$site_url}\n\n";
    $out .= "---\n\n";

    $posts = get_posts( [
        'post_type'      => [ 'post', 'page' ],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    foreach ( $posts as $post ) {
        setup_postdata( $post );

        $title   = get_the_title( $post );
        $url     = get_permalink( $post );
        $date    = get_the_date( 'Y-m-d', $post );
        // apply_filters('the_content') はブロックレンダリングが重いため使用しない
        $content = wp_strip_all_tags( $post->post_content );
        // 連続する空行を1行に圧縮
        $content = preg_replace( '/\n{3,}/', "\n\n", trim( $content ) );

        $out .= "## {$title}\n\n";
        $out .= "URL: {$url}\n";
        $out .= "Published: {$date}\n\n";
        $out .= $content . "\n\n";
        $out .= "---\n\n";
    }

    wp_reset_postdata();

    // 24時間キャッシュ（投稿保存・削除時に自動クリアされるため安全網として）
    set_transient( 'awbase_llms_full_txt', $out, DAY_IN_SECONDS );

    return $out;
}

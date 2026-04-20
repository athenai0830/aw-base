<?php
// AW-Base Performance Optimizations
if ( ! defined( 'ABSPATH' ) ) exit;

$options = awbase_get_settings();

// ============================================================
// 1. Disable unused image sizes
// ============================================================
if ( $options['disable_image_sizes'] == '1' ) {
    function awbase_disable_image_sizes( $sizes ) {
        unset( $sizes['thumbnail'], $sizes['medium'], $sizes['medium_large'],
               $sizes['large'], $sizes['1536x1536'], $sizes['2048x2048'] );
        return $sizes;
    }
    add_filter( 'intermediate_image_sizes_advanced', 'awbase_disable_image_sizes' );
    add_filter( 'big_image_size_threshold', '__return_false' );
}

// ============================================================
// 2. Limit reCAPTCHA loading
// ============================================================
if ( $options['recaptcha_limit'] == '1' ) {
    add_action( 'wp_enqueue_scripts', function() {
        // お問い合わせページ以外では reCAPTCHA を全種ブロック
        // is_page('contact') に加えて日本語スラッグにも対応
        $contact_slugs = apply_filters( 'awbase_recaptcha_pages', [ 'contact', 'お問い合わせ', 'contact-form' ] );
        foreach ( $contact_slugs as $slug ) {
            if ( is_page( $slug ) ) return;
        }
        // CF7 専用ハンドル
        wp_deregister_script( 'google-recaptcha' );
        wp_deregister_script( 'wpcf7-recaptcha' );
        // src URL に "recaptcha" を含む全スクリプトを除去
        // （Google Site Kit / standalone reCAPTCHA 等に対応）
        global $wp_scripts;
        if ( ! empty( $wp_scripts->registered ) ) {
            foreach ( $wp_scripts->registered as $handle => $script ) {
                if ( ! empty( $script->src ) && strpos( $script->src, 'recaptcha' ) !== false ) {
                    wp_deregister_script( $handle );
                }
            }
        }
    }, 999 );
}

// ============================================================
// 3. Lazy Load
// ============================================================
if ( $options['lazy_load'] !== '1' ) {
    add_filter( 'wp_lazy_loading_enabled', '__return_false' );
} else {
    if ( $options['lazy_load_thumb_exclude'] == '1' ) {
        add_filter( 'wp_omit_loading_attr_threshold', function() { return 1; } );
    }
}

// ============================================================
// 4. CSS Minify（インラインCSS）
// ============================================================
if ( $options['css_minify'] == '1' ) {
    add_filter( 'awbase_style_output', function( $css ) {
        $css = preg_replace( '/\/\*.*?\*\//s', '', $css );
        $css = preg_replace( '/\s*([:,;{}])\s*/', '$1', $css );
        $css = preg_replace( '/\s+/', ' ', $css );
        return trim( $css );
    } );
}

// ============================================================
// 5. Sitemap toggle
// ============================================================
if ( $options['sitemap_enable'] !== '1' ) {
    add_filter( 'wp_sitemaps_enabled', '__return_false' );
}

// ============================================================
// 6. WordPress 標準ブロートの除去
// ============================================================
if ( $options['remove_wp_bloat'] == '1' ) {
    // Emoji
    remove_action( 'wp_head',         'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail',          'wp_staticize_emoji_for_email' );
    add_filter( 'emoji_svg_url', '__return_false' );

    // 不要な <head> タグ
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'rest_output_link_wp_head' );

    // wp-embed
    add_action( 'wp_enqueue_scripts', function() {
        wp_deregister_script( 'wp-embed' );
    } );
}

// ============================================================
// 7. Dashicons をフロントエンドで除去（非ログイン時）
// ============================================================
if ( $options['remove_dashicons'] == '1' ) {
    add_action( 'wp_enqueue_scripts', function() {
        if ( ! is_user_logged_in() ) {
            wp_deregister_style( 'dashicons' );
        }
    }, 100 );
}

// ============================================================
// 8. Gutenberg ブロックライブラリ CSS の除去
// ============================================================
if ( $options['remove_block_css'] == '1' ) {
    add_action( 'wp_enqueue_scripts', function() {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'global-styles' );
        wp_dequeue_style( 'classic-theme-styles' );
    }, 100 );
}

// ============================================================
// 9. jQuery をフロントエンドから除去（非ログイン時）
//    ※ jQuery が必要なプラグインを使用している場合はオフに
// ============================================================
if ( $options['remove_jquery'] == '1' ) {
    add_action( 'wp_enqueue_scripts', function() {
        if ( ! is_user_logged_in() ) {
            wp_deregister_script( 'jquery' );
            wp_deregister_script( 'jquery-core' );
            wp_deregister_script( 'jquery-migrate' );
        }
    }, 100 );
}

// ============================================================
// 10. Font Awesome を非同期ロード（render-blocking 解消）
// ============================================================
add_filter( 'style_loader_tag', function( $tag, $handle, $href ) {
    if ( $handle !== 'font-awesome' ) return $tag;
    return '<link rel="preload" href="' . esc_url( $href ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
         . '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>' . "\n";
}, 10, 3 );

// ============================================================
// 11. awbase-script に defer 付与
// ============================================================
add_filter( 'script_loader_tag', function( $tag, $handle ) {
    if ( $handle !== 'awbase-script' ) return $tag;
    return str_replace( '<script ', '<script defer ', $tag );
}, 10, 2 );

// ============================================================
// 12. Preconnect / DNS prefetch
// ============================================================
add_action( 'wp_head', function() {
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">' . "\n";
}, 1 );

// ============================================================
// 13. Font Awesome webfont を preload（FOIT 解消）
// ============================================================
add_action( 'wp_head', function() {
    $base = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/';
    echo '<link rel="preload" href="' . $base . 'fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . $base . 'fa-regular-400.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . $base . 'fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
}, 2 );

// ============================================================
// 14. LCP 対策: FV 背景画像・FV ロゴ画像（TOPページ）
// ============================================================
add_action( 'wp_head', function() {
    if ( ! ( is_front_page() || is_home() ) ) return;
    if ( get_awbase_option( 'show_fv' ) !== '1' ) return;
    $fv_bg   = get_awbase_option( 'fv_bg_image' );
    $fv_logo = get_awbase_option( 'fv_logo_image' );
    if ( ! empty( $fv_bg ) ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $fv_bg ) . '" fetchpriority="high">' . "\n";
    }
    if ( ! empty( $fv_logo ) ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $fv_logo ) . '" fetchpriority="high">' . "\n";
    }
}, 1 );

// ============================================================
// 15. LCP 対策: 投稿のアイキャッチ画像
// ============================================================
add_action( 'wp_head', function() {
    if ( ! is_singular( 'post' ) ) return;
    $thumb_id = get_post_thumbnail_id( get_the_ID() );
    if ( ! $thumb_id ) return;
    $src = wp_get_attachment_image_src( $thumb_id, 'full' );
    if ( $src ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $src[0] ) . '" fetchpriority="high">' . "\n";
    }
}, 1 );

// ============================================================
// 16. LCP 対策: アーカイブ・ホーム（最初のカードのサムネイル + ロゴ）
// ============================================================
add_action( 'wp_head', function() {
    if ( is_singular() ) return;
    // 最初の投稿サムネイルをpreload（アーカイブLCP対策）
    $posts = $GLOBALS['wp_query']->posts ?? [];
    if ( ! empty( $posts ) ) {
        $first_id = ! empty( $posts[0]->ID ) ? (int) $posts[0]->ID : 0;
        if ( $first_id ) {
            $thumb_id = get_post_thumbnail_id( $first_id );
            if ( $thumb_id ) {
                $sizes = awbase_get_thumb_sizes();
                [ $w, $h ] = $sizes['awbase-card'];
                $urls = awbase_get_thumb_urls( $thumb_id, $w, $h );
                if ( ! empty( $urls['webp'] ) ) {
                    echo '<link rel="preload" as="image" href="' . esc_url( $urls['webp'] ) . '" fetchpriority="high">' . "\n";
                } elseif ( ! empty( $urls['jpeg'] ) ) {
                    echo '<link rel="preload" as="image" href="' . esc_url( $urls['jpeg'] ) . '" fetchpriority="high">' . "\n";
                } else {
                    $orig = wp_get_attachment_image_src( $thumb_id, 'full' );
                    if ( $orig ) {
                        echo '<link rel="preload" as="image" href="' . esc_url( $orig[0] ) . '" fetchpriority="high">' . "\n";
                    }
                }
            }
        }
    }
    // ロゴをpreload
    $logo = get_awbase_option( 'logo_image' );
    if ( ! empty( $logo ) ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $logo ) . '">' . "\n";
    }
}, 1 );

// ============================================================
// 17. 先頭カードカウンター（entry-card 用）
// ============================================================
function awbase_is_first_card() {
    static $count = 0;
    return $count++ === 0;
}

// ============================================================
// 18. テーブル横スクロールラッパー（PHP側でプリレンダリング）
//     JS DOM 操作による CLS を解消する
// ============================================================
function awbase_wrap_content_tables( $content ) {
    if ( strpos( $content, '<table' ) === false ) return $content;

    // wp-block-table figure を一時退避（Gutenberg テーブルはラップしない）
    $protected = [];
    $content = preg_replace_callback(
        '/<figure[^>]*class="[^"]*wp-block-table[^"]*"[^>]*>.*?<\/figure>/si',
        function ( $m ) use ( &$protected ) {
            $key              = '<!-- AWBASE_PROTECTED_TABLE_' . count( $protected ) . ' -->';
            $protected[ $key ] = $m[0];
            return $key;
        },
        $content
    );

    // 残ったベアテーブルをラップ（ヒントもプリレンダリング）
    $content = preg_replace_callback(
        '/<table[\s>].*?<\/table>/si',
        function ( $m ) {
            return '<p class="table-scroll-hint is-hidden">横にスライドできます</p>'
                 . '<div class="table-scroll">' . $m[0] . '</div>';
        },
        $content
    );

    // 退避した Gutenberg テーブルを復元
    if ( $protected ) {
        $content = str_replace( array_keys( $protected ), array_values( $protected ), $content );
    }

    return $content;
}
add_filter( 'the_content', 'awbase_wrap_content_tables' );

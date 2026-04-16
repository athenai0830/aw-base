<?php
// AW-Base Theme Functions
if ( ! defined( 'ABSPATH' ) ) exit;

// コメント表示コールバック
function awbase_comment_callback( $comment, $args, $depth ) {
    $tag = ( $args['style'] === 'div' ) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item', $comment ); ?>>
        <article class="comment-body">
            <div class="comment-meta">
                <div class="comment-avatar">
                    <?php echo get_avatar( $comment, 48 ); ?>
                </div>
                <div class="comment-author-info">
                    <span class="comment-author-name"><?php comment_author(); ?></span>
                    <span class="comment-date">
                        <a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
                            <?php echo esc_html( get_comment_date() ); ?>
                        </a>
                    </span>
                </div>
            </div>
            <?php if ( '0' === $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation">コメントは承認待ちです。</p>
            <?php endif; ?>
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
            <div class="comment-reply">
                <?php
                comment_reply_link( array_merge( $args, array(
                    'add_below' => 'comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '',
                    'after'     => '',
                ) ) );
                ?>
            </div>
        </article>
    <?php
}

// 1. Theme Setup
function awbase_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'script', 'style' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    
    register_nav_menus( array(
        'primary' => 'グローバルナビゲーション',
        'footer'  => 'フッターナビゲーション',
        'mobile'  => 'モバイルナビゲーション',
    ) );

    // サムネイルサイズは image-optimizer.php が管理（uploads/aw-thumbs/ に独自生成）
}
add_action( 'after_setup_theme', 'awbase_setup' );

// 2. Enqueue Scripts and Styles
function awbase_enqueue_scripts() {
    // ファイルの更新日時をバージョンに使用 → CSS/JS 変更時に自動キャッシュバスト
    $css_ver = filemtime( get_template_directory() . '/assets/css/style.css' );
    $js_ver  = filemtime( get_template_directory() . '/assets/js/main.js' );
    $theme_version = wp_get_theme()->get('Version');

    // Main layout CSS
    wp_enqueue_style( 'awbase-main-style', get_template_directory_uri() . '/assets/css/style.css', array(), $css_ver );

    // Child theme or Parent root style
    wp_enqueue_style( 'awbase-style', get_stylesheet_uri(), array('awbase-main-style'), $theme_version );

    // Front-end JS – jQuery不要、defer で非同期実行
    wp_enqueue_script( 'awbase-script', get_template_directory_uri() . '/assets/js/main.js', array(), $js_ver, true );
    wp_add_inline_script( 'awbase-script', 'window.awbaseData=' . wp_json_encode( [ 'restUrl' => esc_url_raw( rest_url() ) ] ) . ';', 'before' );

    // Font Awesome 6
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
}
add_action( 'wp_enqueue_scripts', 'awbase_enqueue_scripts' );


// 3. Includes
// Define Theme directory paths
define( 'AWBASE_DIR', get_template_directory() );
define( 'AWBASE_URI', get_template_directory_uri() );

// Files will be required here once they are created
require_once AWBASE_DIR . '/inc/updater.php';
require_once AWBASE_DIR . '/inc/admin.php';
require_once AWBASE_DIR . '/inc/customizer.php';
require_once AWBASE_DIR . '/inc/style-output.php';
require_once AWBASE_DIR . '/inc/seo.php';
require_once AWBASE_DIR . '/inc/post-meta.php';
require_once AWBASE_DIR . '/inc/ai-tracker.php';
require_once AWBASE_DIR . '/inc/pv-tracker.php';
require_once AWBASE_DIR . '/inc/image-optimizer.php';
require_once AWBASE_DIR . '/inc/performance.php';
require_once AWBASE_DIR . '/inc/shortcodes.php';
require_once AWBASE_DIR . '/inc/blocks.php';
require_once AWBASE_DIR . '/inc/widgets.php';
require_once AWBASE_DIR . '/inc/llms-txt.php';
require_once AWBASE_DIR . '/inc/sitemap-xml.php';
require_once AWBASE_DIR . '/inc/user-meta.php';

// 4. Breadcrumb function
function awbase_breadcrumbs() {
    if ( is_front_page() ) return;

    $sep       = '<span class="sep" aria-hidden="true"> &rsaquo; </span>';
    $home_name = get_bloginfo( 'name' );
    $home_url  = home_url( '/' );
    $pos       = 1;

    $item = function( $name, $url, $position, $is_last = false ) use ( $sep ) {
        $li  = '<span class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement">';
        $li .= '<meta itemprop="position" content="' . esc_attr( $position ) . '">';
        if ( $is_last ) {
            $li .= '<span itemprop="name">' . esc_html( $name ) . '</span>';
        } else {
            $li .= '<a href="' . esc_url( $url ) . '" itemprop="item"><span itemprop="name">' . esc_html( $name ) . '</span></a>';
            $li .= $sep;
        }
        $li .= '</span>';
        return $li;
    };

    echo '<nav class="breadcrumbs" aria-label="パンくずリスト" itemscope itemtype="https://schema.org/BreadcrumbList">';

    if ( is_singular( 'post' ) ) {
        echo $item( $home_name, $home_url, $pos++ );
        $cats = get_the_category();
        if ( $cats ) {
            echo $item( $cats[0]->name, get_category_link( $cats[0]->term_id ), $pos++ );
        }
        echo $item( get_the_title(), get_permalink(), $pos, true );

    } elseif ( is_category() ) {
        echo $item( $home_name, $home_url, $pos++ );
        echo $item( single_cat_title( '', false ), get_term_link( get_queried_object() ), $pos, true );

    } elseif ( is_tag() ) {
        echo $item( $home_name, $home_url, $pos++ );
        echo $item( single_tag_title( '', false ), get_term_link( get_queried_object() ), $pos, true );

    } elseif ( is_page() ) {
        echo $item( $home_name, $home_url, $pos++ );
        $ancestors = get_post_ancestors( get_the_ID() );
        foreach ( array_reverse( $ancestors ) as $ancestor ) {
            echo $item( get_the_title( $ancestor ), get_permalink( $ancestor ), $pos++ );
        }
        echo $item( get_the_title(), get_permalink(), $pos, true );

    } elseif ( is_search() ) {
        echo $item( $home_name, $home_url, $pos++ );
        echo '<span class="breadcrumb-item">検索: ' . esc_html( get_search_query() ) . '</span>';

    } elseif ( is_404() ) {
        echo $item( $home_name, $home_url, $pos++ );
        echo '<span class="breadcrumb-item">404 Not Found</span>';

    } elseif ( is_archive() ) {
        echo $item( $home_name, $home_url, $pos++ );
        echo $item( wp_strip_all_tags( get_the_archive_title() ), get_term_link( get_queried_object() ), $pos, true );
    }

    echo '</nav>';
}

// フロントページ（TOP）の固定ページタイトルを非表示にする
add_filter( 'the_title', function( $title, $id ) {
    if ( is_front_page() && $id === get_queried_object_id() && in_the_loop() ) {
        return '';
    }
    return $title;
}, 10, 2 );

// Flush rewrite rules on theme activation so llms.txt and sitemap.xml endpoints work immediately
function awbase_activate_flush_rewrites() {
    awbase_llms_txt_rewrite();
    awbase_sitemap_rewrite();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'awbase_activate_flush_rewrites' );

// テーマバージョンが変わった時（更新後）もリライトルールをフラッシュ
function awbase_maybe_flush_rewrites() {
    $theme   = wp_get_theme();
    $version = $theme->get( 'Version' );
    if ( get_option( 'awbase_rewrite_version' ) !== $version ) {
        flush_rewrite_rules();
        update_option( 'awbase_rewrite_version', $version );
    }
}
add_action( 'admin_init', 'awbase_maybe_flush_rewrites' );

// ---------------------------------------------------------------------------
// 拡張子付きURL（.txt / .xml / .md 等）のトレイリングスラッシュを統一（末尾なし）
// 1. redirect_canonical によるスラッシュ付与を阻止
// 2. /付きでアクセスされた場合は /なしに 301 リダイレクト
// ---------------------------------------------------------------------------
function awbase_no_trailing_slash_for_file_urls( $redirect_url, $requested_url ) {
    $ext = pathinfo( parse_url( $requested_url, PHP_URL_PATH ), PATHINFO_EXTENSION );
    if ( in_array( $ext, [ 'txt', 'xml', 'md', 'json', 'csv' ], true ) ) {
        return false;
    }
    return $redirect_url;
}
add_filter( 'redirect_canonical', 'awbase_no_trailing_slash_for_file_urls', 10, 2 );

function awbase_redirect_file_trailing_slash() {
    $uri  = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    $path = strtok( $uri, '?' );
    $ext  = pathinfo( $path, PATHINFO_EXTENSION );
    if ( in_array( $ext, [ 'txt', 'xml', 'md', 'json', 'csv' ], true ) && substr( $path, -1 ) === '/' ) {
        $query = strpos( $uri, '?' ) !== false ? '?' . substr( $uri, strpos( $uri, '?' ) + 1 ) : '';
        wp_redirect( home_url( rtrim( $path, '/' ) . $query ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'awbase_redirect_file_trailing_slash', 1 );

// Layout helper — header.php / footer.php 共用。サイドバー表示・レイアウトクラスを一元管理
function awbase_get_layout() {
    static $cache = null;
    if ( $cache !== null ) return $cache;

    $options = awbase_get_settings();
    $is_top  = is_front_page() || is_home();
    $is_1col = $options['columns'] === '1';

    $is_wide = false;

    // 個別ページの設定（中優先）
    if ( is_singular() ) {
        $post_layout = get_post_meta( get_queried_object_id(), 'awbase_layout', true );
        if ( $post_layout === 'wide' ) {
            $is_wide = true;
            $is_1col = true;
        } elseif ( $post_layout === '1c' ) {
            $is_1col = true;
        } elseif ( $post_layout === '2c' ) {
            $is_1col = false;
        }
    }

    // TOPページ レイアウト設定（最高優先 — 個別ページ設定を上書き）
    if ( $is_top && $options['front_page_1col'] === '1' ) {
        $is_wide = false;
        $is_1col = true;
    }

    if ( $is_wide ) {
        $layout_class = 'layout-wide';
    } else {
        $layout_class = $is_1col ? 'layout-1c' : 'layout-2c';
    }

    $cache = [
        'layout_class'  => $layout_class,
        'sidebar_class' => $options['sidebar_position'] === 'left' ? 'sidebar-left' : 'sidebar-right',
        'show_sidebar'  => ! $is_1col,
    ];
    return $cache;
}

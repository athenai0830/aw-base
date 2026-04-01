<?php
// AW-Base Gutenberg Blocks
if ( ! defined( 'ABSPATH' ) ) exit;

// ブロックカテゴリ追加
add_filter( 'block_categories_all', function( $categories ) {
    return array_merge(
        array( array( 'slug' => 'aw-base-blocks', 'title' => 'AW-Base', 'icon' => null ) ),
        $categories
    );
}, 10, 2 );

// エディタースクリプト登録
function awbase_register_block_assets() {
    wp_register_script(
        'awbase-blocks',
        AWBASE_URI . '/assets/js/blocks.js',
        array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-server-side-render', 'wp-i18n' ),
        filemtime( AWBASE_DIR . '/assets/js/blocks.js' ),
        true
    );
}
add_action( 'init', 'awbase_register_block_assets' );

// エディターのみでスクリプトを読み込む
add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_script( 'awbase-blocks' );
} );

// ブロック登録
function awbase_register_blocks() {
    // [btn] ボタンブロック
    register_block_type( 'aw-base/btn', array(
        'render_callback' => 'awbase_btn_block_render',
        'attributes'      => array(
            'url'     => array( 'type' => 'string',  'default' => '#' ),
            'text'    => array( 'type' => 'string',  'default' => 'ボタン' ),
            'color'   => array( 'type' => 'string',  'default' => 'primary' ),
            'size'    => array( 'type' => 'string',  'default' => 'normal' ),
            'circle'  => array( 'type' => 'string',  'default' => '0' ),
            'width'   => array( 'type' => 'string',  'default' => '' ),
            'outline' => array( 'type' => 'string',  'default' => '0' ),
            'target'  => array( 'type' => 'string',  'default' => '' ),
        ),
    ) );

    // [blogcard] ブログカードブロック
    register_block_type( 'aw-base/blogcard', array(
        'render_callback' => 'awbase_blogcard_block_render',
        'attributes'      => array(
            'url'    => array( 'type' => 'string', 'default' => '' ),
            'id'     => array( 'type' => 'string', 'default' => '' ),
            'target' => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    // [faq] FAQ ブロック
    register_block_type( 'aw-base/faq', array(
        'render_callback' => 'awbase_faq_block_render',
        'attributes'      => array(
            'items' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );

    // [timeline] タイムラインブロック
    register_block_type( 'aw-base/timeline', array(
        'render_callback' => 'awbase_timeline_block_render',
        'attributes'      => array(
            'items' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );

    // [new-list] 新着記事一覧ブロック
    register_block_type( 'aw-base/new-list', array(
        'render_callback' => 'awbase_new_list_block_render',
        'attributes'      => array(
            'title'     => array( 'type' => 'string',  'default' => '' ),
            'title_tag' => array( 'type' => 'string',  'default' => 'h2' ),
            'cats'      => array( 'type' => 'string',  'default' => '' ),
            'snippet'   => array( 'type' => 'boolean', 'default' => true ),
        ),
    ) );

    // [accordion] アコーディオンブロック
    register_block_type( 'aw-base/accordion', array(
        'render_callback' => 'awbase_accordion_block_render',
        'attributes'      => array(
            'items' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );
}
add_action( 'init', 'awbase_register_blocks' );

// ──────────────────────────────────────────
// Render Callbacks（PHPサーバーサイドレンダリング）
// ──────────────────────────────────────────

function awbase_new_list_block_render( $attrs ) {
    $title     = isset( $attrs['title'] ) ? sanitize_text_field( $attrs['title'] ) : '';
    $title_tag = in_array( $attrs['title_tag'] ?? 'h2', array( 'h2', 'h3', 'h4' ) ) ? $attrs['title_tag'] : 'h2';
    $cats      = isset( $attrs['cats'] ) ? $attrs['cats'] : '';
    $snippet   = isset( $attrs['snippet'] ) ? (bool) $attrs['snippet'] : true;

    // WordPress 表示設定「1ページに表示する最大投稿数」に連動
    $per_page = (int) get_option( 'posts_per_page', 10 );
    // メインクエリと競合しないよう専用クエリ変数を使用
    $paged    = isset( $_GET['awb_nl_page'] ) ? max( 1, intval( $_GET['awb_nl_page'] ) ) : 1;

    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $per_page,
        'paged'               => $paged,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
    );
    if ( ! empty( $cats ) ) {
        $args['category__in'] = wp_parse_id_list( $cats );
    }

    $query = new WP_Query( $args );

    ob_start();
    echo '<div class="awb-shortcode-section awb-new-list-block">';

    if ( ! empty( $title ) ) {
        echo '<div class="awb-section-header">';
        echo '<' . $title_tag . ' class="awb-section-title">' . esc_html( $title ) . '</' . $title_tag . '>';
        echo '</div>';
    }

    if ( $query->have_posts() ) {
        echo '<div class="entry-card-list' . ( ! $snippet ? ' no-snippet' : '' ) . '">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/entry-card' );
        }
        echo '</div>';
        wp_reset_postdata();

        $total = $query->max_num_pages;
        if ( $total > 1 ) {
            $paginate = paginate_links( array(
                'base'      => str_replace( 999999999, '%#%', esc_url( add_query_arg( 'awb_nl_page', 999999999 ) ) ),
                'format'    => '',
                'current'   => $paged,
                'total'     => $total,
                'mid_size'  => 2,
                'prev_text' => '&#8249;',
                'next_text' => '&#8250;',
                'type'      => 'plain',
            ) );
            if ( $paginate ) {
                echo '<div class="awb-block-pagination"><div class="nav-links">' . $paginate . '</div></div>';
            }
        }
    } else {
        wp_reset_postdata();
        echo '<p>' . esc_html__( '記事が見つかりませんでした。', 'aw-base' ) . '</p>';
    }

    echo '</div>';
    return ob_get_clean();
}

function awbase_btn_block_render( $attrs ) {
    $atts_str = '';
    if ( ! empty( $attrs['url'] ) )                                  $atts_str .= ' url="' . esc_attr( $attrs['url'] ) . '"';
    if ( ! empty( $attrs['color'] ) )                                $atts_str .= ' color="' . esc_attr( $attrs['color'] ) . '"';
    if ( ! empty( $attrs['size'] ) && $attrs['size'] !== 'normal' )  $atts_str .= ' size="' . esc_attr( $attrs['size'] ) . '"';
    if ( ! empty( $attrs['circle'] ) && $attrs['circle'] === '1' )   $atts_str .= ' circle="1"';
    if ( ! empty( $attrs['width'] ) )                                $atts_str .= ' width="' . esc_attr( $attrs['width'] ) . '"';
    if ( ! empty( $attrs['outline'] ) && $attrs['outline'] === '1' ) $atts_str .= ' outline="1"';
    if ( ! empty( $attrs['target'] ) )                               $atts_str .= ' target="' . esc_attr( $attrs['target'] ) . '"';
    $text = ! empty( $attrs['text'] ) ? esc_html( $attrs['text'] ) : 'ボタン';
    return do_shortcode( '[btn' . $atts_str . ']' . $text . '[/btn]' );
}

function awbase_blogcard_block_render( $attrs ) {
    $has_url = ! empty( $attrs['url'] );
    $has_id  = ! empty( $attrs['id'] );
    if ( ! $has_url && ! $has_id ) return '';

    // レンダリング済みURL を追跡（Cocoon重複排除用）
    if ( $has_url ) {
        awbase_track_rendered_blogcard_url( esc_url_raw( $attrs['url'] ) );
    }

    $atts_str = '';
    if ( $has_url ) $atts_str .= ' url="' . esc_attr( $attrs['url'] ) . '"';
    if ( $has_id )  $atts_str .= ' id="' . esc_attr( $attrs['id'] ) . '"';
    if ( ! empty( $attrs['target'] ) ) $atts_str .= ' target="' . esc_attr( $attrs['target'] ) . '"';
    return do_shortcode( '[card' . $atts_str . ']' );
}

/**
 * ページ内でレンダリング済みのブログカードURLを追跡・照会する。
 * aw-base/blogcard と cocoon-blocks/blogcard の重複排除に使用。
 */
function awbase_track_rendered_blogcard_url( $url = null ) {
    static $urls = array();
    if ( $url !== null ) {
        $urls[] = $url;
    }
    return $urls;
}

// ============================================================
// Cocoon ブログカードブロック → aw-base ブログカードへ変換
// Cocoonプラグインがない環境でも正しくブログカードを描画する。
// パターン1: <a href="URL">URL</a>  ← 内部URL（aw-baseブロックと重複している場合は非表示）
// パターン2: <p>URL</p>             ← 外部URL（aw-baseショートコードで描画）
// ============================================================
add_filter( 'render_block', 'awbase_render_cocoon_blogcard', 10, 2 );

function awbase_render_cocoon_blogcard( $block_content, $block ) {
    if ( $block['blockName'] !== 'cocoon-blocks/blogcard' ) {
        return $block_content;
    }

    // パターン1: <a href="URL"> からURL抽出
    $url = '';
    if ( preg_match( '/<a\s+href=["\']([^"\']+)["\']/', $block_content, $m ) ) {
        $url = $m[1];
    }
    // パターン2: <p>URL</p> からURL抽出
    if ( empty( $url ) && preg_match( '/<p>\s*(https?:\/\/[^\s<]+)\s*<\/p>/i', $block_content, $m ) ) {
        $url = $m[1];
    }

    if ( empty( $url ) ) {
        return '';
    }

    $url = esc_url_raw( trim( $url ) );

    // 同一URLがすでに aw-base/blogcard ブロックで描画済みならスキップ（重複防止）
    if ( in_array( $url, awbase_track_rendered_blogcard_url(), true ) ) {
        return '';
    }

    // aw-base ブログカードショートコードで描画
    // 外部URLの場合は別タブで開く
    $target = '_blank';
    return awbase_blogcard_shortcode( array(
        'url'    => $url,
        'id'     => '',
        'title'  => '',
        'desc'   => '',
        'img'    => '',
        'target' => $target,
    ) );
}

function awbase_faq_block_render( $attrs ) {
    $items = isset( $attrs['items'] ) && is_array( $attrs['items'] ) ? $attrs['items'] : array();
    $parts = '';
    foreach ( $items as $item ) {
        $q = isset( $item['q'] ) ? $item['q'] : '';
        $a = isset( $item['a'] ) ? $item['a'] : '';
        if ( empty( $q ) ) continue;
        $parts .= '[faq_item q="' . esc_attr( $q ) . '"]' . wp_kses_post( $a ) . '[/faq_item]';
    }
    return empty( $parts ) ? '' : do_shortcode( '[faq]' . $parts . '[/faq]' );
}

function awbase_timeline_block_render( $attrs ) {
    $items = isset( $attrs['items'] ) && is_array( $attrs['items'] ) ? $attrs['items'] : array();
    $parts = '';
    foreach ( $items as $item ) {
        $label   = isset( $item['label'] ) ? $item['label'] : '';
        $content = isset( $item['content'] ) ? $item['content'] : '';
        $parts  .= '[timeline_item label="' . esc_attr( $label ) . '"]' . wp_kses_post( $content ) . '[/timeline_item]';
    }
    return empty( $parts ) ? '' : do_shortcode( '[timeline]' . $parts . '[/timeline]' );
}

function awbase_accordion_block_render( $attrs ) {
    $items = isset( $attrs['items'] ) && is_array( $attrs['items'] ) ? $attrs['items'] : array();
    $parts = '';
    foreach ( $items as $item ) {
        $title   = isset( $item['title'] ) ? $item['title'] : '';
        $content = isset( $item['content'] ) ? $item['content'] : '';
        $open    = ! empty( $item['open'] ) ? ' open="1"' : '';
        $parts  .= '[accordion_item title="' . esc_attr( $title ) . '"' . $open . ']' . wp_kses_post( $content ) . '[/accordion_item]';
    }
    return empty( $parts ) ? '' : do_shortcode( '[accordion]' . $parts . '[/accordion]' );
}

// ============================================================
// core/embed フォールバック
// oEmbed 非対応 URL（一般Webサイト等）が埋め込まれた場合、
// WordPress が生 URL を wrapper 内に残す。
// それを検知してリンクプレビューカード（テキスト左・画像右）に変換する。
// ============================================================
add_filter( 'render_block', 'awbase_embed_fallback_filter', 10, 2 );

function awbase_embed_fallback_filter( $block_content, $block ) {
    if ( $block['blockName'] !== 'core/embed' ) {
        return $block_content;
    }

    // wrapper 内が生 URL のままの場合のみ変換（iframe 等があれば oEmbed 成功 → スルー）
    if ( ! preg_match( '|<div class="wp-block-embed__wrapper">\s*(https?://[^\s<]+)\s*</div>|', $block_content, $m ) ) {
        return $block_content;
    }

    // HTML エンティティをデコードして正規 URL に
    $url_from_html = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    // ブロック属性の url を優先（正規化済みのため）
    $url = ! empty( $block['attrs']['url'] ) ? $block['attrs']['url'] : $url_from_html;
    $url = esc_url_raw( $url );

    if ( empty( $url ) ) {
        return $block_content;
    }

    return awbase_render_linkcard( $url );
}

/**
 * OGP 取得済みの URL からリンクプレビューカード HTML を生成する。
 * レイアウト: テキスト左・サムネイル右（awb-blogcard と逆配置）
 */
function awbase_render_linkcard( $url ) {
    // awbase_fetch_ogp は shortcodes.php で定義済み
    $og = function_exists( 'awbase_fetch_ogp' ) ? awbase_fetch_ogp( $url ) : array();

    $title   = ! empty( $og['title'] )       ? $og['title']       : '';
    $desc    = ! empty( $og['description'] ) ? wp_trim_words( $og['description'], 55 ) : '';
    $img_url = ! empty( $og['image'] )       ? $og['image']       : '';

    if ( empty( $title ) ) {
        $title = wp_parse_url( $url, PHP_URL_HOST ) ?: $url;
    }
    if ( empty( $img_url ) ) {
        $custom_noimage = get_awbase_option( 'blogcard_noimage_url' );
        $img_url = ! empty( $custom_noimage ) ? esc_url( $custom_noimage ) : get_template_directory_uri() . '/assets/img/no-image.svg';
    }

    $display_url = wp_parse_url( $url, PHP_URL_HOST ) ?: $url;

    // 画像読み込み失敗時（ホットリンク保護等）のフォールバックURL
    $fallback_url = get_awbase_option( 'blogcard_noimage_url' );
    if ( empty( $fallback_url ) ) {
        $fallback_url = get_template_directory_uri() . '/assets/img/no-image.svg';
    }
    $onerror = ' onerror="this.onerror=null;this.src=\'' . esc_js( esc_url( $fallback_url ) ) . '\'"';

    $html  = '<div class="awb-linkcard">';
    $html .= '<a href="' . esc_url( $url ) . '" class="awb-linkcard-inner" target="_blank" rel="noopener noreferrer">';
    $html .= '<div class="awb-linkcard-body">';
    $html .= '<p class="awb-linkcard-title">' . esc_html( $title ) . '</p>';
    if ( ! empty( $desc ) ) {
        $html .= '<p class="awb-linkcard-desc">' . esc_html( $desc ) . '</p>';
    }
    $html .= '<span class="awb-linkcard-url">' . esc_html( $display_url ) . '</span>';
    $html .= '</div>';
    $html .= '<div class="awb-linkcard-img">';
    $html .= '<img src="' . esc_url( $img_url ) . '" alt="" loading="lazy"' . $onerror . '>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
}

<?php
// AW-Base Shortcodes
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// 1. Balloon Shortcode
// ============================================================
function awbase_balloon_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'type' => 'left',
        'name' => '',
        'icon' => get_template_directory_uri() . '/assets/img/default-user.jpg',
    ), $atts, 'balloon' );

    $type_class = $atts['type'] === 'right' ? 'balloon-right' : 'balloon-left';

    ob_start();
    ?>
    <div class="balloon-wrap <?php echo esc_attr($type_class); ?>">
        <div class="balloon-icon">
            <img src="<?php echo esc_url($atts['icon']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
            <?php if ( !empty($atts['name']) ): ?>
                <span class="balloon-name"><?php echo esc_html($atts['name']); ?></span>
            <?php endif; ?>
        </div>
        <div class="balloon-text">
            <?php echo do_shortcode( wpautop( wp_kses_post( $content ) ) ); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'balloon', 'awbase_balloon_shortcode' );

// ============================================================
// 2. [new_list] Shortcode - 新着記事一覧（Cocoon互換）
//    title=""       : セクション見出し（省略時は非表示）
//    title_tag=""   : h2/h3/h4（デフォルト h2）
//    link=""        : 「もっと見る」リンクURL（省略時は非表示）
//    link_text=""   : 「もっと見る」テキスト（デフォルト「もっと見る」）
//    count=5        : 件数
//    cats=""        : カテゴリID（カンマ区切り）
//    snippet=1      : 抜粋表示（0で非表示）
// ============================================================
function awbase_new_list_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'title'      => '',
        'title_tag'  => 'h2',
        'link'       => '',
        'link_text'  => 'もっと見る',
        'count'      => 5,
        'cats'       => '',
        'type'       => 'default',
        'snippet'    => 1,
        'arrow'      => 0,
    ), $atts, 'new_list' );

    $count = max( 1, min( 100, intval( $atts['count'] ) ) );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'ignore_sticky_posts' => 1,
    );

    if ( ! empty( $atts['cats'] ) ) {
        $args['category__in'] = wp_parse_id_list( $atts['cats'] );
    }

    $query = new WP_Query( $args );

    ob_start();
    echo '<div class="awb-shortcode-section">';

    // セクションヘッダー（タイトル or リンクがあるときのみ）
    if ( ! empty( $atts['title'] ) || ! empty( $atts['link'] ) ) {
        echo '<div class="awb-section-header">';
        if ( ! empty( $atts['title'] ) ) {
            $tag = in_array( $atts['title_tag'], ['h2','h3','h4'] ) ? $atts['title_tag'] : 'h2';
            echo '<' . $tag . ' class="awb-section-title">' . esc_html( $atts['title'] ) . '</' . $tag . '>';
        }
        if ( ! empty( $atts['link'] ) ) {
            echo '<a href="' . esc_url( $atts['link'] ) . '" class="awb-section-more">' . esc_html( $atts['link_text'] ) . ' &rarr;</a>';
        }
        echo '</div>';
    }

    if ( $query->have_posts() ) {
        echo '<div class="entry-card-list shortcode-new-list ' . ( $atts['snippet'] == 0 ? 'no-snippet' : '' ) . '">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/entry-card' );
        }
        echo '</div>';

        // ボタン（link指定時）
        if ( ! empty( $atts['link'] ) ) {
            echo '<div class="awb-section-btn-wrap"><a href="' . esc_url( $atts['link'] ) . '" class="awb-section-btn">' . esc_html( $atts['link_text'] ) . '</a></div>';
        }
    } else {
        echo '<p>' . esc_html__( '記事が見つかりませんでした。', 'aw-base' ) . '</p>';
    }
    wp_reset_postdata();

    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'new_list', 'awbase_new_list_shortcode' );

// ============================================================
// 3. [popular_list] Shortcode - 人気記事一覧（Cocoon互換）
//    title=""       : セクション見出し
//    link=""        : 「もっと見る」リンクURL
//    link_text=""   : ボタンテキスト（デフォルト「もっと見る」）
//    count=5        : 件数
//    cats=""        : カテゴリID
//    period="total" : total / month / week / day
//    snippet=1      : 抜粋表示
// ============================================================
function awbase_popular_list_shortcode( $atts ) {
    $default_count  = (int) get_awbase_option( 'popular_list_count' );
    $default_period = get_awbase_option( 'popular_list_period' );
    $atts = shortcode_atts( array(
        'title'      => '',
        'title_tag'  => 'h2',
        'link'       => '',
        'link_text'  => 'もっと見る',
        'count'      => $default_count > 0 ? $default_count : 5,
        'cats'       => '',
        'period'     => $default_period ?: 'total',
        'snippet'    => 1,
    ), $atts, 'popular_list' );

    $count  = max( 1, min( 100, intval( $atts['count'] ) ) );
    $period = in_array( $atts['period'], array('total','month','week','day') ) ? $atts['period'] : 'total';

    // 1時間キャッシュ（期間集計は重いため）
    $cache_key    = 'awbase_popular_' . md5( $period . '_' . $count . '_' . $atts['cats'] );
    $cached_html  = get_transient( $cache_key );
    if ( $cached_html !== false ) return $cached_html;

    if ( $period === 'total' ) {
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'meta_key'       => '_awbase_pv_total',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        );
    } else {
        $days   = array( 'day' => 1, 'week' => 7, 'month' => 30 );
        $cutoff = gmdate( 'Y-m-d', strtotime( '-' . $days[$period] . ' days' ) );

        global $wpdb;
        $meta_rows = $wpdb->get_results(
            "SELECT pm.post_id, pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_awbase_pv_daily'
             AND p.post_status = 'publish'
             AND p.post_type = 'post'",
            ARRAY_A
        );

        $scores = array();
        foreach ( $meta_rows as $row ) {
            $daily = maybe_unserialize( $row['meta_value'] );
            if ( ! is_array( $daily ) ) continue;
            $sum = 0;
            foreach ( $daily as $date => $count ) {
                if ( $date >= $cutoff ) $sum += (int) $count;
            }
            if ( $sum > 0 ) $scores[ $row['post_id'] ] = $sum;
        }

        if ( empty( $scores ) ) {
            return awbase_new_list_shortcode( $atts );
        }

        arsort( $scores );
        $ordered_ids = array_slice( array_keys( $scores ), 0, $count );

        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'post__in'       => $ordered_ids,
            'orderby'        => 'post__in',
        );
    }

    if ( ! empty( $atts['cats'] ) ) {
        $args['category__in'] = wp_parse_id_list( $atts['cats'] );
    }

    $query = new WP_Query( $args );

    ob_start();
    echo '<div class="awb-shortcode-section">';

    if ( ! empty( $atts['title'] ) || ! empty( $atts['link'] ) ) {
        echo '<div class="awb-section-header">';
        if ( ! empty( $atts['title'] ) ) {
            $tag = in_array( $atts['title_tag'], ['h2','h3','h4'] ) ? $atts['title_tag'] : 'h2';
            echo '<' . $tag . ' class="awb-section-title">' . esc_html( $atts['title'] ) . '</' . $tag . '>';
        }
        if ( ! empty( $atts['link'] ) ) {
            echo '<a href="' . esc_url( $atts['link'] ) . '" class="awb-section-more">' . esc_html( $atts['link_text'] ) . ' &rarr;</a>';
        }
        echo '</div>';
    }

    if ( $query->have_posts() ) {
        $post_count = $query->post_count;
        echo '<div class="popular-scroll-wrap" data-total="' . esc_attr( $post_count ) . '">';
        echo '<button class="popular-prev-btn" aria-label="前の記事へ" style="display:none"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>';
        echo '<div class="popular-card-slim-list">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/popular-card-slim' );
        }
        echo '</div>';
        echo '<button class="popular-next-btn" aria-label="次の記事へ" style="display:none"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>';
        echo '</div>';

        if ( ! empty( $atts['link'] ) ) {
            echo '<div class="awb-section-btn-wrap"><a href="' . esc_url( $atts['link'] ) . '" class="awb-section-btn">' . esc_html( $atts['link_text'] ) . '</a></div>';
        }
    } else {
        echo '<p>' . esc_html__( '記事が見つかりませんでした。', 'aw-base' ) . '</p>';
    }
    wp_reset_postdata();

    echo '</div>';
    $html = ob_get_clean();
    set_transient( $cache_key, $html, HOUR_IN_SECONDS );
    return $html;
}
add_shortcode( 'popular_list', 'awbase_popular_list_shortcode' );

// ============================================================
// 4. [sitemap] Shortcode - HTMLサイトマップ（Cocoon互換）
//    使い方: ページに [sitemap] を記述するだけ
// ============================================================
function awbase_sitemap_shortcode( $atts ) {
    ob_start();
    echo '<div class="awb-sitemap">';

    // 固定ページ一覧
    $pages = get_pages( array(
        'sort_column'  => 'menu_order',
        'sort_order'   => 'ASC',
        'hierarchical' => true,
        'parent'       => 0,
        'post_status'  => 'publish',
    ) );

    if ( ! empty( $pages ) ) {
        echo '<div class="awb-sitemap-section">';
        echo '<h3 class="awb-sitemap-heading">固定ページ</h3>';
        echo '<ul class="awb-sitemap-list">';
        foreach ( $pages as $page ) {
            echo '<li class="awb-sitemap-item"><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( $page->post_title ) . '</a>';

            // 子ページ
            $children = get_pages( array(
                'parent'      => $page->ID,
                'post_status' => 'publish',
                'sort_column' => 'menu_order',
            ) );
            if ( ! empty( $children ) ) {
                echo '<ul class="awb-sitemap-children">';
                foreach ( $children as $child ) {
                    echo '<li><a href="' . esc_url( get_permalink( $child->ID ) ) . '">' . esc_html( $child->post_title ) . '</a></li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    // カテゴリ別記事
    $cats = get_categories( array(
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );

    foreach ( $cats as $cat ) {
        if ( $cat->parent !== 0 ) continue; // トップカテゴリのみ

        echo '<div class="awb-sitemap-section">';
        echo '<h3 class="awb-sitemap-heading"><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
        if ( $cat->description ) {
            echo '<span class="awb-sitemap-cat-desc">' . esc_html( $cat->description ) . '</span>';
        }
        echo '</h3>';

        $posts = get_posts( array(
            'category'       => $cat->term_id,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( ! empty( $posts ) ) {
            echo '<ul class="awb-sitemap-list">';
            foreach ( $posts as $post ) {
                echo '<li class="awb-sitemap-item"><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';
                echo '<time class="awb-sitemap-date">' . esc_html( get_the_date( 'Y.m.d', $post->ID ) ) . '</time>';
                echo '</li>';
            }
            echo '</ul>';
        }

        echo '</div>';
    }

    // カテゴリに属さない記事（未分類カテゴリを除く）のフォールバック表示
    $uncategorized = get_category_by_slug( 'uncategorized' );
    $uncategorized_id = $uncategorized ? $uncategorized->term_id : 0;

    // サイトマップに掲載済みのIDを収集
    $listed_ids = array();
    foreach ( $cats as $cat ) {
        if ( $cat->parent !== 0 ) continue;
        $pids = get_posts( array(
            'category'       => $cat->term_id,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        $listed_ids = array_merge( $listed_ids, $pids );
    }
    $listed_ids = array_unique( $listed_ids );

    // 未掲載の投稿を取得
    $all_post_ids = get_posts( array(
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ) );
    $unlisted_ids = array_diff( $all_post_ids, $listed_ids );

    if ( ! empty( $unlisted_ids ) ) {
        $unlisted_posts = get_posts( array(
            'post__in'       => array_values( $unlisted_ids ),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
        if ( ! empty( $unlisted_posts ) ) {
            echo '<div class="awb-sitemap-section">';
            echo '<h3 class="awb-sitemap-heading">その他の記事</h3>';
            echo '<ul class="awb-sitemap-list">';
            foreach ( $unlisted_posts as $post ) {
                echo '<li class="awb-sitemap-item"><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';
                echo '<time class="awb-sitemap-date">' . esc_html( get_the_date( 'Y.m.d', $post->ID ) ) . '</time>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'sitemap', 'awbase_sitemap_shortcode' );

// ============================================================
// 5. [btn] Shortcode - ボタン（Cocoon互換）
//    url=""         : リンク先URL（必須）
//    color=""       : primary(デフォルト) / secondary / danger / success / warning / custom
//    bgcolor=""     : カスタム背景色（color="custom"時）
//    textcolor=""   : カスタム文字色
//    size=""        : normal(デフォルト) / large / small
//    outline=0      : 1でアウトラインボタン
//    target=""      : _blank 等
//    icon=""        : Font Awesome クラス（例: fa-solid fa-download）
//    使い方: [btn url="https://example.com"]ダウンロード[/btn]
// ============================================================
function awbase_btn_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'url'       => '#',
        'color'     => 'primary',
        'bgcolor'   => '',
        'textcolor' => '',
        'size'      => 'normal',
        'circle'    => '0',   // '1' = 円形（Cocoon互換）
        'width'     => '',    // '25'/'50'/'75'/'100' = 幅%（Cocoon互換）
        'outline'   => '0',
        'target'    => '',
        'icon'      => '',
        // 後方互換（ショートコード直書き用）
        'shape'     => 'normal',
        'align'     => 'center',
    ), $atts, 'btn' );

    $classes = array( 'awb-btn' );
    $classes[] = 'awb-btn-' . sanitize_html_class( $atts['color'] );
    if ( $atts['size'] !== 'normal' ) {
        $classes[] = 'awb-btn-' . sanitize_html_class( $atts['size'] );
    }
    // circle 指定（Cocoon: 円形にする）
    if ( $atts['circle'] === '1' ) {
        $classes[] = 'awb-btn-circle';
    } elseif ( $atts['shape'] !== 'normal' ) {
        // 後方互換: shape パラメータ（直接ショートコード記述時）
        $classes[] = 'awb-btn-' . sanitize_html_class( $atts['shape'] );
    }
    if ( $atts['outline'] === '1' ) {
        $classes[] = 'awb-btn-outline';
    }

    $btn_style = '';
    if ( ! empty( $atts['bgcolor'] ) ) {
        $btn_style .= 'background-color:' . sanitize_hex_color( $atts['bgcolor'] ) . ';border-color:' . sanitize_hex_color( $atts['bgcolor'] ) . ';';
    }
    if ( ! empty( $atts['textcolor'] ) ) {
        $btn_style .= 'color:' . sanitize_hex_color( $atts['textcolor'] ) . ';';
    }

    $target_attr = '';
    if ( $atts['target'] === '_blank' ) {
        $target_attr = ' target="_blank" rel="noopener noreferrer"';
    }

    $icon_html = '';
    if ( ! empty( $atts['icon'] ) ) {
        $icon_html = '<i class="' . esc_attr( $atts['icon'] ) . '" aria-hidden="true"></i> ';
    }

    // ラッパークラス（Cocoon互換: 幅%）
    $valid_widths = array( '25', '50', '75', '100' );
    $wrap_class   = 'awb-btn-wrap';
    if ( in_array( $atts['width'], $valid_widths, true ) ) {
        $wrap_class .= ' awb-width-' . $atts['width'];
    }

    $html  = '<div class="' . esc_attr( $wrap_class ) . '">';
    $html .= '<a href="' . esc_url( $atts['url'] ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
    if ( $btn_style ) $html .= ' style="' . esc_attr( $btn_style ) . '"';
    $html .= $target_attr . '>';
    $html .= $icon_html . do_shortcode( wp_kses_post( $content ) );
    $html .= '</a></div>';

    return $html;
}
add_shortcode( 'btn',    'awbase_btn_shortcode' );
add_shortcode( 'button', 'awbase_btn_shortcode' ); // エイリアス

// ============================================================
// 6. [card] / [blogcard] Shortcode - ブログカード（Cocoon互換）
//    url=""         : リンク先URL（必須 ※id指定時は省略可）
//    id=""          : 投稿ID（Cocoon互換: url の代わりに使用可）
//    title=""       : カードタイトル（省略時は自動取得）
//    desc=""        : 説明文（省略時は自動取得）
//    img=""         : サムネイルURL（省略時は自動取得）
//    target=""      : _blank 等
//    使い方: [card url="https://example.com/post/"]
//           [card id="123"]  ← Cocoon互換
// ============================================================

/**
 * 外部URLのOGPメタデータ（タイトル・説明・OG画像）を取得してキャッシュする。
 * transient で24時間キャッシュし、取得失敗時は1時間後に再試行する。
 *
 * 属性の記述順・クォートスタイルの違いに対応した堅牢な実装:
 *  1. meta タグ全体をマッチしてから content 値を抽出
 *  2. og:image の相対URL → 絶対URL変換
 *  3. タイムアウト 10 秒
 */
function awbase_fetch_ogp( $url ) {
    $cache_key = 'awbase_ogp_' . md5( $url );
    $cached    = get_transient( $cache_key );
    
    // ログイン中の管理者などはキャッシュを無視して最新を取得し直す
    if ( $cached !== false && ! current_user_can( 'edit_posts' ) ) {
        return $cached;
    }

    $og = array( 'title' => '', 'description' => '', 'image' => '' );

    $args = array(
        'timeout'    => 10,
        'user-agent' => 'Mozilla/5.0 (compatible; WordPress/' . get_bloginfo( 'version' ) . ')',
        'sslverify'  => false,
        'headers'    => array( 'Accept-Language' => 'ja,en;q=0.9' ),
    );

    // ローカル環境等の Basic 認証を透過的に引き継ぐ
    if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
        $user = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) );
        $pass = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_PW'] ) );
        $args['headers']['Authorization'] = 'Basic ' . base64_encode( $user . ':' . $pass );
    } elseif ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
        $args['headers']['Authorization'] = sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) );
    }

    $response = wp_remote_get( $url, $args );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        set_transient( $cache_key, $og, HOUR_IN_SECONDS );
        return $og;
    }

    $body = wp_remote_retrieve_body( $response );
    // <head> 部分のみを対象にパースしてメモリ節約
    $head_end = stripos( $body, '</head>' );
    $head     = $head_end !== false ? substr( $body, 0, $head_end ) : $body;

    /**
     * 指定した属性タイプ（property/name）と値を持つ meta タグの content 値を取得する。
     * 属性の並び順・クォートスタイル（ダブル/シングル）に依存しない。
     */
    $get_meta_content = function( $attr_type, $attr_value ) use ( $head ) {
        $pattern = '/<meta\s[^>]*\b' . $attr_type . '\s*=\s*["\']?' . preg_quote( $attr_value, '/' ) . '["\']?[^>]*\/?>/i';
        if ( ! preg_match( $pattern, $head, $meta ) ) {
            return '';
        }
        $tag = $meta[0];
        if ( preg_match( '/\bcontent\s*=\s*"([^"]*)"/i', $tag, $c ) ) {
            return html_entity_decode( $c[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        }
        if ( preg_match( "/\\bcontent\\s*=\\s*'([^']*)'/i", $tag, $c ) ) {
            return html_entity_decode( $c[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        }
        return '';
    };

    // og:title → <title> の順で取得
    $og['title'] = $get_meta_content( 'property', 'og:title' );
    if ( empty( $og['title'] ) && preg_match( '/<title[^>]*>(.*?)<\/title>/is', $head, $m ) ) {
        $og['title'] = html_entity_decode( strip_tags( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }
    $og['title'] = trim( $og['title'] );

    // og:description → meta description の順で取得
    $og['description'] = $get_meta_content( 'property', 'og:description' );
    if ( empty( $og['description'] ) ) {
        $og['description'] = $get_meta_content( 'name', 'description' );
    }
    $og['description'] = trim( $og['description'] );

    // og:image（相対URL → 絶対URL変換対応）
    $img_raw = $get_meta_content( 'property', 'og:image' );
    if ( ! empty( $img_raw ) ) {
        if ( strpos( $img_raw, 'http' ) !== 0 ) {
            $parsed  = wp_parse_url( $url );
            $base    = $parsed['scheme'] . '://' . $parsed['host'];
            $img_raw = ( strpos( $img_raw, '/' ) === 0 ) ? $base . $img_raw : $base . '/' . ltrim( $img_raw, '/' );
        }
        $og['image'] = esc_url_raw( $img_raw );
    }

    set_transient( $cache_key, $og, DAY_IN_SECONDS );
    return $og;
}

function awbase_blogcard_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'url'    => '',
        'id'     => '',   // Cocoon互換: 投稿IDで指定（url の代替）
        'title'  => '',
        'desc'   => '',
        'img'    => '',
        'target' => '',
    ), $atts, 'card' );

    // id パラメータが指定された場合は permalink に変換（Cocoon互換）
    if ( empty( $atts['url'] ) && ! empty( $atts['id'] ) ) {
        $pid = intval( $atts['id'] );
        if ( $pid > 0 && get_post_status( $pid ) ) {
            $atts['url'] = get_permalink( $pid );
        }
    }

    if ( empty( $atts['url'] ) ) return '';

    $url     = esc_url( $atts['url'] );
    $title   = $atts['title'];
    $desc    = $atts['desc'];
    $img_url = $atts['img'];

    // 投稿IDを特定（URLから, またはid属性から直接）
    $post_id = 0;
    if ( ! empty( $atts['id'] ) && intval( $atts['id'] ) > 0 ) {
        $pid = intval( $atts['id'] );
        if ( get_post_status( $pid ) ) {
            $post_id = $pid;
        }
    }
    if ( ! $post_id ) {
        $post_id = url_to_postid( $atts['url'] );
    }
    // ドメインが異なる場合（ローカル開発・ステージング等）でもパスで照合を試みる
    if ( ! $post_id ) {
        $parsed_url  = wp_parse_url( $atts['url'] );
        $parsed_home = wp_parse_url( home_url() );
        if ( isset( $parsed_url['path'] ) &&
             ( ! isset( $parsed_url['host'] ) || ( isset( $parsed_home['host'] ) && $parsed_url['host'] !== $parsed_home['host'] ) ) ) {
            $post_id = url_to_postid( home_url( $parsed_url['path'] ) );
        }
    }
    $is_inner = ( $post_id > 0 );

    // 内部リンク: WP関数で情報取得
    if ( $is_inner ) {
        if ( empty( $title ) ) {
            $title = get_the_title( $post_id );
        }
        if ( empty( $desc ) ) {
            $excerpt = get_post_field( 'post_excerpt', $post_id );
            if ( empty( $excerpt ) ) {
                $content = get_post_field( 'post_content', $post_id );
                // Gutenberg ブロックコメント（<!-- wp:... -->）を除去してからタグを剥がす
                $excerpt = preg_replace( '/<!--.*?-->/s', '', $content );
            }
            $desc = wp_trim_words( strip_shortcodes( wp_strip_all_tags( $excerpt ) ), 55 );
        }
        if ( empty( $img_url ) ) {
            $thumb_id = get_post_thumbnail_id( $post_id );
            if ( $thumb_id ) {
                foreach ( array( 'awbase-card', 'large', 'full', 'medium_large', 'medium', 'thumbnail' ) as $size ) {
                    $src = wp_get_attachment_image_src( $thumb_id, $size );
                    if ( ! empty( $src[0] ) ) {
                        $img_url = $src[0];
                        break;
                    }
                }
            }
        }
        // アイキャッチ未設定の場合は OGP 画像をフォールバックとして使用
        if ( empty( $img_url ) ) {
            $og = awbase_fetch_ogp( $url );
            if ( ! empty( $og['image'] ) ) {
                $img_url = $og['image'];
            }
        }
    } else {
        // 外部リンク: OGP メタデータをフェッチ
        if ( empty( $title ) || empty( $img_url ) ) {
            $og = awbase_fetch_ogp( $url );
            if ( empty( $title ) && ! empty( $og['title'] ) ) {
                $title = $og['title'];
            }
            if ( empty( $desc ) && ! empty( $og['description'] ) ) {
                $desc = wp_trim_words( $og['description'], 55 );
            }
            if ( empty( $img_url ) && ! empty( $og['image'] ) ) {
                $img_url = $og['image'];
            }
        }
    }

    // noimage URL を一度だけ解決（初期表示 & onerror フォールバックで共用）
    $custom_noimage = get_awbase_option( 'blogcard_noimage_url' );
    $noimage_url    = ! empty( $custom_noimage )
        ? esc_url( $custom_noimage )
        : esc_url( get_template_directory_uri() . '/assets/img/no-image.svg' );

    if ( empty( $img_url ) ) {
        $img_url = $noimage_url;
    }
    $onerror = ' onerror="this.onerror=null;this.src=\'' . esc_attr( $noimage_url ) . '\'"';

    $target_attr = '';
    if ( $atts['target'] === '_blank' ) {
        $target_attr = ' target="_blank" rel="noopener noreferrer"';
    }

    $display_url = wp_parse_url( $url, PHP_URL_HOST ) ?: $url;

    $html  = '<div class="awb-blogcard">';
    $html .= '<a href="' . esc_url( $url ) . '" class="awb-blogcard-inner"' . $target_attr . '>';
    $html .= '<div class="awb-blogcard-img">';
    $html .= '<img src="' . esc_url( $img_url ) . '" alt="" loading="lazy"' . $onerror . '>';
    $html .= '</div>';
    $html .= '<div class="awb-blogcard-body">';
    if ( ! empty( $title ) ) {
        $html .= '<p class="awb-blogcard-title">' . esc_html( $title ) . '</p>';
    }
    if ( ! empty( $desc ) ) {
        $html .= '<p class="awb-blogcard-desc">' . esc_html( $desc ) . '</p>';
    }
    $html .= '<span class="awb-blogcard-url">' . esc_html( $display_url ) . '</span>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'card',     'awbase_blogcard_shortcode' );
add_shortcode( 'blogcard', 'awbase_blogcard_shortcode' );

// ============================================================
// 7. [faq] / [faq_item] Shortcode - FAQ（構造化データ付き）（Cocoon互換）
//    [faq]
//      [faq_item q="質問文"]回答内容[/faq_item]
//    [/faq]
// ============================================================
function awbase_faq_shortcode( $atts, $content = null ) {
    // FAQ専用グローバル変数を初期化
    global $awbase_faq_items;
    $awbase_faq_items = array();

    // 内部のfaq_itemを処理
    do_shortcode( $content );

    $items = $awbase_faq_items;

    ob_start();
    echo '<div class="awb-faq">';

    // JSON-LD 構造化データ
    if ( ! empty( $items ) ) {
        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array(),
        );
        foreach ( $items as $item ) {
            $schema['mainEntity'][] = array(
                '@type'          => 'Question',
                'name'           => $item['q'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( $item['a'] ),
                ),
            );
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
    }

    // FAQ HTML
    $index = 1;
    foreach ( $items as $item ) {
        echo '<div class="awb-faq-item">';
        echo '<div class="awb-faq-q"><span class="awb-faq-q-label">Q' . $index . '</span>' . esc_html( $item['q'] ) . '</div>';
        echo '<div class="awb-faq-a"><span class="awb-faq-a-label">A</span><div class="awb-faq-a-body">' . wp_kses_post( wpautop( $item['a'] ) ) . '</div></div>';
        echo '</div>';
        $index++;
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'faq', 'awbase_faq_shortcode' );

function awbase_faq_item_shortcode( $atts, $content = null ) {
    global $awbase_faq_items;
    $atts = shortcode_atts( array( 'q' => '' ), $atts, 'faq_item' );
    if ( ! empty( $atts['q'] ) ) {
        $awbase_faq_items[] = array(
            'q' => sanitize_text_field( $atts['q'] ),
            'a' => $content,
        );
    }
    return '';
}
add_shortcode( 'faq_item', 'awbase_faq_item_shortcode' );

// ============================================================
// 8. [timeline] / [timeline_item] Shortcode - タイムライン（Cocoon互換）
//    [timeline]
//      [timeline_item label="2024年1月"]出来事の説明[/timeline_item]
//    [/timeline]
// ============================================================
function awbase_timeline_shortcode( $atts, $content = null ) {
    return '<div class="awb-timeline">' . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'timeline', 'awbase_timeline_shortcode' );

function awbase_timeline_item_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'label' => '',
        'color' => '',
    ), $atts, 'timeline_item' );

    $style = '';
    if ( ! empty( $atts['color'] ) ) {
        $style = ' style="--timeline-color:' . sanitize_hex_color( $atts['color'] ) . '"';
    }

    $html  = '<div class="awb-timeline-item"' . $style . '>';
    $html .= '<div class="awb-timeline-label">' . esc_html( $atts['label'] ) . '</div>';
    $html .= '<div class="awb-timeline-content">' . wp_kses_post( wpautop( do_shortcode( $content ) ) ) . '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'timeline_item', 'awbase_timeline_item_shortcode' );

// ============================================================
// 9. [accordion] / [accordion_item] Shortcode - アコーディオン（Cocoon互換）
//    [accordion]
//      [accordion_item title="見出し"]内容[/accordion_item]
//    [/accordion]
// ============================================================
function awbase_accordion_shortcode( $atts, $content = null ) {
    return '<div class="awb-accordion">' . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'accordion', 'awbase_accordion_shortcode' );

function awbase_accordion_item_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'title' => '',
        'open'  => '0',
    ), $atts, 'accordion_item' );

    $open = $atts['open'] === '1' ? ' open' : '';

    $html  = '<details class="awb-accordion-item"' . $open . '>';
    $html .= '<summary class="awb-accordion-head">' . esc_html( $atts['title'] ) . '<span class="awb-accordion-icon" aria-hidden="true"></span></summary>';
    $html .= '<div class="awb-accordion-body">' . wp_kses_post( wpautop( do_shortcode( $content ) ) ) . '</div>';
    $html .= '</details>';

    return $html;
}
add_shortcode( 'accordion_item', 'awbase_accordion_item_shortcode' );

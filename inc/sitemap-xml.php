<?php
// AW-Base XML Sitemap
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AWBASE_SITEMAP_PER_PAGE', 1000 );

// ---------------------------------------------------------------------------
// Rewrite rules
// ---------------------------------------------------------------------------
function awbase_sitemap_rewrite() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?awbase_sitemap=index', 'top' );
    add_rewrite_rule(
        '^sitemap-([a-z]+)-(\d+)\.xml$',
        'index.php?awbase_sitemap_type=$matches[1]&awbase_sitemap_page=$matches[2]',
        'top'
    );
}
add_action( 'init', 'awbase_sitemap_rewrite' );
// after_switch_theme でのフラッシュは functions.php の awbase_activate_flush_rewrites() に集約

// ---------------------------------------------------------------------------
// Query vars
// ---------------------------------------------------------------------------
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'awbase_sitemap';
    $vars[] = 'awbase_sitemap_type';
    $vars[] = 'awbase_sitemap_page';
    return $vars;
} );

// ---------------------------------------------------------------------------
// Request handler
// ---------------------------------------------------------------------------
add_action( 'template_redirect', function() {
    $sitemap = get_query_var( 'awbase_sitemap' );
    $type    = get_query_var( 'awbase_sitemap_type' );

    if ( ! $sitemap && ! $type ) return;

    $allowed = [ 'pages', 'posts', 'categories', 'tags' ];
    if ( $type && ! in_array( $type, $allowed, true ) ) {
        status_header( 404 );
        exit;
    }

    while ( ob_get_level() > 0 ) {
        ob_end_clean();
    }
    header( 'Content-Type: application/xml; charset=utf-8', true );
    status_header( 200 );

    if ( $sitemap === 'index' ) {
        awbase_sitemap_output_index();
    } else {
        awbase_sitemap_output_sub( $type, max( 1, (int) get_query_var( 'awbase_sitemap_page' ) ) );
    }
    exit;
} );

// ---------------------------------------------------------------------------
// Sitemap index
// ---------------------------------------------------------------------------
function awbase_sitemap_xsl_pi() {
    $xsl = get_template_directory_uri() . '/sitemap.xsl';
    echo '<?xml-stylesheet type="text/xsl" href="' . esc_url( $xsl ) . '"?>' . "\n";
}

function awbase_sitemap_output_index() {
    $n = AWBASE_SITEMAP_PER_PAGE;

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    awbase_sitemap_xsl_pi();
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Pages: TOPページ（仮想スロット）＋固定ページ
    $page_total = (int) wp_count_posts( 'page' )->publish + 1;
    awbase_sitemap_index_group( 'pages', $page_total, $n );

    // Posts
    $post_total = (int) wp_count_posts( 'post' )->publish;
    if ( $post_total > 0 ) {
        awbase_sitemap_index_group( 'posts', $post_total, $n );
    }

    // Categories
    $cat_total = (int) get_terms( [ 'taxonomy' => 'category', 'hide_empty' => true, 'fields' => 'count' ] );
    if ( $cat_total > 0 ) {
        awbase_sitemap_index_group( 'categories', $cat_total, $n );
    }

    // Tags
    $tag_total = (int) get_terms( [ 'taxonomy' => 'post_tag', 'hide_empty' => true, 'fields' => 'count' ] );
    if ( $tag_total > 0 ) {
        awbase_sitemap_index_group( 'tags', $tag_total, $n );
    }

    echo '</sitemapindex>';
}

function awbase_sitemap_index_group( $type, $total, $per_page ) {
    $count = max( 1, (int) ceil( $total / $per_page ) );
    for ( $i = 1; $i <= $count; $i++ ) {
        echo "\t<sitemap>\n";
        echo "\t\t<loc>" . esc_url( home_url( "/sitemap-{$type}-{$i}.xml" ) ) . "</loc>\n";
        echo "\t</sitemap>\n";
    }
}

// ---------------------------------------------------------------------------
// Sub sitemaps
// ---------------------------------------------------------------------------
function awbase_sitemap_output_sub( $type, $page ) {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    awbase_sitemap_xsl_pi();
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $n = AWBASE_SITEMAP_PER_PAGE;

    switch ( $type ) {
        case 'pages':
            awbase_sitemap_sub_pages( $page, $n );
            break;
        case 'posts':
            awbase_sitemap_sub_posts( $page, $n );
            break;
        case 'categories':
            awbase_sitemap_sub_terms( 'category', $page, $n );
            break;
        case 'tags':
            awbase_sitemap_sub_terms( 'post_tag', $page, $n );
            break;
    }

    echo '</urlset>';
}

// Pages: スロット0 = TOPページ、スロット1〜 = 固定ページ（メニュー順）
function awbase_sitemap_sub_pages( $page, $per_page ) {
    $start = ( $page - 1 ) * $per_page; // 0-based start index

    if ( $start === 0 ) {
        // スロット0: TOPページ
        echo awbase_sitemap_url( home_url( '/' ), '', 'daily' );
        $db_limit  = $per_page - 1;
        $db_offset = 0;
    } else {
        // スロット0がTOPページの分だけオフセットを -1 する
        $db_limit  = $per_page;
        $db_offset = $start - 1;
    }

    if ( $db_limit > 0 ) {
        $query = new WP_Query( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => $db_limit,
            'offset'         => $db_offset,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ] );
        foreach ( $query->posts as $p ) {
            echo awbase_sitemap_url( get_permalink( $p->ID ), $p->post_modified_gmt, 'monthly' );
        }
    }
}

// Posts: 更新日降順
function awbase_sitemap_sub_posts( $page, $per_page ) {
    $query = new WP_Query( [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'offset'         => ( $page - 1 ) * $per_page,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    foreach ( $query->posts as $p ) {
        echo awbase_sitemap_url( get_permalink( $p->ID ), $p->post_modified_gmt, 'weekly' );
    }
}

// Categories / Tags: 投稿数降順
function awbase_sitemap_sub_terms( $taxonomy, $page, $per_page ) {
    $terms = get_terms( [
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => $per_page,
        'offset'     => ( $page - 1 ) * $per_page,
    ] );
    if ( is_wp_error( $terms ) ) return;
    foreach ( $terms as $term ) {
        $link = get_term_link( $term );
        if ( ! is_wp_error( $link ) ) {
            echo awbase_sitemap_url( $link, '', 'weekly' );
        }
    }
}

// ---------------------------------------------------------------------------
// URL entry helper（priority なし）
// ---------------------------------------------------------------------------
function awbase_sitemap_url( $loc, $modified = '', $changefreq = 'monthly' ) {
    $xml  = "\t<url>\n";
    $xml .= "\t\t<loc>" . esc_url( $loc ) . "</loc>\n";
    if ( $modified ) {
        $xml .= "\t\t<lastmod>" . gmdate( 'Y-m-d', strtotime( $modified ) ) . "</lastmod>\n";
    }
    $xml .= "\t\t<changefreq>" . esc_html( $changefreq ) . "</changefreq>\n";
    $xml .= "\t</url>\n";
    return $xml;
}

<?php
// AW-Base Related Posts Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$post_id    = get_the_ID();
$categories = get_the_category( $post_id );
$tags       = get_the_tags( $post_id );

if ( ! $categories && ! $tags ) return;

$category_ids = $categories ? wp_list_pluck( $categories, 'term_id' ) : array();
$tag_ids      = $tags       ? wp_list_pluck( $tags,       'term_id' ) : array();

// ────────────────────────────────────────────────
// ステップ1: タグ一致記事を取得（最大30件をスコアリング）
// ────────────────────────────────────────────────
$scored = array();

if ( ! empty( $tag_ids ) ) {
    $tag_args = array(
        'tag__in'        => $tag_ids,
        'post__not_in'   => array( $post_id ),
        'posts_per_page' => 30,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $tag_posts = get_posts( $tag_args );

    foreach ( $tag_posts as $pid ) {
        $post_tags   = get_the_tags( $pid );
        $post_tag_ids = $post_tags ? wp_list_pluck( $post_tags, 'term_id' ) : array();
        $tag_score   = count( array_intersect( $tag_ids, $post_tag_ids ) );

        // カテゴリ一致にもボーナス点
        $post_cats   = get_the_category( $pid );
        $post_cat_ids = $post_cats ? wp_list_pluck( $post_cats, 'term_id' ) : array();
        $cat_score   = count( array_intersect( $category_ids, $post_cat_ids ) );

        $scored[ $pid ] = $tag_score * 2 + $cat_score;
    }
    arsort( $scored ); // スコア降順
}

// ステップ2: タグ一致が6件未満の場合、カテゴリ一致で補完
$needed = 6 - count( $scored );
if ( $needed > 0 && ! empty( $category_ids ) ) {
    $exclude = array_merge( array( $post_id ), array_keys( $scored ) );
    $cat_args = array(
        'category__in'   => $category_ids,
        'post__not_in'   => $exclude,
        'posts_per_page' => $needed,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    );
    $cat_posts = get_posts( $cat_args );
    foreach ( $cat_posts as $pid ) {
        $scored[ $pid ] = 0;
    }
}

if ( empty( $scored ) ) return;

// 上位6件のIDを取得
$related_ids = array_slice( array_keys( $scored ), 0, 6 );

$related_query = new WP_Query( array(
    'post__in'            => $related_ids,
    'posts_per_page'      => 6,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'orderby'             => 'post__in', // スコア順を維持
) );

if ( $related_query->have_posts() ) :
?>
<div class="related-entries">
    <h3 class="related-entries-title">関連記事</h3>
    <div class="entry-card-grid-3">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
            <?php get_template_part( 'template-parts/entry-card-grid' ); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php
endif;
wp_reset_postdata();
?>

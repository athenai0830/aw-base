<?php
// AW-Base Related Posts Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$categories = get_the_category( get_the_ID() );
if ( ! $categories ) return;

$category_ids = [];
foreach ( $categories as $category ) {
    $category_ids[] = $category->term_id;
}

$args = array(
    'category__in'   => $category_ids,
    'post__not_in'   => array( get_the_ID() ),
    'posts_per_page' => 6,
    'orderby'        => 'rand'
);

$related_query = new WP_Query( $args );

if ( $related_query->have_posts() ) :
?>
<div class="related-entries">
    <h3 class="related-entries-title">関連記事</h3>
    <div class="entries-list">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
            <?php get_template_part( 'template-parts/entry-card' ); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php
endif;
wp_reset_postdata();
?>

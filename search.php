<?php
// AW-Base Search Results Template
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="archive-header">
    <h1 class="archive-title">
        <i class="fa-solid fa-magnifying-glass"></i>
        「<?php echo esc_html( get_search_query() ); ?>」の検索結果
    </h1>
    <?php if ( have_posts() ) : ?>
        <p class="archive-description"><?php echo number_format( $wp_query->found_posts ); ?> 件見つかりました</p>
    <?php endif; ?>
</div>

<?php if ( ! have_posts() ) : ?>
<div class="no-results">
    <p>「<?php echo esc_html( get_search_query() ); ?>」に一致する記事は見つかりませんでした。</p>
    <p>別のキーワードでお試しください。</p>
    <?php get_search_form(); ?>
</div>
<?php else : ?>
<div class="entries-list">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'template-parts/entry-card' ); ?>
    <?php endwhile; ?>
</div>
<?php
the_posts_pagination( array(
    'mid_size'  => 2,
    'prev_text' => '<i class="fa-solid fa-angle-left"></i>',
    'next_text' => '<i class="fa-solid fa-angle-right"></i>',
) );
endif;
?>

<?php get_footer(); ?>

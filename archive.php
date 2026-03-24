<?php
// AW-Base Archive
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); ?>

<div class="archive-header">
    <?php
        the_archive_title( '<h1 class="archive-title">', '</h1>' );
        the_archive_description( '<div class="archive-description">', '</div>' );
    ?>
</div>

<div class="entries-list">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/entry-card' ); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <p>記事が見つかりませんでした。</p>
    <?php endif; ?>
</div>

<?php 
the_posts_pagination( array(
    'mid_size'  => 2,
    'prev_text' => '<i class="fa-solid fa-angle-left"></i>',
    'next_text' => '<i class="fa-solid fa-angle-right"></i>',
) );
?>

<?php get_footer(); ?>

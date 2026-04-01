<?php
// AW-Base Home (Blog Index)
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); ?>

<div class="archive-header">
    <h1 class="archive-title">最新の記事</h1>
</div>

<div class="entry-card-list">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/entry-card' ); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <p class="no-posts">記事が見つかりませんでした。</p>
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

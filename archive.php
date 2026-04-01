<?php
// AW-Base Archive
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="archive-header">
    <?php
        the_archive_title( '<h1 class="archive-title"><i class="fa-solid fa-folder-open"></i> ', '</h1>' );
        the_archive_description( '<div class="archive-description">', '</div>' );
    ?>
</div>

<?php if ( have_posts() ) : ?>
<div class="entry-card-list">
    <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'template-parts/entry-card' ); ?>
    <?php endwhile; ?>
</div>
<?php else : ?>
<p class="no-posts">記事が見つかりませんでした。</p>
<?php endif; ?>

<?php
$next_link = get_next_posts_link( '次のページ' );
if ( $next_link ) : ?>
<div class="pagination-next-wrap">
    <?php echo $next_link; ?>
</div>
<?php endif; ?>

<?php
the_posts_pagination( array(
    'mid_size'  => 2,
    'prev_text' => '<i class="fa-solid fa-angle-left"></i>',
    'next_text' => '<i class="fa-solid fa-angle-right"></i>',
) );
?>

<?php get_footer(); ?>

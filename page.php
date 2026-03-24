<?php
// AW-Base Page Template
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$post_id = get_the_ID();
$hide_eyecatch = get_post_meta($post_id, 'awbase_hide_eyecatch', true);
$hide_toc = get_post_meta($post_id, 'awbase_hide_toc', true);
?>

<?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-entry'); ?>>
        <header class="single-header">
            <h1 class="single-title"><?php the_title(); ?></h1>
        </header>

        <?php if ( has_post_thumbnail() && $hide_eyecatch !== '1' ) : ?>
            <figure class="single-eyecatch">
                <?php the_post_thumbnail('full'); ?>
            </figure>
        <?php endif; ?>

        <div class="single-content entry-content">
            <?php
            if ( $hide_toc !== '1' ) {
                echo '<div id="awbase-toc-container" class="toc"><div class="toc-title">目次</div><ul class="toc-list"></ul></div>';
            }
            ?>
            
            <?php the_content(); ?>
            
            <?php
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'aw-base' ),
                'after'  => '</div>',
            ) );
            ?>
        </div>
    </article>
<?php endwhile; ?>

<?php get_footer(); ?>

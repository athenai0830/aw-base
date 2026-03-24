<?php
// AW-Base Single Post Template
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

// Get post meta options
$post_id = get_the_ID();
$hide_eyecatch = get_post_meta($post_id, 'awbase_hide_eyecatch', true);
$hide_toc = get_post_meta($post_id, 'awbase_hide_toc', true);
?>

<?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-entry'); ?>>
        <header class="single-header">
            <?php
            // Categories
            $categories = get_the_category();
            if ( $categories ) {
                echo '<div class="single-categories">';
                foreach ( $categories as $category ) {
                    echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="cat-link cat-id-' . esc_attr($category->term_id) . '">' . esc_html( $category->name ) . '</a>';
                }
                echo '</div>';
            }
            ?>
            <h1 class="single-title"><?php the_title(); ?></h1>
            <div class="single-meta">
                <span class="post-date"><i class="fa-regular fa-clock"></i> <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date(); ?></time></span>
                <?php if ( get_the_modified_date('Ymd') > get_the_date('Ymd') ) : ?>
                    <span class="post-update"><i class="fa-solid fa-rotate-right"></i> <time datetime="<?php echo get_the_modified_date('Y-m-d'); ?>"><?php echo get_the_modified_date(); ?></time></span>
                <?php endif; ?>
                <span class="post-author"><i class="fa-solid fa-pen-nib"></i> <?php the_author(); ?></span>
            </div>
        </header>

        <?php if ( has_post_thumbnail() && $hide_eyecatch !== '1' ) : ?>
            <figure class="single-eyecatch">
                <?php the_post_thumbnail('full'); ?>
            </figure>
        <?php endif; ?>

        <div class="single-content entry-content">
            <?php
            // Simple TOC Placeholder (Real TOC requires JS or a complex PHP DOM parser)
            // For now, we will add a shortcode or rely on JS to build it.
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

        <footer class="single-footer">
            <?php
            // Tags
            $tags = get_the_tags();
            if ( $tags ) {
                echo '<div class="tagcloud">';
                foreach ( $tags as $tag ) {
                    echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
                }
                echo '</div>';
            }
            ?>
        </footer>

        <?php get_template_part( 'template-parts/author-box' ); ?>
        <?php get_template_part( 'template-parts/related-posts' ); ?>

    </article>
<?php endwhile; ?>

<?php get_footer(); ?>

<?php
// AW-Base Entry Card Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

// Get thumbnail – awbase-card → large → full の順でフォールバック
$thumb_url = '';
if ( has_post_thumbnail() ) {
    foreach ( array( 'awbase-card', 'large', 'full' ) as $_size ) {
        $thumb_url = get_the_post_thumbnail_url( get_the_ID(), $_size );
        if ( $thumb_url ) break;
    }
}
if ( ! $thumb_url ) {
    $thumb_url = get_template_directory_uri() . '/assets/img/no-image.svg';
}
$is_first   = function_exists('awbase_is_first_card') ? awbase_is_first_card() : false;
$img_loading = $is_first ? 'eager' : 'lazy';
$img_priority = $is_first ? ' fetchpriority="high"' : '';

// Categories
$categories = get_the_category();
$cat_name = ! empty($categories) ? $categories[0]->name : '';
$cat_id = ! empty($categories) ? $categories[0]->term_id : 0;
// Here we might fetch category color if implemented later
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry-card'); ?>>
    <a href="<?php the_permalink(); ?>" class="entry-card-link">
        <figure class="entry-card-thumb">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" loading="<?php echo $img_loading; ?>"<?php echo $img_priority; ?>>
            <?php if ( $cat_name ) : ?>
                <span class="entry-card-cat cat-id-<?php echo esc_attr($cat_id); ?>"><?php echo esc_html($cat_name); ?></span>
            <?php endif; ?>
        </figure>
        <div class="entry-card-content">
            <h3 class="entry-card-title"><?php the_title(); ?></h3>
            <div class="entry-card-meta">
                <span class="post-date"><i class="fa-regular fa-clock"></i> <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date(); ?></time></span>
                <?php if ( get_the_modified_date('Ymd') > get_the_date('Ymd') ) : ?>
                    <span class="post-update"><i class="fa-solid fa-rotate-right"></i> <time datetime="<?php echo get_the_modified_date('Y-m-d'); ?>"><?php echo get_the_modified_date(); ?></time></span>
                <?php endif; ?>
            </div>
            <div class="entry-card-snippet">
                <?php echo wp_trim_words( get_the_excerpt(), 40, '...' ); ?>
            </div>
        </div>
    </a>
</article>

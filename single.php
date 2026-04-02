<?php
// AW-Base Single Post Template
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$post_id           = get_the_ID();
$hide_eyecatch     = get_post_meta( $post_id, 'awbase_hide_eyecatch', true );
$hide_toc          = get_post_meta( $post_id, 'awbase_hide_toc', true );
$hide_title        = get_post_meta( $post_id, 'awbase_hide_title', true );
$hide_reading_time = get_post_meta( $post_id, 'awbase_hide_reading_time', true );

// 読了時間を事前計算（目次の下に表示するため）
$read_min = 0;
if ( $hide_reading_time !== '1' ) {
    $content    = get_post_field( 'post_content', $post_id );
    $word_count = mb_strlen( wp_strip_all_tags( $content ) );
    $read_min   = max( 1, (int) ceil( $word_count / 400 ) );
}
?>

<?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-entry'); ?> itemscope itemprop="blogPost" itemtype="https://schema.org/BlogPosting">
        <header class="single-header">
            <?php
            // Categories
            $categories = get_the_category();
            if ( $categories ) {
                echo '<div class="single-categories">';
                foreach ( $categories as $category ) {
                    echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="cat-link cat-id-' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</a>';
                }
                echo '</div>';
            }
            ?>

            <?php if ( $hide_title !== '1' ) : ?>
            <h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="single-meta">
                <span class="post-date"><i class="fa-regular fa-clock"></i> <time datetime="<?php echo esc_attr( get_the_date('c') ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date() ); ?></time></span>
                <?php if ( get_the_modified_date('Ymd') > get_the_date('Ymd') ) : ?>
                    <span class="post-update"><i class="fa-solid fa-rotate-right"></i> <time datetime="<?php echo esc_attr( get_the_modified_date('c') ); ?>" itemprop="dateModified"><?php echo esc_html( get_the_modified_date() ); ?></time></span>
                <?php else : ?>
                    <meta itemprop="dateModified" content="<?php echo esc_attr( get_the_modified_date('c') ); ?>">
                <?php endif; ?>
                <?php
                $options_s      = get_option( 'awbase_settings', awbase_get_default_settings() );
                $s_author_name  = ! empty( $options_s['schema_author_name'] )    ? $options_s['schema_author_name']    : get_the_author();
                $s_author_alt   = ! empty( $options_s['schema_author_altname'] ) ? $options_s['schema_author_altname'] : '';
                $s_author_url   = ! empty( $options_s['schema_author_url'] )     ? $options_s['schema_author_url']     : get_author_posts_url( get_the_author_meta('ID') );
                ?>
                <span class="post-author" itemprop="editor author creator copyrightHolder" itemscope itemtype="https://schema.org/Person">
                    <meta itemprop="url" content="<?php echo esc_url( $s_author_url ); ?>">
                    <?php if ( $s_author_alt ) : ?>
                        <meta itemprop="alternateName" content="<?php echo esc_attr( $s_author_alt ); ?>">
                    <?php endif; ?>
                    <i class="fa-solid fa-pen-nib"></i> <span itemprop="name"><?php echo esc_html( $s_author_name ); ?></span>
                </span>
            </div>
        </header>

        <?php if ( has_post_thumbnail() && $hide_eyecatch !== '1' ) :
            $thumb_id  = get_post_thumbnail_id();
            $thumb_src = wp_get_attachment_image_src( $thumb_id, 'full' );
        ?>
            <figure class="single-eyecatch" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <?php if ( $thumb_src ) : ?>
                    <meta itemprop="url" content="<?php echo esc_url( $thumb_src[0] ); ?>">
                    <meta itemprop="width" content="<?php echo esc_attr( $thumb_src[1] ); ?>">
                    <meta itemprop="height" content="<?php echo esc_attr( $thumb_src[2] ); ?>">
                <?php endif; ?>
                <?php the_post_thumbnail('full'); ?>
            </figure>
        <?php endif; ?>

        <?php
        // Review star rating (アイキャッチ下に★評価表示)
        $review_enable = get_post_meta( $post_id, 'awbase_review_enable', true );
        $review_rating = (float) get_post_meta( $post_id, 'awbase_review_rating', true );
        if ( $review_enable === '1' && $review_rating > 0 ) :
            $best_rating = (float) ( get_post_meta( $post_id, 'awbase_review_best_rating', true ) ?: 5 );
            // 最高5★に正規化
            $normalized = ( $best_rating > 0 ) ? round( ( $review_rating / $best_rating ) * 5 * 2 ) / 2 : $review_rating;
            $normalized = min( 5, max( 0, $normalized ) );
            $full  = (int) floor( $normalized );
            $half  = ( ( $normalized - $full ) >= 0.5 ) ? 1 : 0;
            $empty = 5 - $full - $half;
        ?>
        <div class="single-review-stars">
            <div class="review-stars" aria-label="評価: <?php echo esc_attr( $review_rating ); ?> / <?php echo esc_attr( $best_rating ); ?>">
                <?php for ( $i = 0; $i < $full; $i++ ) : ?>
                    <i class="fa-solid fa-star review-star-full" aria-hidden="true"></i>
                <?php endfor; ?>
                <?php if ( $half ) : ?>
                    <i class="fa-solid fa-star-half-stroke review-star-half" aria-hidden="true"></i>
                <?php endif; ?>
                <?php for ( $i = 0; $i < $empty; $i++ ) : ?>
                    <i class="fa-regular fa-star review-star-empty" aria-hidden="true"></i>
                <?php endfor; ?>
                <span class="review-rating-num"><?php echo esc_html( $review_rating . ' / ' . $best_rating ); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $sns_opts = get_option( 'awbase_settings', awbase_get_default_settings() );
        if ( ! empty( $sns_opts['sns_share_below_eyecatch'] ) && $sns_opts['sns_share_below_eyecatch'] == '1' ) {
            get_template_part( 'template-parts/sns-share' );
        }
        ?>

        <div class="single-content entry-content" itemprop="mainEntityOfPage">
            <?php if ( $hide_toc !== '1' ) : ?>
                <details id="awbase-toc-container" class="toc" open>
                    <summary class="toc-title">目次</summary>
                    <ul class="toc-list"></ul>
                </details>
            <?php endif; ?>

            <?php if ( $read_min > 0 ) : ?>
                <p class="post-reading-time"><i class="fa-solid fa-book-open"></i> 読了時間: 約<?php echo $read_min; ?>分</p>
            <?php endif; ?>

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
            $tags = get_the_tags();
            if ( $tags ) {
                echo '<div class="tagcloud">';
                foreach ( $tags as $tag ) {
                    echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="tag-link-' . esc_attr( $tag->term_id ) . '">#' . esc_html( $tag->name ) . '</a>';
                }
                echo '</div>';
            }
            ?>
        </footer>

        <?php
        if ( ! empty( $sns_opts['sns_share_above_author'] ) && $sns_opts['sns_share_above_author'] == '1' ) {
            get_template_part( 'template-parts/sns-share' );
        }
        ?>
        <?php get_template_part( 'template-parts/author-box' ); ?>
        <?php get_template_part( 'template-parts/related-posts' ); ?>
        <?php get_template_part( 'template-parts/post-nav' ); ?>
        <?php if ( comments_open() || get_comments_number() ) : ?>
            <?php comments_template(); ?>
        <?php endif; ?>

    </article>
<?php endwhile; ?>

<?php get_footer(); ?>

<?php
// AW-Base Popular Card – slim (thumb + date + title only)
if ( ! defined( 'ABSPATH' ) ) exit;

$thumb_id = (int) get_post_thumbnail_id();

// 更新日 > 公開日 なら更新日を表示
$mod  = get_the_modified_date( 'Y.m.d' );
$pub  = get_the_date( 'Y.m.d' );
$date = ( $mod > $pub ) ? $mod : $pub;
?>
<article class="popular-card-slim">
    <a href="<?php the_permalink(); ?>" class="popular-card-slim-link">
        <figure class="popular-card-slim-thumb">
            <?php echo awbase_picture( $thumb_id, 'awbase-card-sm', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
        </figure>
        <div class="popular-card-slim-body">
            <time class="popular-card-slim-date"
                  datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>">
                <?php echo esc_html( $date ); ?>
            </time>
            <h3 class="popular-card-slim-title"><?php the_title(); ?></h3>
        </div>
    </a>
</article>

<?php
// AW-Base Post Navigation – 前の記事 / 次の記事
if ( ! defined( 'ABSPATH' ) ) exit;

$prev_post = get_previous_post();
$next_post = get_next_post();
if ( ! $prev_post && ! $next_post ) return;
?>
<nav class="post-nav" aria-label="記事ナビゲーション">

    <?php if ( $prev_post ) : ?>
    <div class="post-nav-item post-nav-prev">
        <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="post-nav-link">
            <?php if ( has_post_thumbnail( $prev_post ) ) : ?>
            <div class="post-nav-thumb">
                <img src="<?php echo esc_url( get_the_post_thumbnail_url( $prev_post, 'thumbnail' ) ); ?>" alt="" loading="lazy" decoding="async">
            </div>
            <?php endif; ?>
            <div class="post-nav-body">
                <span class="post-nav-label"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> 前の記事</span>
                <p class="post-nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></p>
            </div>
        </a>
    </div>
    <?php else : ?>
    <div class="post-nav-item"></div>
    <?php endif; ?>

    <?php if ( $next_post ) : ?>
    <div class="post-nav-item post-nav-next">
        <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="post-nav-link">
            <?php if ( has_post_thumbnail( $next_post ) ) : ?>
            <div class="post-nav-thumb">
                <img src="<?php echo esc_url( get_the_post_thumbnail_url( $next_post, 'thumbnail' ) ); ?>" alt="" loading="lazy" decoding="async">
            </div>
            <?php endif; ?>
            <div class="post-nav-body">
                <span class="post-nav-label">次の記事 <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                <p class="post-nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></p>
            </div>
        </a>
    </div>
    <?php else : ?>
    <div class="post-nav-item"></div>
    <?php endif; ?>

</nav>

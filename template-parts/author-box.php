<?php
// AW-Base Author Box Template Part
if ( ! defined( 'ABSPATH' ) ) exit;
$author_id = get_the_author_meta('ID');
$author_desc = get_the_author_meta('description');
$author_url = get_the_author_meta('url');
?>
<div class="author-box">
    <div class="author-widget-name">この記事を書いた人</div>
    <figure class="author-thumb">
        <?php echo get_avatar( $author_id, 100 ); ?>
    </figure>
    <div class="author-content">
        <div class="author-name">
            <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a>
        </div>
        <div class="author-description">
            <p><?php echo wp_kses_post( $author_desc ); ?></p>
        </div>
        <div class="sns-follow-buttons">
            <?php if ($author_url) : ?>
                <a href="<?php echo esc_url($author_url); ?>" class="follow-button website-button" target="_blank" rel="noopener noreferrer" title="Webサイト"><span class="fa-solid fa-house"></span></a>
            <?php endif; ?>
            <!-- Other SNS buttons can be added via User Meta extensions later -->
        </div>
    </div>
</div>

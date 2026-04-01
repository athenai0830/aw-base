<?php
// AW-Base Author Box Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$author_id   = get_the_author_meta('ID');
$author_desc = get_the_author_meta('description');
$author_url  = get_the_author_meta('url');

// SNS links – Cocoon互換 (unprefixed meta keys)
$sns_links = [
    'twitter'   => [ 'icon' => 'fa-brands fa-x-twitter',  'class' => 'twitter-button',   'label' => 'X (Twitter)' ],
    'facebook'  => [ 'icon' => 'fa-brands fa-facebook-f', 'class' => 'facebook-button',  'label' => 'Facebook' ],
    'instagram' => [ 'icon' => 'fa-brands fa-instagram',  'class' => 'instagram-button', 'label' => 'Instagram' ],
    'youtube'   => [ 'icon' => 'fa-brands fa-youtube',    'class' => 'youtube-button',   'label' => 'YouTube' ],
    'linkedin'  => [ 'icon' => 'fa-brands fa-linkedin-in','class' => 'linkedin-button',  'label' => 'LinkedIn' ],
    'github'    => [ 'icon' => 'fa-brands fa-github',     'class' => 'github-button',    'label' => 'GitHub' ],
];
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
            <?php if ( $author_url ) : ?>
                <a href="<?php echo esc_url( $author_url ); ?>" class="follow-button website-button" target="_blank" rel="noopener noreferrer" aria-label="Webサイト">
                    <i class="fa-solid fa-house"></i>
                </a>
            <?php endif; ?>
            <?php foreach ( $sns_links as $key => $link ) :
                $url = get_user_meta( $author_id, $key, true );
                if ( ! $url ) continue;
            ?>
                <a href="<?php echo esc_url( $url ); ?>" class="follow-button <?php echo esc_attr( $link['class'] ); ?>"
                   target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link['label'] ); ?>">
                    <i class="<?php echo esc_attr( $link['icon'] ); ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

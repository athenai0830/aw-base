<?php
// AW-Base SNS Share Buttons
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option( 'awbase_settings', awbase_get_default_settings() );
$url     = rawurlencode( get_permalink() );
$title   = rawurlencode( get_the_title() );
$rss_url = rawurlencode( get_bloginfo('rss2_url') );
?>
<div class="sns-share-wrap">
    <p class="sns-share-label">Share</p>
    <div class="sns-share-buttons">
        <?php if ( ! empty( $options['sns_share_twitter'] ) && $options['sns_share_twitter'] == '1' ) : ?>
        <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>"
           class="sns-share-btn sns-share-twitter" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter) でシェア">
            <i class="fa-brands fa-x-twitter"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_facebook'] ) && $options['sns_share_facebook'] == '1' ) : ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>"
           class="sns-share-btn sns-share-facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook でシェア">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_line'] ) && $options['sns_share_line'] == '1' ) : ?>
        <a href="https://social-plugins.line.me/lineit/share?url=<?php echo $url; ?>"
           class="sns-share-btn sns-share-line" target="_blank" rel="noopener noreferrer" aria-label="LINE でシェア">
            <i class="fa-brands fa-line"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_pocket'] ) && $options['sns_share_pocket'] == '1' ) : ?>
        <a href="https://getpocket.com/save?url=<?php echo $url; ?>&title=<?php echo $title; ?>"
           class="sns-share-btn sns-share-pocket" target="_blank" rel="noopener noreferrer" aria-label="Pocket に保存">
            <i class="fa-brands fa-get-pocket"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_hatena'] ) && $options['sns_share_hatena'] == '1' ) : ?>
        <a href="https://b.hatena.ne.jp/add?mode=confirm&url=<?php echo $url; ?>&title=<?php echo $title; ?>"
           class="sns-share-btn sns-share-hatena" target="_blank" rel="noopener noreferrer" aria-label="はてなブックマークに追加">
            B!
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_feedly'] ) && $options['sns_share_feedly'] == '1' ) : ?>
        <a href="https://feedly.com/i/subscription/feed/<?php echo $rss_url; ?>"
           class="sns-share-btn sns-share-feedly" target="_blank" rel="noopener noreferrer" aria-label="Feedly で購読">
            <i class="fa-solid fa-rss"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_pinterest'] ) && $options['sns_share_pinterest'] == '1' ) : ?>
        <a href="https://pinterest.com/pin/create/button/?url=<?php echo $url; ?>&description=<?php echo $title; ?>"
           class="sns-share-btn sns-share-pinterest" target="_blank" rel="noopener noreferrer" aria-label="Pinterest に保存">
            <i class="fa-brands fa-pinterest-p"></i>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $options['sns_share_copy'] ) && $options['sns_share_copy'] == '1' ) : ?>
        <button type="button" class="sns-share-btn sns-share-copy" data-url="<?php echo esc_attr( get_permalink() ); ?>" aria-label="URLをコピー">
            <i class="fa-solid fa-link"></i>
        </button>
        <?php endif; ?>
    </div>
</div>

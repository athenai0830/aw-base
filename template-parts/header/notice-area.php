<?php
// AW-Base Notice Area Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = awbase_get_settings();
$text = $options['notice_text'];
$url = $options['notice_url'];

if ( empty( $text ) ) return;
?>
<div class="notice-area">
    <div class="site-container notice-inner">
        <?php if ( ! empty($url) ) : ?>
            <a href="<?php echo esc_url($url); ?>" class="notice-link"><?php echo esc_html($text); ?></a>
        <?php else: ?>
            <span class="notice-text"><?php echo esc_html($text); ?></span>
        <?php endif; ?>
    </div>
</div>

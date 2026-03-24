<?php
// AW-Base Notice Area Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());
$text = $options['notice_text'];
$url = $options['notice_url'];

if ( empty( $text ) ) return;
?>
<div class="notice-area">
    <div class="site-container">
        <span class="notice-icon"><i class="fa-solid fa-bullhorn"></i></span>
        <?php if ( ! empty($url) ) : ?>
            <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($text); ?></a>
        <?php else: ?>
            <span><?php echo esc_html($text); ?></span>
        <?php endif; ?>
    </div>
</div>

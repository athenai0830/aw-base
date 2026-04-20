<?php
// AW-Base First View Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = awbase_get_settings();

$dot_class = $options['fv_dot_pattern'] == '1' ? 'fv-dot-pattern' : '';

// FV logo size style
$fv_logo_w = intval($options['fv_logo_width'] ?? 300);
$fv_logo_h = intval($options['fv_logo_height'] ?? 0);
$fv_logo_style = '';
if ( $fv_logo_w > 0 ) {
    $fv_logo_style .= 'width:' . $fv_logo_w . 'px;';
}
if ( $fv_logo_h > 0 ) {
    $fv_logo_style .= 'height:' . $fv_logo_h . 'px;';
} else {
    $fv_logo_style .= 'height:auto;';
}
$fv_logo_url = ! empty($options['fv_logo_url']) ? esc_url($options['fv_logo_url']) : '';

// FV bg image size resolution
$fv_bg_w = '';
$fv_bg_h = '';
if ( ! empty($options['fv_bg_image']) ) {
    $attachment_id = attachment_url_to_postid( $options['fv_bg_image'] );
    if ( $attachment_id ) {
        $img_src = wp_get_attachment_image_src( $attachment_id, 'full' );
        if ( $img_src ) {
            $fv_bg_w = $img_src[1];
            $fv_bg_h = $img_src[2];
        }
    }
}
?>
<div class="first-view <?php echo esc_attr($dot_class); ?>">
    <?php if ( ! empty($options['fv_bg_image']) ) : ?>
        <img class="fv-bg-img skip-lazy no-lazy" src="<?php echo esc_url($options['fv_bg_image']); ?>" alt="" fetchpriority="high" loading="eager" decoding="async" data-no-lazy="1" <?php echo $fv_bg_w ? 'width="' . esc_attr($fv_bg_w) . '"' : ''; ?> <?php echo $fv_bg_h ? 'height="' . esc_attr($fv_bg_h) . '"' : ''; ?>>
    <?php endif; ?>
    <div class="fv-overlay"></div>
    <div class="fv-content">
        <?php if ( ! empty($options['fv_logo_image']) ) :
            $img_w_attr = $fv_logo_w > 0 ? ' width="' . $fv_logo_w . '"' : '';
            $img_h_attr = $fv_logo_h > 0 ? ' height="' . $fv_logo_h . '"' : '';
            $img_tag = '<img class="fv-logo" src="' . esc_url($options['fv_logo_image']) . '" alt="' . esc_attr( get_bloginfo('name') ) . '" style="' . esc_attr($fv_logo_style) . '"' . $img_w_attr . $img_h_attr . ' fetchpriority="high" loading="eager">';
            if ( $fv_logo_url ) :
        ?>
            <a href="<?php echo $fv_logo_url; ?>" class="fv-logo-link"><?php echo $img_tag; ?></a>
        <?php   else :
                echo $img_tag;
            endif;
        else : ?>
            <p class="fv-site-title"><?php echo esc_html( get_bloginfo('name') ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty($options['fv_catchphrase']) ) : ?>
            <div class="fv-catchphrase"><?php echo wp_kses_post( $options['fv_catchphrase'] ); ?></div>
        <?php endif; ?>

        <?php if ( ! empty($options['fv_substack_enable']) && $options['fv_substack_enable'] === '1' && ! empty($options['fv_substack_url']) ) : ?>
            <div class="fv-substack-wrap">
                <a href="<?php echo esc_url($options['fv_substack_url']); ?>" class="fv-substack-btn" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html( ! empty($options['fv_substack_text']) ? $options['fv_substack_text'] : 'Substack で購読' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <button class="fv-scroll-btn" id="fv-scroll-btn" aria-label="コンテンツへスクロール">
        <i class="fa-solid fa-angles-down" aria-hidden="true"></i>
        <span class="fv-scroll-text">Scroll</span>
    </button>
</div>

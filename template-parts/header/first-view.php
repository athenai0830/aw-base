<?php
// AW-Base First View Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());

$bg_image_url = ! empty($options['fv_bg_image']) ? esc_url($options['fv_bg_image']) : '';
$bg_style = $bg_image_url ? 'background-image: url(' . $bg_image_url . ');' : '';

$dot_class = $options['fv_dot_pattern'] ? 'fv-dot-pattern' : '';
?>
<div class="first-view <?php echo esc_attr($dot_class); ?>" style="<?php echo $bg_style; ?>">
    <div class="fv-overlay"></div>
    <div class="fv-content">
        <?php if ( ! empty($options['fv_logo_image']) ) : ?>
            <img class="fv-logo" src="<?php echo esc_url($options['fv_logo_image']); ?>" alt="FV Logo" <?php echo $options['lazy_load_fv_exclude'] ? 'fetchpriority="high"' : ''; ?>>
        <?php endif; ?>

        <?php if ( ! empty($options['fv_catchphrase']) ) : ?>
            <p class="fv-catchphrase"><?php echo esc_html($options['fv_catchphrase']); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
// AW-Base Header Area Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());
$align = $options['header_logo_align'] ? $options['header_logo_align'] : 'left';
$logo_url = ! empty($options['logo_url']) ? esc_url($options['logo_url']) : home_url('/');
?>
<header id="masthead" class="site-header header-align-<?php echo esc_attr($align); ?>">
    <div class="site-container header-inner">
        <div class="site-branding">
            <?php if ( ! empty($options['logo_image']) ) : ?>
                <a href="<?php echo $logo_url; ?>" class="site-logo">
                    <img src="<?php echo esc_url($options['logo_image']); ?>" 
                         width="<?php echo esc_attr($options['logo_width']); ?>" 
                         height="<?php echo esc_attr($options['logo_height']); ?>" 
                         alt="<?php echo esc_attr($options['logo_alt']); ?>" 
                         <?php echo $options['lazy_load'] ? 'loading="eager"' : ''; // logos are often eager ?>>
                </a>
            <?php else : ?>
                <h1 class="site-title"><a href="<?php echo $logo_url; ?>"><?php bloginfo( 'name' ); ?></a></h1>
            <?php endif; ?>

            <?php if ( ! empty($options['catchphrase']) ) : ?>
                <p class="site-catchphrase"><?php echo esc_html($options['catchphrase']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</header>

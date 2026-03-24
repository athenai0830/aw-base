<?php
// AW-Base Global Nav Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());
$align = $options['nav_align'] ? $options['nav_align'] : 'center';
?>
<nav id="site-navigation" class="global-nav nav-align-<?php echo esc_attr($align); ?>">
    <div class="site-container nav-inner">
        <?php
        if ( has_nav_menu('primary') ) {
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => false,
            ) );
        } else {
            echo '<ul><li><a href="' . home_url('/') . '">HOME</a></li></ul>';
        }
        ?>
    </div>
</nav>

<?php
// AW-Base Global Nav Template Part
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());
$align = $options['nav_align'] ? $options['nav_align'] : 'center';
?>
<nav id="site-navigation" class="global-nav nav-align-<?php echo esc_attr($align); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
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
        <button class="nav-toggle" aria-label="メニューを開く" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    <div class="mobile-nav-drawer" id="mobile-nav-drawer" aria-hidden="true">
        <div class="site-container">
            <?php
            if ( has_nav_menu('mobile') ) {
                wp_nav_menu( array(
                    'theme_location' => 'mobile',
                    'container'      => false,
                ) );
            } elseif ( has_nav_menu('primary') ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                ) );
            }
            ?>
        </div>
    </div>
</nav>

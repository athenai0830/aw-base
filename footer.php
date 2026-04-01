<?php
// AW-Base Footer
if ( ! defined( 'ABSPATH' ) ) exit;

$saved         = get_option( 'awbase_settings', [] );
$options       = is_array( $saved ) ? array_merge( awbase_get_default_settings(), $saved ) : awbase_get_default_settings();
$_show_sidebar = awbase_get_layout()['show_sidebar'];
?>
        </main><!-- #primary -->

        <?php if ( $_show_sidebar ) : ?>
            <aside id="secondary" class="site-sidebar">
                <?php get_sidebar(); ?>
            </aside><!-- #secondary -->
        <?php endif; ?>

    </div><!-- .site-main-wrap -->
</div><!-- .site-container -->

<footer class="site-footer" itemscope itemtype="https://schema.org/WPFooter">
    <div class="site-container">
        
        <?php if ( ! empty($options['footer_logo_image']) ) : ?>
        <div class="footer-logo" style="text-align: <?php echo esc_attr($options['footer_logo_align']); ?>;">
            <?php 
            $url = ! empty($options['footer_logo_url']) ? esc_url($options['footer_logo_url']) : home_url('/');
            ?>
            <a href="<?php echo $url; ?>">
                <img src="<?php echo esc_url($options['footer_logo_image']); ?>" width="<?php echo esc_attr($options['footer_logo_width']); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" loading="lazy">
            </a>
        </div>
        <?php endif; ?>

        <?php if ( has_nav_menu('footer') ) : ?>
        <div class="footer-nav footer-nav-align-<?php echo esc_attr($options['footer_nav_align']); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'footer',
                'container'      => false,
                'depth'          => 1,
            ) );
            ?>
        </div>
        <?php endif; ?>

        <div class="footer-copyright">
            <?php echo esc_html($options['footer_copyright']); ?>
        </div>

    </div>
</footer>

<?php if ( is_active_sidebar( 'footer-bottom' ) ) : ?>
<div class="footer-bottom-area">
    <?php dynamic_sidebar( 'footer-bottom' ); ?>
</div>
<?php endif; ?>

</div><!-- #page -->

<button id="pagetop-btn" class="pagetop-btn" aria-label="ページトップへ戻る">
    <i class="fa-solid fa-angles-up" aria-hidden="true"></i>
    <span class="pagetop-text">TOP</span>
</button>

<?php wp_footer(); ?>
</body>
</html>

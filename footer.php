<?php
// AW-Base Footer
if ( ! defined( 'ABSPATH' ) ) exit;
$options = get_option('awbase_settings', awbase_get_default_settings());
?>
        </main><!-- #primary -->

        <?php if ( $options['columns'] !== '1' ) : ?>
            <aside id="secondary" class="site-sidebar">
                <?php get_sidebar(); ?>
            </aside><!-- #secondary -->
        <?php endif; ?>

    </div><!-- .site-main-wrap -->
</div><!-- .site-container -->

<footer class="site-footer">
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
        <div class="footer-nav" style="text-align: <?php echo esc_attr($options['footer_nav_align']); ?>;">
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

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>

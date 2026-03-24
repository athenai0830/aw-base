<?php
// AW-Base Performance Optimizations
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());

// 1. Disable unused image sizes
if ( $options['disable_image_sizes'] === '1' ) {
    function awbase_disable_image_sizes($sizes) {
        unset($sizes['thumbnail']);
        unset($sizes['medium']);
        unset($sizes['medium_large']);
        unset($sizes['large']);
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'awbase_disable_image_sizes');
    add_filter('big_image_size_threshold', '__return_false');
}

// 2. Limit reCAPTCHA loading
if ( $options['recaptcha_limit'] === '1' ) {
    function awbase_recaptcha_limitation() {
        if ( ! is_page( 'contact' ) ) { // Assuming 'contact' is the slug
            wp_deregister_script( 'google-recaptcha' );
            wp_deregister_script( 'wpcf7-recaptcha' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'awbase_recaptcha_limitation', 100 );
}

// 3. Lazy Load adjustments
if ( $options['lazy_load'] !== '1' ) {
    // Disable native lazy load entirely
    add_filter( 'wp_lazy_loading_enabled', '__return_false' );
} else {
    // Tune lazy load
    if ( $options['lazy_load_thumb_exclude'] === '1' ) {
        // Exclude first image (usually thumbnail) from lazy loading
        add_filter( 'wp_omit_loading_attr_threshold', function($omit_threshold){
            return 1; // omit loading attribute for the first 1 image(s) in the loop/content
        });
    }
}

// 4. Basic CSS Minify snippet (If enabled, compress inline CSS from style-output)
// Since we generate css dynamically in style-output.php, we naturally don't output lots of whitespace,
// but we could compress the HTML output buffer if needed, though usually plugins handle this better.
// We'll leave it as a hookable logic for future extension.

// 5. Preload FV Image
function awbase_preload_header_image() {
    $options = get_option('awbase_settings', awbase_get_default_settings());
    if ( $options['show_fv'] === '1' && !empty($options['fv_bg_image']) && (is_front_page() || is_home()) ) {
        echo '<link rel="preload" as="image" href="' . esc_url($options['fv_bg_image']) . '" fetchpriority="high">' . "\n";
    }
}
add_action( 'wp_head', 'awbase_preload_header_image', 1 );

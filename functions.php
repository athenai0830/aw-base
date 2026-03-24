<?php
// AW-Base Theme Functions
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Theme Setup
function awbase_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'script', 'style' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    
    register_nav_menus( array(
        'primary' => 'グローバルナビゲーション',
        'footer'  => 'フッターナビゲーション',
        'mobile'  => 'モバイルナビゲーション',
    ) );
}
add_action( 'after_setup_theme', 'awbase_setup' );

// 2. Enqueue Scripts and Styles
function awbase_enqueue_scripts() {
    // Basic Style
    $theme_version = wp_get_theme()->get('Version');
    wp_enqueue_style( 'awbase-style', get_stylesheet_uri(), array(), $theme_version );
    
    // Front-end JS (if any)
    // wp_enqueue_script( 'awbase-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, true );

    // Font Awesome 6 (Can be toggled via admin settings later)
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );
}
add_action( 'wp_enqueue_scripts', 'awbase_enqueue_scripts' );


// 3. Includes
// Define Theme directory paths
define( 'AWBASE_DIR', get_template_directory() );
define( 'AWBASE_URI', get_template_directory_uri() );

// Files will be required here once they are created
require_once AWBASE_DIR . '/inc/admin.php';
require_once AWBASE_DIR . '/inc/customizer.php';
require_once AWBASE_DIR . '/inc/style-output.php';
// require_once AWBASE_DIR . '/inc/seo.php';
// require_once AWBASE_DIR . '/inc/ai-tracker.php';
// require_once AWBASE_DIR . '/inc/performance.php';
// require_once AWBASE_DIR . '/inc/shortcodes.php';
// require_once AWBASE_DIR . '/inc/widgets.php';
// require_once AWBASE_DIR . '/inc/llms-txt.php';

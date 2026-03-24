<?php
// AW-Base Admin Settings page
if ( ! defined( 'ABSPATH' ) ) exit;

// Register Menu
function awbase_add_admin_menu() {
    add_menu_page(
        'AW-Base 設定',
        'AW-Base',
        'manage_options',
        'awbase_settings',
        'awbase_settings_page',
        'dashicons-admin-generic',
        60
    );
    // Submenus
    add_submenu_page(
        'awbase_settings',
        'AW-Base 設定',
        '設定',
        'manage_options',
        'awbase_settings',
        'awbase_settings_page'
    );
    // Future expansion: Child Theme Settings
    // add_submenu_page('awbase_settings', '子テーマ設定', '子テーマ設定', 'manage_options', 'awbase_child_settings', 'awbase_child_settings_page');
}
add_action( 'admin_menu', 'awbase_add_admin_menu' );

// Register Settings
function awbase_register_settings() {
    register_setting( 'awbase_settings_group', 'awbase_settings', array('sanitize_callback' => 'awbase_sanitize_settings') );
}
add_action( 'admin_init', 'awbase_register_settings' );

// Default settings
function awbase_get_default_settings() {
    return array(
        'color_pattern' => 'original',
        'font_family' => 'hiragino',
        'columns' => '2',
        'sidebar_position' => 'right',
        'site_max_width' => 1200,
        'content_width' => 760,
        'sidebar_width' => 300,
        'logo_image' => '',
        'logo_width' => 300,
        'logo_height' => 50,
        'logo_url' => '',
        'logo_alt' => get_bloginfo('name'),
        'header_logo_align' => 'left',
        'catchphrase' => '',
        
        'show_global_nav' => '1',
        'show_header' => '1',
        'show_notice' => '0',
        'show_fv' => '0',
        
        'header_pattern' => '1',
        'nav_align' => 'center',
        'notice_text' => '',
        'notice_url' => '',
        
        'fv_bg_image' => '',
        'fv_height_unit' => 'vh',
        'fv_height' => 100,
        'fv_logo_image' => '',
        'overlay_color' => 'black',
        'overlay_opacity' => 28,
        'fv_dot_pattern' => '1',
        'fv_catchphrase' => '',
        
        'sitemap_enable' => '1',
        'llms_txt_enable' => '1',
        'llms_txt_content' => "# " . get_bloginfo('name') . "\n\n> " . get_bloginfo('description'),
        'noindex_search' => '0',
        'noindex_404' => '1',
        'noindex_date' => '0',
        'noindex_author' => '0',
        'noindex_paged' => '0',
        'noindex_tag' => '0',
        
        'ai_tracking_enable' => '1',
        
        'lazy_load' => '1',
        'lazy_load_fv_exclude' => '1',
        'lazy_load_thumb_exclude' => '1',
        'css_minify' => '1',
        'js_minify' => '0',
        'disable_image_sizes' => '1',
        'recaptcha_limit' => '1',
        
        'footer_logo_image' => '',
        'footer_logo_width' => 160,
        'footer_logo_url' => '',
        'footer_logo_align' => 'center',
        'footer_nav_align' => 'center',
        'footer_copyright' => '© ' . date('Y') . ' ' . get_bloginfo('name')
    );
}

// Get Theme Option
function get_awbase_option( $key ) {
    $options = get_option( 'awbase_settings', awbase_get_default_settings() );
    $defaults = awbase_get_default_settings();
    return isset( $options[ $key ] ) ? $options[ $key ] : (isset($defaults[$key]) ? $defaults[$key] : '');
}

// Sanitize Settings
function awbase_sanitize_settings( $input ) {
    $output = array();
    $defaults = awbase_get_default_settings();
    foreach ( $defaults as $key => $default_val ) {
        if ( isset( $input[ $key ] ) ) {
            // Very basic sanitize (should refine based on type)
            if ( is_numeric( $input[ $key ] ) && !in_array($key, ['color_pattern', 'font_family', 'columns', 'sidebar_position', 'header_logo_align', 'header_pattern', 'nav_align', 'fv_height_unit', 'overlay_color', 'footer_logo_align', 'footer_nav_align', 'llms_txt_content']) ) {
                $output[ $key ] = intval( $input[ $key ] );
            } else if ( $key === 'llms_txt_content' ) {
                $output[ $key ] = wp_kses_post( $input[ $key ] );
            } else {
                $output[ $key ] = sanitize_text_field( $input[ $key ] );
            }
        } else {
            // Unchecked checkboxes
            if ( in_array($key, ['show_global_nav', 'show_header', 'show_notice', 'show_fv', 'fv_dot_pattern', 'sitemap_enable', 'llms_txt_enable', 'noindex_search', 'noindex_404', 'noindex_date', 'noindex_author', 'noindex_paged', 'noindex_tag', 'ai_tracking_enable', 'lazy_load', 'lazy_load_fv_exclude', 'lazy_load_thumb_exclude', 'css_minify', 'js_minify', 'disable_image_sizes', 'recaptcha_limit']) ) {
                $output[ $key ] = '0';
            } else {
                $output[ $key ] = $default_val;
            }
        }
    }
    return $output;
}

// Enqueue Admin Scripts/Styles
function awbase_admin_enqueue_scripts( $hook ) {
    if ( $hook != 'toplevel_page_awbase_settings' ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style( 'awbase-admin-style', get_template_directory_uri() . '/assets/css/admin.css' );
    wp_enqueue_script( 'awbase-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), null, true );
}
add_action( 'admin_enqueue_scripts', 'awbase_admin_enqueue_scripts' );

// Settings Page HTML
function awbase_settings_page() {
    $options = get_option( 'awbase_settings', awbase_get_default_settings() );
    $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'general';
    ?>
    <div class="wrap">
        <h1>AW-Base 設定</h1>
        <?php settings_errors(); ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=awbase_settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">一般設定</a>
            <a href="?page=awbase_settings&tab=header" class="nav-tab <?php echo $active_tab == 'header' ? 'nav-tab-active' : ''; ?>">ヘッダー/ナビ</a>
            <a href="?page=awbase_settings&tab=firstview" class="nav-tab <?php echo $active_tab == 'firstview' ? 'nav-tab-active' : ''; ?>">ファーストビュー</a>
            <a href="?page=awbase_settings&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>">SEO設定</a>
            <a href="?page=awbase_settings&tab=performance" class="nav-tab <?php echo $active_tab == 'performance' ? 'nav-tab-active' : ''; ?>">高速化</a>
            <a href="?page=awbase_settings&tab=footer" class="nav-tab <?php echo $active_tab == 'footer' ? 'nav-tab-active' : ''; ?>">フッター</a>
            <a href="?page=awbase_settings&tab=ai" class="nav-tab <?php echo $active_tab == 'ai' ? 'nav-tab-active' : ''; ?>">AIトラッキング</a>
        </h2>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'awbase_settings_group' );
            require_once AWBASE_DIR . '/inc/admin/tab-' . $active_tab . '.php';    
            submit_button( '設定を保存' );
            ?>
        </form>
    </div>
    <?php
}

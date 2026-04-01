<?php
// AW-Base Header
if ( ! defined( 'ABSPATH' ) ) exit;

$saved       = get_option( 'awbase_settings', [] );
$options     = is_array( $saved ) ? array_merge( awbase_get_default_settings(), $saved ) : awbase_get_default_settings();
$is_top      = is_front_page() || is_home();
$layout_data = awbase_get_layout();
$layout      = $layout_data['layout_class'];
$sidebar     = $layout_data['sidebar_class'];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class("$layout $sidebar"); ?> itemscope itemtype="https://schema.org/WebPage">
<?php wp_body_open(); ?>

<div id="page" class="site">

<?php
// Build header components based on selected pattern
// Use sub-page pattern on non-front pages if set
$pattern = ( ! $is_top && ! empty( $options['header_pattern_sub'] ) )
    ? $options['header_pattern_sub']
    : $options['header_pattern'];

$parts = [];
if ( $pattern === '1' ) {
    $parts = ['nav', 'header', 'notice', 'fv'];
} elseif ( $pattern === '2' ) {
    $parts = ['notice', 'fv', 'header', 'nav'];
} elseif ( $pattern === '3' ) {
    $parts = ['fv', 'header', 'notice', 'nav'];
} elseif ( $pattern === '4' ) {
    $parts = ['header', 'nav', 'notice', 'fv'];
} elseif ( $pattern === '5' ) {
    $parts = ['fv', 'header', 'nav', 'notice'];
} else {
    $parts = ['nav', 'header', 'notice', 'fv'];
}

// Resolve show/hide per page type
$_resolve = function( $key ) use ( $options, $is_top ) {
    $check_key = $is_top ? $key : $key . '_sub';
    return ( $options[ $check_key ] ?? '' ) === '1';
};

foreach ( $parts as $part ) {
    if ( $part === 'nav' && $_resolve('show_global_nav') ) {
        get_template_part( 'template-parts/header/global-nav' );
    } elseif ( $part === 'header' && $_resolve('show_header') ) {
        get_template_part( 'template-parts/header/header-area' );
    } elseif ( $part === 'notice' && $_resolve('show_notice') ) {
        get_template_part( 'template-parts/header/notice-area' );
    } elseif ( $part === 'fv' && $options['show_fv'] === '1' && $is_top ) {
        get_template_part( 'template-parts/header/first-view' );
    }
}
?>

<div class="site-container">
    <?php awbase_breadcrumbs(); ?>
    <div class="site-main-wrap">
        <main id="primary" class="site-main" itemscope itemtype="https://schema.org/Blog">

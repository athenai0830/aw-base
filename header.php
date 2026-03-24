<?php
// AW-Base Header
if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option('awbase_settings', awbase_get_default_settings());
$layout  = $options['columns'] === '1' ? 'layout-1c' : 'layout-2c';
$sidebar = $options['sidebar_position'] === 'left' ? 'sidebar-left' : 'sidebar-right';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class("$layout $sidebar"); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

<?php
// Build header components based on selected pattern
$pattern = $options['header_pattern'];
$parts = [];
if ( $pattern === '1' ) {
    $parts = ['nav', 'header', 'notice', 'fv'];
} elseif ( $pattern === '2' ) {
    $parts = ['notice', 'fv', 'header', 'nav'];
} else { // 3
    $parts = ['fv', 'header', 'notice', 'nav'];
}

foreach ( $parts as $part ) {
    if ( $part === 'nav' && $options['show_global_nav'] ) {
        get_template_part( 'template-parts/header/global-nav' );
    } elseif ( $part === 'header' && $options['show_header'] ) {
        get_template_part( 'template-parts/header/header-area' );
    } elseif ( $part === 'notice' && $options['show_notice'] ) {
        get_template_part( 'template-parts/header/notice-area' );
    } elseif ( $part === 'fv' && $options['show_fv'] && (is_front_page() || is_home()) ) {
        get_template_part( 'template-parts/header/first-view' );
    }
}
?>

<div class="site-container">
    <div class="site-main-wrap">
        <main id="primary" class="site-main">

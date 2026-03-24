<?php
// AW-Base Dynamic CSS Output
if ( ! defined( 'ABSPATH' ) ) exit;

function awbase_dynamic_css() {
    $options = get_option('awbase_settings', awbase_get_default_settings());
    $pattern = $options['color_pattern'];
    $font    = $options['font_family'];

    $css_vars = [];

    // Colors
    switch ( $pattern ) {
        case 'blacktan':
            $css_vars['--bg-color'] = '#111111';
            $css_vars['--text-color'] = '#ffffff';
            $css_vars['--main-color'] = '#d1a166';
            $css_vars['--accent-color'] = '#a87a41';
            break;
        case 'chocotan':
            $css_vars['--bg-color'] = '#3e2723';
            $css_vars['--text-color'] = '#fff3e0';
            $css_vars['--main-color'] = '#d7ccc8';
            $css_vars['--accent-color'] = '#a1887f';
            break;
        case 'cream':
            $css_vars['--bg-color'] = '#fffdd0';
            $css_vars['--text-color'] = '#5d4037';
            $css_vars['--main-color'] = '#ffeb3b';
            $css_vars['--accent-color'] = '#ffc107';
            break;
        case 'bluetan':
            $css_vars['--bg-color'] = '#37474f';
            $css_vars['--text-color'] = '#eceff1';
            $css_vars['--main-color'] = '#81d4fa';
            $css_vars['--accent-color'] = '#4fc3f7';
            break;
        case 'white':
            $css_vars['--bg-color'] = '#ffffff';
            $css_vars['--text-color'] = '#333333';
            $css_vars['--main-color'] = '#eeeeee';
            $css_vars['--accent-color'] = '#cccccc';
            break;
        case 'original':
        default:
            $css_vars['--bg-color'] = '#F5F6F7';
            $css_vars['--text-color'] = '#333333';
            $css_vars['--main-color'] = '#11114d';
            $css_vars['--accent-color'] = '#c30e24';
            break;
    }

    // Overlay
    $overlay_c = $options['overlay_color'] === 'white' ? '255, 255, 255' : '0, 0, 0';
    $overlay_o = intval($options['overlay_opacity']) / 100;
    $css_vars['--fv-overlay'] = 'rgba(' . $overlay_c . ', ' . $overlay_o . ')';

    // Font
    if ( $font === 'meiryo' ) {
        $css_vars['--font-family'] = '"Meiryo", sans-serif';
    } else if ( $font === 'yugothic' ) {
        $css_vars['--font-family'] = '"Yu Gothic", "YuGothic", sans-serif';
    } else {
        $css_vars['--font-family'] = '"Hiragino Kaku Gothic ProN", "Hiragino Sans", sans-serif';
    }

    // Layout
    $css_vars['--site-max-width'] = intval($options['site_max_width']) . 'px';
    $css_vars['--content-width'] = intval($options['content_width']) . 'px';
    $css_vars['--sidebar-width'] = intval($options['sidebar_width']) . 'px';
    $css_vars['--fv-height'] = intval($options['fv_height']) . $options['fv_height_unit'];

    // Build CSS String
    $custom_css = ":root {\n";
    foreach ($css_vars as $k => $v) {
        $custom_css .= "  {$k}: {$v};\n";
    }
    $custom_css .= "}\n";

    wp_add_inline_style( 'awbase-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'awbase_dynamic_css', 20 );

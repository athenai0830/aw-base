<?php
// AW-Base Customizer Support
if ( ! defined( 'ABSPATH' ) ) exit;

// Register Customizer settings
function awbase_customize_register( $wp_customize ) {

    // Helper function to get default
    $defaults = awbase_get_default_settings();

    // Panel for AW-Base
    $wp_customize->add_panel( 'awbase_theme_options', array(
        'priority'       => 10,
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
        'title'          => 'AW-Base 独自設定',
        'description'    => 'AW-Baseテーマの基本設定を行います。',
    ) );

    // Section: General (Color & Layout)
    $wp_customize->add_section( 'awbase_general_section', array(
        'title'    => 'カラーとレイアウト',
        'panel'    => 'awbase_theme_options',
        'priority' => 10,
    ) );

    // Setting & Control: Color Pattern
    $wp_customize->add_setting( 'awbase_settings[color_pattern]', array(
        'type'              => 'option',
        'default'           => $defaults['color_pattern'],
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage', // Enables live preview via JS
    ) );
    $wp_customize->add_control( 'awbase_color_pattern', array(
        'label'    => 'カラーパターン',
        'section  ' => 'awbase_general_section',
        'settings' => 'awbase_settings[color_pattern]',
        'type'     => 'select',
        'choices'  => array(
            'original' => 'オリジナル',
            'blacktan' => 'ブラックタン',
            'chocotan' => 'チョコタン',
            'cream'    => 'クリーム',
            'bluetan'  => 'ブルータン',
            'white'    => 'ホワイト',
        ),
    ) );

    // Setting & Control: Font Family
    $wp_customize->add_setting( 'awbase_settings[font_family]', array(
        'type'              => 'option',
        'default'           => $defaults['font_family'],
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'awbase_font_family', array(
        'label'    => 'フォントファミリー',
        'section  ' => 'awbase_general_section',
        'settings' => 'awbase_settings[font_family]',
        'type'     => 'select',
        'choices'  => array(
            'hiragino' => 'ヒラギノ系',
            'meiryo'   => 'メイリオ系',
            'yugothic' => 'Yu Gothic系',
        ),
    ) );

    // Setting & Control: Columns
    $wp_customize->add_setting( 'awbase_settings[columns]', array(
        'type'              => 'option',
        'default'           => $defaults['columns'],
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh', // Need full refresh to change layout CSS mostly
    ) );
    $wp_customize->add_control( 'awbase_columns', array(
        'label'    => 'カラムレイアウト',
        'section  ' => 'awbase_general_section',
        'settings' => 'awbase_settings[columns]',
        'type'     => 'radio',
        'choices'  => array(
            '1' => '1カラム',
            '2' => '2カラム',
        ),
    ) );

}
add_action( 'customize_register', 'awbase_customize_register' );

// Customizer Live Preview JS
function awbase_customize_preview_js() {
    wp_enqueue_script( 'awbase_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview', 'jquery' ), null, true );
}
add_action( 'customize_preview_init', 'awbase_customize_preview_js' );

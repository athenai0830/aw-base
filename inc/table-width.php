<?php
/**
 * テーブルブロックの列の幅（％）
 *
 * エディタ右カラムの「列の幅」で列ごとに指定する（assets/js/table-width.js）。
 * 属性 awbaseColWidths（列順の数値配列・0＝自動）はブロックのコメントにだけ持ち、保存HTMLは変えない
 * （テーマを外しても表が壊れない）。表側はここで figure にクラス awb-col-w と CSS 変数 --awb-col-N を付け、
 * 幅の適用は awbase_table_cols_css()（nth-child・12列まで）。指定した表は固定レイアウト・本文幅100%
 * （スマホでも横スクロールにせず、指定どおりの比率で折り返す）。
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'AWBASE_TABLE_MAX_COLS' ) ) {
    define( 'AWBASE_TABLE_MAX_COLS', 12 ); // assets/js/table-width.js の MAX_COLS と揃える
}

function awbase_table_cols_attribute( $args, $block_type ) {
    if ( 'core/table' === $block_type ) {
        $args['attributes']['awbaseColWidths'] = array(
            'type'  => 'array',
            'items' => array( 'type' => 'number' ),
        );
    }
    return $args;
}
add_filter( 'register_block_type_args', 'awbase_table_cols_attribute', 10, 2 );

/**
 * 列幅の CSS（表側・エディタ共通）
 */
function awbase_table_cols_css() {
    $s   = ':is(.entry-content,.is-root-container) .wp-block-table.awb-col-w';
    $css = $s . ' table{table-layout:fixed;width:100%;min-width:0}'
         . $s . ' :is(th,td){white-space:normal;overflow-wrap:anywhere}';
    for ( $i = 1; $i <= AWBASE_TABLE_MAX_COLS; $i++ ) {
        $css .= $s . ' tr > :nth-child(' . $i . '){width:var(--awb-col-' . $i . ',auto)}';
    }
    return $css;
}

// エディタ：設定パネルのスクリプト
function awbase_table_cols_editor_script() {
    wp_enqueue_script(
        'awbase-table-width',
        get_template_directory_uri() . '/assets/js/table-width.js',
        array( 'wp-hooks', 'wp-compose', 'wp-element', 'wp-block-editor', 'wp-components' ),
        filemtime( get_template_directory() . '/assets/js/table-width.js' ),
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'awbase_table_cols_editor_script' );

// エディタ：列幅の CSS（iframe 内のエディタにも届くよう enqueue_block_assets で管理画面のときだけ出す）
function awbase_table_cols_editor_style() {
    if ( ! is_admin() ) {
        return;
    }
    wp_register_style( 'awbase-table-width-editor', false, array(), null );
    wp_enqueue_style( 'awbase-table-width-editor' );
    wp_add_inline_style( 'awbase-table-width-editor', awbase_table_cols_css() );
}
add_action( 'enqueue_block_assets', 'awbase_table_cols_editor_style' );

// 表側：列幅の CSS（メインスタイルに続けて出力）
function awbase_table_cols_front_style() {
    wp_add_inline_style( 'awbase-main-style', awbase_table_cols_css() );
}
add_action( 'wp_enqueue_scripts', 'awbase_table_cols_front_style', 20 );

// 表側：figure にクラスと CSS 変数を付ける
function awbase_table_cols_render( $content, $block ) {
    if ( 'core/table' !== $block['blockName'] || empty( $block['attrs']['awbaseColWidths'] ) || ! is_array( $block['attrs']['awbaseColWidths'] ) ) {
        return $content;
    }
    $vars = array();
    foreach ( array_slice( array_values( $block['attrs']['awbaseColWidths'] ), 0, AWBASE_TABLE_MAX_COLS ) as $i => $w ) {
        $w = (int) $w;
        if ( $w >= 1 && $w <= 100 ) {
            $vars[] = '--awb-col-' . ( $i + 1 ) . ':' . $w . '%';
        }
    }
    if ( ! $vars ) {
        return $content;
    }
    $tags = new WP_HTML_Tag_Processor( $content );
    if ( ! $tags->next_tag( 'figure' ) ) {
        return $content;
    }
    $tags->add_class( 'awb-col-w' );
    $style = (string) $tags->get_attribute( 'style' );
    $style = ( '' !== $style ? rtrim( $style, '; ' ) . ';' : '' ) . implode( ';', $vars );
    $tags->set_attribute( 'style', $style );
    return $tags->get_updated_html();
}
add_filter( 'render_block', 'awbase_table_cols_render', 10, 2 );

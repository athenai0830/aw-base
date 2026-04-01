<?php
// AW-Base Dynamic CSS Output
if ( ! defined( 'ABSPATH' ) ) exit;

function awbase_dynamic_css() {
    $defaults = awbase_get_default_settings();
    $saved    = get_option('awbase_settings', []);
    $options  = is_array($saved) ? array_merge($defaults, $saved) : $defaults;
    $pattern  = $options['color_pattern'];
    $font     = $options['font_family'];

    $css_vars = [];

    // Colors – all values follow the official color palette definition
    // Roles:
    //   --bg-color      : 基本背景
    //   --content-bg    : コンテンツ背景 (#FFFFFF fixed)
    //   --main-color    : アクセント/通知ライン (nav bg, notice bg)
    //   --accent-color  : 見出しアクセント/ボタン (badge bg, h2 decoration)
    //   --text-color    : 文字ポジティブ (body text)
    //   --text-neg      : 文字ネガティブ (#FFFFFF fixed)
    //   --nav-text-color: nav/notice text – chosen for contrast on --main-color bg
    //   --badge-text    : badge text – chosen for contrast on --accent-color bg
    //   --link-color    : リンク (#3365CA, common across all patterns)
    //   --border-color  : 各要素罫線 (#E3E3E3 fixed)
    //   --heading-bg    : 見出し背景
    //   --text-muted    : 補助テキスト (dates, meta etc.)
    switch ( $pattern ) {
        case 'blacktan':
            // 基本背景 #111111 / main-color #6B4C1F / accent #D1A166
            $css_vars['--bg-color']       = '#111111';
            $css_vars['--text-color']     = '#111111'; // ポジティブ (content bg上)
            $css_vars['--main-color']     = '#6B4C1F'; // 通知ライン/ナビ背景
            $css_vars['--accent-color']   = '#D1A166'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#111111';
            $css_vars['--nav-text-color'] = '#FFFFFF'; // 暗褐色ナビ上は白
            $css_vars['--badge-text']     = '#FFFFFF'; // タン色バッジ上は白
            $css_vars['--heading-bg']     = '#FAFAFA';
            $css_vars['--text-muted']     = '#8A7A6A'; // 褐色系ミュート
            break;
        case 'chocotan':
            // 基本背景 #C9B58B / main-color #3F2F0D / accent #3F2F0D
            $css_vars['--bg-color']       = '#C9B58B';
            $css_vars['--text-color']     = '#212121'; // ポジティブ
            $css_vars['--main-color']     = '#3F2F0D'; // 通知ライン/ナビ背景 (accent=main same)
            $css_vars['--accent-color']   = '#3F2F0D'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#212121';
            $css_vars['--nav-text-color'] = '#FFFFFF'; // 濃茶ナビ上は白
            $css_vars['--badge-text']     = '#FFFFFF'; // 濃茶バッジ上は白
            $css_vars['--heading-bg']     = '#F5F5F5';
            $css_vars['--text-muted']     = '#6B5A3A'; // 土色系ミュート
            break;
        case 'cream':
            // 基本背景 #FFFEF2 / main-color #9E682A / accent #DDC673
            $css_vars['--bg-color']       = '#FFFEF2';
            $css_vars['--text-color']     = '#0A0A0A'; // ポジティブ
            $css_vars['--main-color']     = '#9E682A'; // 通知ライン/ナビ背景
            $css_vars['--accent-color']   = '#DDC673'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#0A0A0A';
            $css_vars['--nav-text-color'] = '#FFFFFF'; // アンバー褐色ナビ上は白
            $css_vars['--badge-text']     = '#3A2800'; // 淡黄金バッジ→暗色テキストで可読性確保
            $css_vars['--heading-bg']     = '#FAFAFA';
            $css_vars['--text-muted']     = '#7A6030'; // クリーム系ミュート
            break;
        case 'bluetan':
            // 基本背景 #37474F / main-color #81D4FA / accent #0288D1
            $css_vars['--bg-color']       = '#37474F';
            $css_vars['--text-color']     = '#050505'; // ポジティブ (content bg上)
            $css_vars['--main-color']     = '#81D4FA'; // 通知ライン/ナビ背景 (明るい水色)
            $css_vars['--accent-color']   = '#0288D1'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#050505';
            $css_vars['--nav-text-color'] = '#002244'; // 明水色ナビ上は濃紺テキスト
            $css_vars['--badge-text']     = '#FFFFFF'; // 濃青バッジ上は白
            $css_vars['--heading-bg']     = '#F9F9F9';
            $css_vars['--text-muted']     = '#546E7A'; // ブルーグレー系ミュート
            break;
        case 'white':
            // 基本背景 #FCFCFC / main-color #666666 / accent #636363
            $css_vars['--bg-color']       = '#FCFCFC';
            $css_vars['--text-color']     = '#333333'; // ポジティブ
            $css_vars['--main-color']     = '#666666'; // 通知ライン/ナビ背景
            $css_vars['--accent-color']   = '#636363'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#333333';
            $css_vars['--nav-text-color'] = '#FFFFFF'; // グレーナビ上は白
            $css_vars['--badge-text']     = '#FFFFFF'; // グレーバッジ上は白
            $css_vars['--heading-bg']     = '#F5F5F5';
            $css_vars['--text-muted']     = '#888888'; // モノトーン系ミュート
            break;
        case 'original':
        default:
            // 基本背景 #FCFCFC / main-color #11114D / accent #C30E24
            $css_vars['--bg-color']       = '#FCFCFC';
            $css_vars['--text-color']     = '#333333'; // ポジティブ
            $css_vars['--main-color']     = '#11114D'; // 通知ライン/ナビ背景
            $css_vars['--accent-color']   = '#C30E24'; // アクセント/ボタン
            $css_vars['--content-bg']     = '#FFFFFF';
            $css_vars['--card-bg']        = '#FFFFFF';
            $css_vars['--card-text']      = '#333333';
            $css_vars['--nav-text-color'] = '#FFFFFF'; // 濃紺ナビ上は白
            $css_vars['--badge-text']     = '#FFFFFF'; // 赤バッジ上は白
            $css_vars['--heading-bg']     = '#F5F6F7';
            $css_vars['--text-muted']     = '#888888'; // ニュートラルミュート
            break;
    }

    // Common across all patterns (palette-defined)
    $css_vars['--header-bg']    = '#FFFFFF'; // ヘッダー背景は全パターン白固定
    $css_vars['--text-neg']     = '#FFFFFF';
    $css_vars['--link-color']   = '#3365CA';
    $css_vars['--border-color'] = '#E3E3E3';

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
    $css_vars['--site-max-width']    = intval($options['site_max_width']) . 'px';
    $css_vars['--content-width']     = intval($options['content_width']) . 'px';
    $css_vars['--sidebar-width']     = intval($options['sidebar_width']) . 'px';
    $css_vars['--fv-height']         = intval($options['fv_height']) . $options['fv_height_unit'];
    $css_vars['--gap-main-sidebar']  = intval($options['gap_main_sidebar'] ?? 24) . 'px';
    $css_vars['--header-height']     = intval($options['header_height'] ?? 100) . 'px';

    // Build CSS String
    $custom_css = ":root {\n";
    foreach ($css_vars as $k => $v) {
        $custom_css .= "  {$k}: {$v};\n";
    }
    $custom_css .= "}\n";

    $custom_css = apply_filters( 'awbase_style_output', $custom_css );
    wp_add_inline_style( 'awbase-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'awbase_dynamic_css', 20 );

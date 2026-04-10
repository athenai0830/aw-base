<?php
// AW-Base Admin Settings page
if ( ! defined( 'ABSPATH' ) ) exit;

// Register Menu
// ダッシュボード（位置2）の直下に固定配置する
// WordPress標準: 2=ダッシュボード, 4=区切り, 5=投稿
function awbase_add_admin_menu() {
    add_menu_page(
        'AW-Base 設定',
        'AW-Base',
        'manage_options',
        'awbase_settings',
        'awbase_settings_page',
        'dashicons-admin-generic',
        3
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
}
add_action( 'admin_menu', 'awbase_add_admin_menu' );

// Register Settings
function awbase_register_settings() {
    register_setting( 'awbase_settings_group', 'awbase_settings', array('sanitize_callback' => 'awbase_sanitize_settings') );
}
add_action( 'admin_init', 'awbase_register_settings' );

// Default settings（static でメモ化 — リクエスト内で何度呼ばれても DB アクセスなし）
function awbase_get_default_settings() {
    static $defaults = null;
    if ( $defaults !== null ) return $defaults;
    $defaults = array(
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
        'header_height' => 100,
        'logo_alt' => get_bloginfo('name'),
        'header_logo_align' => 'left',
        'catchphrase' => '',
        
        'show_global_nav' => '1',
        'show_header' => '1',
        'show_notice' => '0',
        'show_fv' => '0',
        'front_page_1col' => '1',

        // Sub-page visibility
        'show_global_nav_sub' => '1',
        'show_header_sub' => '1',
        'show_notice_sub' => '0',
        
        'header_pattern'     => '1',
        'header_pattern_sub' => '',
        'nav_align' => 'center',
        'notice_text' => '',
        'notice_url' => '',
        
        'fv_bg_image' => '',
        'fv_height_unit' => 'vh',
        'fv_height' => 100,
        'fv_logo_image' => '',
        'fv_logo_url' => '',
        'fv_logo_width' => 300,
        'fv_logo_height' => 0,
        'overlay_color' => 'black',
        'overlay_opacity' => 28,
        'fv_dot_pattern' => '1',
        'fv_catchphrase' => '',
        'fv_substack_enable' => '0',
        'fv_substack_url' => '',
        'fv_substack_text' => 'Substack で購読',
        
        'sitemap_enable'        => '1',
        'llms_txt_enable'       => '1',
        'llms_txt_content'      => "# " . get_bloginfo('name') . "\n\n> " . get_bloginfo('description'),
        'llms_full_txt_enable'  => '0',
        'ai_index_md_enable'    => '0',
        'noindex_search' => '0',
        'noindex_404' => '1',
        'noindex_date' => '0',
        'noindex_author' => '0',
        'noindex_paged' => '0',
        'noindex_tag' => '0',
        'canonical_paged_to_p1' => '0',
        
        'ai_tracking_enable' => '1',
        
        'gap_main_sidebar' => 24,
        'lazy_load' => '1',
        'lazy_load_fv_exclude' => '1',
        'lazy_load_thumb_exclude' => '1',
        'css_minify' => '1',
        'js_minify' => '0',
        'disable_image_sizes' => '0',  // デフォルトOFF（既存投稿の画像互換性を保つ）
        'recaptcha_limit' => '1',
        
        'footer_logo_image' => '',
        'footer_logo_width' => 160,
        'footer_logo_url' => '',
        'footer_logo_align' => 'center',
        'footer_nav_align' => 'center',
        'footer_copyright' => '© ' . date('Y') . ' ' . get_bloginfo('name'),

        // SNS Share
        'sns_share_twitter'        => '1',
        'sns_share_facebook'       => '1',
        'sns_share_line'           => '1',
        'sns_share_pocket'         => '0',
        'sns_share_hatena'         => '0',
        'sns_share_feedly'         => '0',
        'sns_share_pinterest'      => '0',
        'sns_share_copy'           => '1',
        'sns_share_above_author'   => '1',
        'sns_share_below_eyecatch' => '1',

        // Favicon
        'favicon_url' => '',

        // Blog Card
        'blogcard_noimage_url' => '',

        // Popular List
        'popular_list_count'  => 5,
        'popular_list_period' => 'total',

        // WordPress bloat removal
        'remove_wp_bloat'  => '1',
        'remove_dashicons' => '1',
        'remove_block_css' => '1',
        'remove_jquery'    => '1',

        // Structured Data / Schema.org
        'schema_author_name'    => '',
        'schema_author_altname' => '',
        'schema_author_url'     => '',
        'schema_org_name'       => '',
        'schema_org_address'    => '',
        'schema_org_phone'      => '',
        'schema_org_email'      => '',
        'schema_og_image'       => '',
        'schema_logo'           => '',
    );
    return $defaults;
}

// Get Theme Option（static でメモ化 — 設定配列のマージはリクエストに1回）
function get_awbase_option( $key ) {
    static $options = null;
    if ( $options === null ) {
        $saved   = get_option( 'awbase_settings', [] );
        $options = is_array( $saved ) ? array_merge( awbase_get_default_settings(), $saved ) : awbase_get_default_settings();
    }
    $defaults = awbase_get_default_settings();
    return isset( $options[ $key ] ) ? $options[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

// Sanitize Settings
function awbase_sanitize_settings( $input ) {
    // Merge with existing values to prevent data loss when saving a single tab
    $existing = get_option( 'awbase_settings', awbase_get_default_settings() );
    $output   = array();
    $defaults = awbase_get_default_settings();

    // Checkbox fields grouped by tab
    // ※ ai_tracking_enable は AI タブ専属。高速化タブに含めると保存時に誤って OFF になる。
    $tab_checkboxes = array(
        'general'     => ['show_global_nav', 'show_header', 'show_notice', 'show_fv', 'front_page_1col', 'show_global_nav_sub', 'show_header_sub', 'show_notice_sub'],
        'header'      => [],
        'firstview'   => ['fv_dot_pattern', 'fv_substack_enable'],
        'seo'         => ['sitemap_enable', 'noindex_search', 'noindex_404', 'noindex_date', 'noindex_author', 'noindex_paged', 'noindex_tag', 'canonical_paged_to_p1'],
        'performance' => ['lazy_load', 'lazy_load_fv_exclude', 'lazy_load_thumb_exclude', 'css_minify', 'js_minify', 'disable_image_sizes', 'recaptcha_limit', 'remove_wp_bloat', 'remove_dashicons', 'remove_block_css', 'remove_jquery'],
        'footer'      => [],
        'sns'         => ['sns_share_twitter', 'sns_share_facebook', 'sns_share_line', 'sns_share_pocket', 'sns_share_hatena', 'sns_share_feedly', 'sns_share_pinterest', 'sns_share_copy', 'sns_share_above_author', 'sns_share_below_eyecatch'],
        'ai'          => ['ai_tracking_enable', 'llms_txt_enable', 'llms_full_txt_enable', 'ai_index_md_enable'],
    );
    $active_tab = sanitize_key( $_POST['awbase_active_tab'] ?? 'general' );
    $active_tab = isset( $tab_checkboxes[ $active_tab ] ) ? $active_tab : 'general';
    $tab_cbs    = $tab_checkboxes[ $active_tab ];

    // String-value fields — includes all checkbox fields so they stay as '0'/'1' (not int)
    // IMPORTANT: intval('1') = 1 (int), but comparisons use === '1' (string), so all
    // boolean/checkbox values must remain strings.
    $all_checkbox_keys = array_merge( ...array_values( $tab_checkboxes ) );
    $string_keys = array_merge(
        ['color_pattern', 'font_family', 'columns', 'sidebar_position', 'header_logo_align',
         'header_pattern', 'header_pattern_sub', 'nav_align', 'fv_height_unit', 'overlay_color',
         'footer_logo_align', 'footer_nav_align', 'llms_txt_content',
         'popular_list_period',
         'schema_author_name', 'schema_author_altname', 'schema_author_url',
         'schema_org_name', 'schema_org_address', 'schema_org_phone', 'schema_org_email',
         'schema_og_image', 'schema_logo', 'blogcard_noimage_url', 'favicon_url'],
        $all_checkbox_keys
    );

    // Keys that allow limited HTML (wp_kses_post)
    $kses_keys = [ 'fv_catchphrase' ];

    // Keys that are plain-text but multiline (sanitize_textarea_field)
    $textarea_keys = [ 'llms_txt_content' ];

    foreach ( $defaults as $key => $default_val ) {
        if ( isset( $input[ $key ] ) ) {
            if ( in_array( $key, $textarea_keys ) ) {
                $output[ $key ] = sanitize_textarea_field( $input[ $key ] );
            } elseif ( in_array( $key, $kses_keys ) ) {
                $output[ $key ] = wp_kses_post( $input[ $key ] );
            } elseif ( is_numeric( $input[ $key ] ) && ! in_array( $key, $string_keys ) ) {
                $output[ $key ] = intval( $input[ $key ] );
            } else {
                $output[ $key ] = sanitize_text_field( $input[ $key ] );
            }
        } else {
            if ( in_array( $key, $tab_cbs ) ) {
                // Checkbox was on this tab's form but not submitted → unchecked
                $output[ $key ] = '0';
            } else {
                // Field is from another tab → preserve existing saved value
                $output[ $key ] = isset( $existing[ $key ] ) ? $existing[ $key ] : $default_val;
            }
        }
    }
    // コンテンツ幅バリデーション（一般設定タブ保存時のみ）
    if ( $active_tab === 'general' ) {
        $site_max  = intval( $output['site_max_width'] );
        $content_w = intval( $output['content_width'] );
        $sidebar_w = intval( $output['sidebar_width'] );
        $gap_w     = intval( $output['gap_main_sidebar'] );
        $total     = $content_w + $sidebar_w + $gap_w;

        if ( $total > $site_max ) {
            add_settings_error(
                'awbase_settings',
                'width_overflow',
                sprintf(
                    'コンテンツ幅設定エラー: メインコンテンツ幅(%dpx) ＋ サイドバー幅(%dpx) ＋ 間隔(%dpx) ＝ %dpx がサイト全体の最大幅(%dpx)を超えています。幅の設定を見直してください。前回の値を維持します。',
                    $content_w, $sidebar_w, $gap_w, $total, $site_max
                ),
                'error'
            );
            $output['site_max_width']   = isset( $existing['site_max_width'] )   ? intval( $existing['site_max_width'] )   : 1200;
            $output['content_width']    = isset( $existing['content_width'] )    ? intval( $existing['content_width'] )    : 760;
            $output['sidebar_width']    = isset( $existing['sidebar_width'] )    ? intval( $existing['sidebar_width'] )    : 300;
            $output['gap_main_sidebar'] = isset( $existing['gap_main_sidebar'] ) ? intval( $existing['gap_main_sidebar'] ) : 24;
        }
    }

    return $output;
}

// ============================================================
// キャッシュ削除アクション
// OGP transient（awbase_ogp_*）と WordPress oEmbed キャッシュを削除する
// ============================================================
function awbase_handle_clear_cache() {
    if ( ! isset( $_POST['awbase_clear_cache_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['awbase_clear_cache_nonce'], 'awbase_clear_cache' ) ) {
        wp_die( 'セキュリティチェック失敗' );
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '権限がありません' );
    }

    global $wpdb;

    // OGP transient（awbase_fetch_ogp のキャッシュ）
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_awbase_ogp_%'
            OR option_name LIKE '_transient_timeout_awbase_ogp_%'"
    );

    // WordPress oEmbed キャッシュ（_oembed_* post_meta）
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta}
         WHERE meta_key LIKE '_oembed_%'"
    );

    do_action( 'awbase_after_clear_cache' );

    // リダイレクト（保存成功メッセージと同じ仕組みで通知）
    $tab = isset( $_POST['awbase_cache_tab'] ) ? sanitize_key( $_POST['awbase_cache_tab'] ) : 'general';
    wp_redirect( add_query_arg(
        array( 'page' => 'awbase_settings', 'tab' => $tab, 'cache_cleared' => '1' ),
        admin_url( 'admin.php' )
    ) );
    exit;
}
add_action( 'admin_post_awbase_clear_cache', 'awbase_handle_clear_cache' );

// Enqueue Admin Scripts/Styles
function awbase_admin_enqueue_scripts( $hook ) {
    // 投稿一覧では AI カラム用 CSS のみ読み込む
    if ( $hook === 'edit.php' ) {
        wp_enqueue_style( 'awbase-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), null );
        return;
    }
    if ( $hook !== 'toplevel_page_awbase_settings' ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
    wp_enqueue_style( 'awbase-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), null );
    wp_enqueue_script( 'awbase-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array('jquery', 'media-editor'), null, true );
}
add_action( 'admin_enqueue_scripts', 'awbase_admin_enqueue_scripts' );

// Settings Page HTML
function awbase_settings_page() {
    $saved   = get_option( 'awbase_settings', [] );
    $options = is_array( $saved ) ? array_merge( awbase_get_default_settings(), $saved ) : awbase_get_default_settings();
    $allowed_tabs = ['general', 'header', 'firstview', 'seo', 'performance', 'footer', 'sns', 'ai'];
    $raw_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
    $active_tab = in_array( $raw_tab, $allowed_tabs, true ) ? $raw_tab : 'general';
    ?>
    <div class="wrap">
        <div class="awbase-page-header">
            <h1><span class="aw-logo-mark">AW</span> AW-Base 設定</h1>
            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0">
                <input type="hidden" name="action" value="awbase_clear_cache">
                <input type="hidden" name="awbase_cache_tab" value="<?php echo esc_attr( $active_tab ); ?>">
                <?php wp_nonce_field( 'awbase_clear_cache', 'awbase_clear_cache_nonce' ); ?>
                <button type="submit" class="awbase-clear-cache-btn">
                    <i class="fa-solid fa-trash-can"></i> キャッシュ削除
                </button>
            </form>
        </div>

        <?php
        settings_errors();
        if ( isset( $_GET['cache_cleared'] ) && $_GET['cache_cleared'] === '1' ) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>キャッシュを削除しました。</strong>（OGP キャッシュ・oEmbed キャッシュ）</p></div>';
        }
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=awbase_settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-sliders"></i> 一般設定</a>
            <a href="?page=awbase_settings&tab=header" class="nav-tab <?php echo $active_tab == 'header' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-heading"></i> ヘッダー/ナビ</a>
            <a href="?page=awbase_settings&tab=firstview" class="nav-tab <?php echo $active_tab == 'firstview' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-image"></i> ファーストビュー</a>
            <a href="?page=awbase_settings&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-magnifying-glass"></i> SEO設定</a>
            <a href="?page=awbase_settings&tab=performance" class="nav-tab <?php echo $active_tab == 'performance' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-bolt"></i> 高速化</a>
            <a href="?page=awbase_settings&tab=footer" class="nav-tab <?php echo $active_tab == 'footer' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-shoe-prints"></i> フッター</a>
            <a href="?page=awbase_settings&tab=ai" class="nav-tab <?php echo $active_tab == 'ai' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-robot"></i> AIトラッキング</a>
            <a href="?page=awbase_settings&tab=sns" class="nav-tab <?php echo $active_tab == 'sns' ? 'nav-tab-active' : ''; ?>"><i class="fa-solid fa-share-nodes"></i> SNSシェア</a>
        </h2>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'awbase_settings_group' );
            ?>
            <input type="hidden" name="awbase_active_tab" value="<?php echo esc_attr( $active_tab ); ?>">
            <?php
            require_once AWBASE_DIR . '/inc/admin/tab-' . $active_tab . '.php';
            submit_button( '設定を保存' );
            ?>
        </form>
    </div>
    <?php
}

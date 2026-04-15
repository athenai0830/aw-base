<?php
// AW-Base AI Traffic Tracker
if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------------------------------------------------------------------
// 0. テーブル作成（テーマ独自テーブル）
// ---------------------------------------------------------------------------
function awbase_ensure_ai_tracker_table() {
    if ( ! is_admin() ) return;
    $option_key = 'awbase_ai_tracker_table_v1';
    if ( get_option( $option_key ) ) return;
    awbase_create_ai_tracker_table();
    update_option( $option_key, true );
}
add_action( 'admin_init', 'awbase_ensure_ai_tracker_table' );

function awbase_create_ai_tracker_table() {
    global $wpdb;
    $table_name      = $wpdb->prefix . 'ai_traffic_log';
    $charset_collate = $wpdb->get_charset_collate();
    // Unified schema: compatible with AW AI Traffic Tracker plugin (post_id/ai_service/access_type/accessed_at)
    // Theme-only columns (bot_identifier/requested_url/user_agent/ip_address) are nullable for plugin-written rows.
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL DEFAULT 0,
        ai_service varchar(50) NOT NULL DEFAULT '',
        access_type varchar(20) NOT NULL DEFAULT '',
        accessed_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        bot_identifier varchar(100) NULL,
        requested_url varchar(500) NULL,
        user_agent text NULL,
        ip_address varchar(45) NULL,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY accessed_at (accessed_at)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'awbase_create_ai_tracker_table' );

// ---------------------------------------------------------------------------
// 1. ボット識別子 → [サービス名, アクセス種別] マッピング
//    プラグイン (ai-traffic-tracker) の分類に準拠
// ---------------------------------------------------------------------------
function awbase_get_bot_map() {
    return [
        // fetch: AIが検索・取得のためにクロール
        'OAI-SearchBot'         => [ 'ChatGPT',     'fetch' ],
        'ChatGPT-User'          => [ 'ChatGPT',     'fetch' ],
        'PerplexityBot'         => [ 'Perplexity',  'fetch' ],
        'Perplexity-User'       => [ 'Perplexity',  'fetch' ],
        'Gemini-Deep-Research'  => [ 'Gemini',      'fetch' ],
        'Google-Gemini'         => [ 'Gemini',      'fetch' ],
        'GoogleOther'           => [ 'Gemini',      'fetch' ],
        'Google-CloudVertexBot' => [ 'Gemini',      'fetch' ],
        'Claude-SearchBot'      => [ 'Claude',      'fetch' ],
        'Claude-User'           => [ 'Claude',      'fetch' ],
        'Claude-Web'            => [ 'Claude',      'fetch' ],
        // learning: AI学習用クロール
        'GPTBot'                => [ 'ChatGPT',     'learning' ],
        'ClaudeBot'             => [ 'Claude',      'learning' ],
        'Anthropic-ai'          => [ 'Claude',      'learning' ],
        'CCBot'                 => [ 'CommonCrawl', 'learning' ],
        'cohere-ai'             => [ 'Cohere',      'learning' ],
        'Google-Extended'       => [ 'Gemini',      'learning' ],
        'GoogleOther-Image'     => [ 'Gemini',      'learning' ],
        'GoogleOther-Video'     => [ 'Gemini',      'learning' ],
        'Bytespider'            => [ 'Bytedance',   'learning' ],
        // referer: AIサービス経由の訪問
        'FacebookBot'           => [ 'Facebook',    'referer' ],
        'Omgili'                => [ 'Omgili',      'referer' ],
    ];
}

// bot_identifier → access_type カテゴリ（後方互換用）
function awbase_get_bot_category( $bot_id ) {
    $map = awbase_get_bot_map();
    return isset( $map[ $bot_id ] ) ? $map[ $bot_id ][1] : 'referer';
}

// ---------------------------------------------------------------------------
// 2. サービス別ブレークダウン構築（統合テーブル）
//    返り値: [ 'ChatGPT' => ['referer'=>0, 'learning'=>0, 'fetch'=>0], ... ]
// ---------------------------------------------------------------------------
function awbase_build_service_breakdown( $post_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_traffic_log';

    static $table_exists = null;
    if ( $table_exists === null ) {
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table;
    }
    if ( ! $table_exists ) return [];

    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT ai_service, access_type, COUNT(*) AS cnt
         FROM {$table}
         WHERE post_id = %d AND post_id > 0
         GROUP BY ai_service, access_type",
        $post_id
    ), ARRAY_A );

    $breakdown = [];
    foreach ( $rows as $row ) {
        $svc = $row['ai_service'];
        $typ = $row['access_type'];
        if ( ! isset( $breakdown[ $svc ] ) ) $breakdown[ $svc ] = [ 'referer' => 0, 'learning' => 0, 'fetch' => 0 ];
        if ( isset( $breakdown[ $svc ][ $typ ] ) ) $breakdown[ $svc ][ $typ ] += (int) $row['cnt'];
    }
    return $breakdown;
}

// ---------------------------------------------------------------------------
// 3. Track traffic (テーマ独自テーブルへの書き込み)
//    プラグインが有効な場合はプラグインに委譲するためスキップ
// ---------------------------------------------------------------------------
function awbase_track_ai_bots() {
    if ( defined( 'AWAIT_VERSION' ) ) return; // プラグイン有効時はスキップ（プラグインに委譲）
    $options = awbase_get_settings();
    if ( $options['ai_tracking_enable'] !== '1' ) return;
    if ( is_admin() ) return;

    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ( empty( $user_agent ) ) return;

    $bot_map = awbase_get_bot_map();
    $matched_bot = '';
    foreach ( array_keys( $bot_map ) as $bot ) {
        if ( stripos( $user_agent, $bot ) !== false ) { $matched_bot = $bot; break; }
    }
    if ( empty( $matched_bot ) ) return;

    $post_id     = get_queried_object_id();
    $ai_service  = $bot_map[ $matched_bot ][0];
    $access_type = $bot_map[ $matched_bot ][1];

    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_traffic_log';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {
        $wpdb->insert( $table_name, [
            'post_id'        => $post_id,
            'ai_service'     => $ai_service,
            'access_type'    => $access_type,
            'accessed_at'    => current_time( 'mysql' ),
            'bot_identifier' => $matched_bot,
            'requested_url'  => esc_url_raw( $_SERVER['REQUEST_URI'] ),
            'user_agent'     => sanitize_textarea_field( $user_agent ),
            'ip_address'     => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
        ], [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );
    }
}
add_action( 'wp', 'awbase_track_ai_bots' );

// ---------------------------------------------------------------------------
// 4. Admin submenu – 常に登録する（プラグイン有効時もページにアクセスできるよう）
// ---------------------------------------------------------------------------
function awbase_ai_tracker_menu() {
    add_submenu_page(
        'awbase_settings',
        'AIボット トラフィックログ',
        'AIボットログ',
        'manage_options',
        'awbase_ai_tracker',
        'awbase_ai_tracker_page'
    );
}
add_action( 'admin_menu', 'awbase_ai_tracker_menu', 1000 );

// CSV ダウンロード（admin_init で早期処理）
function awbase_ai_tracker_csv_download() {
    if (
        ! isset( $_GET['page'], $_GET['action'] ) ||
        $_GET['page'] !== 'awbase_ai_tracker' ||
        $_GET['action'] !== 'download_csv'
    ) return;
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'awbase_download_csv' );

    global $wpdb;
    $table = $wpdb->prefix . 'ai_traffic_log';
    $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table;
    if ( ! $table_exists ) wp_die( 'テーブルが存在しません。' );

    $logs_all = $wpdb->get_results(
        "SELECT accessed_at, ai_service, access_type, bot_identifier, requested_url, user_agent, ip_address FROM {$table} ORDER BY accessed_at DESC",
        ARRAY_A
    );

    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="ai-traffic-log.csv"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );
    echo "\xEF\xBB\xBF";
    echo implode( ',', [ '日時', 'サービス', '種別', 'ボット', 'URL', 'UA', 'IP' ] ) . "\n";
    foreach ( $logs_all as $row ) {
        echo implode( ',', array_map( function( $v ) {
            return '"' . str_replace( '"', '""', $v ) . '"';
        }, $row ) ) . "\n";
    }
    exit;
}
add_action( 'admin_init', 'awbase_ai_tracker_csv_download' );

// ---------------------------------------------------------------------------
// 5. Admin page HTML – 両テーブルを表示
// ---------------------------------------------------------------------------
function awbase_ai_tracker_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_traffic_log';

    $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table;

    // ログクリア
    if ( isset( $_POST['awbase_clear_ai_log'] ) && check_admin_referer( 'awbase_clear_ai_log_nonce' ) ) {
        if ( $table_exists ) $wpdb->query( "TRUNCATE TABLE {$table}" );
        echo '<div class="updated"><p>ログをクリアしました。</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>AIボット トラフィックログ <span class="aw-logo-mark">AW</span></h1>

        <?php if ( defined( 'AWAIT_VERSION' ) ) : ?>
        <div class="notice notice-info inline" style="margin:12px 0;">
            <p><strong>AI Traffic Tracker プラグイン が有効です。</strong>
            テーマの書き込みは停止中。共用テーブル (<code><?php echo esc_html( $table ); ?></code>) のデータを表示しています。</p>
        </div>
        <?php endif; ?>

        <?php
        // ---- サービス別集計 ----
        $combined = []; // service => ['referer'=>0,'learning'=>0,'fetch'=>0]

        if ( $table_exists ) {
            $rows = $wpdb->get_results(
                "SELECT ai_service, access_type, COUNT(*) AS cnt
                 FROM {$table}
                 GROUP BY ai_service, access_type",
                ARRAY_A
            );
            foreach ( $rows as $row ) {
                $s = $row['ai_service']; $t = $row['access_type'];
                if ( ! isset( $combined[$s] ) ) $combined[$s] = ['referer'=>0,'learning'=>0,'fetch'=>0];
                if ( isset( $combined[$s][$t] ) ) $combined[$s][$t] += (int)$row['cnt'];
            }
        }

        // 合計降順ソート
        uasort( $combined, function($a, $b) {
            return array_sum($b) <=> array_sum($a);
        });

        // 表示対象サービスを5種類に限定（収集は継続）
        $display_services = [ 'Claude', 'ChatGPT', 'Gemini', 'Perplexity', 'CommonCrawl' ];
        $combined = array_filter( $combined, fn( $k ) => in_array( $k, $display_services, true ), ARRAY_FILTER_USE_KEY );
        ?>

        <style>
        .awbase-tracker-row { display:flex; gap:32px; align-items:flex-start; flex-wrap:wrap; }
        @media (max-width: 782px) {
            .awbase-tracker-row { flex-direction:column; }
            .awbase-tracker-bar  { width:100% !important; min-width:0 !important; }
            .awbase-tracker-bar > div { width:100% !important; }
        }
        </style>
        <h2>サービス別アクセス集計（全期間）</h2>
        <div class="awbase-tracker-row">
        <table class="wp-list-table widefat fixed striped" style="max-width:560px;flex-shrink:0;">
            <thead>
                <tr>
                    <th>Service</th>
                    <th style="text-align:right;width:80px;">リファラー</th>
                    <th style="text-align:right;width:80px;">学習</th>
                    <th style="text-align:right;width:80px;">取得</th>
                    <th style="text-align:right;width:80px;">合計</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $combined ) : foreach ( $combined as $service => $types ) :
                    $total = array_sum($types); ?>
                    <tr>
                        <td><strong><?php echo esc_html($service); ?></strong></td>
                        <td style="text-align:right;"><?php echo intval($types['referer']); ?></td>
                        <td style="text-align:right;"><?php echo intval($types['learning']); ?></td>
                        <td style="text-align:right;"><?php echo intval($types['fetch']); ?></td>
                        <td style="text-align:right;font-weight:700;"><?php echo number_format($total); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="5" style="text-align:center;">データがありません。</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ( $combined ) :
            $max_total = max( array_map( 'array_sum', $combined ) );
        ?>
        <div class="awbase-tracker-bar" style="min-width:240px;padding-top:4px;">
            <?php foreach ( $combined as $service => $types ) :
                $total = array_sum( $types );
                $pct   = $max_total > 0 ? round( $total / $max_total * 100 ) : 0;
            ?>
            <div style="margin-bottom:10px;">
                <div style="font-size:12px;margin-bottom:3px;display:flex;justify-content:space-between;">
                    <span><?php echo esc_html( $service ); ?></span>
                    <span style="font-weight:700;margin-left:8px;"><?php echo number_format( $total ); ?></span>
                </div>
                <div style="background:#e0e0e0;border-radius:3px;height:14px;width:240px;">
                    <div style="background:#0073aa;width:<?php echo $pct; ?>%;height:100%;border-radius:3px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        </div>

        <?php
        // ---- AI向けファイルアクセス集計（サービス × ファイル マトリクス） ----
        $file_counts = get_option( 'awbase_file_access_counts', [] );
        $file_keys   = [
            'llms_txt'      => 'LLMs.txt',
            'ai_index_md'   => 'ai-index.md',
            'llms_full_txt' => 'LLMs-full.txt',
        ];
        // ファイルにアクセスしたサービスを収集
        $file_services = [];
        foreach ( $file_keys as $fkey => $flabel ) {
            if ( ! empty( $file_counts[ $fkey ] ) ) {
                foreach ( array_keys( $file_counts[ $fkey ] ) as $svc ) {
                    $file_services[ $svc ] = true;
                }
            }
        }
        // 表示対象の5サービスを固定順で表示
        $ordered_services = $display_services;
        ?>
        <h2 style="margin-top:28px;">AI向けファイル アクセス集計（全期間）</h2>
        <table class="wp-list-table widefat fixed striped" style="max-width:560px;">
            <thead>
                <tr>
                    <th>Service</th>
                    <?php foreach ( $file_keys as $flabel ) : ?>
                    <th style="text-align:right;width:80px;white-space:nowrap;"><code><?php echo esc_html( $flabel ); ?></code></th>
                    <?php endforeach; ?>
                    <th style="text-align:right;width:80px;">合計</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $ordered_services as $svc ) :
                    $row_total = 0;
                    foreach ( array_keys( $file_keys ) as $fkey ) {
                        $row_total += isset( $file_counts[ $fkey ][ $svc ] ) ? (int) $file_counts[ $fkey ][ $svc ] : 0;
                    }
                ?>
                <tr>
                    <td><strong><?php echo esc_html( $svc ); ?></strong></td>
                    <?php foreach ( array_keys( $file_keys ) as $fkey ) : ?>
                    <td style="text-align:right;"><?php echo number_format( isset( $file_counts[ $fkey ][ $svc ] ) ? (int) $file_counts[ $fkey ][ $svc ] : 0 ); ?></td>
                    <?php endforeach; ?>
                    <td style="text-align:right;font-weight:700;"><?php echo number_format( $row_total ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $table_exists ) :
            $per_page = 50;
            $page     = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
            $offset   = ( $page - 1 ) * $per_page;
            $total    = $wpdb->get_var( "SELECT COUNT(id) FROM {$table}" );
            $logs     = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY accessed_at DESC LIMIT {$per_page} OFFSET {$offset}" );
        ?>
        <h2 style="margin-top:30px;">収集ログ（総件数: <?php echo esc_html($total); ?>件）</h2>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=awbase_ai_tracker&action=download_csv' ), 'awbase_download_csv' ) ); ?>" class="button button-secondary">CSVダウンロード</a>
            <form method="post" style="margin:0;">
                <?php wp_nonce_field( 'awbase_clear_ai_log_nonce' ); ?>
                <input type="submit" name="awbase_clear_ai_log" class="button" style="color:red;border-color:red;" value="ログをクリア" onclick="return confirm('収集ログをすべて削除しますか？');">
            </form>
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:14%;">日時</th>
                    <th style="width:10%;">サービス</th>
                    <th style="width:10%;">種別</th>
                    <th style="width:12%;">ボット</th>
                    <th style="width:28%;">URL</th>
                    <th style="width:14%;">UA</th>
                    <th style="width:12%;">IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $logs ) : foreach ( $logs as $log ) : ?>
                    <tr>
                        <td><?php echo esc_html( $log->accessed_at ); ?></td>
                        <td><?php echo esc_html( $log->ai_service ); ?></td>
                        <td><?php echo esc_html( $log->access_type ); ?></td>
                        <td><?php echo esc_html( $log->bot_identifier ?? '' ); ?></td>
                        <td style="word-break:break-all;"><?php echo esc_html( $log->requested_url ?? '' ); ?></td>
                        <td style="word-break:break-all;font-size:11px;"><?php echo esc_html( $log->user_agent ?? '' ); ?></td>
                        <td><?php echo esc_html( $log->ip_address ?? '' ); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7">データがありません。</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $total_pages = ceil( $total / $per_page );
        if ( $total_pages > 1 ) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links( [
                'base'    => admin_url( 'admin.php?page=awbase_ai_tracker' ) . '%_%',
                'format'  => '&paged=%#%',
                'current' => $page,
                'total'   => $total_pages,
            ] );
            echo '</div></div>';
        }
        ?>
        <?php else : ?>
        <p style="color:red;margin-top:20px;">テーブルが存在しません。テーマを一度無効にしてから再有効化してください。</p>
        <?php endif; ?>

    </div>
    <?php
}

// ---------------------------------------------------------------------------
// 6. 投稿一覧 AI カラム
// ---------------------------------------------------------------------------
function awbase_add_ai_hits_column( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[$key] = $label;
        if ( $key === 'date' ) $new['awbase_ai_hits'] = 'AI';
    }
    return $new;
}
add_filter( 'manage_posts_columns', 'awbase_add_ai_hits_column' );
add_filter( 'manage_pages_columns', 'awbase_add_ai_hits_column' );

// テーマテーブルの期間別カウント（URL照合）
function awbase_get_ai_count( $permalink_path, $days = 0 ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_traffic_log';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) return 0;
    $like = '%' . $wpdb->esc_like( $permalink_path ) . '%';
    if ( $days > 0 ) {
        $since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(id) FROM {$table_name} WHERE requested_url LIKE %s AND accessed_at >= %s",
            $like, $since
        ) );
    }
    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(id) FROM {$table_name} WHERE requested_url LIKE %s", $like
    ) );
}

function awbase_show_ai_hits_column( $column_name, $post_id ) {
    if ( $column_name !== 'awbase_ai_hits' ) return;

    global $wpdb;
    $table = $wpdb->prefix . 'ai_traffic_log';

    static $table_exists = null;
    if ( $table_exists === null ) {
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table;
    }
    if ( ! $table_exists ) { echo '-'; return; }

    // ---- D/W/M/All カウント ----
    $now_ts = current_time( 'timestamp' );
    $d   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id=%d AND accessed_at >= %s", $post_id, gmdate( 'Y-m-d 00:00:00', $now_ts ) ) );
    $w   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id=%d AND accessed_at >= %s", $post_id, gmdate( 'Y-m-d H:i:s', $now_ts - ( 7 * DAY_IN_SECONDS ) ) ) );
    $m   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id=%d AND accessed_at >= %s", $post_id, gmdate( 'Y-m-d H:i:s', $now_ts - ( 30 * DAY_IN_SECONDS ) ) ) );
    $all = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id=%d", $post_id ) );

    // ---- ツールチップ: Service | Ref | Learn | Fetch テーブル ----
    $breakdown = awbase_build_service_breakdown( $post_id );

    echo '<div class="awbase-ai-col">';
    echo '<div class="awbase-ai-counts">';
    printf( '<div>D: %d</div>',   $d );
    printf( '<div>W: %d</div>',   $w );
    printf( '<div>M: %d</div>',   $m );
    printf( '<div>All: %s</div>', number_format($all) );
    echo '</div>';

    echo '<span class="awbase-ai-trigger">[Details]';
    echo '<span class="awbase-ai-tooltip">';
    echo '<table class="awbase-ai-tip-table">';
    echo '<thead><tr>';
    echo '<th>Service</th><th>Ref</th><th>Learn</th><th>Fetch</th>';
    echo '</tr></thead><tbody>';
    if ( ! empty( $breakdown ) ) {
        foreach ( $breakdown as $service => $types ) {
            printf(
                '<tr><td>%s</td><td>%d</td><td>%d</td><td>%d</td></tr>',
                esc_html( $service ),
                intval( $types['referer'] ),
                intval( $types['learning'] ),
                intval( $types['fetch'] )
            );
        }
    } else {
        echo '<tr><td colspan="4" style="text-align:center;opacity:.6;">No Data</td></tr>';
    }
    echo '</tbody></table></span></span>';
    echo '</div>';
}
add_action( 'manage_posts_custom_column', 'awbase_show_ai_hits_column', 10, 2 );
add_action( 'manage_pages_custom_column', 'awbase_show_ai_hits_column', 10, 2 );

// Sortable
function awbase_ai_sortable_column( $columns ) {
    $columns['awbase_ai_hits'] = 'awbase_ai_hits';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'awbase_ai_sortable_column' );

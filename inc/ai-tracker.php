<?php
// AW-Base AI Traffic Tracker
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Create DB Table on Theme Activation
function awbase_create_ai_tracker_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_traffic_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        bot_identifier varchar(255) NOT NULL,
        requested_url text NOT NULL,
        user_agent text NOT NULL,
        ip_address varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'awbase_create_ai_tracker_table' );

// 2. Track Traffic
function awbase_track_ai_bots() {
    $options = get_option('awbase_settings', awbase_get_default_settings());
    if ( $options['ai_tracking_enable'] !== '1' ) return;

    if ( is_admin() ) return;

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ( empty($user_agent) ) return;

    $ai_bots = array(
        'ChatGPT-User',
        'GPTBot',
        'Google-Extended',
        'Anthropic-ai',
        'Claude-Web',
        'PerplexityBot',
        'Omgili',
        'FacebookBot',
        'CCBot',
        'Bytespider'
    );

    $matched_bot = '';
    foreach ( $ai_bots as $bot ) {
        if ( stripos($user_agent, $bot) !== false ) {
            $matched_bot = $bot;
            break;
        }
    }

    if ( ! empty($matched_bot) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ai_traffic_log';
        if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name ) {
            $wpdb->insert(
                $table_name,
                array(
                    'time' => current_time( 'mysql' ),
                    'bot_identifier' => $matched_bot,
                    'requested_url' => esc_url_raw( $_SERVER['REQUEST_URI'] ),
                    'user_agent' => sanitize_textarea_field( $user_agent ),
                    'ip_address' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] )
                )
            );
        }
    }
}
add_action( 'wp', 'awbase_track_ai_bots' );

// 3. Admin Menu for Viewing Logs
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
add_action( 'admin_menu', 'awbase_ai_tracker_menu' );

// 4. Admin Page HTML
function awbase_ai_tracker_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_traffic_log';

    // Handle Delete
    if ( isset($_POST['awbase_clear_ai_log']) && check_admin_referer('awbase_clear_ai_log_nonce') ) {
        $wpdb->query("TRUNCATE TABLE $table_name");
        echo '<div class="updated"><p>ログをクリアしました。</p></div>';
    }

    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
    if ( ! $table_exists ) {
        echo '<div class="wrap"><h1>AIボット トラフィックログ</h1><p>テーブルが存在しません。テーマを切り替え直してテーブルを作成してください。</p></div>';
        return;
    }

    // Pagination
    $per_page = 50;
    $page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
    $offset = ( $page - 1 ) * $per_page;

    $total = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC LIMIT $per_page OFFSET $offset");
    
    // Aggregation
    $stats = $wpdb->get_results("SELECT bot_identifier, COUNT(id) as counts FROM $table_name GROUP BY bot_identifier ORDER BY counts DESC");

    ?>
    <div class="wrap">
        <h1>AIボット トラフィックログ</h1>
        
        <h2>ボット別アクセス集計</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Bot Identifier</th><th>アクセス数</th></tr></thead>
            <tbody>
                <?php if($stats): foreach($stats as $stat): ?>
                    <tr><td><?php echo esc_html($stat->bot_identifier); ?></td><td><?php echo esc_html($stat->counts); ?></td></tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2">データがありません。</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 style="margin-top: 30px;">アクセスログ一覧 (総件数: <?php echo esc_html($total); ?>件)</h2>
        <form method="post" style="margin-bottom: 20px;">
            <?php wp_nonce_field('awbase_clear_ai_log_nonce'); ?>
            <input type="submit" name="awbase_clear_ai_log" class="button button-danger" style="color:red; border-color:red;" value="すべてのログをクリア" onclick="return confirm('本当にすべてのログを削除しますか？');">
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 15%;">日時</th>
                    <th style="width: 15%;">ボット判定</th>
                    <th style="width: 30%;">リクエストURL</th>
                    <th style="width: 25%;">ユーザーエージェント</th>
                    <th style="width: 15%;">IPアドレス</th>
                </tr>
            </thead>
            <tbody>
                <?php if($logs): foreach($logs as $log): ?>
                    <tr>
                        <td><?php echo esc_html($log->time); ?></td>
                        <td><?php echo esc_html($log->bot_identifier); ?></td>
                        <td><?php echo esc_html($log->requested_url); ?></td>
                        <td><?php echo esc_html($log->user_agent); ?></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5">データがありません。</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
        $total_pages = ceil($total / $per_page);
        if ($total_pages > 1) {
            $current_url = admin_url('admin.php?page=awbase_ai_tracker');
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links(array(
                'base' => $current_url . '%_%',
                'format' => '&paged=%#%',
                'current' => $page,
                'total' => $total_pages
            ));
            echo '</div></div>';
        }
        ?>
    </div>
    <?php
}

// 5. Add Custom Column in Post List to show AI hits for the post
function awbase_add_ai_hits_column($columns) {
    $columns['awbase_ai_hits'] = 'AIアクセス';
    return $columns;
}
add_filter('manage_posts_columns', 'awbase_add_ai_hits_column');
add_filter('manage_pages_columns', 'awbase_add_ai_hits_column');

function awbase_show_ai_hits_column($column_name, $post_id) {
    if ($column_name == 'awbase_ai_hits') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ai_traffic_log';
        if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name ) {
            // Very rudimentary matching - just checks if requested_url includes the post slug/path
            $permalink = str_replace(home_url(), '', get_permalink($post_id));
            if ($permalink === '/' || empty($permalink)) {
                echo "-";
            } else {
                $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $table_name WHERE requested_url LIKE %s", '%' . $wpdb->esc_like($permalink) . '%'));
                echo intval($count) . ' 回';
            }
        } else {
            echo '-';
        }
    }
}
add_action('manage_posts_custom_column', 'awbase_show_ai_hits_column', 10, 2);
add_action('manage_pages_custom_column', 'awbase_show_ai_hits_column', 10, 2);

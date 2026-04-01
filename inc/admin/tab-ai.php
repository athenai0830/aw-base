<?php
// AW-Base Admin Tab: AI Tracking
if ( ! defined( 'ABSPATH' ) ) exit;

// We will fetch stats from ai-traffic-tracker later on here
global $wpdb;
$table_name = $wpdb->prefix . 'ai_traffic_log';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
$total_count = 0;
if ($table_exists) {
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
}
?>
<table class="form-table">
    <tr>
        <th>AI ボットトラッキング機能</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[ai_tracking_enable]" value="1" <?php checked('1', $options['ai_tracking_enable']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ChatGPT・GPTBot などのクローラーを記録・集計する</span>
            </label>
        </td>
    </tr>
    <?php if ( $options['ai_tracking_enable'] == '1' ): ?>
    <tr>
        <th>トラッキング状況</th>
        <td>
            <?php if ($table_exists): ?>
                <p>現在の総記録数: <strong><?php echo number_format($total_count); ?></strong> 件</p>
                <p><a href="<?php echo admin_url('admin.php?page=awbase_ai_tracker'); ?>" class="button button-secondary">詳細なログと集計を見る</a></p>
            <?php else: ?>
                <p style="color:red">トラッキングテーブルが存在しません。テーマを一度無効にしてから再度有効化してください。</p>
            <?php endif; ?>
        </td>
    </tr>
    <?php endif; ?>
</table>

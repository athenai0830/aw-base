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
        <th>LLMs.txt</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[llms_txt_enable]" value="1" <?php checked('1', $options['llms_txt_enable']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">LLMs.txtを生成する（/llms.txt）</span>
            </label>
            <div class="awbase-field-row" style="margin-top: 12px;">
                <textarea name="awbase_settings[llms_txt_content]" class="large-text" rows="8"><?php echo esc_textarea($options['llms_txt_content']); ?></textarea>
            </div>
            <p class="description">Markdown形式で記述。AI向けに自サイトの構成や概要を渡します。</p>
            <div style="margin-top:16px;">
                <label class="awbase-toggle">
                    <input type="checkbox" name="awbase_settings[llms_full_txt_enable]" value="1" <?php checked('1', $options['llms_full_txt_enable'] ?? '0'); ?>>
                    <span class="awbase-toggle-slider"></span>
                    <span class="awbase-toggle-label">LLMs-full.txtを生成する（/llms-full.txt）</span>
                </label>
                <p class="description" style="margin-top:6px;">有効にすると、公開済みの投稿・固定ページの本文をすべて連結して <code>/llms-full.txt</code> として動的出力します。</p>
            </div>
        </td>
    </tr>
    <tr>
        <th>ai-index.md</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[ai_index_md_enable]" value="1" <?php checked('1', $options['ai_index_md_enable'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ai-index.mdを生成する（/ai-index.md）</span>
            </label>
            <p class="description">有効にすると、各投稿のai-index設定（メタボックス内）に基づき <code>/ai-index.md</code> を動的生成します。</p>
        </td>
    </tr>
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

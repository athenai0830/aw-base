<?php
/**
 * AW-Base 管理画面 – 画像最適化タブ
 *
 * @package AW-Base
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$sizes     = awbase_get_thumb_sizes();
$dir       = awbase_thumb_dir();
$all_ids   = get_posts( [
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => -1,
    'fields'         => 'ids',
] );
$total     = count( $all_ids );
$optimized = 0;
foreach ( $all_ids as $id ) {
    $ok = true;
    foreach ( $sizes as [ $w, $h ] ) {
        if ( ! file_exists( "{$dir}/{$id}-{$w}x{$h}.jpg" ) ) {
            $ok = false;
            break;
        }
    }
    if ( $ok ) $optimized++;
}
$pending = $total - $optimized;
?>
<div class="awbase-tab-section">
    <h2>画像最適化</h2>
    <p>
        テーマ専用サムネイル（WebP + JPEG）を生成します。<br>
        元画像は変更せず、<code>uploads/aw-thumbs/</code> に独立して保存されます。<br>
        新規アップロードは自動処理されます。
    </p>

    <table class="awb-img-stats-table">
        <tr>
            <th>生成サイズ</th>
            <td>
                <?php foreach ( $sizes as $key => [ $w, $h ] ) : ?>
                    <code><?php echo esc_html( $key ); ?></code> (<?php echo $w; ?> × <?php echo $h; ?>px)&nbsp;
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th>保存先</th>
            <td><code><?php echo esc_html( $dir ); ?></code></td>
        </tr>
        <tr>
            <th>総画像数</th>
            <td id="awb-total-count"><?php echo esc_html( $total ); ?> 枚</td>
        </tr>
        <tr>
            <th>最適化済み</th>
            <td id="awb-optimized-count"><?php echo esc_html( $optimized ); ?> 枚</td>
        </tr>
        <tr>
            <th>未処理</th>
            <td id="awb-pending-count"><?php echo esc_html( $pending ); ?> 枚</td>
        </tr>
    </table>

    <div class="awb-img-actions">
        <button type="button" id="awb-optimize-btn" class="button button-primary"
            <?php echo $pending === 0 ? 'disabled' : ''; ?>>
            <?php echo $pending === 0 ? '最適化済み' : "すべて最適化（{$pending} 枚）"; ?>
        </button>
        <button type="button" id="awb-delete-btn" class="button"
            <?php echo $optimized === 0 ? 'disabled' : ''; ?>>
            生成ファイルをすべて削除
        </button>
    </div>

    <div id="awb-progress-wrap" style="display:none;">
        <div class="awb-progress-bar-bg">
            <div id="awb-progress-bar" class="awb-progress-bar-fill"></div>
        </div>
        <p id="awb-progress-status"></p>
    </div>

    <div id="awb-result-msg" style="display:none;"></div>
</div>

<style>
.awb-img-stats-table { border-collapse: collapse; margin: 12px 0 20px; }
.awb-img-stats-table th,
.awb-img-stats-table td { padding: 6px 20px 6px 0; vertical-align: top; font-size: 13px; }
.awb-img-stats-table th { font-weight: 600; color: #444; white-space: nowrap; width: 100px; }
.awb-img-actions { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; }
.awb-progress-bar-bg { background: #e0e0e0; border-radius: 4px; height: 18px; max-width: 420px; overflow: hidden; }
.awb-progress-bar-fill { background: #2271b1; height: 100%; width: 0%; transition: width 0.3s ease; }
#awb-progress-status { margin: 6px 0 0; color: #555; font-size: 13px; }
#awb-result-msg { margin-top: 12px; padding: 10px 14px; border-radius: 4px; font-size: 13px; }
#awb-result-msg.is-success { background: #edfaed; border-left: 4px solid #46b450; color: #1a5e1d; }
#awb-result-msg.is-error   { background: #fde8e8; border-left: 4px solid #d63638; color: #8a1f1f; }
</style>

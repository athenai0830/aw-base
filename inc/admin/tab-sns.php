<?php
// AW-Base Admin Tab: SNS Share
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>表示位置（記事詳細ページ）</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_below_eyecatch]" value="1" <?php checked('1', $options['sns_share_below_eyecatch'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">アイキャッチ画像の下に表示</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_above_author]" value="1" <?php checked('1', $options['sns_share_above_author']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">著者ボックスの上に表示（記事下）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>表示するSNS</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_twitter]" value="1" <?php checked('1', $options['sns_share_twitter']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-brands fa-x-twitter"></i> X (Twitter)</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_facebook]" value="1" <?php checked('1', $options['sns_share_facebook']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-brands fa-facebook-f"></i> Facebook</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_line]" value="1" <?php checked('1', $options['sns_share_line']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-brands fa-line"></i> LINE</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_pocket]" value="1" <?php checked('1', $options['sns_share_pocket']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-brands fa-get-pocket"></i> Pocket</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_hatena]" value="1" <?php checked('1', $options['sns_share_hatena']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">B! はてなブックマーク</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_feedly]" value="1" <?php checked('1', $options['sns_share_feedly'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-solid fa-rss"></i> フィード（Feedly）</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_pinterest]" value="1" <?php checked('1', $options['sns_share_pinterest'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-brands fa-pinterest-p"></i> Pinterest</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sns_share_copy]" value="1" <?php checked('1', $options['sns_share_copy']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label"><i class="fa-solid fa-link"></i> URLコピー</span>
            </label>
        </td>
    </tr>
</table>

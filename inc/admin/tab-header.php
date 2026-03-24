<?php
// AW-Base Admin Tab: Header
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>ヘッダーロゴ画像URL</th>
        <td>
            <input type="url" name="awbase_settings[logo_image]" value="<?php echo esc_url($options['logo_image']); ?>" class="regular-text"><br>
            <span class="description">メディアからアップロードした画像のURLを入力してください。</span>
        </td>
    </tr>
    <tr>
        <th>ロゴサイズ・URL</th>
        <td>
            幅: <input type="number" name="awbase_settings[logo_width]" value="<?php echo esc_attr($options['logo_width']); ?>" class="small-text"> px<br>
            高さ: <input type="number" name="awbase_settings[logo_height]" value="<?php echo esc_attr($options['logo_height']); ?>" class="small-text"> px<br>
            リンク先URL: <input type="url" name="awbase_settings[logo_url]" value="<?php echo esc_url($options['logo_url']); ?>" class="regular-text"> <span class="description">（空欄でサイトTOP）</span><br>
            代替テキスト: <input type="text" name="awbase_settings[logo_alt]" value="<?php echo esc_attr($options['logo_alt']); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th>ヘッダーロゴ位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[header_logo_align]" value="left" <?php checked('left', $options['header_logo_align']); ?>> 左寄せ</label>
            <label><input type="radio" name="awbase_settings[header_logo_align]" value="center" <?php checked('center', $options['header_logo_align']); ?>> 中央</label>
            <label><input type="radio" name="awbase_settings[header_logo_align]" value="right" <?php checked('right', $options['header_logo_align']); ?>> 右寄せ</label>
        </td>
    </tr>
    <tr>
        <th>キャッチコピー</th>
        <td>
            <input type="text" name="awbase_settings[catchphrase]" value="<?php echo esc_attr($options['catchphrase']); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th>ヘッダー表示パターン</th>
        <td>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="1" <?php checked('1', $options['header_pattern']); ?>> パターン1（ナビ→ヘッダー→通知→FV）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="2" <?php checked('2', $options['header_pattern']); ?>> パターン2（通知→FV→ヘッダー→ナビ）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="3" <?php checked('3', $options['header_pattern']); ?>> パターン3（FV→ヘッダー→通知→ナビ）</label>
        </td>
    </tr>
    <tr>
        <th>ナビメニュー位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[nav_align]" value="left" <?php checked('left', $options['nav_align']); ?>> 左寄せ</label>
            <label><input type="radio" name="awbase_settings[nav_align]" value="center" <?php checked('center', $options['nav_align']); ?>> 中央寄せ</label>
            <label><input type="radio" name="awbase_settings[nav_align]" value="right" <?php checked('right', $options['nav_align']); ?>> 右寄せ</label>
        </td>
    </tr>
    <tr>
        <th>通知エリア テキスト</th>
        <td>
            <input type="text" name="awbase_settings[notice_text]" value="<?php echo esc_attr($options['notice_text']); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th>通知エリア リンクURL</th>
        <td>
            <input type="url" name="awbase_settings[notice_url]" value="<?php echo esc_url($options['notice_url']); ?>" class="regular-text">
        </td>
    </tr>
</table>

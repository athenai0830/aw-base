<?php
// AW-Base Admin Tab: Header
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>ヘッダーロゴ画像</th>
        <td>
            <div class="awbase-image-upload-wrap">
                <img id="logo_image_preview" class="awbase-image-preview <?php echo !empty($options['logo_image']) ? 'has-image' : ''; ?>"
                     src="<?php echo esc_url($options['logo_image']); ?>" alt="">
                <div>
                    <input type="url" id="logo_image_input" name="awbase_settings[logo_image]"
                           value="<?php echo esc_url($options['logo_image']); ?>"
                           class="regular-text awbase-image-input" data-preview="logo_image_preview"><br>
                    <button type="button" class="awbase-upload-btn"
                            data-input="logo_image_input" data-preview="logo_image_preview">
                        <i class="fa-solid fa-upload"></i> 画像を選択
                    </button>
                    <button type="button" class="awbase-remove-btn"
                            data-input="logo_image_input" data-preview="logo_image_preview">削除</button>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <th>ヘッダー高さ</th>
        <td>
            <input type="number" name="awbase_settings[header_height]" value="<?php echo esc_attr($options['header_height']); ?>" class="small-text" min="40" max="300"> px
            <p class="description">ヘッダーエリアの最小高さ（デフォルト: 100px）</p>
        </td>
    </tr>
    <tr>
        <th>ロゴサイズ・URL</th>
        <td>
            <p class="awbase-field-row">幅: <input type="number" name="awbase_settings[logo_width]" value="<?php echo esc_attr($options['logo_width']); ?>" class="small-text"> px &nbsp; 高さ: <input type="number" name="awbase_settings[logo_height]" value="<?php echo esc_attr($options['logo_height']); ?>" class="small-text"> px</p>
            <p class="awbase-field-row">リンク先URL: <input type="url" name="awbase_settings[logo_url]" value="<?php echo esc_url($options['logo_url']); ?>" class="regular-text"> <span class="description">（空欄でサイトTOP）</span></p>
            <p class="awbase-field-row">代替テキスト: <input type="text" name="awbase_settings[logo_alt]" value="<?php echo esc_attr($options['logo_alt']); ?>" class="regular-text"></p>
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
        <th>ヘッダー表示パターン<br><small>（TOPページ）</small></th>
        <td>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="1" <?php checked('1', $options['header_pattern']); ?>> パターン1（ナビ→ヘッダー→通知→FV）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="2" <?php checked('2', $options['header_pattern']); ?>> パターン2（通知→FV→ヘッダー→ナビ）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="3" <?php checked('3', $options['header_pattern']); ?>> パターン3（FV→ヘッダー→通知→ナビ）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="4" <?php checked('4', $options['header_pattern']); ?>> パターン4（ヘッダー→ナビ→通知→FV）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern]" value="5" <?php checked('5', $options['header_pattern']); ?>> パターン5（FV→ヘッダー→ナビ→通知）</label>
        </td>
    </tr>
    <tr>
        <th>ヘッダー表示パターン<br><small>（下層ページ）</small></th>
        <td>
            <label><input type="radio" name="awbase_settings[header_pattern_sub]" value="" <?php checked('', $options['header_pattern_sub'] ?? ''); ?>> TOPと同じ</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern_sub]" value="1" <?php checked('1', $options['header_pattern_sub'] ?? ''); ?>> パターン1（ナビ→ヘッダー→通知）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern_sub]" value="2" <?php checked('2', $options['header_pattern_sub'] ?? ''); ?>> パターン2（通知→ヘッダー→ナビ）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern_sub]" value="3" <?php checked('3', $options['header_pattern_sub'] ?? ''); ?>> パターン3（ヘッダー→通知→ナビ）</label><br>
            <label><input type="radio" name="awbase_settings[header_pattern_sub]" value="4" <?php checked('4', $options['header_pattern_sub'] ?? ''); ?>> パターン4（ヘッダー→ナビ→通知）</label>
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

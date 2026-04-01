<?php
// AW-Base Admin Tab: Footer
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>フッターロゴ画像</th>
        <td>
            <input type="text" id="awbase_footer_logo_image" name="awbase_settings[footer_logo_image]" value="<?php echo esc_url($options['footer_logo_image']); ?>" class="awbase-image-input regular-text" placeholder="https://..." data-preview="awbase_footer_logo_image_preview">
            <br>
            <img id="awbase_footer_logo_image_preview" class="awbase-image-preview" src="<?php echo esc_url($options['footer_logo_image']); ?>" alt="">
            <br>
            <button type="button" class="awbase-upload-btn button" data-input="awbase_footer_logo_image" data-preview="awbase_footer_logo_image_preview">メディアライブラリから選択</button>
            <button type="button" class="awbase-remove-btn button" data-input="awbase_footer_logo_image" data-preview="awbase_footer_logo_image_preview">削除</button>
            <p class="description">URLを直接入力するか「メディアライブラリから選択」ボタンを使用してください。</p>
        </td>
    </tr>
    <tr>
        <th>ロゴサイズ・URL</th>
        <td>
            <p class="awbase-field-row">幅: <input type="number" name="awbase_settings[footer_logo_width]" value="<?php echo esc_attr($options['footer_logo_width']); ?>" class="small-text"> px</p>
            <p class="awbase-field-row">リンク先URL: <input type="url" name="awbase_settings[footer_logo_url]" value="<?php echo esc_url($options['footer_logo_url']); ?>" class="regular-text"></p>
        </td>
    </tr>
    <tr>
        <th>フッターロゴ表示位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[footer_logo_align]" value="left" <?php checked('left', $options['footer_logo_align']); ?>> 左寄せ</label>
            <label><input type="radio" name="awbase_settings[footer_logo_align]" value="center" <?php checked('center', $options['footer_logo_align']); ?>> 中央</label>
            <label><input type="radio" name="awbase_settings[footer_logo_align]" value="right" <?php checked('right', $options['footer_logo_align']); ?>> 右寄せ</label>
        </td>
    </tr>
    <tr>
        <th>フッターメニュー位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[footer_nav_align]" value="left" <?php checked('left', $options['footer_nav_align']); ?>> 左寄せ</label>
            <label><input type="radio" name="awbase_settings[footer_nav_align]" value="center" <?php checked('center', $options['footer_nav_align']); ?>> 中央</label>
            <label><input type="radio" name="awbase_settings[footer_nav_align]" value="right" <?php checked('right', $options['footer_nav_align']); ?>> 右寄せ</label>
        </td>
    </tr>
    <tr>
        <th>コピーライト表記</th>
        <td>
            <input type="text" name="awbase_settings[footer_copyright]" value="<?php echo esc_attr($options['footer_copyright']); ?>" class="regular-text">
        </td>
    </tr>
</table>

<?php
// AW-Base Admin Tab: Footer
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>フッターロゴ画像URL</th>
        <td>
            <input type="url" name="awbase_settings[footer_logo_image]" value="<?php echo esc_url($options['footer_logo_image']); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th>ロゴサイズ・URL</th>
        <td>
            幅: <input type="number" name="awbase_settings[footer_logo_width]" value="<?php echo esc_attr($options['footer_logo_width']); ?>" class="small-text"> px<br>
            リンク先URL: <input type="url" name="awbase_settings[footer_logo_url]" value="<?php echo esc_url($options['footer_logo_url']); ?>" class="regular-text">
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

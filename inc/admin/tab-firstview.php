<?php
// AW-Base Admin Tab: First View
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>FV背景画像URL</th>
        <td>
            <input type="url" name="awbase_settings[fv_bg_image]" value="<?php echo esc_url($options['fv_bg_image']); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th>FV高さ</th>
        <td>
            <input type="number" name="awbase_settings[fv_height]" value="<?php echo esc_attr($options['fv_height']); ?>" class="small-text">
            <select name="awbase_settings[fv_height_unit]">
                <option value="vh" <?php selected('vh', $options['fv_height_unit']); ?>>vh</option>
                <option value="px" <?php selected('px', $options['fv_height_unit']); ?>>px</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>FV重ねロゴ画像URL</th>
        <td>
            <input type="url" name="awbase_settings[fv_logo_image]" value="<?php echo esc_url($options['fv_logo_image']); ?>" class="regular-text"> <span class="description">透過PNG推奨</span>
        </td>
    </tr>
    <tr>
        <th>オーバーレイ色/濃さ</th>
        <td>
            <label><input type="radio" name="awbase_settings[overlay_color]" value="black" <?php checked('black', $options['overlay_color']); ?>> 黒</label>
            <label><input type="radio" name="awbase_settings[overlay_color]" value="white" <?php checked('white', $options['overlay_color']); ?>> 白</label>
            <br>
            濃さ: <input type="number" name="awbase_settings[overlay_opacity]" value="<?php echo esc_attr($options['overlay_opacity']); ?>" class="small-text" min="0" max="100"> %
        </td>
    </tr>
    <tr>
        <th>ドットパターン</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[fv_dot_pattern]" value="1" <?php checked('1', $options['fv_dot_pattern']); ?>> ドットパターンを表示する</label>
        </td>
    </tr>
    <tr>
        <th>キャッチコピー</th>
        <td>
            <input type="text" name="awbase_settings[fv_catchphrase]" value="<?php echo esc_attr($options['fv_catchphrase']); ?>" class="regular-text">
        </td>
    </tr>
</table>

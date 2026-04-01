<?php
// AW-Base Admin Tab: First View
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>FV背景画像</th>
        <td>
            <input type="text" id="awbase_fv_bg_image" name="awbase_settings[fv_bg_image]" value="<?php echo esc_url($options['fv_bg_image']); ?>" class="awbase-image-input regular-text" placeholder="https://..." data-preview="awbase_fv_bg_image_preview">
            <br>
            <img id="awbase_fv_bg_image_preview" class="awbase-image-preview" src="<?php echo esc_url($options['fv_bg_image']); ?>" alt="">
            <br>
            <button type="button" class="awbase-upload-btn button" data-input="awbase_fv_bg_image" data-preview="awbase_fv_bg_image_preview">メディアライブラリから選択</button>
            <button type="button" class="awbase-remove-btn button" data-input="awbase_fv_bg_image" data-preview="awbase_fv_bg_image_preview">削除</button>
            <p class="description">URLを直接入力するか「メディアライブラリから選択」ボタンを使用してください。</p>
        </td>
    </tr>
    <tr>
        <th>FV高さ</th>
        <td>
            <p class="awbase-field-row">
                <input type="number" name="awbase_settings[fv_height]" value="<?php echo esc_attr($options['fv_height']); ?>" class="small-text">
                <select name="awbase_settings[fv_height_unit]">
                    <option value="vh" <?php selected('vh', $options['fv_height_unit']); ?>>vh</option>
                    <option value="px" <?php selected('px', $options['fv_height_unit']); ?>>px</option>
                </select>
            </p>
        </td>
    </tr>
    <tr>
        <th>FV重ねロゴ画像</th>
        <td>
            <div class="awbase-field-row">
                <input type="text" id="awbase_fv_logo_image" name="awbase_settings[fv_logo_image]" value="<?php echo esc_url($options['fv_logo_image']); ?>" class="awbase-image-input regular-text" placeholder="https://..." data-preview="awbase_fv_logo_image_preview">
                <img id="awbase_fv_logo_image_preview" class="awbase-image-preview <?php echo !empty($options['fv_logo_image']) ? 'has-image' : ''; ?>" src="<?php echo esc_url($options['fv_logo_image']); ?>" alt="">
            </div>
            <p class="awbase-field-row">
                <button type="button" class="awbase-upload-btn button" data-input="awbase_fv_logo_image" data-preview="awbase_fv_logo_image_preview">メディアライブラリから選択</button>
                <button type="button" class="awbase-remove-btn button" data-input="awbase_fv_logo_image" data-preview="awbase_fv_logo_image_preview">削除</button>
            </p>
            <p class="description">URLを直接入力するか「メディアライブラリから選択」ボタンを使用してください。透過PNG推奨。</p>
            <p class="awbase-field-row" style="margin-top:12px;">
                リンクURL: <input type="url" name="awbase_settings[fv_logo_url]" value="<?php echo esc_url($options['fv_logo_url'] ?? ''); ?>" class="regular-text" placeholder="https://...（空欄でリンクなし）">
            </p>
            <p class="awbase-field-row">
                幅: <input type="number" name="awbase_settings[fv_logo_width]" value="<?php echo esc_attr($options['fv_logo_width'] ?? 300); ?>" class="small-text" min="0"> px
                &nbsp;&nbsp;
                高さ: <input type="number" name="awbase_settings[fv_logo_height]" value="<?php echo esc_attr($options['fv_logo_height'] ?? 0); ?>" class="small-text" min="0"> px
                <span class="description" style="display:inline;">&nbsp;（0 = 幅に合わせて自動）</span>
            </p>
        </td>
    </tr>
    <tr>
        <th>オーバーレイ色/濃さ</th>
        <td>
            <p class="awbase-field-row">
                <label><input type="radio" name="awbase_settings[overlay_color]" value="black" <?php checked('black', $options['overlay_color']); ?>> 黒</label>
                &nbsp;
                <label><input type="radio" name="awbase_settings[overlay_color]" value="white" <?php checked('white', $options['overlay_color']); ?>> 白</label>
            </p>
            <p class="awbase-field-row">濃さ: <input type="number" name="awbase_settings[overlay_opacity]" value="<?php echo esc_attr($options['overlay_opacity']); ?>" class="small-text" min="0" max="100"> %</p>
        </td>
    </tr>
    <tr>
        <th>ドットパターン</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[fv_dot_pattern]" value="1" <?php checked('1', $options['fv_dot_pattern']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ドットパターンを表示する</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>キャッチコピー</th>
        <td>
            <textarea name="awbase_settings[fv_catchphrase]" rows="4" class="large-text"><?php echo esc_textarea($options['fv_catchphrase']); ?></textarea>
            <p class="description">HTMLタグ使用可（例: <code>&lt;br&gt;</code> で改行）。<br>使用可能タグ: p, br, strong, em, span, a</p>
        </td>
    </tr>
    <tr>
        <th>Substackボタン</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[fv_substack_enable]" value="1" <?php checked('1', $options['fv_substack_enable']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">Substackボタンを表示する</span>
            </label>
            <p class="awbase-field-row">ボタンテキスト: <input type="text" name="awbase_settings[fv_substack_text]" value="<?php echo esc_attr($options['fv_substack_text']); ?>" class="regular-text"></p>
            <p class="awbase-field-row">Substack URL: <input type="url" name="awbase_settings[fv_substack_url]" value="<?php echo esc_url($options['fv_substack_url']); ?>" class="regular-text"></p>
        </td>
    </tr>
</table>

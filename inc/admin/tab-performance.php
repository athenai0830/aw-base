<?php
// AW-Base Admin Tab: Performance
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>CSS / JS 最適化</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[css_minify]" value="1" <?php checked('1', $options['css_minify']); ?>> CSSを縮小（ミニファイ）して出力する</label><br>
            <label><input type="checkbox" name="awbase_settings[js_minify]" value="1" <?php checked('1', $options['js_minify']); ?>> JSを縮小して出力し、フッターで読み込む</label>
        </td>
    </tr>
    <tr>
        <th>画像最適化</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[disable_image_sizes]" value="1" <?php checked('1', $options['disable_image_sizes']); ?>> WP標準の不要な画像サイズ生成を停止する（thumbnail, medium 等）</label>
        </td>
    </tr>
    <tr>
        <th>レイジーロード (画像遅延読み込み)</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[lazy_load]" value="1" <?php checked('1', $options['lazy_load']); ?>> 画像の遅延読み込みを有効にする (loading="lazy")</label><br>
            <label><input type="checkbox" name="awbase_settings[lazy_load_fv_exclude]" value="1" <?php checked('1', $options['lazy_load_fv_exclude']); ?>> ファーストビューの画像を遅延読み込みから除外 (fetchpriority="high")</label><br>
            <label><input type="checkbox" name="awbase_settings[lazy_load_thumb_exclude]" value="1" <?php checked('1', $options['lazy_load_thumb_exclude']); ?>> アイキャッチ画像を遅延読み込みから除外</label>
        </td>
    </tr>
    <tr>
        <th>reCAPTCHA 制御</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[recaptcha_limit]" value="1" <?php checked('1', $options['recaptcha_limit']); ?>> お問い合わせページ以外で reCAPTCHA の読み込みを制限する</label><br>
            <span class="description">※Contact Form 7 と reCAPTCHA v3 の併用時に全ページでスクリプトが読み込まれるのを防ぎます。</span>
        </td>
    </tr>
</table>

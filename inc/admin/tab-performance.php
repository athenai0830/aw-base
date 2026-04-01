<?php
// AW-Base Admin Tab: Performance
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>CSS / JS 最適化</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[css_minify]" value="1" <?php checked('1', $options['css_minify']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">CSSを縮小（ミニファイ）して出力する</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[js_minify]" value="1" <?php checked('1', $options['js_minify']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">JSを縮小して出力し、フッターで読み込む</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>画像最適化</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[disable_image_sizes]" value="1" <?php checked('1', $options['disable_image_sizes']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">WP標準の不要な画像サイズ生成を停止する（thumbnail, medium 等）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>レイジーロード (画像遅延読み込み)</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[lazy_load]" value="1" <?php checked('1', $options['lazy_load']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">画像の遅延読み込みを有効にする (loading="lazy")</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[lazy_load_fv_exclude]" value="1" <?php checked('1', $options['lazy_load_fv_exclude']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ファーストビューの画像を遅延読み込みから除外 (fetchpriority="high")</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[lazy_load_thumb_exclude]" value="1" <?php checked('1', $options['lazy_load_thumb_exclude']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">アイキャッチ画像を遅延読み込みから除外</span>
            </label>
        </td>
    </tr>
    <tr>
        <th colspan="2" style="padding-top:2em; font-size:1.1em; border-top:2px solid #ddd;">
            <i class="fa-solid fa-trash-can"></i> WordPress 不要リソースの除去
        </th>
    </tr>
    <tr>
        <th>WordPress ブロート除去</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[remove_wp_bloat]" value="1" <?php checked('1', $options['remove_wp_bloat'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">絵文字スクリプト・不要な &lt;head&gt; タグ・wp-embed を除去する</span>
            </label>
            <p class="description">print_emoji / rsd_link / wlwmanifest / wp_generator / wp-embed 等を削除します。ほぼ全サイトで安全に有効化できます。</p>
        </td>
    </tr>
    <tr>
        <th>Dashicons 除去</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[remove_dashicons]" value="1" <?php checked('1', $options['remove_dashicons'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">非ログイン時に Dashicons CSS（約30KB）を除去する</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>ブロックライブラリ CSS 除去</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[remove_block_css]" value="1" <?php checked('1', $options['remove_block_css'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">wp-block-library / global-styles CSS（合計約50KB）を除去する</span>
            </label>
            <p class="description">Gutenberg ブロックのデフォルトスタイルを削除します。ブロックの見た目が崩れる場合はオフにしてください。</p>
        </td>
    </tr>
    <tr>
        <th>jQuery 除去</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[remove_jquery]" value="1" <?php checked('1', $options['remove_jquery'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">非ログイン時に jQuery（約90KB）を除去する</span>
            </label>
            <p class="description">⚠️ jQuery を必要とするプラグイン（Contact Form 7 等）を使用している場合は <strong>オフ</strong> にしてください。</p>
        </td>
    </tr>
    <tr>
        <th>reCAPTCHA 制御</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[recaptcha_limit]" value="1" <?php checked('1', $options['recaptcha_limit']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">お問い合わせページ以外で reCAPTCHA の読み込みを制限する</span>
            </label><br>
            <span class="description">※Contact Form 7 と reCAPTCHA v3 の併用時に全ページでスクリプトが読み込まれるのを防ぎます。</span>
        </td>
    </tr>
</table>

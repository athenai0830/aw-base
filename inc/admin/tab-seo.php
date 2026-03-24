<?php
// AW-Base Admin Tab: SEO
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>サイトマップ生成</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[sitemap_enable]" value="1" <?php checked('1', $options['sitemap_enable']); ?>> XMLサイトマップを生成する（/sitemap.xml）</label>
        </td>
    </tr>
    <tr>
        <th>LLMs.txt</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[llms_txt_enable]" value="1" <?php checked('1', $options['llms_txt_enable']); ?>> LLMs.txtを生成する（/llms.txt）</label><br><br>
            <textarea name="awbase_settings[llms_txt_content]" class="large-text" rows="8"><?php echo esc_textarea($options['llms_txt_content']); ?></textarea>
            <p class="description">Markdown形式で記述。AI向けに自サイトの構成や概要を渡します。</p>
        </td>
    </tr>
    <tr>
        <th>noindex 設定全体</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[noindex_search]" value="1" <?php checked('1', $options['noindex_search']); ?>> 検索結果ページ</label><br>
            <label><input type="checkbox" name="awbase_settings[noindex_404]" value="1" <?php checked('1', $options['noindex_404']); ?>> 404ページ</label><br>
            <label><input type="checkbox" name="awbase_settings[noindex_date]" value="1" <?php checked('1', $options['noindex_date']); ?>> 日付アーカイブ</label><br>
            <label><input type="checkbox" name="awbase_settings[noindex_author]" value="1" <?php checked('1', $options['noindex_author']); ?>> 著者アーカイブ</label><br>
            <label><input type="checkbox" name="awbase_settings[noindex_tag]" value="1" <?php checked('1', $options['noindex_tag']); ?>> タグアーカイブ</label><br>
            <label><input type="checkbox" name="awbase_settings[noindex_paged]" value="1" <?php checked('1', $options['noindex_paged']); ?>> ページネーション（2ページ以降）</label>
        </td>
    </tr>
</table>

<?php
// AW-Base Admin Tab: SEO
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>サイトマップ生成</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[sitemap_enable]" value="1" <?php checked('1', $options['sitemap_enable']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">XMLサイトマップを生成する（/sitemap.xml）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>LLMs.txt</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[llms_txt_enable]" value="1" <?php checked('1', $options['llms_txt_enable']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">LLMs.txtを生成する（/llms.txt）</span>
            </label>
            <div class="awbase-field-row" style="margin-top: 12px;">
                <textarea name="awbase_settings[llms_txt_content]" class="large-text" rows="8"><?php echo esc_textarea($options['llms_txt_content']); ?></textarea>
            </div>
            <p class="description">Markdown形式で記述。AI向けに自サイトの構成や概要を渡します。</p>
            <div style="margin-top:16px;">
                <label class="awbase-toggle">
                    <input type="checkbox" name="awbase_settings[llms_full_txt_enable]" value="1" <?php checked('1', $options['llms_full_txt_enable'] ?? '0'); ?>>
                    <span class="awbase-toggle-slider"></span>
                    <span class="awbase-toggle-label">LLMs-full.txtを生成する（/llms-full.txt）</span>
                </label>
                <div class="awbase-field-row" style="margin-top: 12px;">
                    <textarea name="awbase_settings[llms_full_txt_content]" class="large-text" rows="8"><?php echo esc_textarea($options['llms_full_txt_content'] ?? ''); ?></textarea>
                </div>
                <p class="description">LLMs.txtより詳細な全文コンテンツ。Markdown形式で自由に記述。</p>
            </div>
        </td>
    </tr>
    <tr>
        <th>ai-index.md</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[ai_index_md_enable]" value="1" <?php checked('1', $options['ai_index_md_enable'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ai-index.mdを生成する（/ai-index.md）</span>
            </label>
            <p class="description">有効にすると、各投稿のai-index設定（メタボックス内）に基づき /ai-index.md を動的生成します。</p>
        </td>
    </tr>
    <tr>
        <th>Canonicalタグ</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[canonical_paged_to_p1]" value="1" <?php checked('1', $options['canonical_paged_to_p1'] ?? '0'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ページネーション（2ページ以降）のcanonicalをページ1に向ける</span>
            </label>
            <p class="description">有効にすると <code>/category/news/page/2/</code> などのcanonicalが <code>/category/news/</code> になります。<br>無効の場合は各ページが自身のURLをcanonicalとして持ちます。</p>
        </td>
    </tr>
    <tr>
        <th>noindex 設定全体</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_search]" value="1" <?php checked('1', $options['noindex_search']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">検索結果ページ</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_404]" value="1" <?php checked('1', $options['noindex_404']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">404ページ</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_date]" value="1" <?php checked('1', $options['noindex_date']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">日付アーカイブ</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_author]" value="1" <?php checked('1', $options['noindex_author']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">著者アーカイブ</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_tag]" value="1" <?php checked('1', $options['noindex_tag']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">タグアーカイブ</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[noindex_paged]" value="1" <?php checked('1', $options['noindex_paged']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ページネーション（2ページ以降）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th colspan="2" style="padding-top:2em; font-size:1.1em; border-top:2px solid #ddd;">
            <i class="fa-solid fa-sitemap"></i> 構造化データ（Schema.org）
        </th>
    </tr>
    <tr>
        <th colspan="2" style="color:#666; font-weight:normal; padding-top:0;">
            WebSite・BlogPosting・BreadcrumbList・Organization の JSON-LD を自動出力します。
        </th>
    </tr>

    <tr>
        <th>OGP 代替画像 URL<br><small>（アイキャッチなし時のフォールバック）</small></th>
        <td>
            <input type="url" name="awbase_settings[schema_og_image]" value="<?php echo esc_url( $options['schema_og_image'] ?? '' ); ?>" class="large-text" placeholder="https://example.com/ogp-default.jpg">
            <p class="description">投稿にアイキャッチが設定されていない場合に、OGP（<code>og:image</code>）および構造化データの <code>image</code> プロパティへフォールバックとして使用する画像 URL を指定してください。<br><strong>推奨サイズ: 16:9（例: 1200×630px）</strong></p>
        </td>
    </tr>

    <tr>
        <th colspan="2" style="padding-top:1.5em; color:#333; border-top:1px solid #eee;">
            著者情報（author プロパティ）
        </th>
    </tr>
    <tr>
        <th>著者 表示名</th>
        <td>
            <input type="text" name="awbase_settings[schema_author_name]" value="<?php echo esc_attr( $options['schema_author_name'] ?? '' ); ?>" class="regular-text" placeholder="例: 山田太郎">
            <p class="description">JSON-LD の <code>author.name</code> に出力されます。空欄の場合は投稿の WordPress ユーザー表示名を使用。</p>
        </td>
    </tr>
    <tr>
        <th>著者 本名（alternateName）</th>
        <td>
            <input type="text" name="awbase_settings[schema_author_altname]" value="<?php echo esc_attr( $options['schema_author_altname'] ?? '' ); ?>" class="regular-text" placeholder="例: Taro Yamada">
            <p class="description">エンティティ識別・Google Scholar 等で本名を示したい場合に入力。<code>author.alternateName</code> として出力されます。</p>
        </td>
    </tr>
    <tr>
        <th>著者 プロフィール URL</th>
        <td>
            <input type="url" name="awbase_settings[schema_author_url]" value="<?php echo esc_url( $options['schema_author_url'] ?? '' ); ?>" class="large-text" placeholder="例: https://example.com/about/">
            <p class="description"><code>author.url</code> として出力されます。空欄の場合は著者アーカイブページ URL を使用。</p>
        </td>
    </tr>

    <tr>
        <th colspan="2" style="padding-top:1.5em; color:#333; border-top:1px solid #eee;">
            組織情報（Organization プロパティ）
        </th>
    </tr>
    <tr>
        <th>組織名</th>
        <td>
            <input type="text" name="awbase_settings[schema_org_name]" value="<?php echo esc_attr( $options['schema_org_name'] ?? '' ); ?>" class="regular-text" placeholder="例: 株式会社サンプル">
            <p class="description">空欄の場合はサイト名を使用します。</p>
        </td>
    </tr>
    <tr>
        <th>住所</th>
        <td>
            <input type="text" name="awbase_settings[schema_org_address]" value="<?php echo esc_attr( $options['schema_org_address'] ?? '' ); ?>" class="large-text" placeholder="例: 東京都渋谷区...">
            <p class="description"><code>address.streetAddress</code> として出力されます。</p>
        </td>
    </tr>
    <tr>
        <th>電話番号</th>
        <td>
            <input type="text" name="awbase_settings[schema_org_phone]" value="<?php echo esc_attr( $options['schema_org_phone'] ?? '' ); ?>" class="regular-text" placeholder="例: 03-0000-0000">
        </td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td>
            <input type="email" name="awbase_settings[schema_org_email]" value="<?php echo esc_attr( $options['schema_org_email'] ?? '' ); ?>" class="regular-text" placeholder="例: info@example.com">
        </td>
    </tr>
</table>

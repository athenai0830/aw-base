<?php
// AW-Base Admin Tab: General
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>TOPページ 表示設定</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_global_nav]" value="1" <?php checked('1', $options['show_global_nav']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">グローバルナビ（メニュー）</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_header]" value="1" <?php checked('1', $options['show_header']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ヘッダーエリア</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_notice]" value="1" <?php checked('1', $options['show_notice']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">通知エリア（お知らせライン）</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_fv]" value="1" <?php checked('1', $options['show_fv']); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ファーストビュー（FVはTOPページのみ表示）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>TOPページ レイアウト</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[front_page_1col]" value="1" <?php checked('1', $options['front_page_1col'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">TOPページは1カラムにする（サイドバーを非表示）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>下層ページ 表示設定</th>
        <td>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_global_nav_sub]" value="1" <?php checked('1', $options['show_global_nav_sub'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">グローバルナビ（メニュー）</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_header_sub]" value="1" <?php checked('1', $options['show_header_sub'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">ヘッダーエリア</span>
            </label><br>
            <label class="awbase-toggle">
                <input type="checkbox" name="awbase_settings[show_notice_sub]" value="1" <?php checked('1', $options['show_notice_sub'] ?? '1'); ?>>
                <span class="awbase-toggle-slider"></span>
                <span class="awbase-toggle-label">通知エリア（お知らせライン）</span>
            </label>
        </td>
    </tr>
    <tr>
        <th>カラーパターン選択</th>
        <td>
            <div class="color-swatches">
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="original" <?php checked('original', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-original"></span>
                    <span class="swatch-label">オリジナル</span>
                </label>
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="blacktan" <?php checked('blacktan', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-blacktan"></span>
                    <span class="swatch-label">ブラックタン</span>
                </label>
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="chocotan" <?php checked('chocotan', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-chocotan"></span>
                    <span class="swatch-label">チョコタン</span>
                </label>
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="cream" <?php checked('cream', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-cream"></span>
                    <span class="swatch-label">クリーム</span>
                </label>
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="bluetan" <?php checked('bluetan', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-bluetan"></span>
                    <span class="swatch-label">ブルータン</span>
                </label>
                <label class="swatch-item">
                    <input type="radio" name="awbase_settings[color_pattern]" value="white" <?php checked('white', $options['color_pattern']); ?>>
                    <span class="swatch-circle swatch-white"></span>
                    <span class="swatch-label">ホワイト</span>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <th>フォントファミリー</th>
        <td>
            <select name="awbase_settings[font_family]">
                <option value="hiragino" <?php selected('hiragino', $options['font_family']); ?>>ヒラギノ系</option>
                <option value="meiryo" <?php selected('meiryo', $options['font_family']); ?>>メイリオ系</option>
                <option value="yugothic" <?php selected('yugothic', $options['font_family']); ?>>Yu Gothic系</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>カラム設定</th>
        <td>
            <label><input type="radio" name="awbase_settings[columns]" value="2" <?php checked('2', $options['columns']); ?>> 2カラム（メインコンテンツ＋サイドバー）</label><br>
            <label><input type="radio" name="awbase_settings[columns]" value="1" <?php checked('1', $options['columns']); ?>> 1カラム（サイドバーなし）</label>
        </td>
    </tr>
    <tr>
        <th>サイドバー表示位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[sidebar_position]" value="right" <?php checked('right', $options['sidebar_position']); ?>> 右（デフォルト）</label>
            <label><input type="radio" name="awbase_settings[sidebar_position]" value="left" <?php checked('left', $options['sidebar_position']); ?>> 左</label>
        </td>
    </tr>
    <tr>
        <th>コンテンツエリア幅</th>
        <td>
            <p class="awbase-field-row">サイト全体の最大幅: <input type="number" name="awbase_settings[site_max_width]" value="<?php echo esc_attr($options['site_max_width']); ?>" class="small-text"> px</p>
            <p class="awbase-field-row">メインコンテンツ幅: <input type="number" name="awbase_settings[content_width]" value="<?php echo esc_attr($options['content_width']); ?>" class="small-text"> px</p>
            <p class="awbase-field-row">サイドバー幅: <input type="number" name="awbase_settings[sidebar_width]" value="<?php echo esc_attr($options['sidebar_width']); ?>" class="small-text"> px</p>
            <p class="awbase-field-row">メインコンテンツ/サイドバー間隔: <input type="number" name="awbase_settings[gap_main_sidebar]" value="<?php echo esc_attr($options['gap_main_sidebar']); ?>" class="small-text"> px</p>
            <span class="description">※ メインコンテンツ幅＋サイドバー幅がサイト全体の最大幅を超えないようにしてください。</span>
        </td>
    </tr>
    <tr>
        <th>代替画像<br><small style="font-weight:normal;color:#646970;">ブログカード・埋め込みブロック用</small></th>
        <td>
            <input type="url" name="awbase_settings[blogcard_noimage_url]" value="<?php echo esc_attr( $options['blogcard_noimage_url'] ?? '' ); ?>" class="regular-text" placeholder="https://example.com/noimage.png">
            <p class="description">アイキャッチ未設定時にブログカードおよびURL埋め込みブロックで表示する代替画像のURLを指定してください。<br><strong>推奨サイズ: 正方形（200×200px）</strong><br>未設定の場合は「no image」テキストのみ表示されます。</p>
        </td>
    </tr>
    <tr>
        <th>ファビコン</th>
        <td>
            <input type="url" name="awbase_settings[favicon_url]" value="<?php echo esc_attr( $options['favicon_url'] ?? '' ); ?>" class="regular-text" placeholder="https://example.com/favicon.png">
            <p class="description">ブラウザタブやブックマークに表示されるアイコンのURLを指定してください。<br><strong>推奨サイズ: 32×32px または 180×180px（PNG）</strong></p>
        </td>
    </tr>
    <tr>
        <th>人気記事ランキング設定</th>
        <td>
            <p class="awbase-field-row">表示件数: <input type="number" name="awbase_settings[popular_list_count]" value="<?php echo esc_attr($options['popular_list_count'] ?? 5); ?>" class="small-text" min="1" max="20"> 件</p>
            <p class="awbase-field-row">集計期間:
                <select name="awbase_settings[popular_list_period]">
                    <option value="total" <?php selected('total', $options['popular_list_period'] ?? 'total'); ?>>全期間</option>
                    <option value="month" <?php selected('month', $options['popular_list_period'] ?? 'total'); ?>>30日間</option>
                    <option value="week"  <?php selected('week',  $options['popular_list_period'] ?? 'total'); ?>>7日間</option>
                    <option value="day"   <?php selected('day',   $options['popular_list_period'] ?? 'total'); ?>>1日</option>
                </select>
            </p>
            <p class="description">※ <code>[popular_list]</code> ショートコードのデフォルト値。ショートコードで個別に上書き可能。</p>
        </td>
    </tr>
</table>

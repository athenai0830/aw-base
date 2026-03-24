<?php
// AW-Base Admin Tab: General
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<table class="form-table">
    <tr>
        <th>表示/非表示</th>
        <td>
            <label><input type="checkbox" name="awbase_settings[show_global_nav]" value="1" <?php checked('1', $options['show_global_nav']); ?>> グローバルナビ（メニュー）を表示する</label><br>
            <label><input type="checkbox" name="awbase_settings[show_header]" value="1" <?php checked('1', $options['show_header']); ?>> ヘッダーエリアを表示する</label><br>
            <label><input type="checkbox" name="awbase_settings[show_notice]" value="1" <?php checked('1', $options['show_notice']); ?>> 通知エリア（お知らせライン）を表示する</label><br>
            <label><input type="checkbox" name="awbase_settings[show_fv]" value="1" <?php checked('1', $options['show_fv']); ?>> ファーストビュー（FV）を表示する</label>
        </td>
    </tr>
    <tr>
        <th>カラーパターン選択</th>
        <td>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="original" <?php checked('original', $options['color_pattern']); ?>> オリジナル</label>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="blacktan" <?php checked('blacktan', $options['color_pattern']); ?>> ブラックタン</label>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="chocotan" <?php checked('chocotan', $options['color_pattern']); ?>> チョコタン</label>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="cream" <?php checked('cream', $options['color_pattern']); ?>> クリーム</label>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="bluetan" <?php checked('bluetan', $options['color_pattern']); ?>> ブルータン</label>
            <label><input type="radio" name="awbase_settings[color_pattern]" value="white" <?php checked('white', $options['color_pattern']); ?>> ホワイト</label>
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
            <label><input type="radio" name="awbase_settings[columns]" value="2" <?php checked('2', $options['columns']); ?>> 2カラム</label>
            <label><input type="radio" name="awbase_settings[columns]" value="1" <?php checked('1', $options['columns']); ?>> 1カラム</label>
        </td>
    </tr>
    <tr>
        <th>サイドバー位置</th>
        <td>
            <label><input type="radio" name="awbase_settings[sidebar_position]" value="left" <?php checked('left', $options['sidebar_position']); ?>> 左</label>
            <label><input type="radio" name="awbase_settings[sidebar_position]" value="right" <?php checked('right', $options['sidebar_position']); ?>> 右</label>
        </td>
    </tr>
    <tr>
        <th>コンテンツエリア幅</th>
        <td>
            サイト全体幅: <input type="number" name="awbase_settings[site_max_width]" value="<?php echo esc_attr($options['site_max_width']); ?>" class="small-text"> px<br>
            メインコンテンツ幅: <input type="number" name="awbase_settings[content_width]" value="<?php echo esc_attr($options['content_width']); ?>" class="small-text"> px<br>
            サイドバー幅: <input type="number" name="awbase_settings[sidebar_width]" value="<?php echo esc_attr($options['sidebar_width']); ?>" class="small-text"> px
        </td>
    </tr>
</table>

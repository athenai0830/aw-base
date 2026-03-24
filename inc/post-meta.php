<?php
// AW-Base Post Meta Fields
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Add Meta Box
function awbase_add_post_meta_boxes() {
    $screens = [ 'post', 'page' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'awbase_post_options',
            'AW-Base 投稿／個別SEO・表示設定',
            'awbase_post_options_html',
            $screen,
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'awbase_add_post_meta_boxes' );

// 2. Meta Box HTML
function awbase_post_options_html( $post ) {
    // Nonce field
    wp_nonce_field( 'awbase_save_post_meta', 'awbase_post_meta_nonce' );

    // Get current values
    $seo_title = get_post_meta( $post->ID, 'awbase_seo_title', true );
    $seo_desc = get_post_meta( $post->ID, 'awbase_seo_desc', true );
    $noindex = get_post_meta( $post->ID, 'awbase_noindex', true );
    $hide_eyecatch = get_post_meta( $post->ID, 'awbase_hide_eyecatch', true );
    $hide_toc = get_post_meta( $post->ID, 'awbase_hide_toc', true );

    ?>
    <table class="form-table">
        <tr>
            <th><label for="awbase_seo_title">SEOタイトル</label></th>
            <td><input type="text" id="awbase_seo_title" name="awbase_seo_title" value="<?php echo esc_attr($seo_title); ?>" class="large-text"></td>
        </tr>
        <tr>
            <th><label for="awbase_seo_desc">メタディスクリプション</label></th>
            <td><textarea id="awbase_seo_desc" name="awbase_seo_desc" rows="3" class="large-text"><?php echo esc_textarea($seo_desc); ?></textarea></td>
        </tr>
        <tr>
            <th>SEOインデックス設定</th>
            <td>
                <label><input type="checkbox" name="awbase_noindex" value="1" <?php checked('1', $noindex); ?>> 検索エンジンにインデックスさせない (noindex)</label>
            </td>
        </tr>
        <tr>
            <th>Cocoon互換: 表示設定</th>
            <td>
                <label><input type="checkbox" name="awbase_hide_eyecatch" value="1" <?php checked('1', $hide_eyecatch); ?>> アイキャッチ画像を表示しない</label><br>
                <label><input type="checkbox" name="awbase_hide_toc" value="1" <?php checked('1', $hide_toc); ?>> 目次を表示しない</label>
            </td>
        </tr>
    </table>
    <?php
}

// 3. Save Meta Box data
function awbase_save_post_meta( $post_id ) {
    // Check nonces and permissions
    if ( ! isset( $_POST['awbase_post_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['awbase_post_meta_nonce'], 'awbase_save_post_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Save fields
    $fields = ['awbase_seo_title', 'awbase_seo_desc', 'awbase_noindex', 'awbase_hide_eyecatch', 'awbase_hide_toc'];

    foreach ( $fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            // Checkboxes might be empty in POST if unchecked
            $value = sanitize_text_field( $_POST[$field] );
            update_post_meta( $post_id, $field, $value );
        } else {
            // For checkboxes not sent
            if ( in_array($field, ['awbase_noindex', 'awbase_hide_eyecatch', 'awbase_hide_toc']) ) {
                update_post_meta( $post_id, $field, '0' );
            }
        }
    }
}
add_action( 'save_post', 'awbase_save_post_meta' );

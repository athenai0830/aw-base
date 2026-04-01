<?php
// AW-Base User Meta – SNS links & profile photo
// Cocoon互換: meta key は Cocoon と同じ unprefixed 名で保存
if ( ! defined( 'ABSPATH' ) ) exit;

// -----------------------------------------------------------------------
// プロフィール・ユーザー編集ページでメディアスクリプトを読み込む
// -----------------------------------------------------------------------
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( in_array( $hook, [ 'profile.php', 'user-edit.php' ], true ) ) {
        wp_enqueue_media();
    }
} );

// -----------------------------------------------------------------------
// ユーザープロフィール画面にフィールドを追加
// -----------------------------------------------------------------------
function awbase_user_profile_fields( $user ) {
    $sns_fields = [
        'twitter'   => [ 'label' => 'X (Twitter)', 'icon' => 'fa-brands fa-x-twitter', 'placeholder' => 'https://twitter.com/username' ],
        'facebook'  => [ 'label' => 'Facebook',    'icon' => 'fa-brands fa-facebook-f', 'placeholder' => 'https://www.facebook.com/username' ],
        'instagram' => [ 'label' => 'Instagram',   'icon' => 'fa-brands fa-instagram',  'placeholder' => 'https://www.instagram.com/username' ],
        'youtube'   => [ 'label' => 'YouTube',     'icon' => 'fa-brands fa-youtube',    'placeholder' => 'https://www.youtube.com/@channel' ],
        'linkedin'  => [ 'label' => 'LinkedIn',    'icon' => 'fa-brands fa-linkedin-in','placeholder' => 'https://www.linkedin.com/in/username' ],
        'github'    => [ 'label' => 'GitHub',      'icon' => 'fa-brands fa-github',     'placeholder' => 'https://github.com/username' ],
    ];
    $profile_photo_id  = get_user_meta( $user->ID, 'awbase_profile_photo', true );
    $profile_photo_url_saved = get_user_meta( $user->ID, 'awbase_profile_photo_url', true );
    // 表示用URL: メディアライブラリID優先、なければURL直接指定
    $preview_url = '';
    if ( $profile_photo_id ) {
        $preview_url = wp_get_attachment_url( $profile_photo_id );
    } elseif ( $profile_photo_url_saved ) {
        $preview_url = $profile_photo_url_saved;
    }
    ?>
    <h2>SNS リンク（AW-Base）</h2>
    <table class="form-table">
        <tr>
            <th><label>プロフィール写真</label></th>
            <td>
                <img id="awbase_photo_preview"
                     src="<?php echo esc_url( $preview_url ); ?>"
                     style="width:80px;height:80px;object-fit:cover;border-radius:50%;display:<?php echo $preview_url ? 'block' : 'none'; ?>;margin-bottom:8px;">
                <input type="hidden" name="awbase_profile_photo" id="awbase_profile_photo" value="<?php echo esc_attr( $profile_photo_id ); ?>">
                <p style="margin:4px 0 2px;">
                    <button type="button" class="button" id="awbase_photo_btn">メディアライブラリから選択</button>
                    <button type="button" class="button" id="awbase_photo_remove" style="<?php echo ( $profile_photo_id || $profile_photo_url_saved ) ? '' : 'display:none;'; ?>">削除</button>
                </p>
                <p style="margin:8px 0 2px;"><label style="font-weight:600;">または URL で指定:</label></p>
                <input type="url" name="awbase_profile_photo_url" id="awbase_profile_photo_url"
                       value="<?php echo esc_url( $profile_photo_url_saved ); ?>"
                       class="regular-text" placeholder="https://example.com/photo.jpg">
                <p class="description">メディアライブラリで選択した場合は URL 欄を空のままにしてください。Gravatar の代わりに使用されます。</p>
                <script>
                jQuery(function($){
                    var frame;
                    $('#awbase_photo_btn').on('click', function(e){
                        e.preventDefault();
                        if ( frame ) { frame.open(); return; }
                        frame = wp.media({
                            title: 'プロフィール写真を選択',
                            button: { text: '選択' },
                            multiple: false,
                            library: { type: 'image' }
                        });
                        frame.on('select', function(){
                            var att = frame.state().get('selection').first().toJSON();
                            $('#awbase_profile_photo').val(att.id);
                            $('#awbase_profile_photo_url').val(''); // URL欄をクリア
                            var imgUrl = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                            $('#awbase_photo_preview').attr('src', imgUrl).show();
                            $('#awbase_photo_remove').show();
                        });
                        frame.open();
                    });
                    $('#awbase_profile_photo_url').on('input', function(){
                        var url = $(this).val();
                        if ( url ) {
                            $('#awbase_profile_photo').val('');
                            $('#awbase_photo_preview').attr('src', url).show();
                            $('#awbase_photo_remove').show();
                        }
                    });
                    $('#awbase_photo_remove').on('click', function(){
                        $('#awbase_profile_photo').val('');
                        $('#awbase_profile_photo_url').val('');
                        $('#awbase_photo_preview').attr('src','').hide();
                        $(this).hide();
                    });
                });
                </script>
            </td>
        </tr>
        <?php foreach ( $sns_fields as $key => $field ) : ?>
        <tr>
            <th><label for="awbase_sns_<?php echo esc_attr($key); ?>"><i class="<?php echo esc_attr($field['icon']); ?>"></i> <?php echo esc_html($field['label']); ?></label></th>
            <td>
                <input type="url" name="awbase_sns_<?php echo esc_attr($key); ?>" id="awbase_sns_<?php echo esc_attr($key); ?>"
                       value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>"
                       placeholder="<?php echo esc_attr($field['placeholder']); ?>" class="regular-text">
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php
}
add_action( 'show_user_profile', 'awbase_user_profile_fields' );
add_action( 'edit_user_profile', 'awbase_user_profile_fields' );

// -----------------------------------------------------------------------
// 保存
// -----------------------------------------------------------------------
function awbase_save_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) return;

    $sns_keys = [ 'twitter', 'facebook', 'instagram', 'youtube', 'linkedin', 'github' ];
    foreach ( $sns_keys as $key ) {
        if ( isset( $_POST[ 'awbase_sns_' . $key ] ) ) {
            update_user_meta( $user_id, $key, sanitize_url( $_POST[ 'awbase_sns_' . $key ] ) );
        }
    }

    if ( isset( $_POST['awbase_profile_photo'] ) ) {
        $photo_id = intval( $_POST['awbase_profile_photo'] );
        if ( $photo_id > 0 ) {
            update_user_meta( $user_id, 'awbase_profile_photo', $photo_id );
        } else {
            delete_user_meta( $user_id, 'awbase_profile_photo' );
        }
    }

    if ( isset( $_POST['awbase_profile_photo_url'] ) ) {
        $photo_url = sanitize_url( $_POST['awbase_profile_photo_url'] );
        if ( $photo_url ) {
            update_user_meta( $user_id, 'awbase_profile_photo_url', $photo_url );
        } else {
            delete_user_meta( $user_id, 'awbase_profile_photo_url' );
        }
    }
}
add_action( 'personal_options_update', 'awbase_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'awbase_save_user_profile_fields' );

// -----------------------------------------------------------------------
// get_avatar フィルター: カスタム写真があれば Gravatar を置き換え
// -----------------------------------------------------------------------
function awbase_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
    } elseif ( is_object( $id_or_email ) && isset( $id_or_email->user_id ) ) {
        $user = get_user_by( 'id', $id_or_email->user_id );
    } elseif ( is_string( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
    }
    if ( ! $user ) return $avatar;

    $photo_id  = get_user_meta( $user->ID, 'awbase_profile_photo', true );
    $photo_url_direct = get_user_meta( $user->ID, 'awbase_profile_photo_url', true );

    if ( $photo_id ) {
        $photo_url = wp_get_attachment_image_url( $photo_id, 'thumbnail' );
    } elseif ( $photo_url_direct ) {
        $photo_url = $photo_url_direct;
    } else {
        return $avatar;
    }
    if ( ! $photo_url ) return $avatar;

    return '<img src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $alt ) . '" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" class="avatar avatar-' . esc_attr( $size ) . ' photo" style="border-radius:50%;">';
}
add_filter( 'get_avatar', 'awbase_custom_avatar', 10, 5 );

<?php
// AW-Base Comments Template
if ( ! defined( 'ABSPATH' ) ) exit;

// パスワード保護記事はコメント非表示
if ( post_password_required() ) {
    echo '<p class="comments-password-notice">' . esc_html__( 'コメントを表示するにはパスワードが必要です。', 'aw-base' ) . '</p>';
    return;
}
?>

<section id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html( _n( '%s件のコメント', '%s件のコメント', $comment_count, 'aw-base' ) ),
                '<span>' . esc_html( number_format_i18n( $comment_count ) ) . '</span>'
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'awbase_comment_callback',
            ) );
            ?>
        </ol>

        <?php
        the_comments_pagination( array(
            'prev_text' => '&laquo; 前のコメント',
            'next_text' => '次のコメント &raquo;',
        ) );
        ?>

    <?php endif; ?>

    <?php
    $commenter    = wp_get_current_commenter();
    $require_name = (bool) get_option( 'require_name_email' );

    comment_form( array(
        'title_reply'          => 'コメントを残す',
        'title_reply_to'       => '%s へ返信',
        'cancel_reply_link'    => 'キャンセル',
        'label_submit'         => 'コメントを送信',
        'comment_notes_before' => '<p class="comment-notes">'
            . ( $require_name
                ? '<span class="required-field-message"><span class="required" aria-hidden="true">*</span> は必須項目です。</span>'
                : '' )
            . '</p>',
        'comment_notes_after'  => '',
        'fields'               => array(
            'author' => '<p class="comment-form-author">'
                . '<label for="author">' . esc_html__( 'お名前', 'aw-base' )
                . ( $require_name ? ' <span class="required" aria-hidden="true">*</span>' : '' )
                . '</label>'
                . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"'
                . ( $require_name ? ' required' : '' ) . '>'
                . '</p>',
            'email'  => '<p class="comment-form-email">'
                . '<label for="email">' . esc_html__( 'メールアドレス', 'aw-base' )
                . ( $require_name ? ' <span class="required" aria-hidden="true">*</span>' : '' )
                . '</label>'
                . '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"'
                . ( $require_name ? ' required' : '' ) . '>'
                . '<span class="comment-form-email-note">メールアドレスは公開されません。</span>'
                . '</p>',
            'url'    => '',  // URLフィールドは非表示
        ),
        'comment_field'        => '<p class="comment-form-comment">'
            . '<label for="comment">' . esc_html__( 'コメント', 'aw-base' ) . ' <span class="required" aria-hidden="true">*</span></label>'
            . '<textarea id="comment" name="comment" cols="45" rows="6" required></textarea>'
            . '</p>',
        'class_submit'         => 'submit comment-submit-btn',
    ) );
    ?>

</section>

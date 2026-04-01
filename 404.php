<?php
// AW-Base 404 Template
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="not-found-wrap">
    <div class="not-found-inner">
        <p class="not-found-code">404</p>
        <h1 class="not-found-title">ページが見つかりませんでした</h1>
        <p class="not-found-text">お探しのページは移動・削除されたか、URLが間違っている可能性があります。</p>
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="btn-back-home">
            <i class="fa-solid fa-house"></i> トップページへ戻る
        </a>
        <div class="not-found-search">
            <?php get_search_form(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

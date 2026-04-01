<?php
/**
 * AW-Base Front Page Template
 *
 * WPの「表示設定 > ホームページの表示」に従って動作する:
 * - 固定ページ (show_on_front=page): ページ本文を表示（WPエディタで編集した内容）
 * - 最新の投稿 (show_on_front=posts): 投稿一覧を表示
 *
 * @package AW-Base
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<?php if ( get_option('show_on_front') === 'posts' ) : ?>
    <?php
    // ── 最新の投稿モード ────────────────────────────────────────────
    ?>
    <div class="home-content posts-index">
        <?php if ( have_posts() ) : ?>
            <div class="entry-card-list">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/entry-card' ); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <p class="no-posts"><?php esc_html_e( '投稿が見つかりません。', 'aw-base' ); ?></p>
        <?php endif; ?>
    </div>

<?php else : ?>
    <?php
    // ── 固定ページモード ────────────────────────────────────────────
    // WPエディタで入力したページ本文をそのまま表示する
    ?>
    <div class="home-content">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="entry-content home-page-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>

<?php endif; ?>

<?php get_footer(); ?>

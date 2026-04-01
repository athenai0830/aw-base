<?php
/**
 * AW-Base PV Tracker
 * Cocoon 方式：is_singular() 時に post_meta でカウント
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. フロントエンドでカウントアップ
function awbase_track_pv() {
    if ( is_admin() || ! is_singular() ) return;

    // ボットを除外
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
    $bot_keywords = [ 'bot', 'crawler', 'spider', 'slurp', 'baiduspider', 'yandex', 'ia_archiver' ];
    foreach ( $bot_keywords as $kw ) {
        if ( strpos( $ua, $kw ) !== false ) return;
    }

    $post_id = get_the_ID();
    if ( ! $post_id ) return;

    // 総PV
    $total = (int) get_post_meta( $post_id, '_awbase_pv_total', true );
    update_post_meta( $post_id, '_awbase_pv_total', $total + 1 );

    // 日付別PV（YYYY-MM-DD をキーにした配列）
    $daily_key = '_awbase_pv_daily';
    $daily = get_post_meta( $post_id, $daily_key, true );
    if ( ! is_array( $daily ) ) $daily = [];
    $today = gmdate( 'Y-m-d' );
    $daily[ $today ] = isset( $daily[ $today ] ) ? $daily[ $today ] + 1 : 1;

    // 古いデータ（31日以上前）を削除してメモリを節約
    $cutoff = gmdate( 'Y-m-d', strtotime( '-31 days' ) );
    foreach ( array_keys( $daily ) as $date ) {
        if ( $date < $cutoff ) unset( $daily[ $date ] );
    }
    update_post_meta( $post_id, $daily_key, $daily );
}
add_action( 'wp', 'awbase_track_pv' );

// 2. 期間別PV取得ヘルパー
function awbase_get_pv( $post_id, $period = 'total' ) {
    if ( $period === 'total' ) {
        return (int) get_post_meta( $post_id, '_awbase_pv_total', true );
    }

    $daily = get_post_meta( $post_id, '_awbase_pv_daily', true );
    if ( ! is_array( $daily ) || empty( $daily ) ) return 0;

    $days = 0;
    if ( $period === 'day'   ) $days = 1;
    if ( $period === 'week'  ) $days = 7;
    if ( $period === 'month' ) $days = 30;

    $cutoff = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );
    $sum = 0;
    foreach ( $daily as $date => $count ) {
        if ( $date >= $cutoff ) $sum += (int) $count;
    }
    return $sum;
}

// 3. 投稿一覧カラム追加 – AI カラムの直後に挿入、なければ date の直後
function awbase_add_pv_column( $columns ) {
    $new = [];
    $inserted = false;
    foreach ( $columns as $key => $label ) {
        $new[$key] = $label;
        if ( $key === 'awbase_ai_hits' ) {
            $new['awbase_pv'] = 'PV';
            $inserted = true;
        }
    }
    if ( ! $inserted ) {
        $new2 = [];
        foreach ( $new as $key => $label ) {
            $new2[$key] = $label;
            if ( $key === 'date' ) $new2['awbase_pv'] = 'PV';
        }
        $new = $new2;
    }
    return $new;
}
add_filter( 'manage_posts_columns', 'awbase_add_pv_column' );

function awbase_show_pv_column( $column_name, $post_id ) {
    if ( $column_name !== 'awbase_pv' ) return;
    $d   = awbase_get_pv( $post_id, 'day' );
    $w   = awbase_get_pv( $post_id, 'week' );
    $m   = awbase_get_pv( $post_id, 'month' );
    $all = awbase_get_pv( $post_id, 'total' );
    printf(
        '<div style="font-size:12px;line-height:1.8;white-space:nowrap">D: %d<br>W: %d<br>M: %d<br>All: %d</div>',
        $d, $w, $m, $all
    );
}
add_action( 'manage_posts_custom_column', 'awbase_show_pv_column', 10, 2 );

// 4. ソート対応
function awbase_pv_sortable_column( $columns ) {
    $columns['awbase_pv'] = 'awbase_pv';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'awbase_pv_sortable_column' );

function awbase_pv_orderby( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) return;
    if ( $query->get( 'orderby' ) === 'awbase_pv' ) {
        $query->set( 'meta_key', '_awbase_pv_total' );
        $query->set( 'orderby', 'meta_value_num' );
    }
}
add_action( 'pre_get_posts', 'awbase_pv_orderby' );

// 5. サムネイルカラム（最右端）
function awbase_add_thumb_column( $columns ) {
    $columns['awbase_thumb'] = 'Thumb';
    return $columns;
}
add_filter( 'manage_posts_columns', 'awbase_add_thumb_column' );

function awbase_show_thumb_column( $column_name, $post_id ) {
    if ( $column_name !== 'awbase_thumb' ) return;
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        echo wp_get_attachment_image( $thumb_id, 'thumbnail', false, [ 'class' => 'awbase-col-thumb' ] );
    } else {
        echo '<span class="awbase-col-thumb-none">-</span>';
    }
}
add_action( 'manage_posts_custom_column', 'awbase_show_thumb_column', 10, 2 );

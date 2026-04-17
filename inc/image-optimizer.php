<?php
/**
 * AW-Base Image Optimizer
 *
 * テーマ専用サムネイルを /wp-content/uploads/aw-thumbs/ に生成・管理する。
 * - 元画像（uploads/YYYY/MM/）は一切変更しない
 * - ファイル名は {attachment_id}-{width}x{height}.jpg / .webp
 * - WordPress の intermediate_image_sizes_advanced を使わず自前で生成するため
 *   uploads/YYYY/MM/ 内にテーマ用サイズは作られない
 *
 * @package AW-Base
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// テーマが使用するサムネイルサイズ定義
// ============================================================

/**
 * @return array<string, int[]>  'size-key' => [ width, height ]
 */
function awbase_get_thumb_sizes(): array {
    return [
        'awbase-card'    => [ 800, 450 ],
        'awbase-card-sm' => [ 460, 259 ],
    ];
}

// ============================================================
// ストレージパス / URL（static でメモ化）
// ============================================================

function awbase_thumb_dir(): string {
    static $dir = null;
    if ( $dir === null ) {
        $dir = wp_upload_dir()['basedir'] . '/aw-thumbs';
    }
    return $dir;
}

function awbase_thumb_url(): string {
    static $url = null;
    if ( $url === null ) {
        $url = wp_upload_dir()['baseurl'] . '/aw-thumbs';
    }
    return $url;
}

// ============================================================
// サムネイル生成
// ============================================================

/**
 * 指定アタッチメントのサムネイル（JPEG + WebP）を生成する。
 * WebP非対応サーバーでもJPEGのみで成功扱いにする。
 *
 * @param bool $force true なら既存ファイルを上書き
 */
function awbase_generate_thumb( int $attachment_id, int $width, int $height, bool $force = false ): bool {
    $dir  = awbase_thumb_dir();
    $base = "{$dir}/{$attachment_id}-{$width}x{$height}";
    $jpeg = $base . '.jpg';
    $webp = $base . '.webp';

    if ( ! $force && file_exists( $jpeg ) && file_exists( $webp ) ) {
        return true;
    }

    if ( ! wp_mkdir_p( $dir ) ) return false;

    // ディレクトリリスティング防止
    $idx = $dir . '/index.php';
    if ( ! file_exists( $idx ) ) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        file_put_contents( $idx, '<?php // Silence is golden.' );
    }

    $src = get_attached_file( $attachment_id );
    if ( ! $src || ! file_exists( $src ) ) return false;

    $editor = wp_get_image_editor( $src );
    if ( is_wp_error( $editor ) ) return false;

    if ( is_wp_error( $editor->resize( $width, $height, true ) ) ) return false;

    // JPEG保存
    if ( $force || ! file_exists( $jpeg ) ) {
        if ( is_wp_error( $editor->save( $jpeg, 'image/jpeg' ) ) ) return false;
    }

    // WebP保存（非対応サーバーでも失敗しない）
    if ( $force || ! file_exists( $webp ) ) {
        $editor->save( $webp, 'image/webp' );
    }

    return true;
}

// ============================================================
// サムネイルURLを返す（生成済みファイルのみ）
// ============================================================

/**
 * @return array{webp: string, jpeg: string}  空文字 = 未生成
 */
function awbase_get_thumb_urls( int $attachment_id, int $width, int $height ): array {
    $dir    = awbase_thumb_dir();
    $url    = awbase_thumb_url();
    $base_f = "{$dir}/{$attachment_id}-{$width}x{$height}";
    $base_u = "{$url}/{$attachment_id}-{$width}x{$height}";

    return [
        'webp' => file_exists( $base_f . '.webp' ) ? $base_u . '.webp' : '',
        'jpeg' => file_exists( $base_f . '.jpg'  ) ? $base_u . '.jpg'  : '',
    ];
}

// ============================================================
// <picture> 要素を文字列で返す
// ============================================================

/**
 * WebP <source> + JPEG <img> の <picture> 要素を返す。
 * サムネイル未生成の場合はオリジナル画像でフォールバック。
 *
 * @param int    $attachment_id  アタッチメントID（0 = no-image）
 * @param string $size_key       awbase_get_thumb_sizes() のキー
 * @param array  $attrs {
 *     @type string $alt           alt テキスト（省略時はメディアライブラリの alt）
 *     @type string $loading       'lazy'（省略時）| 'eager'
 *     @type string $fetchpriority 'high' など（省略時は属性なし）
 * }
 */
function awbase_picture( int $attachment_id, string $size_key, array $attrs = [] ): string {
    $sizes = awbase_get_thumb_sizes();
    if ( ! isset( $sizes[ $size_key ] ) ) return '';

    [ $width, $height ] = $sizes[ $size_key ];

    // アタッチメントなし → no-image.svg
    if ( ! $attachment_id ) {
        return sprintf(
            '<img src="%s" alt="" width="%d" height="%d" loading="lazy" decoding="async">',
            esc_url( get_template_directory_uri() . '/assets/img/no-image.svg' ),
            $width,
            $height
        );
    }

    $urls     = awbase_get_thumb_urls( $attachment_id, $width, $height );
    $alt      = esc_attr( $attrs['alt'] ?? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
    $loading  = esc_attr( $attrs['loading'] ?? 'lazy' );
    $priority = ! empty( $attrs['fetchpriority'] ) ? ' fetchpriority="' . esc_attr( $attrs['fetchpriority'] ) . '"' : '';

    // サムネイル未生成 → オリジナルにフォールバック
    if ( ! $urls['webp'] && ! $urls['jpeg'] ) {
        $orig = wp_get_attachment_image_src( $attachment_id, 'full' );
        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" loading="%s" decoding="async"%s>',
            esc_url( $orig ? $orig[0] : '' ),
            $alt,
            $width,
            $height,
            $loading,
            $priority
        );
    }

    $img_src = $urls['jpeg'] ?: $urls['webp'];

    $html  = '<picture>';
    if ( $urls['webp'] ) {
        $html .= '<source srcset="' . esc_url( $urls['webp'] ) . '" type="image/webp">';
    }
    $html .= sprintf(
        '<img src="%s" alt="%s" width="%d" height="%d" loading="%s" decoding="async"%s>',
        esc_url( $img_src ),
        $alt,
        $width,
        $height,
        $loading,
        $priority
    );
    $html .= '</picture>';

    return $html;
}

// ============================================================
// 新規アップロード時に自動生成
// wp_generate_attachment_metadata は WordPress の標準サイズ生成後に発火する
// ============================================================
add_filter( 'wp_generate_attachment_metadata', function( array $metadata, int $attachment_id ): array {
    if ( wp_attachment_is_image( $attachment_id ) ) {
        foreach ( awbase_get_thumb_sizes() as [ $w, $h ] ) {
            awbase_generate_thumb( $attachment_id, $w, $h );
        }
    }
    return $metadata;
}, 10, 2 );

// ============================================================
// 添付ファイル削除時に生成済みサムネイルも削除
// ============================================================
add_action( 'delete_attachment', function( int $attachment_id ): void {
    $dir = awbase_thumb_dir();
    if ( ! is_dir( $dir ) ) return;
    foreach ( awbase_get_thumb_sizes() as [ $w, $h ] ) {
        foreach ( [ 'webp', 'jpg' ] as $ext ) {
            $file = "{$dir}/{$attachment_id}-{$w}x{$h}.{$ext}";
            if ( file_exists( $file ) ) {
                unlink( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
            }
        }
    }
} );

// ============================================================
// WordPress による awbase-* サイズの自動生成を無効化
// uploads/YYYY/MM/ 内にテーマ用サイズが作られないようにする
// ============================================================
add_filter( 'intermediate_image_sizes_advanced', function( array $sizes ): array {
    unset( $sizes['awbase-card'], $sizes['awbase-card-sm'] );
    return $sizes;
} );

// ============================================================
// WordPress の大画像自動スケールダウンを無効化
// image-scaled.jpg が生成されないようにする
// ============================================================
add_filter( 'big_image_size_threshold', '__return_false' );

// ============================================================
// AJAX: 一括最適化バッチ処理（10枚/リクエスト）
// ============================================================
add_action( 'wp_ajax_awbase_optimize_batch', function(): void {
    check_ajax_referer( 'awbase_optimize', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( '', '', [ 'response' => 403 ] );

    set_time_limit( 120 );

    $limit = 10;
    $dir   = awbase_thumb_dir();
    $sizes = awbase_get_thumb_sizes();

    // 全ラスター画像IDを取得し、未処理のものだけ抽出
    $all_ids = get_posts( [
        'post_type'      => 'attachment',
        'post_mime_type' => [ 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ],
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ] );

    $pending = [];
    foreach ( $all_ids as $id ) {
        // 元ファイルが存在しない孤立レコードはスキップ
        $src = get_attached_file( $id );
        if ( ! $src || ! file_exists( $src ) ) continue;

        foreach ( $sizes as [ $w, $h ] ) {
            if ( ! file_exists( "{$dir}/{$id}-{$w}x{$h}.jpg" ) ) {
                $pending[] = $id;
                break;
            }
        }
    }

    // 初回リクエスト時に総未処理件数をキャッシュ（進捗率の分母に使用）
    $initial_total = (int) get_transient( 'awbase_pending_total' );
    if ( ! $initial_total ) {
        $initial_total = count( $pending );
        set_transient( 'awbase_pending_total', $initial_total, HOUR_IN_SECONDS );
    }

    // 未処理の先頭 $limit 件だけ処理
    $batch = array_slice( $pending, 0, $limit );
    foreach ( $batch as $id ) {
        foreach ( $sizes as [ $w, $h ] ) {
            awbase_generate_thumb( $id, $w, $h );
        }
    }

    $remaining = count( $pending ) - count( $batch );
    $done      = $remaining === 0;

    if ( $done ) {
        delete_transient( 'awbase_pending_total' );
    }

    $pct = $initial_total > 0
        ? min( 100, (int) round( ( $initial_total - $remaining ) / $initial_total * 100 ) )
        : 100;

    wp_send_json_success( [
        'processed' => count( $batch ),
        'remaining' => $remaining,
        'done'      => $done,
        'total'     => $initial_total,
        'pct'       => $pct,
    ] );
} );

// ============================================================
// AJAX: 生成済みサムネイルをすべて削除
// ============================================================
add_action( 'wp_ajax_awbase_delete_thumbs', function(): void {
    check_ajax_referer( 'awbase_delete_thumbs', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( '', '', [ 'response' => 403 ] );

    $dir   = awbase_thumb_dir();
    $count = 0;

    if ( is_dir( $dir ) ) {
        $files = glob( $dir . '/*.{jpg,webp}', GLOB_BRACE );
        if ( $files ) {
            foreach ( $files as $file ) {
                if ( unlink( $file ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
                    $count++;
                }
            }
        }
    }

    delete_transient( 'awbase_pending_total' );
    wp_send_json_success( [ 'deleted' => $count ] );
} );

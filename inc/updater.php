<?php
// AW-Base Theme Updater – GitHub Releases
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AWBASE_GITHUB_OWNER', 'athenai0830' );
define( 'AWBASE_GITHUB_REPO',  'aw-base' );

/**
 * GitHub API から最新リリース情報を取得（12時間キャッシュ）
 */
function awbase_get_github_release() {
    $cache_key = 'awbase_github_release';
    $cached    = get_transient( $cache_key );
    if ( $cached !== false ) return $cached;

    $url      = 'https://api.github.com/repos/' . AWBASE_GITHUB_OWNER . '/' . AWBASE_GITHUB_REPO . '/releases/latest';
    $response = wp_remote_get( $url, [
        'timeout'    => 10,
        'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        'headers'    => [ 'Accept' => 'application/vnd.github+json' ],
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        // 失敗時は1時間後に再試行
        set_transient( $cache_key, null, HOUR_IN_SECONDS );
        return null;
    }

    $body    = json_decode( wp_remote_retrieve_body( $response ), true );
    $release = [
        'version'      => ltrim( $body['tag_name'] ?? '', 'v' ),
        'download_url' => $body['zipball_url'] ?? '',
        'details_url'  => $body['html_url']    ?? '',
    ];

    set_transient( $cache_key, $release, 12 * HOUR_IN_SECONDS );
    return $release;
}

/**
 * WordPress の更新チェックに AW-Base の更新情報を注入する
 */
function awbase_check_theme_update( $transient ) {
    if ( empty( $transient->checked ) ) return $transient;

    $theme_slug    = 'aw-base';
    $current_ver   = wp_get_theme( $theme_slug )->get('Version');
    $release       = awbase_get_github_release();

    if ( ! $release || empty( $release['version'] ) ) return $transient;

    if ( version_compare( $release['version'], $current_ver, '>' ) ) {
        $transient->response[ $theme_slug ] = [
            'theme'       => $theme_slug,
            'new_version' => $release['version'],
            'url'         => $release['details_url'],
            'package'     => $release['download_url'],
        ];
    }

    return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'awbase_check_theme_update' );

/**
 * 手動更新チェック時（「今すぐ確認」ボタン）にキャッシュをクリア
 */
function awbase_delete_update_cache() {
    delete_transient( 'awbase_github_release' );
}
add_action( 'delete_site_transient_update_themes', 'awbase_delete_update_cache' );

/**
 * GitHubのzipを展開した際のフォルダ名を正しく "aw-base" に修正する
 * （GitHub自動生成zipは "athenai0830-aw-base-{hash}" というフォルダ名になるため）
 */
function awbase_fix_theme_update_source( $source, $remote_source, $upgrader ) {
    if ( ! isset( $upgrader->skin->theme ) || $upgrader->skin->theme !== 'aw-base' ) {
        return $source;
    }

    $corrected = trailingslashit( $remote_source ) . 'aw-base/';

    if ( $source !== $corrected ) {
        global $wp_filesystem;
        $wp_filesystem->move( $source, $corrected );
        return $corrected;
    }

    return $source;
}
add_filter( 'upgrader_source_selection', 'awbase_fix_theme_update_source', 10, 3 );

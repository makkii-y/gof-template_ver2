<?php
/**
 * セキュリティ設定
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XML-RPC無効化
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * REST API制限（ログインユーザーのみ） - 注意: プラグインが動作しない可能性があります
 * 必要に応じてコメントアウトしてください
 */
/*
add_filter('rest_authentication_errors', function($result) {
    if (!empty($result)) {
        return $result;
    }
    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', 'REST APIを使用するにはログインが必要です。', array('status' => 401));
    }
    return $result;
});
*/

/**
 * ログイン画面のエラーメッセージを曖昧にする
 */
add_filter('login_errors', function() {
    return 'ログイン情報が正しくありません。';
});

/**
 * 作者アーカイブページを無効化
 */
add_action('template_redirect', function() {
    if (is_author()) {
        wp_redirect(home_url());
        exit;
    }
});

/**
 * セキュリティヘッダーの追加
 */
add_action('send_headers', function() {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
});

/**
 * WordPressのバージョン情報をCSS/JSから削除
 */
add_filter('style_loader_src', 'vc_remove_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'vc_remove_wp_ver_css_js', 9999);
function vc_remove_wp_ver_css_js($src) {
    if (strpos($src, 'ver=' . get_bloginfo('version')))
        $src = remove_query_arg('ver', $src);
    return $src;
}

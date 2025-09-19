<?php
/**
 * パフォーマンス最適化
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * すべての自動更新を停止
 */
add_filter('automatic_updater_disabled', '__return_true');

/**
 * WordPress標準jQueryを無効化し、CDNから読み込み
 * 注意: プラグインとの互換性問題が発生する可能性があります
 * 必要に応じてコメントアウトしてください
 */
/*
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script(
            'jquery',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
            array(),
            '3.6.0',
            true
        );
        wp_enqueue_script('jquery');
    }
});
*/

/**
 * Heartbeat API制限
 */
add_action('init', function() {
    if (is_admin()) {
        // 管理画面では60秒間隔
        add_filter('heartbeat_settings', function($settings) {
            $settings['interval'] = 60;
            return $settings;
        });
    } else {
        // フロントエンドでは無効化
        wp_deregister_script('heartbeat');
    }
});

/**
 * 画像のsrcsetとsizes属性を無効化
 */
add_filter('wp_calculate_image_srcset', '__return_false');

/**
 * DNS prefetch削除
 */
add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2);
function remove_dns_prefetch($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        return array_diff(wp_dependencies_unique_hosts(), $hints);
    }
    return $hints;
}

/**
 * WP-Cronを無効化（サーバーのcronを使用することを推奨）
 * 注意: この設定はwp-config.phpに記述することを推奨
 */
// define('DISABLE_WP_CRON', true);

/**
 * リビジョン数を制限（パフォーマンス向上）
 * 注意: この設定はwp-config.phpに記述することを推奨
 */
// define('WP_POST_REVISIONS', 3);

/**
 * 自動保存間隔を延長（パフォーマンス向上）
 * 注意: この設定はwp-config.phpに記述することを推奨
 */
// define('AUTOSAVE_INTERVAL', 300);

/**
 * ファイル編集を無効化（セキュリティ向上）
 * 注意: この設定はwp-config.phpに記述することを推奨
 */
// define('DISALLOW_FILE_EDIT', true);

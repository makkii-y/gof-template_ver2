<?php
/**
 * コメント機能無効化
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * コメント機能を完全に無効化
 */
add_action('admin_init', function() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
    remove_post_type_support('post', 'trackbacks');
});

/**
 * コメント関連のフィルター
 */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

/**
 * セルフピンバック無効化
 */
add_action('pre_ping', function(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
});

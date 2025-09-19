<?php
/**
 * 管理画面のカスタマイズ
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 管理画面のメニューを削除
 */
add_action('admin_menu', 'remove_menus');
function remove_menus() {
    //remove_menu_page( 'index.php' ); //ダッシュボード
    // remove_menu_page('edit.php'); //投稿メニュー
    //remove_menu_page( 'upload.php' ); //メディア
    //remove_menu_page( 'edit.php?post_type=page' ); //ページ追加
    remove_menu_page('edit-comments.php'); //コメントメニュー
    //remove_menu_page( 'themes.php' ); //外観メニュー
    //remove_menu_page( 'plugins.php' ); //プラグインメニュー
    //remove_menu_page( 'tools.php' ); //ツールメニュー
    //remove_menu_page( 'options-general.php' ); //設定メニュー
}

/**
 * ダッシュボードウィジェット削除
 */
add_action('wp_dashboard_setup', function() {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
});

/**
 * 不要なウィジェットを削除
 */
add_action('widgets_init', function() {
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Meta');
});

/**
 * ログイン画面のカスタマイズ
 */
// ログイン画面のロゴリンクをホームページに変更
add_filter('login_headerurl', function() {
    return home_url();
});

// ログイン画面のロゴのtitle属性を変更
add_filter('login_headertext', function() {
    return get_bloginfo('name');
});

/**
 * 管理画面のカスタムCSS
 */
add_action('admin_head', function() {
    echo '<style>
        /* 管理画面のカスタムスタイル */
        #wpadminbar { background: #333; }
        .wp-admin #wpadminbar .ab-top-menu > li.hover > .ab-item,
        .wp-admin #wpadminbar .ab-top-menu > li:hover > .ab-item,
        .wp-admin #wpadminbar .ab-top-menu > li.current > .ab-item { background: #555; }
    </style>';
});

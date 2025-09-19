<?php
/**
 * テーマの基本設定
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * テーマのセットアップ
 */
function gof_template_setup() {
    // テーマサポートを追加
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // メニューの登録
    register_nav_menus(array(
        'primary' => 'プライマリーメニュー',
        'footer'  => 'フッターメニュー',
    ));
}
add_action('after_setup_theme', 'gof_template_setup');

/**
 * スタイルとスクリプトの読み込み
 */
function gof_template_scripts() {
    wp_enqueue_style('gof-template-style', get_stylesheet_uri());
    wp_enqueue_script('gof-template-script', get_template_directory_uri() . '/js/script.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'gof_template_scripts');

/**
 * ウィジェットエリアの登録
 */
function gof_template_widgets_init() {
    register_sidebar(array(
        'name'          => 'サイドバー',
        'id'            => 'sidebar-1',
        'description'   => 'サイドバーのウィジェットエリア',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'gof_template_widgets_init');

/**
 * 抜粋の長さを変更
 */
function gof_template_excerpt_length($length) {
    return 50;
}
add_filter('excerpt_length', 'gof_template_excerpt_length');

/**
 * 抜粋の続きを読むテキストを変更
 */
function gof_template_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'gof_template_excerpt_more');

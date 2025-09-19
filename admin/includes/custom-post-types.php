<?php
/**
 * カスタム投稿タイプ & タクソノミー
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタム投稿タイプ登録用のクラス
 */
class GOF_Custom_Post_Type {
    
    /**
     * カスタム投稿タイプを登録
     */
    public static function register_post_type($post_type, $args = array()) {
        $defaults = array(
            'labels' => array(),
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => $post_type),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // 詳細ページを無効化したい場合のrewrite設定
        if (isset($args['disable_single_page']) && $args['disable_single_page'] === true) {
            $args['rewrite'] = array(
                'slug' => $post_type,
                'with_front' => false,
                'feeds' => false,
                'pages' => false, // 詳細ページのrewriteルールを無効化
            );
            // publicly_queryableは一覧ページアクセスのためtrueのまま
            $args['publicly_queryable'] = true;
            
            // 詳細ページアクセス時の404処理を追加
            add_action('template_redirect', function() use ($post_type) {
                if (is_singular($post_type)) {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header(404);
                    nocache_headers();
                }
            });
            
            // 管理画面でプレビューボタンを非表示にする
            add_action('admin_head', function() use ($post_type) {
                global $current_screen;
                if ($current_screen && $current_screen->post_type === $post_type) {
                    echo '<style>
                        /* プレビューボタン関連の要素を全て非表示 */
                        #preview-action,
                        .preview.button,
                        #post-preview,
                        .submitbox #preview-action,
                        #submitpost #preview-action,
                        .misc-pub-section.misc-pub-preview,
                        .preview-button,
                        a[href*="preview=true"],
                        input[name="wp-preview"],
                        #preview-action input,
                        .button.preview,
                        #post-body .preview,
                        .wp-toolbar .preview,
                        .components-button.is-compact.has-icon,
                        .components-button[aria-label*="プレビュー"],
                        .components-button[aria-label*="Preview"],
                        .components-button[title*="プレビュー"],
                        .components-button[title*="Preview"],
                        .editor-post-preview,
                        .editor-post-preview__dropdown,
                        .edit-post-header-toolbar__left .components-button.has-icon,
                        .edit-post-header .components-button[aria-label*="プレビュー"] {
                            display: none !important;
                            visibility: hidden !important;
                        }
                        
                        /* プレビューリンクを非表示 */
                        .row-actions .view,
                        .row-actions .preview,
                        .page-title-action.preview {
                            display: none !important;
                        }
                    </style>';
                    
                    echo '<script>
                        jQuery(document).ready(function($) {
                            // プレビューボタン要素を削除
                            $("#preview-action").remove();
                            $(".preview.button").remove();
                            $("#post-preview").remove();
                            $("input[name=\"wp-preview\"]").remove();
                            $(".misc-pub-section.misc-pub-preview").remove();
                            $("a[href*=\"preview=true\"]").remove();
                            $(".components-button.is-compact.has-icon").remove();
                            $(".components-button[aria-label*=\"プレビュー\"]").remove();
                            $(".components-button[aria-label*=\"Preview\"]").remove();
                            $(".components-button[title*=\"プレビュー\"]").remove();
                            $(".components-button[title*=\"Preview\"]").remove();
                            $(".editor-post-preview").remove();
                            $(".editor-post-preview__dropdown").remove();
                            
                            // DOM変更を監視してプレビューボタンが追加されたら削除
                            var observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    mutation.addedNodes.forEach(function(node) {
                                        if (node.nodeType === 1) {
                                            $(node).find("#preview-action, .preview.button, input[name=\"wp-preview\"], .components-button.is-compact.has-icon, .components-button[aria-label*=\"プレビュー\"], .components-button[aria-label*=\"Preview\"], .editor-post-preview").remove();
                                            if ($(node).is("#preview-action, .preview.button, input[name=\"wp-preview\"], .components-button.is-compact.has-icon, .components-button[aria-label*=\"プレビュー\"], .components-button[aria-label*=\"Preview\"], .editor-post-preview")) {
                                                $(node).remove();
                                            }
                                        }
                                    });
                                });
                            });
                            
                            observer.observe(document.body, {
                                childList: true,
                                subtree: true
                            });
                        });
                    </script>';
                }
            });
            
            // より早い段階でプレビューボタンを非表示にする
            add_action('admin_init', function() use ($post_type) {
                if (isset($_GET['post_type']) && $_GET['post_type'] === $post_type) {
                    add_action('admin_head', function() {
                        echo '<style>
                            #preview-action,
                            .preview.button,
                            input[name="wp-preview"],
                            .misc-pub-section.misc-pub-preview,
                            .components-button.is-compact.has-icon,
                            .components-button[aria-label*="プレビュー"],
                            .components-button[aria-label*="Preview"],
                            .components-button[title*="プレビュー"],
                            .components-button[title*="Preview"],
                            .editor-post-preview,
                            .editor-post-preview__dropdown {
                                display: none !important;
                            }
                        </style>';
                    }, 1);
                }
                
                if (isset($_GET['post']) && get_post_type($_GET['post']) === $post_type) {
                    add_action('admin_head', function() {
                        echo '<style>
                            #preview-action,
                            .preview.button,
                            input[name="wp-preview"],
                            .misc-pub-section.misc-pub-preview,
                            .components-button.is-compact.has-icon,
                            .components-button[aria-label*="プレビュー"],
                            .components-button[aria-label*="Preview"],
                            .components-button[title*="プレビュー"],
                            .components-button[title*="Preview"],
                            .editor-post-preview,
                            .editor-post-preview__dropdown {
                                display: none !important;
                            }
                        </style>';
                    }, 1);
                }
            });
            
            // 投稿リストでプレビューリンクを非表示にする
            add_filter('post_row_actions', function($actions, $post) use ($post_type) {
                if ($post->post_type === $post_type) {
                    unset($actions['view']);
                    unset($actions['preview']);
                }
                return $actions;
            }, 10, 2);
            
            // 管理バーのプレビューリンクを非表示にする
            add_action('admin_bar_menu', function($wp_admin_bar) use ($post_type) {
                if (is_admin() && function_exists('get_current_screen')) {
                    $screen = get_current_screen();
                    if ($screen && $screen->post_type === $post_type) {
                        $wp_admin_bar->remove_node('preview');
                        $wp_admin_bar->remove_node('view');
                    }
                }
            }, 81);
        }
        
        // ラベルが空の場合、デフォルトラベルを生成
        if (empty($args['labels'])) {
            $args['labels'] = self::generate_labels($post_type, $args);
        }
        
        register_post_type($post_type, $args);
    }
    
    /**
     * カスタムタクソノミーを登録
     */
    public static function register_taxonomy($taxonomy, $post_types, $args = array()) {
        $defaults = array(
            'labels' => array(),
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $taxonomy),
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // ラベルが空の場合、デフォルトラベルを生成
        if (empty($args['labels'])) {
            $args['labels'] = self::generate_taxonomy_labels($taxonomy, $args);
        }
        
        register_taxonomy($taxonomy, $post_types, $args);
    }
    
    /**
     * 投稿タイプ用のデフォルトラベルを生成
     */
    private static function generate_labels($post_type, $args) {
        $singular = isset($args['singular_name']) ? $args['singular_name'] : $post_type;
        $plural = isset($args['plural_name']) ? $args['plural_name'] : $post_type;
        
        return array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'add_new' => $singular . 'を追加',
            'add_new_item' => '新しい' . $singular . 'を追加',
            'edit_item' => $singular . 'を編集',
            'new_item' => '新しい' . $singular,
            'view_item' => $singular . 'を表示',
            'view_items' => $plural . 'を表示',
            'search_items' => $plural . 'を検索',
            'not_found' => $plural . 'が見つかりません',
            'not_found_in_trash' => 'ゴミ箱に' . $plural . 'はありません',
            'all_items' => 'すべての' . $plural,
            'archives' => $plural . 'アーカイブ',
            'attributes' => $singular . '属性',
            'insert_into_item' => $singular . 'に挿入',
            'uploaded_to_this_item' => $singular . 'にアップロード',
        );
    }
    
    /**
     * タクソノミー用のデフォルトラベルを生成
     */
    private static function generate_taxonomy_labels($taxonomy, $args) {
        $singular = isset($args['singular_name']) ? $args['singular_name'] : $taxonomy;
        $plural = isset($args['plural_name']) ? $args['plural_name'] : $taxonomy;
        
        return array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => 'すべての' . $plural,
            'edit_item' => $singular . 'を編集',
            'view_item' => $singular . 'を表示',
            'update_item' => $singular . 'を更新',
            'add_new_item' => '新しい' . $singular . 'を追加',
            'new_item_name' => '新しい' . $singular . '名',
            'parent_item' => '親' . $singular,
            'parent_item_colon' => '親' . $singular . '：',
            'search_items' => $plural . 'を検索',
            'popular_items' => '人気の' . $plural,
            'separate_items_with_commas' => $plural . 'をコンマで区切ってください',
            'add_or_remove_items' => $plural . 'を追加または削除',
            'choose_from_most_used' => 'よく使われる' . $plural . 'から選択',
            'not_found' => $plural . 'が見つかりません',
        );
    }
}

/**
 * カスタム投稿タイプの定義配列
 * 注意: この関数は functions.php で上書きできます
 */
function gof_get_custom_post_types() {
    // functions.php で gof_custom_post_types_config が定義されている場合はそれを使用
    if (function_exists('gof_custom_post_types_config')) {
        return gof_custom_post_types_config();
    }
    
    // デフォルト設定
    return array(
        // 作品・ポートフォリオ
        'works' => array(
            'singular_name' => '作品',
            'plural_name' => '作品',
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'has_archive' => true,
            'menu_position' => 5,
        ),
        
        // お知らせ
        'news' => array(
            'singular_name' => 'お知らせ',
            'plural_name' => 'お知らせ',
            'menu_icon' => 'dashicons-megaphone',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive' => true,
            'menu_position' => 6,
        ),
        
        // FAQ
        'faq' => array(
            'singular_name' => 'FAQ',
            'plural_name' => 'FAQ',
            'menu_icon' => 'dashicons-editor-help',
            'supports' => array('title', 'editor'),
            'has_archive' => true,
            'menu_position' => 7,
        ),
        
        // サービス
        'services' => array(
            'singular_name' => 'サービス',
            'plural_name' => 'サービス',
            'menu_icon' => 'dashicons-admin-tools',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => true,
            'menu_position' => 8,
        ),
    );
}

/**
 * カスタムタクソノミーの定義配列
 * 注意: この関数は functions.php で上書きできます
 */
function gof_get_custom_taxonomies() {
    // functions.php で gof_custom_taxonomies_config が定義されている場合はそれを使用
    if (function_exists('gof_custom_taxonomies_config')) {
        return gof_custom_taxonomies_config();
    }
    
    // デフォルト設定
    return array(
        // 作品カテゴリー
        'works_category' => array(
            'post_types' => array('works'),
            'singular_name' => '作品カテゴリー',
            'plural_name' => '作品カテゴリー',
            'hierarchical' => true,
        ),
        
        // 作品タグ
        'works_tag' => array(
            'post_types' => array('works'),
            'singular_name' => '作品タグ',
            'plural_name' => '作品タグ',
            'hierarchical' => false,
        ),
        
        // お知らせカテゴリー
        'news_category' => array(
            'post_types' => array('news'),
            'singular_name' => 'お知らせカテゴリー',
            'plural_name' => 'お知らせカテゴリー',
            'hierarchical' => true,
        ),
        
        // FAQカテゴリー
        'faq_category' => array(
            'post_types' => array('faq'),
            'singular_name' => 'FAQカテゴリー',
            'plural_name' => 'FAQカテゴリー',
            'hierarchical' => true,
        ),
        
        // サービスカテゴリー
        'services_category' => array(
            'post_types' => array('services'),
            'singular_name' => 'サービスカテゴリー',
            'plural_name' => 'サービスカテゴリー',
            'hierarchical' => true,
        ),
    );
}

/**
 * カスタム投稿タイプとタクソノミーを登録
 */
add_action('init', function() {
    // カスタム投稿タイプの登録
    $post_types = gof_get_custom_post_types();
    foreach ($post_types as $post_type => $args) {
        GOF_Custom_Post_Type::register_post_type($post_type, $args);
    }
    
    // カスタムタクソノミーの登録
    $taxonomies = gof_get_custom_taxonomies();
    foreach ($taxonomies as $taxonomy => $args) {
        $post_types = $args['post_types'];
        unset($args['post_types']);
        GOF_Custom_Post_Type::register_taxonomy($taxonomy, $post_types, $args);
    }
});

/**
 * カスタム投稿タイプのパーマリンク設定を自動で更新
 */
add_action('after_switch_theme', function() {
    flush_rewrite_rules();
});

/**
 * カスタム投稿タイプのアーカイブページタイトルをカスタマイズ
 */
add_filter('get_the_archive_title', function($title) {
    if (is_post_type_archive()) {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);
        if ($post_type_obj) {
            $title = $post_type_obj->labels->name;
        }
    }
    return $title;
});

/**
 * 管理画面でカスタム投稿タイプの投稿数を表示
 */
add_action('dashboard_glance_items', function() {
    $post_types = gof_get_custom_post_types();
    foreach ($post_types as $post_type => $args) {
        $num_posts = wp_count_posts($post_type);
        $num = number_format_i18n($num_posts->publish);
        $text = $args['plural_name'];
        
        if (current_user_can('edit_posts')) {
            $link = admin_url('edit.php?post_type=' . $post_type);
            echo '<li class="' . $post_type . '-count"><a href="' . $link . '">' . $num . ' ' . $text . '</a></li>';
        } else {
            echo '<li class="' . $post_type . '-count">' . $num . ' ' . $text . '</li>';
        }
    }
});

<?php
/**
 * パンくずリストテンプレート
 * 構造化データ（BreadcrumbList）も出力
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * パンくずリストクラス
 */
if (!class_exists('GOF_Breadcrumb')) {
    class GOF_Breadcrumb {
    
    private $breadcrumbs = array();
    
    /**
     * パンくずリストを生成
     */
    public function generate_breadcrumb() {
        // ホームを追加
        $this->add_breadcrumb('ホーム', home_url('/'));
        
        if (is_home() || is_front_page()) {
            // ホームページの場合は何も追加しない
            return;
        }
        
        if (is_single()) {
            $this->generate_single_breadcrumb();
        } elseif (is_page()) {
            $this->generate_page_breadcrumb();
        } elseif (is_category()) {
            $this->generate_category_breadcrumb();
        } elseif (is_tag()) {
            $this->generate_tag_breadcrumb();
        } elseif (is_tax()) {
            $this->generate_taxonomy_breadcrumb();
        } elseif (is_archive()) {
            $this->generate_archive_breadcrumb();
        } elseif (is_search()) {
            $this->add_breadcrumb('検索結果', '');
        } elseif (is_404()) {
            $this->add_breadcrumb('ページが見つかりません', '');
        }
    }
    
    /**
     * 投稿ページのパンくずリスト
     */
    private function generate_single_breadcrumb() {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);
        
        // カスタム投稿タイプの場合はアーカイブページを追加
        if ($post_type !== 'post' && $post_type_obj && $post_type_obj->has_archive) {
            $archive_link = get_post_type_archive_link($post_type);
            if ($archive_link) {
                $this->add_breadcrumb($post_type_obj->labels->name, $archive_link);
            }
        } elseif ($post_type === 'post') {
            // 通常の投稿の場合はブログページを追加
            $blog_page_id = get_option('page_for_posts');
            if ($blog_page_id) {
                $this->add_breadcrumb(get_the_title($blog_page_id), get_permalink($blog_page_id));
            }
        }
        
        // カテゴリーを追加
        $this->add_categories_to_breadcrumb();
        
        // 現在の投稿を追加
        $this->add_breadcrumb(get_the_title(), '');
    }
    
    /**
     * 固定ページのパンくずリスト
     */
    private function generate_page_breadcrumb() {
        $post = get_post();
        $parents = array();
        
        // 親ページを取得
        if ($post->post_parent) {
            $parent_id = $post->post_parent;
            while ($parent_id) {
                $parent = get_post($parent_id);
                $parents[] = $parent;
                $parent_id = $parent->post_parent;
            }
            $parents = array_reverse($parents);
        }
        
        // 親ページを追加
        foreach ($parents as $parent) {
            $this->add_breadcrumb(get_the_title($parent->ID), get_permalink($parent->ID));
        }
        
        // 現在のページを追加
        $this->add_breadcrumb(get_the_title(), '');
    }
    
    /**
     * カテゴリーページのパンくずリスト
     */
    private function generate_category_breadcrumb() {
        $category = get_queried_object();
        
        // 親カテゴリーを追加
        if ($category->parent) {
            $parents = get_category_parents($category->parent, true, '|||');
            $parents = explode('|||', $parents);
            foreach ($parents as $parent) {
                if (!empty(trim($parent))) {
                    // HTMLタグを除去してタイトルとURLを抽出
                    preg_match('/<a href="([^"]*)"[^>]*>([^<]*)<\/a>/', $parent, $matches);
                    if (isset($matches[1]) && isset($matches[2])) {
                        $this->add_breadcrumb(trim($matches[2]), $matches[1]);
                    }
                }
            }
        }
        
        // 現在のカテゴリーを追加
        $this->add_breadcrumb($category->name, '');
    }
    
    /**
     * タグページのパンくずリスト
     */
    private function generate_tag_breadcrumb() {
        $tag = get_queried_object();
        $this->add_breadcrumb('タグ: ' . $tag->name, '');
    }
    
    /**
     * タクソノミーページのパンくずリスト
     */
    private function generate_taxonomy_breadcrumb() {
        $term = get_queried_object();
        $taxonomy = get_taxonomy($term->taxonomy);
        
        // 関連する投稿タイプのアーカイブを追加
        if (!empty($taxonomy->object_type)) {
            $post_type = $taxonomy->object_type[0];
            $post_type_obj = get_post_type_object($post_type);
            
            if ($post_type_obj && $post_type_obj->has_archive) {
                $archive_link = get_post_type_archive_link($post_type);
                if ($archive_link) {
                    $this->add_breadcrumb($post_type_obj->labels->name, $archive_link);
                }
            }
        }
        
        // 親タームを追加
        if ($term->parent) {
            $parents = get_ancestors($term->term_id, $term->taxonomy);
            $parents = array_reverse($parents);
            
            foreach ($parents as $parent_id) {
                $parent = get_term($parent_id, $term->taxonomy);
                $this->add_breadcrumb($parent->name, get_term_link($parent));
            }
        }
        
        // 現在のタームを追加
        $this->add_breadcrumb($term->name, '');
    }
    
    /**
     * アーカイブページのパンくずリスト
     */
    private function generate_archive_breadcrumb() {
        if (is_author()) {
            $author = get_queried_object();
            $this->add_breadcrumb('著者: ' . $author->display_name, '');
        } elseif (is_date()) {
            if (is_year()) {
                $this->add_breadcrumb(get_the_date('Y年'), '');
            } elseif (is_month()) {
                $this->add_breadcrumb(get_the_date('Y年'), get_year_link(get_the_date('Y')));
                $this->add_breadcrumb(get_the_date('n月'), '');
            } elseif (is_day()) {
                $this->add_breadcrumb(get_the_date('Y年'), get_year_link(get_the_date('Y')));
                $this->add_breadcrumb(get_the_date('n月'), get_month_link(get_the_date('Y'), get_the_date('n')));
                $this->add_breadcrumb(get_the_date('j日'), '');
            }
        } elseif (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj) {
                $this->add_breadcrumb($post_type_obj->labels->name, '');
            }
        }
    }
    
    /**
     * カテゴリーをパンくずリストに追加
     */
    private function add_categories_to_breadcrumb() {
        $post_type = get_post_type();
        
        // カスタム投稿タイプのカテゴリー名を動的に決定
        $category_taxonomy = ($post_type === 'post') ? 'category' : $post_type . '_category';
        
        // カテゴリーを取得
        $categories = get_the_terms(get_the_ID(), $category_taxonomy);
        
        if (!empty($categories) && !is_wp_error($categories)) {
            // 最初のカテゴリーのみ使用
            $category = $categories[0];
            
            // 親カテゴリーを追加
            if ($category->parent) {
                $parents = get_ancestors($category->term_id, $category_taxonomy);
                $parents = array_reverse($parents);
                
                foreach ($parents as $parent_id) {
                    $parent = get_term($parent_id, $category_taxonomy);
                    $this->add_breadcrumb($parent->name, get_term_link($parent));
                }
            }
            
            // 現在のカテゴリーを追加
            $this->add_breadcrumb($category->name, get_term_link($category));
        }
    }
    
    /**
     * パンくずリスト項目を追加
     */
    private function add_breadcrumb($title, $url) {
        $this->breadcrumbs[] = array(
            'title' => $title,
            'url' => $url
        );
    }
    
    /**
     * パンくずリストを表示
     */
    public function display_breadcrumb() {
        $this->generate_breadcrumb();
        
        if (empty($this->breadcrumbs) || count($this->breadcrumbs) <= 1) {
            return;
        }
        
        echo '<nav class="breadcrumb-nav" aria-label="パンくずリスト">';
        echo '<ol class="breadcrumb-list">';
        
        foreach ($this->breadcrumbs as $index => $breadcrumb) {
            $is_last = ($index === count($this->breadcrumbs) - 1);
            
            echo '<li class="breadcrumb-item' . ($is_last ? ' current' : '') . '">';
            
            if (!$is_last && !empty($breadcrumb['url'])) {
                echo '<a href="' . esc_url($breadcrumb['url']) . '">';
                echo esc_html($breadcrumb['title']);
                echo '</a>';
            } else {
                echo '<span>' . esc_html($breadcrumb['title']) . '</span>';
            }
            
            if (!$is_last) {
                echo '<span class="breadcrumb-separator" aria-hidden="true">›</span>';
            }
            
            echo '</li>';
        }
        
        echo '</ol>';
        echo '</nav>';
    }
    
    /**
     * 構造化データを取得
     */
    public function get_structured_data() {
        $this->generate_breadcrumb();
        
        if (empty($this->breadcrumbs) || count($this->breadcrumbs) <= 1) {
            return array();
        }
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array()
        );
        
        foreach ($this->breadcrumbs as $index => $breadcrumb) {
            $item = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['title']
            );
            
            // 最後の項目以外はURLを含める
            if ($index < count($this->breadcrumbs) - 1 && !empty($breadcrumb['url'])) {
                $item['item'] = $breadcrumb['url'];
            }
            
            $structured_data['itemListElement'][] = $item;
        }
        
        return $structured_data;
    }
    
    /**
     * 構造化データを出力
     */
    public function output_structured_data() {
        // このメソッドは直接呼び出さず、structured-data.phpクラスから呼び出される
        // 重複を避けるためコメントアウト
        /*
        $structured_data = $this->get_structured_data();
        
        if (!empty($structured_data)) {
            echo "\n" . '<script type="application/ld+json">' . "\n";
            echo wp_json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo "\n" . '</script>' . "\n";
        }
        */
    }
}
}

// パンくずリストを表示（構造化データは structured-data.php で管理）
if (!is_home() && !is_front_page()) {
    $breadcrumb = new GOF_Breadcrumb();
    $breadcrumb->display_breadcrumb();
}
?>

<!-- パンくずリスト用CSS -->
<style>
.breadcrumb-nav {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 0.9rem;
}

.breadcrumb-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child) {
    margin-right: 0.5rem;
}

.breadcrumb-item a {
    color: #007cba;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #005a87;
    text-decoration: underline;
}

.breadcrumb-item.current span {
    color: #666;
    font-weight: 500;
}

.breadcrumb-separator {
    margin: 0 0.5rem;
    color: #999;
    font-weight: normal;
}

/* レスポンシブ対応 */
@media (max-width: 480px) {
    .breadcrumb-nav {
        font-size: 0.8rem;
        padding: 0.75rem;
    }
    
    .breadcrumb-separator {
        margin: 0 0.25rem;
    }
}
</style>

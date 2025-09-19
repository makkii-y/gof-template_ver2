<?php
/**
 * 汎用アーカイブページテンプレート
 * 任意のカスタム投稿タイプで使用可能
 * 
 * 使用方法: archive-{投稿タイプ名}.php としてコピーして使用
 * 
 * @package GOF_Template_Ver2
 */

get_header(); 

// 投稿タイプ情報を取得
$post_type = get_post_type();
$post_type_obj = get_post_type_object($post_type);
$post_type_name = $post_type_obj->labels->name ?? '投稿';
$post_type_singular = $post_type_obj->labels->singular_name ?? '投稿';

// タクソノミー名を動的に決定
$category_taxonomy = $post_type . '_category';
$tag_taxonomy = $post_type . '_tag';
$taxonomies = array($category_taxonomy, $tag_taxonomy);
?>

<main id="main" class="site-main">
    
    <!-- ページヘッダー -->
    <?php get_template_part('templates/parts/archive', 'header', array(
        'post_type_name' => $post_type_name
    )); ?>

    <!-- アーカイブコンテンツ -->
    <section class="archive-content">
        <div class="container">
            
            <?php if (have_posts()) : ?>
                
                <!-- フィルター（カテゴリー・タグ） -->
                <?php get_template_part('templates/parts/taxonomy', 'filter', array(
                    'post_type' => $post_type,
                    'taxonomies' => $taxonomies
                )); ?>

                <!-- 投稿一覧 -->
                <?php get_template_part('templates/parts/posts', 'grid', array(
                    'post_type' => $post_type,
                    'taxonomies' => $taxonomies
                )); ?>

                <!-- ページネーション -->
                <div class="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '&laquo; 前のページ',
                        'next_text' => '次のページ &raquo;',
                    ));
                    ?>
                </div>

            <?php else : ?>
                
                <!-- 投稿が見つからない場合 -->
                <div class="no-posts">
                    <h2><?php echo esc_html($post_type_name); ?>が見つかりませんでした</h2>
                    <p>現在、表示できる<?php echo esc_html($post_type_name); ?>がありません。</p>
                    <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary">ホームに戻る</a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<!-- アーカイブページ用スタイル -->
<style>
/* アーカイブページ汎用スタイル */
.archive-header {
    background: linear-gradient(135deg, #007cba 0%, #005a8b 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.archive-title {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: bold;
}

.archive-description {
    font-size: 1.2rem;
    opacity: 0.9;
}

.archive-content {
    padding: 3rem 0;
}

.content-filter {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
}

.filter-section {
    margin-bottom: 2rem;
}

.filter-section:last-child {
    margin-bottom: 0;
}

.filter-title {
    margin-bottom: 1rem;
    color: #333;
    font-size: 1.1rem;
}

.filter-all {
    background: #007cba;
    color: white !important;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 1rem;
}

.filter-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-link {
    background: #f0f0f0;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.filter-link:hover {
    background: #007cba;
    color: white;
}

.filter-link .count {
    font-size: 0.9rem;
    opacity: 0.7;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.post-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.post-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.post-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.post-image {
    height: 200px;
    overflow: hidden;
}

.post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-image img {
    transform: scale(1.05);
}

.post-info {
    padding: 2rem;
}

.post-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #333;
    line-height: 1.3;
}

.post-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.post-meta {
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

.post-taxonomy {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.taxonomy-tag {
    background: #f5f5f5;
    color: #666;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.pagination-wrapper {
    text-align: center;
    margin: 3rem 0;
}

.page-numbers {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    background: white;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 4px;
}

.page-numbers:hover,
.page-numbers.current {
    background: #007cba;
    color: white;
    border-color: #007cba;
}

.no-posts {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.no-posts h2 {
    color: #333;
    margin-bottom: 1rem;
}

.no-posts p {
    color: #666;
    margin-bottom: 2rem;
}

.btn {
    display: inline-block;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007cba;
    color: white;
}

.btn-primary:hover {
    background: #005a8b;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,124,186,0.3);
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-list {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .post-info {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .archive-header {
        padding: 2rem 0;
    }
    
    .archive-content {
        padding: 2rem 0;
    }
    
    .content-filter {
        padding: 1.5rem;
    }
    
    .post-info {
        padding: 1rem;
    }
}
</style>

<?php get_footer(); ?>

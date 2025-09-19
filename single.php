<?php
/**
 * 汎用シングルページテンプレート
 * 任意のカスタム投稿タイプで使用可能
 * 
 * 使用方法: single-{投稿タイプ名}.php としてコピーして使用
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
    
    <!-- パンくずリスト -->
    <div class="container">
        <?php get_template_part('template-parts/breadcrumb'); ?>
    </div>
    
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
            
            <!-- ページヘッダー -->
            <header class="post-header">
                <div class="container">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    
                    <!-- 投稿抜粋 -->
                    <?php if (!empty(get_the_excerpt())) : ?>
                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large', array('class' => 'post-image')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <!-- 投稿基本情報 -->
            <section class="post-info">
                <div class="container">
                    
                    <!-- メインコンテンツ -->
                    <div class="post-content">
                        
                        <!-- 投稿本文 -->
                        <?php if (!empty(get_the_content())) : ?>
                            <div class="post-content-body">
                                <?php the_content(); ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- 投稿詳細情報 -->
                    <div class="post-details">
                        
                        <!-- 投稿情報 -->
                        <div class="post-detail-item">
                            <h3>投稿情報</h3>
                            <p><strong>公開日:</strong> <?php echo get_the_date(); ?></p>
                            <?php if (get_the_date() !== get_the_modified_date()) : ?>
                                <p><strong>更新日:</strong> <?php echo get_the_modified_date(); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- タクソノミー情報 -->
                        <?php foreach ($taxonomies as $taxonomy) : ?>
                            <?php 
                            $terms = get_the_terms(get_the_ID(), $taxonomy);
                            if (!empty($terms) && !is_wp_error($terms)) : 
                                $taxonomy_obj = get_taxonomy($taxonomy);
                                $taxonomy_label = $taxonomy_obj->labels->name ?? $taxonomy;
                            ?>
                                <div class="post-detail-item">
                                    <h3><?php echo esc_html($taxonomy_label); ?></h3>
                                    <ul class="post-terms">
                                        <?php foreach ($terms as $term) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_term_link($term)); ?>">
                                                    <?php echo esc_html($term->name); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                    
                </div>
            </section>

            <!-- ナビゲーション -->
            <nav class="post-navigation">
                <div class="container">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    <?php if ($prev_post || $next_post) : ?>
                        <div class="nav-links">
                            <?php if ($prev_post) : ?>
                                <div class="nav-previous">
                                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" rel="prev">
                                        <span class="nav-title">前の記事</span>
                                        <span class="nav-subtitle"><?php echo esc_html($prev_post->post_title); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_post) : ?>
                                <div class="nav-next">
                                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" rel="next">
                                        <span class="nav-title">次の記事</span>
                                        <span class="nav-subtitle"><?php echo esc_html($next_post->post_title); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>

        </article>
    <?php endwhile; ?>
</main>

<!-- シングルページ用スタイル -->
<style>
/* シングルページ汎用スタイル */
.single-post {
    margin-bottom: 2rem;
}

.post-header {
    background: #f8f9fa;
    padding: 3rem 0;
    margin-bottom: 2rem;
}

.post-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #333;
    line-height: 1.2;
}

.post-thumbnail {
    margin-top: 2rem;
}

.post-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    max-height: 400px;
    object-fit: cover;
}

.post-info {
    margin-bottom: 3rem;
}

.post-content {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.post-content-body {
    line-height: 1.8;
}

.post-content-body h2,
.post-content-body h3,
.post-content-body h4 {
    color: #333;
    margin: 2rem 0 1rem 0;
    border-bottom: 2px solid #007cba;
    padding-bottom: 0.5rem;
}

.post-content-body p {
    margin-bottom: 1.5rem;
}

.post-excerpt {
    margin-top: 2rem;
}

.post-details {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.post-detail-item {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.post-detail-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.post-detail-item h3 {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1rem;
}

.post-detail-item p {
    margin-bottom: 0.5rem;
}

.post-terms {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.post-terms li {
    background: #f0f0f0;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.9rem;
}

.post-terms a {
    color: #333;
    text-decoration: none;
}

.post-terms a:hover {
    color: #007cba;
}

.post-navigation {
    background: #f8f9fa;
    padding: 2rem 0;
    margin-top: 3rem;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.nav-previous,
.nav-next {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-previous a,
.nav-next a {
    display: block;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.3s ease;
}

.nav-previous a:hover,
.nav-next a:hover {
    background: #f8f9fa;
}

.nav-title {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.nav-subtitle {
    display: block;
    font-weight: bold;
    color: #333;
}

.nav-next {
    text-align: right;
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .post-title {
        font-size: 2rem;
    }
    
    .nav-links {
        grid-template-columns: 1fr;
    }
    
    .nav-next {
        text-align: left;
    }
}

@media (max-width: 480px) {
    .post-header {
        padding: 2rem 0;
    }
    
    .post-content,
    .post-details {
        padding: 1.5rem;
    }
    
    .post-title {
        font-size: 1.8rem;
    }
}
</style>

<?php get_footer(); ?>

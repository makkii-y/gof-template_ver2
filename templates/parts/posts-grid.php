<?php
/**
 * 投稿グリッド表示部分
 * 
 * 使用例:
 * 
 * // 基本的な使用方法
 * get_template_part('templates/parts/posts', 'grid', array(
 *     'post_type' => 'service'
 * ));
 * 
 * // タクソノミー情報も表示
 * get_template_part('templates/parts/posts', 'grid', array(
 *     'post_type' => 'service',
 *     'taxonomies' => array('service_category', 'service_tag')
 * ));
 * 
 * @param array $args {
 *     @type string $post_type 投稿タイプ名
 *     @type array $taxonomies 表示するタクソノミー名の配列（オプション）
 * }
 */
$post_type = $args['post_type'] ?? '';
$taxonomies = $args['taxonomies'] ?? array();
?>

<div class="posts-grid">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
            <a href="<?php the_permalink(); ?>" class="post-link">
                
                <!-- 投稿画像 -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-image">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                <?php endif; ?>

                <!-- 投稿情報 -->
                <div class="post-info">
                    <h2 class="post-title"><?php the_title(); ?></h2>
                    
                    <!-- 投稿説明 -->
                    <?php if (has_excerpt()) : ?>
                        <p class="post-description"><?php the_excerpt(); ?></p>
                    <?php else : ?>
                        <p class="post-description"><?php echo esc_html(wp_trim_words(get_the_content(), 20)); ?></p>
                    <?php endif; ?>

                    <!-- 投稿メタ情報 -->
                    <div class="post-meta">
                        
                        <!-- タクソノミー情報 -->
                        <?php if (!empty($taxonomies)) : ?>
                            <?php foreach ($taxonomies as $taxonomy) : ?>
                                <?php 
                                $terms = get_the_terms(get_the_ID(), $taxonomy);
                                if (!empty($terms) && !is_wp_error($terms)) : ?>
                                    <div class="post-taxonomy">
                                        <?php foreach ($terms as $term) : ?>
                                            <span class="taxonomy-tag"><?php echo esc_html($term->name); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </a>
        </article>
    <?php endwhile; ?>
</div>

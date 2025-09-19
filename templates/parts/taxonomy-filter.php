<?php
/**
 * タクソノミーフィルター部分（カテゴリー・タグ対応）
 * 
 * 使用例:
 * 
 * // カテゴリーのみ表示
 * get_template_part('templates/parts/taxonomy', 'filter', array(
 *     'post_type' => 'service',
 *     'taxonomies' => array('service_category')
 * ));
 * 
 * // カテゴリーとタグ両方表示
 * get_template_part('templates/parts/taxonomy', 'filter', array(
 *     'post_type' => 'service',
 *     'taxonomies' => array('service_category', 'service_tag')
 * ));
 * 
 * @param array $args {
 *     @type string $post_type 投稿タイプ名
 *     @type array $taxonomies タクソノミー名の配列
 * }
 */
$post_type = $args['post_type'] ?? '';
$taxonomies = $args['taxonomies'] ?? array();

if (empty($taxonomies) || empty($post_type)) return;

// 複数のタクソノミーに対応
$all_terms = array();
$taxonomy_labels = array();

foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true
    ));
    
    if (!empty($terms) && !is_wp_error($terms)) {
        $taxonomy_obj = get_taxonomy($taxonomy);
        $taxonomy_labels[$taxonomy] = $taxonomy_obj->labels->name ?? $taxonomy;
        $all_terms[$taxonomy] = $terms;
    }
}

if (empty($all_terms)) return;
?>

<div class="content-filter">
    <h3>絞り込み</h3>
    
    <!-- すべて表示リンク -->
    <div class="filter-section">
        <a href="<?php echo esc_url(get_post_type_archive_link($post_type)); ?>" class="filter-link filter-all">すべて表示</a>
    </div>
    
    <!-- 各タクソノミーのフィルター -->
    <?php foreach ($all_terms as $taxonomy => $terms) : ?>
        <div class="filter-section">
            <h4 class="filter-title"><?php echo esc_html($taxonomy_labels[$taxonomy]); ?>で絞り込み</h4>
            <ul class="filter-list">
                <?php foreach ($terms as $term) : ?>
                    <li>
                        <a href="<?php echo esc_url(get_term_link($term)); ?>" class="filter-link">
                            <?php echo esc_html($term->name); ?>
                            <span class="count">(<?php echo $term->count; ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

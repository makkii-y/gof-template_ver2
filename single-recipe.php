<?php
/**
 * レシピ詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * レシピ専用の構造化データを追加します
 */

// レシピ構造化データ関数を先に定義
function gof_get_recipe_structured_data($data, $post_id) {
    // カスタムフィールドから基本情報を取得
    $recipe_name = get_post_meta($post_id, 'recipe_name', true);
    $recipe_description = get_post_meta($post_id, 'recipe_description', true);
    $author_name = get_post_meta($post_id, 'recipe_author_name', true);
    
    // 時間情報
    $prep_time = get_post_meta($post_id, 'prep_time_minutes', true);
    $cook_time = get_post_meta($post_id, 'cook_time_minutes', true);
    $total_time = get_post_meta($post_id, 'total_time_minutes', true);
    
    // 分量・カテゴリ情報
    $recipe_yield = get_post_meta($post_id, 'recipe_yield', true);
    $recipe_category = get_post_meta($post_id, 'recipe_category', true);
    $recipe_cuisine = get_post_meta($post_id, 'recipe_cuisine', true);
    $recipe_keywords = get_post_meta($post_id, 'recipe_keywords', true);
    
    // 栄養情報
    $nutrition_calories = get_post_meta($post_id, 'nutrition_calories', true);
    
    // 日付情報（投稿日から取得）
    $date_published = get_the_date('c', $post_id);
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($recipe_description)) {
        $description = $recipe_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // Recipe型構造化データを構築
    $recipe_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        'name' => !empty($recipe_name) ? $recipe_name : get_the_title($post_id),
        'description' => $description,
        'datePublished' => $date_published,
        'url' => get_permalink($post_id)
    );
    
    // 作者情報
    if (!empty($author_name)) {
        $recipe_data['author'] = array(
            '@type' => 'Person',
            'name' => $author_name
        );
    } else {
        $recipe_data['author'] = array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', get_post_field('post_author', $post_id))
        );
    }
    
    // 画像情報
    $images = array();
    
    // Featured image
    if (has_post_thumbnail($post_id)) {
        $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
        if ($featured_image) {
            $images[] = $featured_image[0];
        }
    }
    
    // Additional images
    $recipe_images = get_post_meta($post_id, 'recipe_images', true);
    if ($recipe_images && is_array($recipe_images)) {
        foreach ($recipe_images as $recipe_image) {
            if (!empty($recipe_image['image'])) {
                $image_url = wp_get_attachment_image_src($recipe_image['image'], 'large');
                if ($image_url) {
                    $images[] = $image_url[0];
                }
            }
        }
    }
    
    if (!empty($images)) {
        $recipe_data['image'] = $images;
    }
    
    // 時間情報（ISO 8601 Duration形式）
    if ($prep_time) {
        $recipe_data['prepTime'] = 'PT' . $prep_time . 'M';
    }
    if ($cook_time) {
        $recipe_data['cookTime'] = 'PT' . $cook_time . 'M';
    }
    if ($total_time) {
        $recipe_data['totalTime'] = 'PT' . $total_time . 'M';
    }
    
    // 分量・カテゴリ
    if ($recipe_yield) {
        $recipe_data['recipeYield'] = $recipe_yield;
    }
    if ($recipe_category) {
        $recipe_data['recipeCategory'] = $recipe_category;
    }
    if ($recipe_cuisine) {
        $recipe_data['recipeCuisine'] = $recipe_cuisine;
    }
    if ($recipe_keywords) {
        $recipe_data['keywords'] = $recipe_keywords;
    }
    
    // 栄養情報
    if ($nutrition_calories) {
        $recipe_data['nutrition'] = array(
            '@type' => 'NutritionInformation',
            'calories' => $nutrition_calories . ' calories'
        );
    }
    
    // 材料リスト
    $ingredients = get_post_meta($post_id, 'recipe_ingredients', true);
    $recipe_ingredients = array();
    if ($ingredients && is_array($ingredients)) {
        foreach ($ingredients as $ingredient) {
            if (!empty($ingredient['ingredient'])) {
                $recipe_ingredients[] = $ingredient['ingredient'];
            }
        }
    }
    
    if (!empty($recipe_ingredients)) {
        $recipe_data['recipeIngredient'] = $recipe_ingredients;
    }
    
    // 作り方手順
    $instructions_data = get_post_meta($post_id, 'recipe_instructions', true);
    $recipe_instructions = array();
    if ($instructions_data && is_array($instructions_data)) {
        foreach ($instructions_data as $index => $instruction) {
            if (!empty($instruction['step_text'])) {
                $instruction_item = array(
                    '@type' => 'HowToStep',
                    'position' => $index + 1,
                    'text' => $instruction['step_text']
                );
                
                if (!empty($instruction['step_name'])) {
                    $instruction_item['name'] = $instruction['step_name'];
                }
                
                if (!empty($instruction['step_url'])) {
                    $instruction_item['url'] = $instruction['step_url'];
                }
                
                if (!empty($instruction['step_image'])) {
                    $instruction_item['image'] = $instruction['step_image'];
                }
                
                $recipe_instructions[] = $instruction_item;
            }
        }
    }
    
    if (!empty($recipe_instructions)) {
        $recipe_data['recipeInstructions'] = $recipe_instructions;
    }
    
    // 動画情報
    $video_name = get_post_meta($post_id, 'recipe_video_name', true);
    $video_description = get_post_meta($post_id, 'recipe_video_description', true);
    $video_thumbnail_url = get_post_meta($post_id, 'recipe_video_thumbnail_url', true);
    $video_content_url = get_post_meta($post_id, 'recipe_video_content_url', true);
    $video_upload_date = get_post_meta($post_id, 'recipe_video_upload_date', true);
    
    if ($video_name && $video_content_url) {
        $video_data = array(
            '@type' => 'VideoObject',
            'name' => $video_name,
            'contentUrl' => $video_content_url
        );
        
        if ($video_description) {
            $video_data['description'] = $video_description;
        }
        
        if ($video_thumbnail_url) {
            $video_data['thumbnailUrl'] = $video_thumbnail_url;
        }
        
        if ($video_upload_date) {
            $video_data['uploadDate'] = gof_convert_to_iso8601($video_upload_date);
        }
        
        $recipe_data['video'] = $video_data;
    }
    
    // フィルターフックでカスタマイズ可能
    $recipe_data = apply_filters('gof_recipe_structured_data', $recipe_data);
    
    return $recipe_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_recipe', 'gof_get_recipe_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

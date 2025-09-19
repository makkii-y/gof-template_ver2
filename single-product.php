<?php
/**
 * 商品詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * 商品専用の構造化データを追加します
 */

// 商品構造化データ関数を先に定義
function gof_get_product_structured_data($data, $post_id) {
    // カスタムフィールドから情報を取得
    $product_description = get_post_meta($post_id, 'product_description', true);
    $product_sku = get_post_meta($post_id, 'product_sku', true);
    $product_mpn = get_post_meta($post_id, 'product_mpn', true);
    $product_brand = get_post_meta($post_id, 'product_brand', true);
    $product_material = get_post_meta($post_id, 'product_material', true);
    $product_color = get_post_meta($post_id, 'product_color', true);
    $product_pattern = get_post_meta($post_id, 'product_pattern', true);
    $product_size = get_post_meta($post_id, 'product_size', true);
    $product_audience_gender = get_post_meta($post_id, 'product_audience_gender', true);
    $product_audience_min_age = get_post_meta($post_id, 'product_audience_min_age', true);
    $product_audience_max_age = get_post_meta($post_id, 'product_audience_max_age', true);
    $product_price = get_post_meta($post_id, 'product_price', true);
    $product_currency = get_post_meta($post_id, 'product_currency', true);
    $product_condition = get_post_meta($post_id, 'product_condition', true);
    $product_availability = get_post_meta($post_id, 'product_availability', true);
    $product_url = get_post_meta($post_id, 'product_url', true);
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($product_description)) {
        $description = $product_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // Product型構造化データを構築
    $product_data = array(
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => get_the_title($post_id),
        'description' => $description
    );
    
    // 商品画像（複数対応）
    $images = array();
    
    // アイキャッチ画像を最初に追加
    if (has_post_thumbnail($post_id)) {
        $images[] = get_the_post_thumbnail_url($post_id, 'large');
    }
    
    // 繰り返しフィールドから画像を取得
    $product_images = get_post_meta($post_id, 'product_images', true);
    if (!empty($product_images) && is_array($product_images)) {
        foreach ($product_images as $image_data) {
            if (!empty($image_data['image'])) {
                $image_id = $image_data['image'];
                $image_url = wp_get_attachment_image_url($image_id, 'large');
                
                if ($image_url) {
                    $images[] = $image_url;
                }
            }
        }
    }
    
    if (!empty($images)) {
        $product_data['image'] = $images;
    }
    
    // SKU（商品管理番号）
    if (!empty($product_sku)) {
        $product_data['sku'] = $product_sku;
    }
    
    // MPN（製造者商品番号）
    if (!empty($product_mpn)) {
        $product_data['mpn'] = $product_mpn;
    }
    
    // ブランド情報
    if (!empty($product_brand)) {
        $product_data['brand'] = array(
            '@type' => 'Brand',
            'name' => $product_brand
        );
    }
    
    // 対象者情報
    if (!empty($product_audience_gender) || !empty($product_audience_min_age) || !empty($product_audience_max_age)) {
        $audience = array(
            '@type' => 'PeopleAudience'
        );
        
        if (!empty($product_audience_gender)) {
            $audience['suggestedGender'] = $product_audience_gender;
        }
        
        if (!empty($product_audience_min_age)) {
            $audience['suggestedMinAge'] = $product_audience_min_age;
        }
        
        if (!empty($product_audience_max_age)) {
            $audience['suggestedMaxAge'] = $product_audience_max_age;
        }
        
        $product_data['audience'] = $audience;
    }
    
    // 商品属性
    if (!empty($product_material)) {
        $product_data['material'] = $product_material;
    }
    
    if (!empty($product_color)) {
        $product_data['color'] = $product_color;
    }
    
    if (!empty($product_pattern)) {
        $product_data['pattern'] = $product_pattern;
    }
    
    if (!empty($product_size)) {
        $product_data['size'] = $product_size;
    }
    
    // 価格・販売情報
    if (!empty($product_price) && !empty($product_currency)) {
        $offer = array(
            '@type' => 'Offer',
            'priceCurrency' => $product_currency,
            'price' => $product_price
        );
        
        // 商品購入URL
        if (!empty($product_url)) {
            $offer['url'] = $product_url;
        } else {
            $offer['url'] = get_permalink($post_id);
        }
        
        // 商品状態
        if (!empty($product_condition)) {
            $offer['itemCondition'] = $product_condition;
        }
        
        // 在庫状況
        if (!empty($product_availability)) {
            $offer['availability'] = $product_availability;
        }
        
        $product_data['offers'] = $offer;
    }
    
    // フィルターフックでカスタマイズ可能
    $product_data = apply_filters('gof_product_structured_data', $product_data);
    
    return $product_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_product', 'gof_get_product_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

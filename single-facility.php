<?php
/**
 * 設備詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * 設備専用の構造化データを追加します
 */

// 設備構造化データ関数を先に定義
function gof_get_facility_structured_data($data, $post_id) {
    // カスタムフィールドから基本情報を取得
    $facility_name = get_post_meta($post_id, 'facility_name', true);
    $facility_description = get_post_meta($post_id, 'facility_description', true);
    $facility_model = get_post_meta($post_id, 'facility_model', true);
    $facility_mpn = get_post_meta($post_id, 'facility_mpn', true);
    
    // ブランド・製造者情報
    $facility_brand = get_post_meta($post_id, 'facility_brand', true);
    $manufacturer_name = get_post_meta($post_id, 'manufacturer_name', true);
    $manufacturer_url = get_post_meta($post_id, 'manufacturer_url', true);
    
    // 価格・在庫情報
    $facility_availability = get_post_meta($post_id, 'facility_availability', true);
    $facility_price_description = get_post_meta($post_id, 'facility_price_description', true);
    $facility_price = get_post_meta($post_id, 'facility_price', true);
    $facility_currency = get_post_meta($post_id, 'facility_currency', true);
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($facility_description)) {
        $description = $facility_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // Product型構造化データを構築
    $facility_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => !empty($facility_name) ? $facility_name : get_the_title($post_id),
        'description' => $description,
        'url' => get_permalink($post_id)
    );
    
    // モデル番号
    if (!empty($facility_model)) {
        $facility_data['model'] = $facility_model;
    }
    
    // MPN（製造者商品番号）
    if (!empty($facility_mpn)) {
        $facility_data['mpn'] = $facility_mpn;
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
    $facility_images = get_post_meta($post_id, 'facility_images', true);
    if ($facility_images && is_array($facility_images)) {
        foreach ($facility_images as $facility_image) {
            if (!empty($facility_image['image'])) {
                $image_url = wp_get_attachment_image_src($facility_image['image'], 'large');
                if ($image_url) {
                    $images[] = $image_url[0];
                }
            }
        }
    }
    
    if (!empty($images)) {
        $facility_data['image'] = $images;
    }
    
    // ブランド情報
    if (!empty($facility_brand)) {
        $facility_data['brand'] = array(
            '@type' => 'Brand',
            'name' => $facility_brand
        );
    }
    
    // 製造者情報
    if (!empty($manufacturer_name)) {
        $manufacturer_data = array(
            '@type' => 'Organization',
            'name' => $manufacturer_name
        );
        
        if (!empty($manufacturer_url)) {
            $manufacturer_data['url'] = $manufacturer_url;
        }
        
        $facility_data['manufacturer'] = $manufacturer_data;
    }
    
    // 設備仕様（追加プロパティ）
    $specifications = get_post_meta($post_id, 'facility_specifications', true);
    $additional_properties = array();
    if ($specifications && is_array($specifications)) {
        foreach ($specifications as $spec) {
            if (!empty($spec['spec_name']) && !empty($spec['spec_value'])) {
                $property = array(
                    '@type' => 'PropertyValue',
                    'name' => $spec['spec_name'],
                    'value' => $spec['spec_value']
                );
                
                // 単位コードがある場合は追加
                if (!empty($spec['spec_unit_code'])) {
                    $property['unitCode'] = $spec['spec_unit_code'];
                }
                
                $additional_properties[] = $property;
            }
        }
    }
    
    if (!empty($additional_properties)) {
        $facility_data['additionalProperty'] = $additional_properties;
    }
    
    // オファー情報
    $offer_data = array(
        '@type' => 'Offer'
    );
    
    // 在庫状況
    if (!empty($facility_availability)) {
        $offer_data['availability'] = $facility_availability;
    } else {
        $offer_data['availability'] = 'https://schema.org/InStock';
    }
    
    // 価格情報
    if (!empty($facility_price) && is_numeric($facility_price)) {
        $offer_data['price'] = $facility_price;
        
        if (!empty($facility_currency)) {
            $offer_data['priceCurrency'] = $facility_currency;
        } else {
            $offer_data['priceCurrency'] = 'JPY';
        }
    } else {
        // 価格が設定されていない場合は価格仕様で説明
        $price_spec = array();
        
        if (!empty($facility_price_description)) {
            $price_spec['description'] = $facility_price_description;
        } else {
            $price_spec['description'] = '価格はお問い合わせください（要見積もり）。';
        }
        
        $offer_data['priceSpecification'] = $price_spec;
    }
    
    $facility_data['offers'] = $offer_data;
    
    // フィルターフックでカスタマイズ可能
    $facility_data = apply_filters('gof_facility_structured_data', $facility_data);
    
    return $facility_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_facility', 'gof_get_facility_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

<?php
/**
 * サービス詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * サービス専用の構造化データを追加します
 */

// サービス構造化データ関数を先に定義
function gof_get_service_structured_data($data, $post_id) {
    // デバッグ出力
    // if (defined('WP_DEBUG') && WP_DEBUG) {
    //     error_log('gof_get_service_structured_data called for post ID: ' . $post_id);
    // }
    
    // カスタムフィールドから情報を取得
    $service_type = get_post_meta($post_id, 'service_type', true);
    $service_description = get_post_meta($post_id, 'service_description', true);
    $service_area_served = get_post_meta($post_id, 'service_area_served', true);
    $service_price = get_post_meta($post_id, 'service_price', true);
    $service_currency = get_post_meta($post_id, 'service_currency', true);
    
    // 組織情報を取得
    $org_data = get_option('gof_organization_data', array());
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($service_description)) {
        $description = $service_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // Service型構造化データを構築
    $service_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink($post_id)
        ),
        'name' => get_the_title($post_id),
        'description' => $description
    );
    
    // サービスタイプ
    if (!empty($service_type)) {
        $service_data['serviceType'] = $service_type;
    }
    
    // サービス画像（アイキャッチ画像を使用）
    if (has_post_thumbnail($post_id)) {
        $service_data['image'] = get_the_post_thumbnail_url($post_id, 'large');
    }
    
    // プロバイダー（組織情報から取得）
    if (!empty($org_data['name'])) {
        $provider = array(
            '@type' => 'Organization',
            'name' => $org_data['name']
        );
        
        if (!empty($org_data['url'])) {
            $provider['url'] = $org_data['url'];
        }
        
        $service_data['provider'] = $provider;
    }
    
    // サービス提供地域
    if (!empty($service_area_served)) {
        if ($service_area_served === 'Online') {
            $service_data['areaServed'] = array(
                '@type' => 'Place',
                'name' => 'オンライン（地域不問）'
            );
        } else {
            $area_names = array(
                'Japan' => '日本',
                'Tokyo' => '東京都',
                'Osaka' => '大阪府',
                'Kyoto' => '京都府',
                'Kanagawa' => '神奈川県'
            );
            
            $area_name = isset($area_names[$service_area_served]) ? $area_names[$service_area_served] : $service_area_served;
            
            if ($service_area_served === 'Japan') {
                $service_data['areaServed'] = array(
                    '@type' => 'Country',
                    'name' => $area_name
                );
            } else {
                $service_data['areaServed'] = array(
                    '@type' => 'State',
                    'name' => $area_name
                );
            }
        }
    }
    
    // 料金情報
    if (!empty($service_price) && !empty($service_currency)) {
        $service_data['offers'] = array(
            '@type' => 'Offer',
            'priceCurrency' => $service_currency,
            'price' => $service_price
        );
    }
    
    // フィルターフックでカスタマイズ可能
    $service_data = apply_filters('gof_service_structured_data', $service_data);
    
    return $service_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_service', 'gof_get_service_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');


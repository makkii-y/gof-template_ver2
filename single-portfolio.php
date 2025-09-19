<?php
/**
 * 実績詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * 実績専用の構造化データを追加します
 */

// 実績構造化データ関数を先に定義
function gof_get_portfolio_structured_data($data, $post_id) {
    // カスタムフィールドから情報を取得
    $portfolio_description = get_post_meta($post_id, 'portfolio_description', true);
    $client_company_name = get_post_meta($post_id, 'client_company_name', true);
    $client_website_name = get_post_meta($post_id, 'client_website_name', true);
    $client_website_url = get_post_meta($post_id, 'client_website_url', true);
    $client_website_image = get_post_meta($post_id, 'client_website_image', true);
    $project_price = get_post_meta($post_id, 'project_price', true);
    $project_currency = get_post_meta($post_id, 'project_currency', true);
    $review_rating = get_post_meta($post_id, 'review_rating', true);
    $review_author_name = get_post_meta($post_id, 'review_author_name', true);
    $review_author_job_title = get_post_meta($post_id, 'review_author_job_title', true);
    $review_body = get_post_meta($post_id, 'review_body', true);
    
    // 組織情報を取得（Publisher用）
    $org_data = get_option('gof_organization_data', array());
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($portfolio_description)) {
        $description = $portfolio_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // TechArticle型構造化データを構築
    $portfolio_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'TechArticle',
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink($post_id)
        ),
        'headline' => get_the_title($post_id),
        'datePublished' => get_the_date('c', $post_id),
        'description' => $description
    );
    
    // 組織情報（Author & Publisher）
    $org_name = !empty($org_data['name']) ? $org_data['name'] : get_bloginfo('name');
    $org_url = !empty($org_data['url']) ? $org_data['url'] : home_url('/');
    $org_logo = !empty($org_data['logo']) ? $org_data['logo'] : get_site_icon_url();
    
    $portfolio_data['author'] = array(
        '@type' => 'Organization',
        'name' => $org_name,
        'url' => $org_url
    );
    
    $publisher = array(
        '@type' => 'Organization',
        'name' => $org_name
    );
    
    if (!empty($org_logo)) {
        $publisher['logo'] = array(
            '@type' => 'ImageObject',
            'url' => $org_logo
        );
    }
    
    $portfolio_data['publisher'] = $publisher;
    
    // 実績画像（複数対応）
    $images = array();
    
    // アイキャッチ画像を最初に追加
    if (has_post_thumbnail($post_id)) {
        $images[] = get_the_post_thumbnail_url($post_id, 'large');
    }
    
    // 繰り返しフィールドから画像を取得
    $portfolio_images = get_post_meta($post_id, 'portfolio_images', true);
    if (!empty($portfolio_images) && is_array($portfolio_images)) {
        foreach ($portfolio_images as $image_data) {
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
        $portfolio_data['image'] = $images;
    }
    
    // about（クライアント情報とWebサイト情報）
    $about = array();
    
    // クライアント組織情報
    if (!empty($client_company_name)) {
        $client_org = array(
            '@type' => 'Organization',
            'name' => $client_company_name
        );
        
        if (!empty($client_website_url)) {
            $client_org['url'] = $client_website_url;
        }
        
        $about[] = $client_org;
    }
    
    // Webサイト情報
    if (!empty($client_website_name) && !empty($client_website_url)) {
        $website_info = array(
            '@type' => 'WebSite',
            'name' => $client_website_name,
            'url' => $client_website_url,
            'producer' => array(
                '@type' => 'Organization',
                'name' => $org_name
            )
        );
        
        // クライアントサイト画像
        if (!empty($client_website_image)) {
            $website_info['image'] = $client_website_image;
        }
        
        // プロジェクト料金
        if (!empty($project_price) && !empty($project_currency)) {
            $price_string = $project_currency === 'JPY' ? '¥' . number_format($project_price) : 
                           ($project_currency === 'USD' ? '$' . number_format($project_price) : 
                           $project_currency . number_format($project_price));
            $website_info['Price'] = $price_string;
        }
        
        $about[] = $website_info;
    }
    
    if (!empty($about)) {
        $portfolio_data['about'] = $about;
    }
    
    // レビュー情報
    if (!empty($review_rating) && !empty($review_author_name) && !empty($review_body)) {
        $review = array(
            '@type' => 'Review',
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => $review_rating,
                'bestRating' => '5'
            ),
            'author' => array(
                '@type' => 'Person',
                'name' => $review_author_name
            ),
            'reviewBody' => $review_body
        );
        
        // レビュー者の役職
        if (!empty($review_author_job_title)) {
            $review['author']['jobTitle'] = $review_author_job_title;
        }
        
        // レビュー者の所属組織
        if (!empty($client_company_name)) {
            $review['author']['worksFor'] = array(
                '@type' => 'Organization',
                'name' => $client_company_name
            );
        }
        
        $portfolio_data['review'] = $review;
    }
    
    // フィルターフックでカスタマイズ可能
    $portfolio_data = apply_filters('gof_portfolio_structured_data', $portfolio_data);
    
    return $portfolio_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_portfolio', 'gof_get_portfolio_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

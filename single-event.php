<?php
/**
 * イベント詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * イベント専用の構造化データを追加します
 */

// イベント構造化データ関数を先に定義
function gof_get_event_structured_data($data, $post_id) {
    // カスタムフィールドから情報を取得
    $event_name = get_post_meta($post_id, 'event_name', true);
    $event_description = get_post_meta($post_id, 'event_description', true);
    $event_start_date = get_post_meta($post_id, 'event_start_date', true);
    $event_end_date = get_post_meta($post_id, 'event_end_date', true);
    $event_status = get_post_meta($post_id, 'event_status', true);
    $event_attendance_mode = get_post_meta($post_id, 'event_attendance_mode', true);
    
    // 会場情報
    $venue_name = get_post_meta($post_id, 'venue_name', true);
    $venue_street = get_post_meta($post_id, 'venue_street', true);
    $venue_locality = get_post_meta($post_id, 'venue_locality', true);
    $venue_region = get_post_meta($post_id, 'venue_region', true);
    $venue_postal_code = get_post_meta($post_id, 'venue_postal_code', true);
    $virtual_location_url = get_post_meta($post_id, 'virtual_location_url', true);
    
    // チケット情報
    $ticket_name = get_post_meta($post_id, 'ticket_name', true);
    $ticket_price = get_post_meta($post_id, 'ticket_price', true);
    $ticket_currency = get_post_meta($post_id, 'ticket_currency', true);
    $ticket_availability = get_post_meta($post_id, 'ticket_availability', true);
    $ticket_url = get_post_meta($post_id, 'ticket_url', true);
    $ticket_valid_from = get_post_meta($post_id, 'ticket_valid_from', true);
    
    // 出演者・主催者情報
    $performers = get_post_meta($post_id, 'performers', true);
    $organizer_name = get_post_meta($post_id, 'organizer_name', true);
    $organizer_url = get_post_meta($post_id, 'organizer_url', true);
    
    // 組織情報を取得（デフォルト値用）
    $org_data = get_option('gof_organization_data', array());
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($event_description)) {
        $description = $event_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // Event型構造化データを構築
    $event_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => !empty($event_name) ? $event_name : get_the_title($post_id),
        'description' => $description
    );
    
    // 開始・終了日時
    if (!empty($event_start_date)) {
        $event_data['startDate'] = $event_start_date;
    }
    
    if (!empty($event_end_date)) {
        $event_data['endDate'] = $event_end_date;
    }
    
    // イベント画像（複数対応）
    $images = array();
    
    // アイキャッチ画像を最初に追加
    if (has_post_thumbnail($post_id)) {
        $images[] = get_the_post_thumbnail_url($post_id, 'large');
    }
    
    // 繰り返しフィールドから画像を取得
    $event_images = get_post_meta($post_id, 'event_images', true);
    if (!empty($event_images) && is_array($event_images)) {
        foreach ($event_images as $image_data) {
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
        $event_data['image'] = $images;
    }
    
    // イベント状態
    if (!empty($event_status)) {
        $event_data['eventStatus'] = $event_status;
    }
    
    // 参加方法
    if (!empty($event_attendance_mode)) {
        $event_data['eventAttendanceMode'] = $event_attendance_mode;
    }
    
    // 開催場所情報
    $locations = array();
    
    // 物理的な会場
    if (!empty($venue_name) || !empty($venue_street) || !empty($venue_locality)) {
        $physical_location = array(
            '@type' => 'Place'
        );
        
        if (!empty($venue_name)) {
            $physical_location['name'] = $venue_name;
        }
        
        if (!empty($venue_street) || !empty($venue_locality) || !empty($venue_region)) {
            $address = array(
                '@type' => 'PostalAddress',
                'addressCountry' => 'JP'
            );
            
            if (!empty($venue_street)) {
                $address['streetAddress'] = $venue_street;
            }
            
            if (!empty($venue_locality)) {
                $address['addressLocality'] = $venue_locality;
            }
            
            if (!empty($venue_region)) {
                $address['addressRegion'] = $venue_region;
            }
            
            if (!empty($venue_postal_code)) {
                $address['postalCode'] = $venue_postal_code;
            }
            
            $physical_location['address'] = $address;
        }
        
        $locations[] = $physical_location;
    }
    
    // バーチャル会場
    if (!empty($virtual_location_url)) {
        $virtual_location = array(
            '@type' => 'VirtualLocation',
            'url' => $virtual_location_url
        );
        
        $locations[] = $virtual_location;
    }
    
    if (!empty($locations)) {
        if (count($locations) === 1) {
            $event_data['location'] = $locations[0];
        } else {
            $event_data['location'] = $locations;
        }
    }
    
    // チケット・料金情報
    if (!empty($ticket_price) || !empty($ticket_name)) {
        $offer = array(
            '@type' => 'Offer'
        );
        
        if (!empty($ticket_name)) {
            $offer['name'] = $ticket_name;
        }
        
        if (!empty($ticket_price)) {
            $offer['price'] = $ticket_price;
        }
        
        if (!empty($ticket_currency)) {
            $offer['priceCurrency'] = $ticket_currency;
        }
        
        if (!empty($ticket_availability)) {
            $offer['availability'] = $ticket_availability;
        }
        
        if (!empty($ticket_url)) {
            $offer['url'] = $ticket_url;
        }
        
        if (!empty($ticket_valid_from)) {
            $offer['validFrom'] = $ticket_valid_from;
        }
        
        $event_data['offers'] = $offer;
    }
    
    // 出演者情報
    if (!empty($performers) && is_array($performers)) {
        $performer_list = array();
        
        foreach ($performers as $performer) {
            if (!empty($performer['name'])) {
                $performer_data = array(
                    '@type' => 'Person',
                    'name' => $performer['name']
                );
                
                if (!empty($performer['url'])) {
                    $performer_data['url'] = $performer['url'];
                }
                
                $performer_list[] = $performer_data;
            }
        }
        
        if (!empty($performer_list)) {
            $event_data['performer'] = $performer_list;
        }
    }
    
    // 主催者情報
    $org_name = !empty($organizer_name) ? $organizer_name : (!empty($org_data['name']) ? $org_data['name'] : get_bloginfo('name'));
    $org_url = !empty($organizer_url) ? $organizer_url : (!empty($org_data['url']) ? $org_data['url'] : home_url('/'));
    
    if (!empty($org_name)) {
        $organizer = array(
            '@type' => 'Organization',
            'name' => $org_name
        );
        
        if (!empty($org_url)) {
            $organizer['url'] = $org_url;
        }
        
        $event_data['organizer'] = $organizer;
    }
    
    // フィルターフックでカスタマイズ可能
    $event_data = apply_filters('gof_event_structured_data', $event_data);
    
    return $event_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_event', 'gof_get_event_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

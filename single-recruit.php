<?php
/**
 * 採用詳細ページテンプレート
 * 
 * 汎用のsingle.phpテンプレートを継承し、
 * 採用専用の構造化データを追加します
 */

// 採用構造化データ関数を先に定義
function gof_get_recruit_structured_data($data, $post_id) {
    // カスタムフィールドから情報を取得
    $job_title = get_post_meta($post_id, 'job_title', true);
    $job_description = get_post_meta($post_id, 'job_description', true);
    $job_identifier = get_post_meta($post_id, 'job_identifier', true);
    $employment_type = get_post_meta($post_id, 'employment_type', true);
    $valid_through = get_post_meta($post_id, 'valid_through', true);
    
    // 勤務地情報
    $job_location_street = get_post_meta($post_id, 'job_location_street', true);
    $job_location_locality = get_post_meta($post_id, 'job_location_locality', true);
    $job_location_region = get_post_meta($post_id, 'job_location_region', true);
    $job_location_postal_code = get_post_meta($post_id, 'job_location_postal_code', true);
    
    // 給与情報
    $salary_min = get_post_meta($post_id, 'salary_min', true);
    $salary_max = get_post_meta($post_id, 'salary_max', true);
    $salary_currency = get_post_meta($post_id, 'salary_currency', true);
    
    // 業務・スキル情報
    $responsibilities = get_post_meta($post_id, 'responsibilities', true);
    $required_skills = get_post_meta($post_id, 'required_skills', true);
    $experience_months = get_post_meta($post_id, 'experience_months', true);
    
    // 会社情報
    $company_name = get_post_meta($post_id, 'company_name', true);
    $company_url = get_post_meta($post_id, 'company_url', true);
    $company_logo = get_post_meta($post_id, 'company_logo', true);
    
    // 組織情報を取得（デフォルト値用）
    $org_data = get_option('gof_organization_data', array());
    
    // 抜粋または本文の要約を取得
    $description = get_the_excerpt();
    if (empty($description) && !empty($job_description)) {
        $description = $job_description;
    } elseif (empty($description)) {
        $description = wp_trim_words(get_the_content(), 50, '...');
    }
    
    // JobPosting型構造化データを構築
    $recruit_data = array(
        '@context' => 'https://schema.org/',
        '@type' => 'JobPosting',
        'title' => !empty($job_title) ? $job_title : get_the_title($post_id),
        'description' => $description,
        'datePosted' => get_the_date('c', $post_id)
    );
    
    // 識別子
    if (!empty($job_identifier) && !empty($company_name)) {
        $recruit_data['identifier'] = array(
            '@type' => 'PropertyValue',
            'name' => $company_name,
            'value' => $job_identifier
        );
    }
    
    // 有効期限
    if (!empty($valid_through)) {
        $recruit_data['validThrough'] = $valid_through;
    }
    
    // 雇用形態
    if (!empty($employment_type)) {
        $recruit_data['employmentType'] = $employment_type;
    }
    
    // 採用企業情報
    $org_name = !empty($company_name) ? $company_name : (!empty($org_data['name']) ? $org_data['name'] : get_bloginfo('name'));
    $org_url = !empty($company_url) ? $company_url : (!empty($org_data['url']) ? $org_data['url'] : home_url('/'));
    $org_logo = !empty($company_logo) ? $company_logo : (!empty($org_data['logo']) ? $org_data['logo'] : get_site_icon_url());
    
    $hiring_org = array(
        '@type' => 'Organization',
        'name' => $org_name
    );
    
    if (!empty($org_url)) {
        $hiring_org['sameAs'] = $org_url;
    }
    
    if (!empty($org_logo)) {
        $hiring_org['logo'] = $org_logo;
    }
    
    $recruit_data['hiringOrganization'] = $hiring_org;
    
    // 勤務地情報
    if (!empty($job_location_street) || !empty($job_location_locality) || !empty($job_location_region)) {
        $address = array(
            '@type' => 'PostalAddress',
            'addressCountry' => 'JP'
        );
        
        if (!empty($job_location_street)) {
            $address['streetAddress'] = $job_location_street;
        }
        
        if (!empty($job_location_locality)) {
            $address['addressLocality'] = $job_location_locality;
        }
        
        if (!empty($job_location_region)) {
            $address['addressRegion'] = $job_location_region;
        }
        
        if (!empty($job_location_postal_code)) {
            $address['postalCode'] = $job_location_postal_code;
        }
        
        $recruit_data['jobLocation'] = array(
            '@type' => 'Place',
            'address' => $address
        );
    }
    
    // 給与情報
    if (!empty($salary_min) || !empty($salary_max)) {
        $base_salary = array(
            '@type' => 'MonetaryAmount',
            'currency' => !empty($salary_currency) ? $salary_currency : 'JPY'
        );
        
        if (!empty($salary_min) || !empty($salary_max)) {
            $value = array(
                '@type' => 'QuantitativeValue',
                'unitText' => 'YEAR'
            );
            
            if (!empty($salary_min)) {
                $value['minValue'] = (int)$salary_min;
            }
            
            if (!empty($salary_max)) {
                $value['maxValue'] = (int)$salary_max;
            }
            
            // 単一の値の場合
            if (!empty($salary_min) && empty($salary_max)) {
                $value['value'] = (int)$salary_min;
            } elseif (empty($salary_min) && !empty($salary_max)) {
                $value['value'] = (int)$salary_max;
            }
            
            $base_salary['value'] = $value;
        }
        
        $recruit_data['baseSalary'] = $base_salary;
    }
    
    // 業務内容
    if (!empty($responsibilities)) {
        $responsibilities_array = array_map('trim', explode(',', $responsibilities));
        $recruit_data['responsibilities'] = $responsibilities_array;
    }
    
    // 必要スキル
    if (!empty($required_skills)) {
        $recruit_data['skills'] = $required_skills;
    }
    
    // 経験要件
    if (!empty($experience_months) || $experience_months === '0') {
        $recruit_data['experienceRequirements'] = array(
            '@type' => 'OccupationalExperienceRequirements',
            'monthsOfExperience' => $experience_months
        );
    }
    
    // フィルターフックでカスタマイズ可能
    $recruit_data = apply_filters('gof_recruit_structured_data', $recruit_data);
    
    return $recruit_data;
}

// フィルターを登録
add_filter('gof_custom_structured_data_recruit', 'gof_get_recruit_structured_data', 10, 2);

// 汎用のsingle.phpテンプレートを読み込み
get_template_part('single');

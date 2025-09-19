<?php
/**
 * 構造化データ（JSON-LD）機能
 * 組織情報とローカルビジネス情報の構造化データを出力
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 構造化データマネージャー
 */
class GOF_Structured_Data {
    
    private static $instance = null;
    
    /**
     * シングルトンインスタンス取得
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * コンストラクタ
     */
    private function __construct() {
        add_action('wp_head', array($this, 'output_structured_data'));
        add_action('wp_head', array($this, 'output_breadcrumb_structured_data'));
    }
    
    /**
     * 構造化データを出力
     * 1ページにつき1つの構造化データのみ出力する
     * 
     * 優先順位:
     * 1. カスタム構造化データ (テンプレート別): 最優先（ホームページを除く単体ページのみ）
     * 2. Article型構造化データ (single.php): 上記がない場合のみ（ホームページを除く単体ページのみ）
     * 3. FAQPage型構造化データ (archive-faq.php): FAQ一覧ページ専用
     * 4. ローカルビジネス情報 (structured-data.php): 実店舗・地域ビジネス向け（全ページ対象）
     * 5. 組織情報 (structured-data.php): 一般企業向け（全ページ対象）
     * 
     * パンくずリストの構造化データは別途出力されます
     * ホームページ（固定ページ設定を含む）では優先順位4、5のみが適用されます
     */
    public function output_structured_data() {
        $structured_data = null;
        $data_type = '';
        
        // 優先順位1: カスタム構造化データ（テンプレート別）
        // ホームページ（フロントページ）では除外
        if (is_singular() && !is_front_page()) {
            $post_type = get_post_type();
            $custom_data = apply_filters('gof_custom_structured_data_' . $post_type, null, get_the_ID());
            if (!empty($custom_data)) {
                $structured_data = $custom_data;
                $data_type = 'custom_' . $post_type;
                // // デバッグ: カスタム構造化データが見つかったことを示すコメント
                // echo "\n<!-- Custom structured data for {$post_type} found -->\n";
            }
        }
        
        // 優先順位2: Article型構造化データ（シングルページのみ、ホームページを除く）
        if (empty($structured_data) && is_singular() && !is_front_page()) {
            $article_data = $this->get_article_data();
            if (!empty($article_data)) {
                $structured_data = $article_data;
                $data_type = 'article';
            }
        }
        
        // 優先順位3: FAQPage型構造化データ（FAQ一覧ページ専用）
        if (empty($structured_data) && is_post_type_archive('faq')) {
            $faq_data = $this->get_faq_page_data();
            if (!empty($faq_data)) {
                $structured_data = $faq_data;
                $data_type = 'faq_page';
            }
        }
        
        // 優先順位4: ローカルビジネス情報を優先（実店舗・地域ビジネス向け）
        if (empty($structured_data)) {
            $local_business_data = $this->get_local_business_data();
            if (!empty($local_business_data)) {
                $structured_data = $local_business_data;
                $data_type = 'local_business';
            }
        }
        
        // 優先順位5: 通常の組織情報（一般企業向け）
        if (empty($structured_data)) {
            $organization_data = $this->get_organization_data();
            if (!empty($organization_data)) {
                $structured_data = $organization_data;
                $data_type = 'organization';
            }
        }
        
        // テンプレートからのカスタマイズを適用
        if (!empty($structured_data)) {
            // 汎用フィルター: すべての構造化データに適用
            $structured_data = apply_filters('gof_structured_data', $structured_data, $data_type);
            
            // タイプ別フィルター: 特定のタイプにのみ適用
            $structured_data = apply_filters("gof_structured_data_{$data_type}", $structured_data);
            
            // 投稿タイプ別フィルター: 特定の投稿タイプにのみ適用
            $post_type = get_post_type();
            if ($post_type) {
                $structured_data = apply_filters("gof_structured_data_{$post_type}", $structured_data, $data_type);
            }
            
            $this->output_json_ld($structured_data);
        }
    }

    /**
     * Article型構造化データを取得
     */
    private function get_article_data() {
        // 投稿ページでない場合、またはホームページの場合は何も返さない
        if (!is_singular() || is_front_page()) {
            return array();
        }
        
        // 組織情報を取得
        $org_data = get_option('gof_organization_data', array());
        
        // デフォルト値の設定
        $publisher_name = !empty($org_data['name']) ? $org_data['name'] : get_bloginfo('name');
        $publisher_logo = !empty($org_data['logo']) ? $org_data['logo'] : get_site_icon_url();
        $author_name = get_the_author_meta('display_name');
        $author_url = get_author_posts_url(get_the_author_meta('ID'));
        
        // 抜粋または本文の要約を取得
        $description = get_the_excerpt();
        if (empty($description)) {
            $description = wp_trim_words(get_the_content(), 30, '...');
        }
        
        // Article型構造化データを構築
        $article_data = array(
            "@context" => "https://schema.org",
            "@type" => "Article",
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id" => get_permalink()
            ),
            "headline" => get_the_title(),
            "description" => $description,
            "author" => array(
                "@type" => "Person",
                "name" => $author_name
            ),
            "publisher" => array(
                "@type" => "Organization",
                "name" => $publisher_name
            ),
            "datePublished" => get_the_date('c'),
            "dateModified" => get_the_modified_date('c')
        );
        
        // 画像情報を追加
        if (has_post_thumbnail()) {
            $article_data["image"] = array(get_the_post_thumbnail_url(get_the_ID(), 'large'));
        }
        
        // 著者URL追加
        if (!empty($author_url)) {
            $article_data["author"]["url"] = $author_url;
        }
        
        // Publisher情報を詳細化
        if (!empty($publisher_logo)) {
            $article_data["publisher"]["logo"] = array(
                "@type" => "ImageObject",
                "url" => $publisher_logo,
                "width" => !empty($org_data['logo']) ? 600 : 512,
                "height" => !empty($org_data['logo']) ? 60 : 512
            );
        }
        
        if (!empty($org_data['url'])) {
            $article_data["publisher"]["url"] = $org_data['url'];
        }
        
        // フィルターフックでカスタマイズ可能にする
        $article_data = apply_filters('gof_article_structured_data', $article_data);
        
        return $article_data;
    }

    /**
     * FAQPage型構造化データを取得
     */
    private function get_faq_page_data() {
        // FAQ投稿を取得
        $faq_query = new WP_Query(array(
            'post_type' => 'faq',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        
        if (!$faq_query->have_posts()) {
            return array();
        }
        
        $faq_items = array();
        
        while ($faq_query->have_posts()) {
            $faq_query->the_post();
            $post_id = get_the_ID();
            
            // 質問の取得（タイトルを質問として使用）
            $question_text = get_the_title($post_id);
            
            // 回答の取得（要約優先、フォールバックは本文）
            $answer_text = get_post_meta($post_id, 'faq_answer_summary', true);
            if (empty($answer_text)) {
                $answer_text = wp_trim_words(get_the_content(), 50, '...');
            }
            
            if (!empty($question_text) && !empty($answer_text)) {
                $faq_items[] = array(
                    '@type' => 'Question',
                    'name' => $question_text,
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => $answer_text
                    )
                );
            }
        }
        
        wp_reset_postdata();
        
        if (empty($faq_items)) {
            return array();
        }
        
        // FAQPage構造化データを構築
        $faq_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
        
        // フィルターフックでカスタマイズ可能にする
        $faq_data = apply_filters('gof_faq_page_structured_data', $faq_data);
        
        return $faq_data;
    }

    /**
     * 組織情報の構造化データを取得
     */
    private function get_organization_data() {
        $org_data = get_option('gof_organization_data', array());
        
        if (empty($org_data) || empty($org_data['name'])) {
            return array();
        }
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
        );
        
        // 基本情報
        if (!empty($org_data['name'])) {
            $structured_data['name'] = $org_data['name'];
        }
        
        if (!empty($org_data['url'])) {
            $structured_data['url'] = $org_data['url'];
        }
        
        if (!empty($org_data['logo'])) {
            $structured_data['logo'] = $org_data['logo'];
        }
        
        if (!empty($org_data['description'])) {
            $structured_data['description'] = $org_data['description'];
        }
        
        if (!empty($org_data['email'])) {
            $structured_data['email'] = $org_data['email'];
        }
        
        if (!empty($org_data['telephone'])) {
            $structured_data['telephone'] = $org_data['telephone'];
        }
        
        // 住所情報
        if (!empty($org_data['address'])) {
            $address = array(
                '@type' => 'PostalAddress'
            );
            
            if (!empty($org_data['street_address'])) {
                $address['streetAddress'] = $org_data['street_address'];
            }
            
            if (!empty($org_data['address_locality'])) {
                $address['addressLocality'] = $org_data['address_locality'];
            }
            
            if (!empty($org_data['address_region'])) {
                $address['addressRegion'] = $org_data['address_region'];
            }
            
            if (!empty($org_data['address_country'])) {
                $address['addressCountry'] = $org_data['address_country'];
            }
            
            if (!empty($org_data['postal_code'])) {
                $address['postalCode'] = $org_data['postal_code'];
            }
            
            if (count($address) > 1) { // @type以外にデータがある場合
                $structured_data['address'] = $address;
            }
        }
        
        // 創業者情報
        if (!empty($org_data['founder_name'])) {
            $structured_data['founder'] = array(
                '@type' => 'Person',
                'name' => $org_data['founder_name']
            );
        }
        
        if (!empty($org_data['founding_date'])) {
            $structured_data['foundingDate'] = $org_data['founding_date'];
        }
        
        // 従業員数
        if (!empty($org_data['employees_min']) || !empty($org_data['employees_max'])) {
            $employees = array('@type' => 'QuantitativeValue');
            
            if (!empty($org_data['employees_min'])) {
                $employees['minValue'] = $org_data['employees_min'];
            }
            
            if (!empty($org_data['employees_max'])) {
                $employees['maxValue'] = $org_data['employees_max'];
            }
            
            $structured_data['numberOfEmployees'] = $employees;
        }
        
        // ソーシャルメディア
        $social_urls = array();
        if (!empty($org_data['facebook_url'])) {
            $social_urls[] = $org_data['facebook_url'];
        }
        if (!empty($org_data['twitter_url'])) {
            $social_urls[] = $org_data['twitter_url'];
        }
        if (!empty($org_data['linkedin_url'])) {
            $social_urls[] = $org_data['linkedin_url'];
        }
        if (!empty($org_data['instagram_url'])) {
            $social_urls[] = $org_data['instagram_url'];
        }
        if (!empty($org_data['youtube_url'])) {
            $social_urls[] = $org_data['youtube_url'];
        }
        if (!empty($org_data['tiktok_url'])) {
            $social_urls[] = $org_data['tiktok_url'];
        }
        if (!empty($org_data['pinterest_url'])) {
            $social_urls[] = $org_data['pinterest_url'];
        }
        if (!empty($org_data['github_url'])) {
            $social_urls[] = $org_data['github_url'];
        }
        if (!empty($org_data['note_url'])) {
            $social_urls[] = $org_data['note_url'];
        }
        if (!empty($org_data['line_url'])) {
            $social_urls[] = $org_data['line_url'];
        }
        
        if (!empty($social_urls)) {
            $structured_data['sameAs'] = $social_urls;
        }
        
        return $structured_data;
    }
    
    /**
     * ローカルビジネス情報の構造化データを取得
     */
    private function get_local_business_data() {
        $local_data = get_option('gof_local_business_data', array());
        
        if (empty($local_data) || empty($local_data['name']) || empty($local_data['business_type'])) {
            return array();
        }
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => $local_data['business_type'],
        );
        
        // 基本情報
        if (!empty($local_data['name'])) {
            $structured_data['name'] = $local_data['name'];
        }
        
        if (!empty($local_data['url'])) {
            $structured_data['url'] = $local_data['url'];
        }
        
        if (!empty($local_data['telephone'])) {
            $structured_data['telephone'] = $local_data['telephone'];
        }
        
        // 画像
        if (!empty($local_data['images'])) {
            $images = explode("\n", $local_data['images']);
            $images = array_map('trim', $images);
            $images = array_filter($images);
            
            if (!empty($images)) {
                $structured_data['image'] = $images;
            }
        }
        
        // 住所情報
        if (!empty($local_data['street_address'])) {
            $address = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $local_data['street_address']
            );
            
            if (!empty($local_data['address_locality'])) {
                $address['addressLocality'] = $local_data['address_locality'];
            }
            
            if (!empty($local_data['address_region'])) {
                $address['addressRegion'] = $local_data['address_region'];
            }
            
            if (!empty($local_data['address_country'])) {
                $address['addressCountry'] = $local_data['address_country'];
            }
            
            if (!empty($local_data['postal_code'])) {
                $address['postalCode'] = $local_data['postal_code'];
            }
            
            $structured_data['address'] = $address;
        }
        
        // 地理座標
        if (!empty($local_data['latitude']) && !empty($local_data['longitude'])) {
            $structured_data['geo'] = array(
                '@type' => 'GeoCoordinates',
                'latitude' => floatval($local_data['latitude']),
                'longitude' => floatval($local_data['longitude'])
            );
        }
        
        // レストラン特有の情報
        if ($local_data['business_type'] === 'Restaurant') {
            if (!empty($local_data['serves_cuisine'])) {
                $structured_data['servesCuisine'] = $local_data['serves_cuisine'];
            }
            
            if (!empty($local_data['price_range'])) {
                $structured_data['priceRange'] = $local_data['price_range'];
            }
            
            if (!empty($local_data['menu_url'])) {
                $structured_data['menu'] = $local_data['menu_url'];
            }
        }
        
        // 営業時間
        $opening_hours = $this->get_opening_hours($local_data);
        if (!empty($opening_hours)) {
            $structured_data['openingHoursSpecification'] = $opening_hours;
        }
        
        return $structured_data;
    }
    
    /**
     * 営業時間情報を取得
     */
    private function get_opening_hours($local_data) {
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $day_names = array(
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        );
        
        $opening_hours = array();
        
        foreach ($days as $day) {
            $opens_key = $day . '_opens';
            $closes_key = $day . '_closes';
            
            if (!empty($local_data[$opens_key]) && !empty($local_data[$closes_key])) {
                $opens = $local_data[$opens_key];
                $closes = $local_data[$closes_key];
                
                $hour_spec = array(
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $day_names[$day]
                );
                
                if ($opens === 'none' || $closes === 'none') {
                    $hour_spec['opens'] = 'none';
                    $hour_spec['closes'] = 'none';
                } else {
                    $hour_spec['opens'] = $opens;
                    $hour_spec['closes'] = $closes;
                }
                
                $opening_hours[] = $hour_spec;
            }
        }
        
        return $opening_hours;
    }
    
    /**
     * JSON-LDを出力
     */
    private function output_json_ld($data) {
        if (empty($data)) {
            return;
        }
        
        echo "\n" . '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo "\n" . '</script>' . "\n";
    }
    
    /**
     * パンくずリストの構造化データを出力
     */
    public function output_breadcrumb_structured_data() {
        // ホームページではパンくずリストを表示しない
        if (is_home() || is_front_page()) {
            return;
        }
        
        // パンくずリストクラスが存在しない場合は読み込む
        if (!class_exists('GOF_Breadcrumb')) {
            require_once get_template_directory() . '/templates/parts/breadcrumb.php';
        }
        
        // パンくずリストインスタンスを作成して構造化データを取得
        $breadcrumb = new GOF_Breadcrumb();
        $breadcrumb_data = $breadcrumb->get_structured_data();
        
        // デバッグコメント
        echo "\n<!-- Breadcrumb structured data check: " . (empty($breadcrumb_data) ? 'empty' : 'found') . " -->\n";
        
        if (!empty($breadcrumb_data)) {
            $this->output_json_ld($breadcrumb_data);
        }
    }
}

// 構造化データマネージャー初期化
GOF_Structured_Data::get_instance();

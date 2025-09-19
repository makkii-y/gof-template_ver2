<?php
/**
 * Functions and definitions
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

// ==============================================
// ファイル読み込み
// ==============================================

/**
 * テーマファイルの読み込み
 */
$includes = array(
    'admin/includes/theme-setup.php',           // 1. テーマの基本設定
    'admin/includes/security.php',              // 2. セキュリティ設定
    'admin/includes/performance.php',           // 3. パフォーマンス最適化
    'admin/includes/cleanup.php',               // 4. wp_head クリーンアップ
    'admin/includes/disable-comments.php',      // 5. コメント機能無効化
    'admin/includes/admin-customization.php',   // 6. 管理画面のカスタマイズ
    'admin/includes/custom-post-types.php',     // 7. カスタム投稿タイプ & タクソノミー
    'admin/includes/custom-fields.php',         // 8. カスタムフィールド
    'admin/includes/custom-fields-helpers.php', // 9. カスタムフィールドヘルパー関数
    'admin/includes/structured-data.php',       // 10. 構造化データ
    'admin/includes/structured-data-admin.php', // 11. 構造化データ管理画面
    'admin/includes/theme-customizer.php',      // 12. テーマカスタマイザー
);

// ファイルの存在確認と読み込み
foreach ($includes as $file) {
    $filepath = get_template_directory() . '/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

// ==============================================
// スタイルシート・スクリプトの読み込み
// ==============================================

/**
 * テーマのスタイルシートとスクリプトをエンキュー
 */
function gof_enqueue_scripts() {
    // カスタムカラーCSS
    wp_enqueue_style(
        'gof-custom-colors',
        get_template_directory_uri() . '/assets/css/custom-colors.css',
        array(),
        '1.0.0'
    );
    
    // jQueryエンキュー
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'gof_enqueue_scripts');

// ==============================================
// カスタム投稿タイプ & タクソノミーの設定
// ==============================================

/**
 * カスタム投稿タイプの設定
 * includes/custom-post-types.php の設定を上書きします
 */
function gof_custom_post_types_config() {
    return array(
        // サービス投稿
        'service' => array(
            'singular_name' => 'サービス投稿',
            'plural_name' => 'サービス投稿',
            'menu_icon' => 'dashicons-admin-tools',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => true,
            'menu_position' => 5,
        ),
        
        // 商品投稿
        'product' => array(
            'singular_name' => '商品投稿',
            'plural_name' => '商品投稿',
            'menu_icon' => 'dashicons-cart',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 6,
        ),
        
        // 実績投稿
        'portfolio' => array(
            'singular_name' => '実績投稿',
            'plural_name' => '実績投稿',
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 7,
        ),
        
        // 採用投稿
        'recruit' => array(
            'singular_name' => '採用投稿',
            'plural_name' => '採用投稿',
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 8,
        ),
        
        // イベント投稿
        'event' => array(
            'singular_name' => 'イベント投稿',
            'plural_name' => 'イベント投稿',
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 9,
        ),
        
        // レシピ投稿
        'recipe' => array(
            'singular_name' => 'レシピ投稿',
            'plural_name' => 'レシピ投稿',
            'menu_icon' => 'dashicons-carrot',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 10,
        ),
        
        // FAQ投稿
        'faq' => array(
            'singular_name' => 'FAQ投稿',
            'plural_name' => 'FAQ投稿',
            'menu_icon' => 'dashicons-editor-help',
            'supports' => array('title', 'editor', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 11,
            'publicly_queryable' => true,  // 一覧ページにアクセスできるように変更
            'disable_single_page' => true, // 詳細ページを無効化
        ),
        
        // 設備投稿
        'facility' => array(
            'singular_name' => '設備投稿',
            'plural_name' => '設備投稿',
            'menu_icon' => 'dashicons-admin-tools',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 12,
        ),
        
        // 新しいカスタム投稿タイプをここに追加してください
        /*
        'example' => array(
            'singular_name' => '例',
            'plural_name' => '例',
            'menu_icon' => 'dashicons-admin-post',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'has_archive' => true,
            'menu_position' => 6,
        ),
        */
    );
}

/**
 * カスタムタクソノミーの設定
 * includes/custom-post-types.php の設定を上書きします
 */
function gof_custom_taxonomies_config() {
    return array(
        // サービスカテゴリー
        'service_category' => array(
            'post_types' => array('service'),
            'singular_name' => 'サービスカテゴリー',
            'plural_name' => 'サービスカテゴリー',
            'hierarchical' => true,
        ),
        
        // サービスタグ
        'service_tag' => array(
            'post_types' => array('service'),
            'singular_name' => 'サービスタグ',
            'plural_name' => 'サービスタグ',
            'hierarchical' => false,
        ),
        
        // 商品カテゴリー
        'product_category' => array(
            'post_types' => array('product'),
            'singular_name' => '商品カテゴリー',
            'plural_name' => '商品カテゴリー',
            'hierarchical' => true,
        ),
        
        // 商品タグ
        'product_tag' => array(
            'post_types' => array('product'),
            'singular_name' => '商品タグ',
            'plural_name' => '商品タグ',
            'hierarchical' => false,
        ),
        
        // 実績カテゴリー
        'portfolio_category' => array(
            'post_types' => array('portfolio'),
            'singular_name' => '実績カテゴリー',
            'plural_name' => '実績カテゴリー',
            'hierarchical' => true,
        ),
        
        // 実績タグ
        'portfolio_tag' => array(
            'post_types' => array('portfolio'),
            'singular_name' => '実績タグ',
            'plural_name' => '実績タグ',
            'hierarchical' => false,
        ),
        
        // 採用カテゴリー
        'recruit_category' => array(
            'post_types' => array('recruit'),
            'singular_name' => '採用カテゴリー',
            'plural_name' => '採用カテゴリー',
            'hierarchical' => true,
        ),
        
        // 採用タグ
        'recruit_tag' => array(
            'post_types' => array('recruit'),
            'singular_name' => '採用タグ',
            'plural_name' => '採用タグ',
            'hierarchical' => false,
        ),
        
        // イベントカテゴリー
        'event_category' => array(
            'post_types' => array('event'),
            'singular_name' => 'イベントカテゴリー',
            'plural_name' => 'イベントカテゴリー',
            'hierarchical' => true,
        ),
        
        // イベントタグ
        'event_tag' => array(
            'post_types' => array('event'),
            'singular_name' => 'イベントタグ',
            'plural_name' => 'イベントタグ',
            'hierarchical' => false,
        ),
        
        // レシピカテゴリー
        'recipe_category' => array(
            'post_types' => array('recipe'),
            'singular_name' => 'レシピカテゴリー',
            'plural_name' => 'レシピカテゴリー',
            'hierarchical' => true,
        ),
        
        // レシピタグ
        'recipe_tag' => array(
            'post_types' => array('recipe'),
            'singular_name' => 'レシピタグ',
            'plural_name' => 'レシピタグ',
            'hierarchical' => false,
        ),
        
        // 設備カテゴリー
        'facility_category' => array(
            'post_types' => array('facility'),
            'singular_name' => '設備カテゴリー',
            'plural_name' => '設備カテゴリー',
            'hierarchical' => true,
        ),
        
        // 設備タグ
        'facility_tag' => array(
            'post_types' => array('facility'),
            'singular_name' => '設備タグ',
            'plural_name' => '設備タグ',
            'hierarchical' => false,
        ),
        
        // 新しいカスタムタクソノミーをここに追加してください
        /*
        'example_category' => array(
            'post_types' => array('example'),
            'singular_name' => '例カテゴリー',
            'plural_name' => '例カテゴリー',
            'hierarchical' => true,
        ),
        */
    );
}

// ==============================================
// カスタムフィールドの設定
// ==============================================

/**
 * カスタムフィールドグループの設定
 * includes/custom-fields.php の設定を上書きします
 */
function gof_custom_fields_config() {
    return array(
        // サービス詳細情報（構造化データ用）
        'service_details' => array(
            'title' => 'サービス詳細情報（構造化データ用）',
            'post_types' => array('service'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // Schema.org Service型に必要な基本項目
                'service_type' => array(
                    'type' => 'text',
                    'label' => 'サービスの概要',
                    'placeholder' => 'ウェブサイト制作および開発',
                    'description' => '構造化データで使用されるサービスの種類',
                ),
                'service_description' => array(
                    'type' => 'textarea',
                    'label' => 'サービスの詳細（120〜140文字推奨）',
                    'placeholder' => 'お客様のビジネス課題を解決するための、戦略的なオーダーメイドのホームページを制作します。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                'service_area_served' => array(
                    'type' => 'select',
                    'label' => 'サービス提供地域',
                    'options' => array(
                        'Japan' => '日本全国',
                        'Tokyo' => '東京都',
                        'Osaka' => '大阪府',
                        'Kyoto' => '京都府',
                        'Kanagawa' => '神奈川県',
                        'Online' => 'オンライン（地域不問）',
                    ),
                    'default' => 'Japan',
                    'description' => '構造化データのareaServedプロパティで使用',
                ),
                
                // 料金情報（Schema.org Offer型）
                'service_price' => array(
                    'type' => 'text',
                    'label' => '基本料金（数値のみ）',
                    'placeholder' => '300000',
                    'description' => '構造化データのpriceプロパティで使用（数値のみ入力）',
                ),
                'service_currency' => array(
                    'type' => 'select',
                    'label' => '通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                    ),
                    'default' => 'JPY',
                    'description' => '構造化データのpriceCurrencyプロパティで使用',
                ),
                'service_price_description' => array(
                    'type' => 'text',
                    'label' => '価格説明（120〜140文字推奨）',
                    'placeholder' => '価格はお問い合わせください（要見積もり）。',
                    'description' => '価格に関する説明文（見積もり制など）',
                ),
            ),
        ),
        
        // 商品詳細情報（構造化データ用）
        'product_details' => array(
            'title' => '商品詳細情報（構造化データ用）',
            'post_types' => array('product'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // 基本商品情報
                'product_description' => array(
                    'type' => 'textarea',
                    'label' => '商品の詳細説明',
                    'placeholder' => '錆びにくく、鋭い切れ味が持続するモリブデンバナジウム鋼を使用した、家庭用三徳包丁です。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                'product_sku' => array(
                    'type' => 'text',
                    'label' => 'SKU（商品管理番号）（自社内で識別するための番号：自由入力OK）',
                    'placeholder' => 'SANT-JP180',
                    'description' => '在庫管理用の商品番号',
                ),
                'product_mpn' => array(
                    'type' => 'text',
                    'label' => 'MPN（製造者商品番号）（自社内で識別するための番号：自由入力OK）',
                    'placeholder' => '987654321',
                    'description' => '製造者が定義した商品番号',
                ),
                'product_images' => array(
                    'type' => 'repeater',
                    'label' => '商品画像',
                    'description' => '商品の画像を複数追加できます。最初の画像がメイン画像として使用されます。',
                    'button_label' => '画像を追加',
                    'sub_fields' => array(
                        'image' => array(
                            'type' => 'image',
                            'label' => '画像',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ),
                    ),
                ),
                
                // ブランド情報
                'product_brand' => array(
                    'type' => 'text',
                    'label' => 'ブランド名',
                    'placeholder' => '燕三条キッチン',
                    'description' => '商品のブランド名',
                ),
                
                // 商品属性
                'product_material' => array(
                    'type' => 'text',
                    'label' => '素材',
                    'placeholder' => 'オーガニックコットン100%',
                    'description' => '商品の素材',
                ),
                'product_color' => array(
                    'type' => 'text',
                    'label' => '色',
                    'placeholder' => '青',
                    'description' => '商品の色',
                ),
                'product_pattern' => array(
                    'type' => 'text',
                    'label' => '柄・パターン',
                    'placeholder' => 'ボーダー',
                    'description' => '商品の柄やパターン',
                ),
                'product_size' => array(
                    'type' => 'text',
                    'label' => 'サイズ',
                    'placeholder' => '110cm',
                    'description' => '商品のサイズ',
                ),
                
                // 対象者情報
                'product_audience_gender' => array(
                    'type' => 'select',
                    'label' => '対象性別',
                    'options' => array(
                        '' => '指定なし',
                        'male' => '男性',
                        'female' => '女性',
                        'unisex' => 'ユニセックス',
                    ),
                    'description' => '商品の対象性別',
                ),
                'product_audience_min_age' => array(
                    'type' => 'text',
                    'label' => '対象最低年齢',
                    'placeholder' => '3',
                    'description' => '対象年齢の下限（年）',
                ),
                'product_audience_max_age' => array(
                    'type' => 'text',
                    'label' => '対象最高年齢',
                    'placeholder' => '8',
                    'description' => '対象年齢の上限（年）',
                ),
                
                // 価格・販売情報
                'product_price' => array(
                    'type' => 'text',
                    'label' => '価格（数値のみ）',
                    'placeholder' => '8980',
                    'description' => '構造化データのpriceプロパティで使用（数値のみ入力）',
                ),
                'product_currency' => array(
                    'type' => 'select',
                    'label' => '通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                    ),
                    'default' => 'JPY',
                    'description' => '構造化データのpriceCurrencyプロパティで使用',
                ),
                'product_condition' => array(
                    'type' => 'select',
                    'label' => '商品状態',
                    'options' => array(
                        'https://schema.org/NewCondition' => '新品',
                        'https://schema.org/UsedCondition' => '中古',
                        'https://schema.org/RefurbishedCondition' => '整備済み',
                        'https://schema.org/DamagedCondition' => '破損品',
                    ),
                    'default' => 'https://schema.org/NewCondition',
                    'description' => '商品の状態',
                ),
                'product_availability' => array(
                    'type' => 'select',
                    'label' => '在庫状況',
                    'options' => array(
                        'https://schema.org/InStock' => '在庫あり',
                        'https://schema.org/OutOfStock' => '在庫切れ',
                        'https://schema.org/LimitedAvailability' => '在庫僅少',
                        'https://schema.org/PreOrder' => '予約注文',
                    ),
                    'default' => 'https://schema.org/InStock',
                    'description' => '商品の在庫状況',
                ),
                'product_url' => array(
                    'type' => 'text',
                    'label' => '商品購入URL',
                    'placeholder' => 'https://example.com/product/santoku-knife',
                    'description' => '商品の購入ページURL',
                ),
            ),
        ),
        
        // 実績詳細情報（構造化データ用）
        'portfolio_details' => array(
            'title' => '実績詳細情報（構造化データ用）',
            'post_types' => array('portfolio'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // TechArticle基本情報
                'portfolio_description' => array(
                    'type' => 'textarea',
                    'label' => '実績の詳細説明',
                    'placeholder' => '株式会社〇〇様のコーポレートサイトリニューアルに関する制作実績です。ブランドイメージの向上と、問い合わせ数の増加という課題解決を実現しました。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                
                'portfolio_images' => array(
                    'type' => 'repeater',
                    'label' => '実績画像',
                    'description' => '実績のスクリーンショットや画像を複数追加できます。',
                    'button_label' => '画像を追加',
                    'sub_fields' => array(
                        'image' => array(
                            'type' => 'image',
                            'label' => '画像',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ),
                    ),
                ),
                
                // クライアント情報
                'client_company_name' => array(
                    'type' => 'text',
                    'label' => 'クライアント会社名',
                    'placeholder' => '株式会社〇〇',
                    'description' => 'クライアントの会社名',
                ),
                'client_website_name' => array(
                    'type' => 'text',
                    'label' => 'クライアントサイト種別',
                    'placeholder' => '株式会社〇〇 コーポレートサイト',
                    'description' => '制作したサイトの名前',
                ),
                'client_website_url' => array(
                    'type' => 'text',
                    'label' => 'クライアントサイトURL',
                    'placeholder' => 'https://クライアントのサイトURL/',
                    'description' => '制作したサイトのURL',
                ),
                'client_website_image' => array(
                    'type' => 'image',
                    'label' => 'クライアントサイト画像',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'description' => '制作したサイトのメイン画像',
                ),
                
                // プロジェクト情報
                'project_price' => array(
                    'type' => 'text',
                    'label' => 'プロジェクト料金（数値のみ）',
                    'placeholder' => '600000',
                    'description' => '構造化データのPriceプロパティで使用（数値のみ入力）',
                ),
                'project_currency' => array(
                    'type' => 'select',
                    'label' => '通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                    ),
                    'default' => 'JPY',
                    'description' => '料金の通貨',
                ),
                
                // レビュー・評価情報
                'review_rating' => array(
                    'type' => 'select',
                    'label' => '評価（5段階）',
                    'options' => array(
                        '5.0' => '5.0（最高）',
                        '4.9' => '4.9（非常に良い）',
                        '4.8' => '4.8（非常に良い）',
                        '4.7' => '4.7（良い）',
                        '4.6' => '4.6（良い）',
                        '4.5' => '4.5（良い）',
                        '4.0' => '4.0（普通）',
                        '3.5' => '3.5（普通）',
                        '3.0' => '3.0（普通）',
                    ),
                    'default' => '4.9',
                    'description' => 'クライアントからの評価',
                ),
                'review_author_name' => array(
                    'type' => 'text',
                    'label' => 'レビュー者名',
                    'placeholder' => 'クライアント担当者名',
                    'description' => 'レビューを書いた人の名前',
                ),
                'review_author_job_title' => array(
                    'type' => 'text',
                    'label' => 'レビュー者の役職',
                    'placeholder' => '代表取締役',
                    'description' => 'レビューを書いた人の役職',
                ),
                'review_body' => array(
                    'type' => 'textarea',
                    'label' => 'レビュー本文',
                    'placeholder' => 'こちらにお客様からの推薦文や評価コメントを記載します。丁寧なヒアリングと質の高いデザインで、期待以上のサイトが完成しました。',
                    'rows' => 4,
                    'description' => 'クライアントからのレビュー・推薦文',
                ),
            ),
        ),
        
        // 採用詳細情報（構造化データ用）
        'recruit_details' => array(
            'title' => '採用詳細情報（構造化データ用）',
            'post_types' => array('recruit'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // JobPosting基本情報
                'job_title' => array(
                    'type' => 'text',
                    'label' => '求人タイトル',
                    'placeholder' => 'Webデザイナー',
                    'description' => '求人の職種タイトル',
                ),
                'job_description' => array(
                    'type' => 'textarea',
                    'label' => '求人詳細説明',
                    'placeholder' => '【未経験可】Webデザイナー:企画～デザイン・制作・更新・運用業務に携わっていただきます。',
                    'rows' => 6,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                'job_identifier' => array(
                    'type' => 'text',
                    'label' => '求人識別子（自社内で識別するための番号：自由入力OK）',
                    'placeholder' => 'web-designer-2025',
                    'description' => '求人の一意な識別子',
                ),
                
                // 雇用形態・期間
                'employment_type' => array(
                    'type' => 'select',
                    'label' => '雇用形態',
                    'options' => array(
                        'FULL_TIME' => '正社員（フルタイム）',
                        'PART_TIME' => 'パートタイム',
                        'CONTRACTOR' => '契約社員',
                        'TEMPORARY' => '派遣・臨時',
                        'INTERN' => 'インターン',
                        'VOLUNTEER' => 'ボランティア',
                        'PER_DIEM' => '日雇い',
                        'OTHER' => 'その他',
                    ),
                    'default' => 'FULL_TIME',
                    'description' => '雇用形態の種別',
                ),
                'valid_through' => array(
                    'type' => 'datetime-local',
                    'label' => '求人有効期限',
                    'description' => '求人の有効期限',
                ),
                
                // 勤務地情報
                'job_location_street' => array(
                    'type' => 'text',
                    'label' => '勤務地（番地）',
                    'placeholder' => '中野3-49-5',
                    'description' => '勤務地の番地・建物名',
                ),
                'job_location_locality' => array(
                    'type' => 'text',
                    'label' => '勤務地（市区町村）',
                    'placeholder' => '中野区',
                    'description' => '勤務地の市区町村',
                ),
                'job_location_region' => array(
                    'type' => 'text',
                    'label' => '勤務地（都道府県）',
                    'placeholder' => '東京都',
                    'description' => '勤務地の都道府県',
                ),
                'job_location_postal_code' => array(
                    'type' => 'text',
                    'label' => '勤務地（郵便番号）',
                    'placeholder' => '164-0001',
                    'description' => '勤務地の郵便番号',
                ),
                
                // 給与情報
                'salary_min' => array(
                    'type' => 'text',
                    'label' => '最低年収（数値のみ）',
                    'placeholder' => '2352000',
                    'description' => '年収の最低額（数値のみ入力）',
                ),
                'salary_max' => array(
                    'type' => 'text',
                    'label' => '最高年収（数値のみ）',
                    'placeholder' => '12000000',
                    'description' => '年収の最高額（数値のみ入力）',
                ),
                'salary_currency' => array(
                    'type' => 'select',
                    'label' => '給与通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                    ),
                    'default' => 'JPY',
                    'description' => '給与の通貨',
                ),
                
                // 業務内容・スキル
                'responsibilities' => array(
                    'type' => 'textarea',
                    'label' => '主な業務内容',
                    'placeholder' => 'Webサイト・LPのUI/UXデザイン,ワイヤーフレームおよびプロトタイプの作成,クライアントへのデザイン提案,デザインシステムの構築・運用',
                    'rows' => 4,
                    'description' => '主な業務内容（カンマ区切りで複数入力可能）',
                ),
                'required_skills' => array(
                    'type' => 'text',
                    'label' => '必要スキル',
                    'placeholder' => 'Figma, Adobe XD, Photoshop, Illustrator, HTML, CSS',
                    'description' => '必要なスキル（カンマ区切り）',
                ),
                'experience_months' => array(
                    'type' => 'text',
                    'label' => '必要経験月数',
                    'placeholder' => '0',
                    'description' => '必要な経験月数（0=未経験可）',
                ),
                
                // 会社情報
                'company_name' => array(
                    'type' => 'text',
                    'label' => '会社名',
                    'placeholder' => '株式会社GoF',
                    'description' => '採用企業名',
                ),
                'company_url' => array(
                    'type' => 'text',
                    'label' => '会社URL',
                    'placeholder' => 'https://gofool.co.jp/',
                    'description' => '会社のWebサイトURL',
                ),
                'company_logo' => array(
                    'type' => 'image',
                    'label' => '会社ロゴ',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'description' => '会社のロゴ画像',
                ),
            ),
        ),
        
        // イベント詳細情報（構造化データ用）
        'event_details' => array(
            'title' => 'イベント詳細情報（構造化データ用）',
            'post_types' => array('event'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // Event基本情報
                'event_name' => array(
                    'type' => 'text',
                    'label' => 'イベント名',
                    'placeholder' => 'Webデザイントレンドセミナー2025',
                    'description' => 'イベントの正式名称',
                ),
                'event_description' => array(
                    'type' => 'textarea',
                    'label' => 'イベント詳細説明',
                    'placeholder' => '2025年のWebデザイントレンドを徹底解説！UI/UXの最新動向から、AIがデザインに与える影響まで、第一線で活躍するデザイナーが語ります。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                
                // 日時情報
                'event_start_date' => array(
                    'type' => 'datetime-local',
                    'label' => '開始日時',
                    'description' => 'イベント開始日時',
                ),
                'event_end_date' => array(
                    'type' => 'datetime-local',
                    'label' => '終了日時',
                    'description' => 'イベント終了日時',
                ),
                
                // イベント画像
                'event_images' => array(
                    'type' => 'repeater',
                    'label' => 'イベント画像',
                    'description' => 'イベントの画像を複数追加できます。',
                    'button_label' => '画像を追加',
                    'sub_fields' => array(
                        'image' => array(
                            'type' => 'image',
                            'label' => '画像',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ),
                    ),
                ),
                
                // イベント状態・参加方法
                'event_status' => array(
                    'type' => 'select',
                    'label' => 'イベント状態',
                    'options' => array(
                        'https://schema.org/EventScheduled' => '開催予定',
                        'https://schema.org/EventRescheduled' => '延期',
                        'https://schema.org/EventPostponed' => '延期',
                        'https://schema.org/EventCancelled' => '中止',
                        'https://schema.org/EventMovedOnline' => 'オンライン移行',
                    ),
                    'default' => 'https://schema.org/EventScheduled',
                    'description' => 'イベントの開催状況',
                ),
                'event_attendance_mode' => array(
                    'type' => 'select',
                    'label' => '参加方法',
                    'options' => array(
                        'https://schema.org/OfflineEventAttendanceMode' => 'オフライン（会場のみ）',
                        'https://schema.org/OnlineEventAttendanceMode' => 'オンライン（配信のみ）',
                        'https://schema.org/MixedEventAttendanceMode' => 'ハイブリッド（会場＋配信）',
                    ),
                    'default' => 'https://schema.org/OfflineEventAttendanceMode',
                    'description' => 'イベントの参加形式',
                ),
                
                // 会場情報
                'venue_name' => array(
                    'type' => 'text',
                    'label' => '会場名',
                    'placeholder' => 'なかのZEROホール',
                    'description' => 'イベント会場の名称',
                ),
                'venue_street' => array(
                    'type' => 'text',
                    'label' => '会場住所（番地）',
                    'placeholder' => '中野2-9-7',
                    'description' => '会場の番地・建物名',
                ),
                'venue_locality' => array(
                    'type' => 'text',
                    'label' => '会場住所（市区町村）',
                    'placeholder' => '中野区',
                    'description' => '会場の市区町村',
                ),
                'venue_region' => array(
                    'type' => 'text',
                    'label' => '会場住所（都道府県）',
                    'placeholder' => '東京都',
                    'description' => '会場の都道府県',
                ),
                'venue_postal_code' => array(
                    'type' => 'text',
                    'label' => '会場住所（郵便番号）',
                    'placeholder' => '164-0001',
                    'description' => '会場の郵便番号',
                ),
                
                // オンライン会場情報
                'virtual_location_url' => array(
                    'type' => 'text',
                    'label' => 'オンライン配信URL',
                    'placeholder' => 'https://example.com/live-stream',
                    'description' => 'ライブ配信のURL（オンライン・ハイブリッド開催時）',
                ),
                
                // チケット・料金情報
                'ticket_name' => array(
                    'type' => 'text',
                    'label' => 'チケット名',
                    'placeholder' => '一般参加チケット',
                    'description' => 'チケットの種類名',
                ),
                'ticket_price' => array(
                    'type' => 'text',
                    'label' => '参加費（数値のみ）',
                    'placeholder' => '5000',
                    'description' => '参加費用（数値のみ入力）',
                ),
                'ticket_currency' => array(
                    'type' => 'select',
                    'label' => '料金通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                    ),
                    'default' => 'JPY',
                    'description' => '参加費の通貨',
                ),
                'ticket_availability' => array(
                    'type' => 'select',
                    'label' => 'チケット販売状況',
                    'options' => array(
                        'https://schema.org/InStock' => '販売中',
                        'https://schema.org/SoldOut' => '完売',
                        'https://schema.org/PreOrder' => '先行販売',
                        'https://schema.org/OutOfStock' => '販売終了',
                    ),
                    'default' => 'https://schema.org/InStock',
                    'description' => 'チケットの販売状況',
                ),
                'ticket_url' => array(
                    'type' => 'text',
                    'label' => 'チケット購入URL',
                    'placeholder' => 'https://example.com/tickets/buy',
                    'description' => 'チケット購入ページのURL',
                ),
                'ticket_valid_from' => array(
                    'type' => 'datetime-local',
                    'label' => 'チケット販売開始日時',
                    'description' => 'チケット販売開始日時',
                ),
                
                // 出演者情報
                'performers' => array(
                    'type' => 'repeater',
                    'label' => '出演者・講師',
                    'description' => 'イベントの出演者や講師を追加できます。',
                    'button_label' => '出演者を追加',
                    'sub_fields' => array(
                        'name' => array(
                            'type' => 'text',
                            'label' => '出演者名',
                            'placeholder' => '鈴木 誠',
                        ),
                        'url' => array(
                            'type' => 'text',
                            'label' => '出演者URL',
                            'placeholder' => 'https://example.com/profile',
                        ),
                    ),
                ),
                
                // 主催者情報
                'organizer_name' => array(
                    'type' => 'text',
                    'label' => '主催者名',
                    'placeholder' => '東京デザインギルド',
                    'description' => 'イベント主催者・団体名',
                ),
                'organizer_url' => array(
                    'type' => 'text',
                    'label' => '主催者URL',
                    'placeholder' => 'https://example-guild.com',
                    'description' => '主催者のWebサイトURL',
                ),
            ),
        ),
        
        // レシピ詳細情報（構造化データ用）
        'recipe_details' => array(
            'title' => 'レシピ詳細情報（構造化データ用）',
            'post_types' => array('recipe'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // Recipe基本情報
                'recipe_name' => array(
                    'type' => 'text',
                    'label' => 'レシピ名',
                    'placeholder' => '基本の肉じゃが',
                    'description' => 'レシピの正式名称',
                ),
                'recipe_description' => array(
                    'type' => 'textarea',
                    'label' => 'レシピの詳細説明',
                    'placeholder' => 'じゃがいもホクホク、味がしっかり染み込んだ、日本の家庭料理の定番「肉じゃが」の基本レシピです。初心者でも失敗なく作れます。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                
                // 作者情報
                'recipe_author_name' => array(
                    'type' => 'text',
                    'label' => 'レシピ作者名',
                    'placeholder' => '山田 花子',
                    'description' => 'レシピの作者・料理人の名前',
                ),
                
                // レシピ画像
                'recipe_images' => array(
                    'type' => 'repeater',
                    'label' => 'レシピ画像',
                    'description' => 'レシピの画像を複数追加できます。',
                    'button_label' => '画像を追加',
                    'sub_fields' => array(
                        'image' => array(
                            'type' => 'image',
                            'label' => '画像',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ),
                    ),
                ),
                
                // レシピ分類
                'recipe_category' => array(
                    'type' => 'select',
                    'label' => 'レシピカテゴリ',
                    'options' => array(
                        '主菜' => '主菜',
                        '副菜' => '副菜',
                        '汁物' => '汁物',
                        'ご飯もの' => 'ご飯もの',
                        '麺類' => '麺類',
                        'デザート' => 'デザート',
                        '飲み物' => '飲み物',
                        'その他' => 'その他',
                    ),
                    'default' => '主菜',
                    'description' => 'レシピのカテゴリ分類',
                ),
                'recipe_cuisine' => array(
                    'type' => 'select',
                    'label' => '料理の種類',
                    'options' => array(
                        '和食' => '和食',
                        '洋食' => '洋食',
                        '中華' => '中華',
                        'イタリアン' => 'イタリアン',
                        'フレンチ' => 'フレンチ',
                        'エスニック' => 'エスニック',
                        'その他' => 'その他',
                    ),
                    'default' => '和食',
                    'description' => '料理の種類・ジャンル',
                ),
                
                // 調理時間情報
                'prep_time_minutes' => array(
                    'type' => 'text',
                    'label' => '準備時間（分）',
                    'placeholder' => '15',
                    'description' => '下準備にかかる時間（分単位）',
                ),
                'cook_time_minutes' => array(
                    'type' => 'text',
                    'label' => '調理時間（分）',
                    'placeholder' => '25',
                    'description' => '実際の調理にかかる時間（分単位）',
                ),
                'total_time_minutes' => array(
                    'type' => 'text',
                    'label' => '合計時間（分）',
                    'placeholder' => '40',
                    'description' => '準備から完成までの合計時間（分単位）',
                ),
                
                // 分量・カロリー
                'recipe_yield' => array(
                    'type' => 'text',
                    'label' => '何人前',
                    'placeholder' => '2人前',
                    'description' => 'レシピの分量（何人前か）',
                ),
                'recipe_keywords' => array(
                    'type' => 'text',
                    'label' => 'キーワード',
                    'placeholder' => '肉じゃが, 和食, 定番, 煮物',
                    'description' => 'レシピのキーワード（カンマ区切り）',
                ),
                'nutrition_calories' => array(
                    'type' => 'text',
                    'label' => 'カロリー',
                    'placeholder' => '450',
                    'description' => '1人前あたりのカロリー（数値のみ）',
                ),
                
                // 材料リスト
                'recipe_ingredients' => array(
                    'type' => 'repeater',
                    'label' => '材料',
                    'description' => 'レシピに必要な材料を追加してください。',
                    'button_label' => '材料を追加',
                    'sub_fields' => array(
                        'ingredient' => array(
                            'type' => 'text',
                            'label' => '材料',
                            'placeholder' => 'じゃがいも: 3個',
                        ),
                    ),
                ),
                
                // 作り方手順
                'recipe_instructions' => array(
                    'type' => 'repeater',
                    'label' => '作り方手順',
                    'description' => 'レシピの作り方を手順ごとに追加してください。',
                    'button_label' => '手順を追加',
                    'sub_fields' => array(
                        'step_name' => array(
                            'type' => 'text',
                            'label' => '工程名',
                            'placeholder' => '下準備',
                        ),
                        'step_text' => array(
                            'type' => 'textarea',
                            'label' => '手順の説明',
                            'placeholder' => 'じゃがいもは皮をむいて一口大に切り、水にさらす。玉ねぎはくし切り、にんじんは乱切りにする。',
                            'rows' => 3,
                        ),
                        'step_url' => array(
                            'type' => 'text',
                            'label' => '手順の詳細URL（任意）',
                            'placeholder' => 'https://example.com/recipe/nikujaga#step1',
                        ),
                        'step_image' => array(
                            'type' => 'image',
                            'label' => '手順の画像（任意）',
                            'return_format' => 'url',
                            'preview_size' => 'thumbnail',
                        ),
                    ),
                ),
                
                // 動画情報（任意）
                'recipe_video_name' => array(
                    'type' => 'text',
                    'label' => '動画タイトル（任意）',
                    'placeholder' => '動画で見る！基本の肉じゃがの作り方',
                    'description' => 'レシピ動画のタイトル',
                ),
                'recipe_video_description' => array(
                    'type' => 'textarea',
                    'label' => '動画の説明（任意）',
                    'placeholder' => '初心者でも分かりやすい、肉じゃがの作り方を動画で解説します。',
                    'rows' => 2,
                    'description' => 'レシピ動画の説明',
                ),
                'recipe_video_thumbnail_url' => array(
                    'type' => 'text',
                    'label' => '動画サムネイルURL（任意）',
                    'placeholder' => 'https://example.com/images/nikujaga-video-thumbnail.jpg',
                    'description' => '動画のサムネイル画像URL',
                ),
                'recipe_video_content_url' => array(
                    'type' => 'text',
                    'label' => '動画URL（任意）',
                    'placeholder' => 'https://www.example.com/videos/nikujaga.mp4',
                    'description' => '動画ファイルの直接URL',
                ),
                'recipe_video_upload_date' => array(
                    'type' => 'datetime-local',
                    'label' => '動画アップロード日時（任意）',
                    'description' => '動画をアップロードした日時',
                ),
            ),
        ),
        
        // FAQ詳細情報（構造化データ用）
        'faq_details' => array(
            'title' => 'FAQ詳細情報（構造化データ用）',
            'post_types' => array('faq'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // FAQ基本情報
                'faq_answer_summary' => array(
                    'type' => 'textarea',
                    'label' => '回答',
                    'placeholder' => '基本プランは月額10,000円から利用可能です。詳細は料金表をご確認ください。',
                    'rows' => 3,
                    'description' => '',
                ),
            ),
        ),
        
        // 設備詳細情報（構造化データ用）
        'facility_details' => array(
            'title' => '設備詳細情報（構造化データ用）',
            'post_types' => array('facility'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                // Product基本情報
                'facility_name' => array(
                    'type' => 'text',
                    'label' => '設備名',
                    'placeholder' => '高速CNC旋盤 モデルABC-123',
                    'description' => '設備の正式名称',
                ),
                'facility_description' => array(
                    'type' => 'textarea',
                    'label' => '設備の詳細説明',
                    'placeholder' => '高精度な部品加工を実現する最新のCNC旋盤。耐久性と操作性に優れ、生産効率を大幅に向上させます。',
                    'rows' => 4,
                    'description' => '構造化データのdescriptionプロパティで使用',
                ),
                
                // 基本仕様
                'facility_model' => array(
                    'type' => 'text',
                    'label' => 'モデル番号（自社内で識別するための番号：自由入力OK）',
                    'placeholder' => 'ABC-123',
                    'description' => '設備のモデル番号',
                ),
                'facility_mpn' => array(
                    'type' => 'text',
                    'label' => 'MPN（製造者商品番号）（自社内で識別するための番号：自由入力OK）',
                    'placeholder' => '987654321',
                    'description' => '製造者が定義した製品番号',
                ),
                
                // 設備画像
                'facility_images' => array(
                    'type' => 'repeater',
                    'label' => '設備画像',
                    'description' => '設備の画像を複数追加できます。最初の画像がメイン画像として使用されます。',
                    'button_label' => '画像を追加',
                    'sub_fields' => array(
                        'image' => array(
                            'type' => 'image',
                            'label' => '画像',
                            'description' => '設備の画像を選択してください',
                        ),
                    ),
                ),
                
                // ブランド・製造者情報
                'facility_brand' => array(
                    'type' => 'text',
                    'label' => 'ブランド名',
                    'placeholder' => '山田重工',
                    'description' => '設備のブランド名',
                ),
                'manufacturer_name' => array(
                    'type' => 'text',
                    'label' => '製造者名',
                    'placeholder' => '山田重工株式会社',
                    'description' => '設備の製造者・会社名',
                ),
                'manufacturer_url' => array(
                    'type' => 'url',
                    'label' => '製造者URL',
                    'placeholder' => 'https://example.com/yamada-heavy-industries',
                    'description' => '製造者のWebサイトURL',
                ),
                
                // 設備仕様（追加プロパティ）
                'facility_specifications' => array(
                    'type' => 'repeater',
                    'label' => '設備仕様',
                    'description' => '設備の仕様・スペックを追加できます',
                    'button_label' => '仕様を追加',
                    'sub_fields' => array(
                        'spec_name' => array(
                            'type' => 'text',
                            'label' => '仕様名',
                            'placeholder' => '最大加工径',
                            'description' => '仕様項目の名前',
                        ),
                        'spec_value' => array(
                            'type' => 'text',
                            'label' => '値',
                            'placeholder' => '300 mm',
                            'description' => '仕様の値（単位含む）',
                        ),
                        'spec_unit_code' => array(
                            'type' => 'text',
                            'label' => '単位コード（任意）',
                            'placeholder' => 'KGM',
                            'description' => 'UN/CEFACT単位コード（重量：KGM、長さ：MMTなど）',
                        ),
                    ),
                ),
                
                // 価格・在庫情報
                'facility_availability' => array(
                    'type' => 'select',
                    'label' => '在庫状況',
                    'options' => array(
                        'https://schema.org/InStock' => '在庫あり',
                        'https://schema.org/OutOfStock' => '在庫なし',
                        'https://schema.org/PreOrder' => '予約注文',
                        'https://schema.org/BackOrder' => '取り寄せ',
                        'https://schema.org/Discontinued' => '製造終了',
                        'https://schema.org/LimitedAvailability' => '限定在庫',
                    ),
                    'default' => 'https://schema.org/InStock',
                    'description' => '設備の在庫・入手可能状況',
                ),
                'facility_price_description' => array(
                    'type' => 'text',
                    'label' => '価格説明（120〜140文字推奨）',
                    'placeholder' => '価格はお問い合わせください（要見積もり）。',
                    'description' => '価格に関する説明文（見積もり制など）',
                ),
                'facility_price' => array(
                    'type' => 'text',
                    'label' => '価格（数値のみ、任意）',
                    'placeholder' => '5000000',
                    'description' => '設備の価格（数値のみ入力、見積もり制の場合は空欄）',
                ),
                'facility_currency' => array(
                    'type' => 'select',
                    'label' => '通貨',
                    'options' => array(
                        'JPY' => '日本円（JPY）',
                        'USD' => '米ドル（USD）',
                        'EUR' => 'ユーロ（EUR）',
                        'GBP' => '英ポンド（GBP）',
                        'CNY' => '中国元（CNY）',
                        'KRW' => '韓国ウォン（KRW）',
                    ),
                    'default' => 'JPY',
                    'description' => '価格の通貨',
                ),
            ),
        ),
        
        // 新しいカスタムフィールドグループをここに追加してください
        /*
        'example_details' => array(
            'title' => '例詳細情報',
            'post_types' => array('example'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                'example_field' => array(
                    'type' => 'text',
                    'label' => '例フィールド',
                    'placeholder' => '例の値を入力',
                ),
            ),
        ),
        */
    );
}

// ==============================================
// wp-config.php 推奨設定（コメント）
// ==============================================

/*
以下の設定をwp-config.phpに追加することを推奨します：

// WP-Cron無効化
define('DISABLE_WP_CRON', true);

// リビジョン制限
define('WP_POST_REVISIONS', 3);

// 自動保存間隔延長
define('AUTOSAVE_INTERVAL', 300);

// ファイル編集無効化
define('DISALLOW_FILE_EDIT', true);

// デバッグモード（開発時のみ）
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
*/

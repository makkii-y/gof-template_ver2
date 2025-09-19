<?php
/**
 * カスタムフィールド管理システム
 * ACF風のカスタムフィールド実装
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタムフィールドマネージャー
 */
class GOF_Custom_Fields {
    
    private static $instance = null;
    private $field_groups = array();
    
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
        add_action('init', array($this, 'init'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // 繰り返しフィールドクラスの読み込み
        require_once get_template_directory() . '/admin/includes/repeater-fields.php';
    }
    
    /**
     * 初期化
     */
    public function init() {
        // 設定があれば読み込み
        if (function_exists('gof_custom_fields_config')) {
            $this->field_groups = gof_custom_fields_config();
        } else {
            $this->field_groups = $this->get_default_field_groups();
        }
    }
    
    /**
     * デフォルトのフィールドグループ設定
     */
    private function get_default_field_groups() {
        return array(
            // 作品詳細情報
            'works_details' => array(
                'title' => '作品詳細情報',
                'post_types' => array('works'),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array(
                    'work_client' => array(
                        'type' => 'text',
                        'label' => 'クライアント名',
                        'placeholder' => 'クライアント名を入力',
                    ),
                    'work_date' => array(
                        'type' => 'text',
                        'label' => '制作日',
                        'placeholder' => '2024年1月',
                    ),
                    'work_url' => array(
                        'type' => 'text',
                        'label' => 'サイトURL',
                        'placeholder' => 'https://example.com',
                    ),
                    'work_description' => array(
                        'type' => 'textarea',
                        'label' => '詳細説明',
                        'placeholder' => '作品の詳細説明を入力',
                    ),
                    'work_image' => array(
                        'type' => 'image',
                        'label' => 'メイン画像',
                    ),
                    'work_category_type' => array(
                        'type' => 'select',
                        'label' => '作品タイプ',
                        'options' => array(
                            'website' => 'ウェブサイト',
                            'application' => 'アプリケーション',
                            'design' => 'デザイン',
                            'other' => 'その他',
                        ),
                    ),
                ),
            ),
            
            // お知らせ詳細情報
            'news_details' => array(
                'title' => 'お知らせ詳細情報',
                'post_types' => array('news'),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array(
                    'news_important' => array(
                        'type' => 'select',
                        'label' => '重要度',
                        'options' => array(
                            'normal' => '通常',
                            'important' => '重要',
                            'urgent' => '緊急',
                        ),
                        'default' => 'normal',
                    ),
                    'news_external_url' => array(
                        'type' => 'text',
                        'label' => '外部リンク',
                        'placeholder' => 'https://example.com',
                    ),
                ),
            ),
        );
    }
    
    /**
     * メタボックス追加
     */
    public function add_meta_boxes() {
        foreach ($this->field_groups as $group_key => $group) {
            if (!empty($group['post_types'])) {
                foreach ($group['post_types'] as $post_type) {
                    add_meta_box(
                        'gof_' . $group_key,
                        $group['title'],
                        array($this, 'render_meta_box'),
                        $post_type,
                        isset($group['context']) ? $group['context'] : 'normal',
                        isset($group['priority']) ? $group['priority'] : 'default',
                        array('group_key' => $group_key, 'group' => $group)
                    );
                }
            }
        }
    }
    
    /**
     * メタボックス表示
     */
    public function render_meta_box($post, $meta_box) {
        $group_key = $meta_box['args']['group_key'];
        $group = $meta_box['args']['group'];
        
        // nonce設定
        wp_nonce_field('gof_custom_fields_nonce_' . $group_key, 'gof_custom_fields_nonce_' . $group_key);
        
        echo '<div class="gof-custom-fields-wrapper">';
        
        foreach ($group['fields'] as $field_key => $field) {
            $value = get_post_meta($post->ID, $field_key, true);
            $this->render_field($field_key, $field, $value);
        }
        
        echo '</div>';
    }
    
    /**
     * フィールド表示
     */
    private function render_field($field_key, $field, $value = '') {
        $field_id = 'gof_field_' . $field_key;
        $field_name = 'gof_fields[' . $field_key . ']';
        
        echo '<div class="gof-field-wrapper gof-field-type-' . $field['type'] . '">';
        echo '<label for="' . $field_id . '">' . $field['label'] . '</label>';
        
        switch ($field['type']) {
            case 'text':
                $this->render_text_field($field_id, $field_name, $field, $value);
                break;
            case 'textarea':
                $this->render_textarea_field($field_id, $field_name, $field, $value);
                break;
            case 'image':
                $this->render_image_field($field_id, $field_name, $field, $value);
                break;
            case 'select':
                $this->render_select_field($field_id, $field_name, $field, $value);
                break;
            case 'datetime-local':
                $this->render_datetime_field($field_id, $field_name, $field, $value);
                break;
            case 'repeater':
                if (class_exists('GOF_Repeater_Fields')) {
                    GOF_Repeater_Fields::render_repeater_field($field_key, $field, $value);
                } else {
                    echo '<p>繰り返しフィールドクラスが読み込まれていません。</p>';
                }
                break;
        }
        
        echo '</div>';
    }
    
    /**
     * テキストフィールド表示
     */
    private function render_text_field($field_id, $field_name, $field, $value) {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        echo '<input type="text" id="' . $field_id . '" name="' . $field_name . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" class="regular-text" />';
    }
    
    /**
     * テキストエリアフィールド表示
     */
    private function render_textarea_field($field_id, $field_name, $field, $value) {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $rows = isset($field['rows']) ? $field['rows'] : 4;
        echo '<textarea id="' . $field_id . '" name="' . $field_name . '" placeholder="' . esc_attr($placeholder) . '" rows="' . $rows . '" class="large-text">' . esc_textarea($value) . '</textarea>';
    }
    
    /**
     * 画像フィールド表示
     */
    private function render_image_field($field_id, $field_name, $field, $value) {
        $image_url = '';
        if ($value) {
            $image_url = wp_get_attachment_image_url($value, 'medium');
        }
        
        echo '<div class="gof-image-field">';
        echo '<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' . esc_attr($value) . '" />';
        echo '<div class="gof-image-preview" style="margin-bottom: 10px;">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="max-width: 200px; height: auto;" />';
        }
        echo '</div>';
        echo '<button type="button" class="button gof-select-image">' . ($value ? '画像を変更' : '画像を選択') . '</button>';
        if ($value) {
            echo ' <button type="button" class="button gof-remove-image">画像を削除</button>';
        }
        echo '</div>';
    }
    
    /**
     * 日時フィールド表示
     */
    private function render_datetime_field($field_id, $field_name, $field, $value) {
        // ISO 8601形式の値をdatetime-localフォーマットに変換
        $datetime_value = '';
        if (!empty($value)) {
            if (strtotime($value)) {
                $datetime_value = date('Y-m-d\TH:i', strtotime($value));
            } else {
                $datetime_value = $value;
            }
        }
        
        echo '<input type="datetime-local" id="' . $field_id . '" name="' . $field_name . '" value="' . esc_attr($datetime_value) . '" class="regular-text" />';
        echo '<p class="description">選択した日時は自動的にISO 8601形式で保存されます</p>';
    }

    /**
     * セレクトフィールド表示
     */
    private function render_select_field($field_id, $field_name, $field, $value) {
        if (empty($value) && isset($field['default'])) {
            $value = $field['default'];
        }
        
        echo '<select id="' . $field_id . '" name="' . $field_name . '">';
        if (isset($field['options']) && is_array($field['options'])) {
            foreach ($field['options'] as $option_value => $option_label) {
                $selected = selected($value, $option_value, false);
                echo '<option value="' . esc_attr($option_value) . '"' . $selected . '>' . esc_html($option_label) . '</option>';
            }
        }
        echo '</select>';
    }
    
    /**
     * メタボックス保存
     */
    public function save_meta_boxes($post_id) {
        // 自動保存の場合は何もしない
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // 権限チェック
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // フィールドグループごとに保存
        foreach ($this->field_groups as $group_key => $group) {
            $nonce_field = 'gof_custom_fields_nonce_' . $group_key;
            
            // nonceチェック
            if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $nonce_field)) {
                continue;
            }
            
            // 対象の投稿タイプかチェック
            if (!in_array(get_post_type($post_id), $group['post_types'])) {
                continue;
            }
            
            // フィールド保存
            if (isset($_POST['gof_fields']) && is_array($_POST['gof_fields'])) {
                foreach ($group['fields'] as $field_key => $field) {
                    if (isset($_POST['gof_fields'][$field_key])) {
                        $value = $_POST['gof_fields'][$field_key];
                        
                        // フィールドタイプに応じた処理
                        switch ($field['type']) {
                            case 'text':
                                $value = sanitize_text_field($value);
                                break;
                            case 'datetime-local':
                                $value = sanitize_text_field($value);
                                // datetime-localの場合、ISO 8601形式に変換
                                if (!empty($value)) {
                                    $timestamp = strtotime($value);
                                    if ($timestamp) {
                                        $value = date('c', $timestamp); // ISO 8601形式
                                    }
                                }
                                break;
                            case 'textarea':
                                $value = sanitize_textarea_field($value);
                                break;
                            case 'image':
                                $value = intval($value);
                                break;
                            case 'select':
                                $value = sanitize_text_field($value);
                                break;
                            case 'repeater':
                                $value = $this->sanitize_repeater_field($value, $field);
                                break;
                        }
                        
                        update_post_meta($post_id, $field_key, $value);
                    } else {
                        // チェックボックスなど、値が送信されない場合
                        delete_post_meta($post_id, $field_key);
                    }
                }
            }
        }
    }
    
    /**
     * 管理画面用スクリプト読み込み
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_media();
            wp_enqueue_script(
                'gof-custom-fields',
                get_template_directory_uri() . '/admin/assets/js/admin-custom-fields.js',
                array('jquery'),
                '1.0.0',
                true
            );
            wp_enqueue_style(
                'gof-custom-fields',
                get_template_directory_uri() . '/admin/assets/css/admin-custom-fields.css',
                array(),
                '1.0.0'
            );
        }
    }
    
    /**
     * 繰り返しフィールドのサニタイズ
     */
    private function sanitize_repeater_field($values, $field) {
        if (!is_array($values)) {
            return array();
        }
        
        $sanitized = array();
        $sub_fields = isset($field['sub_fields']) ? $field['sub_fields'] : array();
        
        foreach ($values as $row_index => $row_values) {
            if (!is_array($row_values)) {
                continue;
            }
            
            $sanitized_row = array();
            foreach ($sub_fields as $sub_field_key => $sub_field) {
                if (isset($row_values[$sub_field_key])) {
                    $sub_value = $row_values[$sub_field_key];
                    
                    switch ($sub_field['type']) {
                        case 'text':
                            $sub_value = sanitize_text_field($sub_value);
                            break;
                        case 'textarea':
                            $sub_value = sanitize_textarea_field($sub_value);
                            break;
                        case 'image':
                            $sub_value = intval($sub_value);
                            break;
                        case 'select':
                            $sub_value = sanitize_text_field($sub_value);
                            break;
                    }
                    
                    $sanitized_row[$sub_field_key] = $sub_value;
                }
            }
            
            // 空の行は除外
            if (!empty(array_filter($sanitized_row))) {
                $sanitized[] = $sanitized_row;
            }
        }
        
        return $sanitized;
    }
}

/**
 * カスタムフィールド値取得関数
 */
function gof_get_field($field_key, $post_id = null) {
    if ($post_id === null) {
        global $post;
        $post_id = $post->ID;
    }
    
    return get_post_meta($post_id, $field_key, true);
}

/**
 * カスタムフィールド値更新関数
 */
function gof_update_field($field_key, $value, $post_id = null) {
    if ($post_id === null) {
        global $post;
        $post_id = $post->ID;
    }
    
    return update_post_meta($post_id, $field_key, $value);
}

// カスタムフィールドマネージャー初期化
GOF_Custom_Fields::get_instance();

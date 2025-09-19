<?php
/**
 * カスタムフィールドヘルパー関数
 * 
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 画像フィールド値から画像タグを生成
 */
function gof_get_image_html($field_key, $size = 'large', $post_id = null) {
    $image_id = gof_get_field($field_key, $post_id);
    
    if (!$image_id) {
        return '';
    }
    
    return wp_get_attachment_image($image_id, $size);
}

/**
 * 画像フィールド値から画像URLを取得
 */
function gof_get_image_url($field_key, $size = 'large', $post_id = null) {
    $image_id = gof_get_field($field_key, $post_id);
    
    if (!$image_id) {
        return '';
    }
    
    return wp_get_attachment_image_url($image_id, $size);
}

/**
 * 画像フィールド値から画像情報を取得
 */
function gof_get_image_data($field_key, $post_id = null) {
    $image_id = gof_get_field($field_key, $post_id);
    
    if (!$image_id) {
        return false;
    }
    
    $attachment = get_post($image_id);
    if (!$attachment) {
        return false;
    }
    
    $metadata = wp_get_attachment_metadata($image_id);
    
    return array(
        'id' => $image_id,
        'title' => $attachment->post_title,
        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'url' => wp_get_attachment_url($image_id),
        'sizes' => array(
            'thumbnail' => wp_get_attachment_image_url($image_id, 'thumbnail'),
            'medium' => wp_get_attachment_image_url($image_id, 'medium'),
            'large' => wp_get_attachment_image_url($image_id, 'large'),
            'full' => wp_get_attachment_image_url($image_id, 'full'),
        ),
        'width' => isset($metadata['width']) ? $metadata['width'] : 0,
        'height' => isset($metadata['height']) ? $metadata['height'] : 0,
        'mime_type' => get_post_mime_type($image_id),
    );
}

/**
 * 繰り返しフィールドが空かどうかチェック
 */
function gof_has_repeater_rows($field_key, $post_id = null) {
    $repeater_values = gof_get_field($field_key, $post_id);
    
    return !empty($repeater_values) && is_array($repeater_values);
}

/**
 * 繰り返しフィールドの行数を取得
 */
function gof_get_repeater_count($field_key, $post_id = null) {
    $repeater_values = gof_get_field($field_key, $post_id);
    
    if (!is_array($repeater_values)) {
        return 0;
    }
    
    return count($repeater_values);
}

/**
 * セレクトフィールドの選択肢ラベルを取得
 */
function gof_get_select_label($field_key, $group_key = '', $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (empty($value)) {
        return '';
    }
    
    // フィールド設定を取得
    $custom_fields = GOF_Custom_Fields::get_instance();
    $field_groups = function_exists('gof_custom_fields_config') ? 
                   gof_custom_fields_config() : 
                   array();
    
    // 該当するフィールドの選択肢を検索
    foreach ($field_groups as $current_group_key => $group) {
        if (!empty($group_key) && $current_group_key !== $group_key) {
            continue;
        }
        
        if (isset($group['fields'][$field_key])) {
            $field = $group['fields'][$field_key];
            if ($field['type'] === 'select' && isset($field['options'][$value])) {
                return $field['options'][$value];
            }
        }
    }
    
    return $value;
}

/**
 * カスタムフィールドの値をフォーマットして出力
 */
function gof_the_field($field_key, $post_id = null, $format = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (empty($value)) {
        return;
    }
    
    switch ($format) {
        case 'url':
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                echo '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
            } else {
                echo esc_html($value);
            }
            break;
            
        case 'email':
            if (is_email($value)) {
                echo '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
            } else {
                echo esc_html($value);
            }
            break;
            
        case 'tel':
            $clean_tel = preg_replace('/[^0-9+\-]/', '', $value);
            echo '<a href="tel:' . esc_attr($clean_tel) . '">' . esc_html($value) . '</a>';
            break;
            
        case 'nl2br':
            echo nl2br(esc_html($value));
            break;
            
        case 'raw':
            echo $value;
            break;
            
        default:
            echo esc_html($value);
            break;
    }
}

/**
 * 条件付きでカスタムフィールドを表示
 */
function gof_field_if($field_key, $callback, $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (!empty($value)) {
        if (is_callable($callback)) {
            call_user_func($callback, $value);
        } else {
            echo esc_html($value);
        }
    }
}

/**
 * 複数のフィールドをまとめて取得
 */
function gof_get_fields($field_keys, $post_id = null) {
    $fields = array();
    
    foreach ($field_keys as $key) {
        $fields[$key] = gof_get_field($key, $post_id);
    }
    
    return $fields;
}

/**
 * カスタムフィールドの値でクエリを実行
 */
function gof_query_by_field($field_key, $value, $post_type = 'post', $compare = '=') {
    $args = array(
        'post_type' => $post_type,
        'meta_query' => array(
            array(
                'key' => $field_key,
                'value' => $value,
                'compare' => $compare,
            ),
        ),
        'post_status' => 'publish',
    );
    
    return new WP_Query($args);
}

/**
 * カスタムフィールドでソートされたクエリを実行
 */
function gof_query_order_by_field($field_key, $post_type = 'post', $order = 'ASC', $meta_type = 'CHAR') {
    $args = array(
        'post_type' => $post_type,
        'meta_key' => $field_key,
        'orderby' => 'meta_value',
        'order' => $order,
        'meta_type' => $meta_type,
        'post_status' => 'publish',
    );
    
    return new WP_Query($args);
}

/**
 * フィールド値が存在するかチェック
 */
function gof_field_exists($field_key, $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    return !empty($value);
}

/**
 * 日時フィールド値をフォーマットして取得
 */
function gof_get_datetime_formatted($field_key, $format = 'Y年n月j日 H:i', $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (empty($value)) {
        return '';
    }
    
    $timestamp = strtotime($value);
    if (!$timestamp) {
        return $value;
    }
    
    return date($format, $timestamp);
}

/**
 * 日時フィールド値をISO 8601形式で取得
 */
function gof_get_datetime_iso($field_key, $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (empty($value)) {
        return '';
    }
    
    $timestamp = strtotime($value);
    if (!$timestamp) {
        return $value;
    }
    
    return date('c', $timestamp);
}

/**
 * 日時フィールド値を相対時間で取得
 */
function gof_get_datetime_relative($field_key, $post_id = null) {
    $value = gof_get_field($field_key, $post_id);
    
    if (empty($value)) {
        return '';
    }
    
    $timestamp = strtotime($value);
    if (!$timestamp) {
        return $value;
    }
    
    $now = time();
    $diff = $timestamp - $now;
    
    if ($diff < 0) {
        $diff = abs($diff);
        if ($diff < 60) {
            return $diff . '秒前';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . '分前';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . '時間前';
        } else {
            return floor($diff / 86400) . '日前';
        }
    } else {
        if ($diff < 60) {
            return $diff . '秒後';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . '分後';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . '時間後';
        } else {
            return floor($diff / 86400) . '日後';
        }
    }
}

/**
 * 繰り返しフィールドをHTMLリストとして出力
 */
function gof_the_repeater_list($field_key, $sub_field_key, $tag = 'ul', $post_id = null) {
    $repeater_values = gof_get_field($field_key, $post_id);
    
    if (!gof_has_repeater_rows($field_key, $post_id)) {
        return;
    }
    
    $list_tag = in_array($tag, array('ul', 'ol')) ? $tag : 'ul';
    $item_tag = $list_tag === 'ol' ? 'li' : 'li';
    
    echo '<' . $list_tag . ' class="gof-repeater-list gof-repeater-' . esc_attr($field_key) . '">';
    
    foreach ($repeater_values as $row) {
        if (isset($row[$sub_field_key]) && !empty($row[$sub_field_key])) {
            echo '<' . $item_tag . '>' . esc_html($row[$sub_field_key]) . '</' . $item_tag . '>';
        }
    }
    
    echo '</' . $list_tag . '>';
}

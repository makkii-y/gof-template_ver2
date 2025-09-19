<?php

/**
 * 繰り返しフィールド機能
 * 
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 繰り返しフィールドクラス
 */
class GOF_Repeater_Fields
{

    /**
     * 初期化チェック
     */
    public static function init()
    {
        // クラスが正常に読み込まれているかテスト
        return true;
    }

    /**
     * 繰り返しフィールドの表示
     */
    public static function render_repeater_field($field_key, $field, $values = array())
    {
        $field_id = 'gof_field_' . $field_key;
        $field_name = 'gof_fields[' . $field_key . ']';

        // サブフィールドの取得
        $sub_fields = isset($field['sub_fields']) ? $field['sub_fields'] : array();

        echo '<div class="gof-repeater-field" data-field-key="' . htmlspecialchars($field_key) . '">';
        echo '<div class="gof-repeater-header">';
        echo '<h4>' . htmlspecialchars($field['label']) . '</h4>';
        echo '<button type="button" class="button gof-add-row">行を追加</button>';
        echo '</div>';

        echo '<div class="gof-repeater-rows">';

        // 既存の値を表示
        if (!empty($values) && is_array($values)) {
            foreach ($values as $row_index => $row_values) {
                self::render_repeater_row($field_key, $sub_fields, $row_values, $row_index);
            }
        } else {
            // 空の行を1つ表示
            self::render_repeater_row($field_key, $sub_fields, array(), 0);
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * 繰り返しフィールドの行表示
     */
    private static function render_repeater_row($field_key, $sub_fields, $values = array(), $row_index = 0)
    {
        echo '<div class="gof-repeater-row" data-row-index="' . $row_index . '">';
        echo '<div class="gof-repeater-row-header">';
        echo '<span class="gof-repeater-row-number"><strong>行 ' . ($row_index + 1) . '</strong></span>';
        echo '<button type="button" class="button-link gof-remove-row">削除</button>';
        echo '</div>';

        echo '<div class="gof-repeater-row-content">';

        foreach ($sub_fields as $sub_field_key => $sub_field) {
            $sub_field_name = 'gof_fields[' . $field_key . '][' . $row_index . '][' . $sub_field_key . ']';
            $sub_field_id = 'gof_field_' . $field_key . '_' . $row_index . '_' . $sub_field_key;
            $sub_value = isset($values[$sub_field_key]) ? $values[$sub_field_key] : '';

            echo '<div class="gof-sub-field-wrapper">';
            echo '<label for="' . $sub_field_id . '"><strong>' . htmlspecialchars($sub_field['label']) . '</strong></label><br>';

            self::render_sub_field($sub_field_id, $sub_field_name, $sub_field, $sub_value);

            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * サブフィールドの表示
     */
    private static function render_sub_field($field_id, $field_name, $field, $value = '')
    {
        switch ($field['type']) {
            case 'text':
                self::render_text_field($field_id, $field_name, $field, $value);
                break;
            case 'textarea':
                self::render_textarea_field($field_id, $field_name, $field, $value);
                break;
            case 'image':
                self::render_image_field($field_id, $field_name, $field, $value);
                break;
            case 'select':
                self::render_select_field($field_id, $field_name, $field, $value);
                break;
            case 'datetime-local':
                self::render_datetime_field($field_id, $field_name, $field, $value);
                break;
            default:
                echo '<p>フィールドタイプ「' . htmlspecialchars($field['type']) . '」は未対応です。</p>';
                break;
        }
    }

    /**
     * テキストフィールド表示
     */
    private static function render_text_field($field_id, $field_name, $field, $value)
    {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        echo '<input type="text" id="' . $field_id . '" name="' . $field_name . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '" class="regular-text" />';
    }

    /**
     * テキストエリアフィールド表示
     */
    private static function render_textarea_field($field_id, $field_name, $field, $value)
    {
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $rows = isset($field['rows']) ? $field['rows'] : 3;
        echo '<textarea id="' . $field_id . '" name="' . $field_name . '" placeholder="' . htmlspecialchars($placeholder) . '" rows="' . $rows . '" class="large-text">' . htmlspecialchars($value) . '</textarea>';
    }

    /**
     * 画像フィールド表示
     */
    private static function render_image_field($field_id, $field_name, $field, $value)
    {
        $image_url = '';
        if ($value) {
            $image_url = wp_get_attachment_image_url($value, 'thumbnail');
        }

        echo '<div class="gof-image-field">';
        echo '<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' . intval($value) . '" />';
        echo '<div class="gof-image-preview">';
        if ($image_url) {
            echo '<img src="' . htmlspecialchars($image_url) . '" />';
        }
        echo '</div>';
        echo '<button type="button" class="button button-small gof-select-image">' . ($value ? '変更' : '選択') . '</button>';
        if ($value) {
            echo ' <button type="button" class="button button-small gof-remove-image">削除</button>';
        }
        echo '</div>';
    }

    /**
     * 日時フィールド表示
     */
    private static function render_datetime_field($field_id, $field_name, $field, $value)
    {
        // ISO 8601形式の値をdatetime-localフォーマットに変換
        $datetime_value = '';
        if (!empty($value)) {
            if (strtotime($value)) {
                $datetime_value = date('Y-m-d\TH:i', strtotime($value));
            } else {
                $datetime_value = $value;
            }
        }
        
        echo '<input type="datetime-local" id="' . $field_id . '" name="' . $field_name . '" value="' . htmlspecialchars($datetime_value) . '" class="regular-text" />';
    }

    /**
     * セレクトフィールド表示
     */
    private static function render_select_field($field_id, $field_name, $field, $value)
    {
        if (empty($value) && isset($field['default'])) {
            $value = $field['default'];
        }

        echo '<select id="' . $field_id . '" name="' . $field_name . '">';
        if (isset($field['options']) && is_array($field['options'])) {
            foreach ($field['options'] as $option_value => $option_label) {
                $selected = ($value === $option_value) ? ' selected="selected"' : '';
                echo '<option value="' . htmlspecialchars($option_value) . '"' . $selected . '>' . htmlspecialchars($option_label) . '</option>';
            }
        }
        echo '</select>';
    }
}

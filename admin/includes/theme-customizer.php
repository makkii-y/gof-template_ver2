<?php
/**
 * Theme Customizer Settings
 * カスタマイザーでサイトカラーを設定する機能
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * カスタマイザーの設定を追加
 */
function gof_customize_register($wp_customize) {
    
    // ==============================================
    // サイトカラー設定セクション
    // ==============================================
    
    $wp_customize->add_section('gof_site_colors', array(
        'title' => 'サイトカラー設定',
        'description' => 'サイト全体で使用するメインカラーを設定します。',
        'priority' => 30,
    ));
    
    // メインカラー（プライマリ）
    $wp_customize->add_setting('gof_primary_color', array(
        'default' => '#007cba',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'gof_primary_color', array(
        'label' => 'メインカラー（プライマリ）',
        'description' => 'サイトのメインとなる色を設定します。ボタン、リンク、アクセント要素に使用されます。',
        'section' => 'gof_site_colors',
        'settings' => 'gof_primary_color',
    )));
}
add_action('customize_register', 'gof_customize_register');

/**
 * プライマリカラーをCSS変数として出力
 */
function gof_output_custom_css_variables() {
    // プライマリカラーを取得
    $primary_color = get_theme_mod('gof_primary_color', '#007cba');
    
    // RGB値を計算（透明度指定用）
    $primary_rgb = gof_hex_to_rgb($primary_color);
    
    // CSS変数として出力
    echo '<style id="gof-custom-colors">';
    echo ':root {';
    
    // プライマリカラーとその派生版のみ
    echo '--gof-color-primary: ' . esc_attr($primary_color) . ';';
    echo '--gof-color-primary-rgb: ' . esc_attr($primary_rgb) . ';';
    echo '--gof-color-primary-light: rgba(' . esc_attr($primary_rgb) . ', 0.1);';
    echo '--gof-color-primary-dark: ' . esc_attr(gof_darken_color($primary_color, 15)) . ';';
    
    // リンクカラーもプライマリカラーに連動
    echo '--gof-color-link: ' . esc_attr($primary_color) . ';';
    echo '--gof-color-link-hover: ' . esc_attr(gof_darken_color($primary_color, 15)) . ';';
    
    echo '}';
    echo '</style>';
}
add_action('wp_head', 'gof_output_custom_css_variables');

/**
 * Hex色をRGB文字列に変換
 */
function gof_hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return $r . ', ' . $g . ', ' . $b;
}

/**
 * 色を暗くする
 */
function gof_darken_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r - ($r * $percent / 100)));
    $g = max(0, min(255, $g - ($g * $percent / 100)));
    $b = max(0, min(255, $b - ($b * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * カスタマイザーでライブプレビューを有効化するためのJavaScript
 */
function gof_customize_preview_js() {
    wp_enqueue_script(
        'gof-customize-preview',
        get_template_directory_uri() . '/assets/js/customize-preview.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
}
add_action('customize_preview_init', 'gof_customize_preview_js');

/**
 * カスタマイザー用CSS
 */
function gof_customizer_styles() {
    ?>
    <style>
        #customize-control-gof_primary_color .color-picker-hex {
            border-left: 4px solid var(--gof-color-primary, #007cba);
        }
        .customize-section-title {
            font-weight: 600;
            color: #333;
        }
        .customize-control-description {
            font-style: italic;
            color: #666;
            font-size: 12px;
        }
    </style>
    <?php
}
add_action('customize_controls_print_styles', 'gof_customizer_styles');

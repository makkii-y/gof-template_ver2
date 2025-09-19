/**
 * Customizer Live Preview JavaScript
 * プライマリカラーのリアルタイムプレビュー機能
 *
 * @package GOF_Template_Ver2
 */

(function($) {
    'use strict';

    // CSS変数を動的に更新する関数
    function updateCSSVariable(property, value) {
        document.documentElement.style.setProperty(property, value);
    }

    // Hex色をRGBに変換する関数
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? 
            parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) :
            null;
    }

    // 色を暗くする関数
    function darkenColor(hex, percent) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }

    // プライマリカラーのライブプレビュー
    wp.customize('gof_primary_color', function(value) {
        value.bind(function(newval) {
            updateCSSVariable('--gof-color-primary', newval);
            updateCSSVariable('--gof-color-primary-rgb', hexToRgb(newval));
            updateCSSVariable('--gof-color-primary-light', 'rgba(' + hexToRgb(newval) + ', 0.1)');
            updateCSSVariable('--gof-color-primary-dark', darkenColor(newval, 15));
            // リンクカラーもプライマリカラーに連動
            updateCSSVariable('--gof-color-link', newval);
            updateCSSVariable('--gof-color-link-hover', darkenColor(newval, 15));
        });
    });

})(jQuery);

/**
 * 構造化データ管理画面用JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        initTabs();
        initBusinessTypeToggle();
        initClosedDayToggle();
    });
    
    /**
     * タブ機能の初期化
     */
    function initTabs() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            var targetTab = $(this).data('tab');
            
            // アクティブタブの切り替え
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // コンテンツの切り替え
            $('.tab-content').removeClass('active');
            $('#' + targetTab).addClass('active');
        });
    }
    
    /**
     * ビジネスタイプによる表示切り替え
     */
    function initBusinessTypeToggle() {
        $('select[name="gof_local_business_data[business_type]"]').on('change', function() {
            var businessType = $(this).val();
            
            if (businessType === 'Restaurant') {
                $('#restaurant-fields').show();
            } else {
                $('#restaurant-fields').hide();
            }
        });
    }
    
    /**
     * 定休日チェックボックスの制御
     */
    function initClosedDayToggle() {
        $('.closed-checkbox').on('change', function() {
            var day = $(this).data('day');
            var isChecked = $(this).is(':checked');
            var opensInput = $('input[name="gof_local_business_data[' + day + '_opens]"]');
            var closesInput = $('input[name="gof_local_business_data[' + day + '_closes]"]');
            
            if (isChecked) {
                // 定休日の場合
                opensInput.val('none').prop('disabled', true);
                closesInput.val('none').prop('disabled', true);
            } else {
                // 営業日の場合
                opensInput.val('').prop('disabled', false);
                closesInput.val('').prop('disabled', false);
            }
        });
        
        // 初期状態の設定
        $('.closed-checkbox').each(function() {
            var day = $(this).data('day');
            var opensInput = $('input[name="gof_local_business_data[' + day + '_opens]"]');
            var closesInput = $('input[name="gof_local_business_data[' + day + '_closes]"]');
            
            if (opensInput.val() === 'none' || closesInput.val() === 'none') {
                $(this).prop('checked', true);
                opensInput.prop('disabled', true);
                closesInput.prop('disabled', true);
            }
        });
    }
    
})(jQuery);

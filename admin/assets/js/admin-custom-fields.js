/**
 * カスタムフィールド管理画面用JavaScript - シンプル版
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        console.log('カスタムフィールドJavaScript読み込み完了');
        initCustomFields();
    });
    
    /**
     * カスタムフィールド初期化
     */
    function initCustomFields() {
        initImageFields();
        initRepeaterFields();
    }
    
    /**
     * 画像フィールド初期化
     */
    function initImageFields() {
        // 画像選択ボタン
        $(document).on('click', '.gof-select-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var field = button.closest('.gof-image-field');
            var input = field.find('input[type="hidden"]');
            var preview = field.find('.gof-image-preview');
            
            // メディアライブラリを開く
            var mediaUploader = wp.media({
                title: '画像を選択',
                button: {
                    text: '選択'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                // 値を設定
                input.val(attachment.id);
                
                // プレビュー更新
                var imageUrl = attachment.sizes && attachment.sizes.medium ? 
                               attachment.sizes.medium.url : attachment.url;
                preview.html('<img src="' + imageUrl + '" style="max-width: 200px; height: auto;" />');
                
                // ボタンテキスト変更
                button.text('変更');
                
                // 削除ボタンを追加（まだなければ）
                if (!field.find('.gof-remove-image').length) {
                    button.after(' <button type="button" class="button button-small gof-remove-image">削除</button>');
                }
            });
            
            mediaUploader.open();
        });
        
        // 画像削除ボタン
        $(document).on('click', '.gof-remove-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var field = button.closest('.gof-image-field');
            var input = field.find('input[type="hidden"]');
            var preview = field.find('.gof-image-preview');
            var selectButton = field.find('.gof-select-image');
            
            // 値をクリア
            input.val('');
            
            // プレビュークリア
            preview.empty();
            
            // ボタンテキスト変更
            selectButton.text('画像を選択');
            
            // 削除ボタンを削除
            button.remove();
        });
    }
    
    /**
     * 繰り返しフィールド初期化
     */
    function initRepeaterFields() {
        console.log('繰り返しフィールド初期化開始');
        
        // 行追加ボタン
        $(document).on('click', '.gof-add-row', function(e) {
            e.preventDefault();
            console.log('行追加ボタンがクリックされました');
            
            var button = $(this);
            var repeater = button.closest('.gof-repeater-field');
            var rowsContainer = repeater.find('.gof-repeater-rows');
            
            // 現在の最後の行を取得
            var lastRow = rowsContainer.find('.gof-repeater-row').last();
            if (lastRow.length === 0) {
                console.log('エラー: 行が見つかりません');
                return;
            }
            
            // 行を複製
            var newRow = lastRow.clone(true);
            var currentRows = rowsContainer.find('.gof-repeater-row').length;
            var newIndex = currentRows;
            
            console.log('新しい行のインデックス: ' + newIndex);
            
            // 新しい行の設定
            newRow.attr('data-row-index', newIndex);
            newRow.find('.gof-repeater-row-number').html('<strong>行 ' + (newIndex + 1) + '</strong>');
            
            // 入力フィールドを更新
            newRow.find('input, textarea, select').each(function() {
                var field = $(this);
                var name = field.attr('name');
                var id = field.attr('id');
                
                if (name) {
                    // [数字] を [新しいインデックス] に置換
                    var newName = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                    field.attr('name', newName);
                    console.log('name更新: ' + name + ' -> ' + newName);
                }
                
                if (id) {
                    // _数字_ を _新しいインデックス_ に置換
                    var newId = id.replace(/_\d+_/, '_' + newIndex + '_');
                    field.attr('id', newId);
                    console.log('id更新: ' + id + ' -> ' + newId);
                }
                
                // 値をクリア
                if (field.attr('type') !== 'hidden') {
                    field.val('');
                } else {
                    // hiddenフィールド（画像IDなど）もクリア
                    field.val('');
                }
            });
            
            // 画像プレビューをクリア
            newRow.find('.gof-image-preview').empty();
            
            // 画像ボタンのテキストをリセット
            newRow.find('.gof-select-image').text('選択');
            
            // 画像削除ボタンを削除
            newRow.find('.gof-remove-image').remove();
            
            // ラベルのfor属性も更新
            newRow.find('label').each(function() {
                var label = $(this);
                var forAttr = label.attr('for');
                if (forAttr) {
                    var newFor = forAttr.replace(/_\d+_/, '_' + newIndex + '_');
                    label.attr('for', newFor);
                }
            });
            
            // 行を追加
            rowsContainer.append(newRow);
            console.log('新しい行を追加しました');
        });
        
        // 行削除ボタン
        $(document).on('click', '.gof-remove-row', function(e) {
            e.preventDefault();
            console.log('行削除ボタンがクリックされました');
            
            var button = $(this);
            var row = button.closest('.gof-repeater-row');
            var repeater = button.closest('.gof-repeater-field');
            var rowsContainer = repeater.find('.gof-repeater-rows');
            
            // 最低1行は残す
            if (rowsContainer.find('.gof-repeater-row').length <= 1) {
                alert('最低1行は必要です。');
                return;
            }
            
            // 削除確認
            if (confirm('この行を削除しますか？')) {
                row.remove();
                console.log('行を削除しました');
                
                // 行番号を更新
                updateRepeaterRowNumbers(repeater);
            }
        });
    }
    
    /**
     * 繰り返しフィールドの行番号を更新
     */
    function updateRepeaterRowNumbers(repeater) {
        repeater.find('.gof-repeater-row').each(function(index) {
            $(this).attr('data-row-index', index);
            $(this).find('.gof-repeater-row-number').html('<strong>行 ' + (index + 1) + '</strong>');
        });
    }
    
})(jQuery);

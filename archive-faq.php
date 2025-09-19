<?php
/**
 * FAQ一覧ページテンプレート
 * 
 * Schema.org FAQPage構造化データは structured-data.php で自動出力
 * アコーディオン形式でFAQを表示
 * 
 * @package GOF_Template_Ver2
 */

get_header(); 

// 投稿タイプ情報を取得
$post_type = 'faq';
$post_type_obj = get_post_type_object($post_type);
$post_type_name = $post_type_obj->labels->name ?? 'FAQ';

// FAQページ用クエリ
$faq_query = new WP_Query(array(
    'post_type' => 'faq',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<main id="main" class="site-main">
    
    <!-- パンくずリスト -->
    <div class="container">
        <?php if (function_exists('gof_breadcrumb')) : ?>
            <?php gof_breadcrumb(); ?>
        <?php endif; ?>
    </div>
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo esc_html($post_type_name); ?></h1>
            <p class="page-description">よくあるご質問とその回答をまとめました。お探しの情報が見つからない場合は、お気軽にお問い合わせください。</p>
            
            <!-- 構造化データについて -->
            <!-- 
            このページのSchema.org FAQPage構造化データは、
            admin/includes/structured-data.php の GOF_Structured_Data クラスにより自動生成されます。
            
            優先順位: 3番目（FAQ一覧ページ専用）
            - カスタム構造化データ（単体ページのみ）
            - Article構造化データ（単体ページのみ）
            - FAQPage構造化データ（★このページ）
            - ローカルビジネス構造化データ
            - 組織構造化データ
            
            カスタマイズ: 'gof_faq_page_structured_data' フィルターフックで変更可能
            -->
        </div>
    </header>

    <!-- FAQコンテンツ -->
    <div class="container">
        <div class="faq-container">
            
            <?php if ($faq_query->have_posts()) : ?>
                
                <!-- FAQ一覧 -->
                <div class="faq-list">
                    <?php 
                    $faq_index = 0;
                    while ($faq_query->have_posts()) : 
                        $faq_query->the_post(); 
                        $faq_index++;
                        
                        // カスタムフィールドから情報を取得
                        $answer_summary = get_post_meta(get_the_ID(), 'faq_answer_summary', true);
                    ?>
                        
                        <div class="faq-item">
                            <div class="faq-question" role="button" tabindex="0" aria-expanded="false" aria-controls="faq-answer-<?php echo $faq_index; ?>">
                                <h3><?php echo esc_html(get_the_title()); ?></h3>
                                <span class="faq-toggle" aria-hidden="true">+</span>
                            </div>
                            
                            <div class="faq-answer" id="faq-answer-<?php echo $faq_index; ?>" aria-hidden="true">
                                <div class="faq-answer-content">
                                    <?php if (!empty($answer_summary)) : ?>
                                        <div class="faq-summary">
                                            <?php echo wp_kses_post(wpautop($answer_summary)); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="faq-detail">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
            <?php else : ?>
                
                <div class="no-faq-found">
                    <h2>FAQが見つかりませんでした</h2>
                    <p>現在、FAQは登録されていません。</p>
                </div>
                
            <?php endif; ?>
            
        </div>
    </div>

</main>

<!-- FAQ用CSS -->
<style>
.faq-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 0;
}

.faq-list {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.faq-item {
    border-bottom: 1px solid #e0e0e0;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-question {
    padding: 1.5rem;
    background: #f9f9f9;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: background-color 0.3s ease;
    position: relative;
}

.faq-question:hover {
    background: #f0f0f0;
}

.faq-question h3 {
    margin: 0;
    flex: 1;
    font-size: 1.1rem;
    line-height: 1.4;
}

.faq-toggle {
    font-size: 1.5rem;
    font-weight: bold;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-toggle {
    transform: rotate(45deg);
}

.faq-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease;
}

.faq-item.active .faq-answer {
    max-height: 1000px;
}

.faq-answer-content {
    padding: 1.5rem;
    background: white;
}

.faq-summary {
    font-weight: 500;
    color: #333;
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-left: 4px solid #007cba;
    border-radius: 4px;
}

.faq-detail {
    line-height: 1.6;
}

.faq-categories {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
    font-size: 0.9rem;
    color: #666;
}

.no-faq-found {
    text-align: center;
    padding: 3rem;
    color: #666;
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .faq-container {
        padding: 1rem;
    }
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .faq-question {
        padding: 1rem;
    }
    
    .faq-question h3 {
        font-size: 1rem;
    }
    
    .faq-answer-content {
        padding: 1rem;
    }
}
</style>

<!-- FAQ用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // アコーディオン機能
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(function(question) {
        question.addEventListener('click', function() {
            const faqItem = this.closest('.faq-item');
            const isActive = faqItem.classList.contains('active');
            
            // 他のFAQアイテムを閉じる（オプション：複数開いたままにしたい場合はコメントアウト）
            document.querySelectorAll('.faq-item.active').forEach(function(item) {
                if (item !== faqItem) {
                    item.classList.remove('active');
                    item.querySelector('.faq-answer').setAttribute('aria-hidden', 'true');
                    item.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                }
            });
            
            // 現在のアイテムをトグル
            if (isActive) {
                faqItem.classList.remove('active');
                this.setAttribute('aria-expanded', 'false');
                faqItem.querySelector('.faq-answer').setAttribute('aria-hidden', 'true');
            } else {
                faqItem.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
                faqItem.querySelector('.faq-answer').setAttribute('aria-hidden', 'false');
            }
        });
        
        // キーボード対応
        question.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});
</script>

<?php
wp_reset_postdata();
get_sidebar();
get_footer();
?>

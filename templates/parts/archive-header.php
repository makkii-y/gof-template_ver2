<?php
/**
 * アーカイブページヘッダー部分
 */
$post_type_name = $args['post_type_name'] ?? '投稿';
?>

<header class="archive-header">
    <div class="container">
        <h1 class="archive-title"><?php echo esc_html($post_type_name); ?>一覧</h1>
        <p class="archive-description">私たちが提供する<?php echo esc_html($post_type_name); ?>をご紹介します。</p>
    </div>
</header>

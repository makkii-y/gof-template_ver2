<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-branding">
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>" style="color: var(--gof-color-primary);">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
                <p class="site-description" style="color: var(--gof-color-secondary);">
                    <?php bloginfo('description'); ?>
                </p>
            </div>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'primary-menu',
                ));
                ?>
            </nav>
        </div>
    </div>
</header>

<main class="site-main">
    <div class="container">
        
        <!-- カスタムカラー使用例 -->
        <section class="color-demo" style="padding: 40px 0;">
            <h2 style="color: var(--gof-color-heading); margin-bottom: 30px;">カスタムカラー使用例</h2>
            
            <!-- ボタンサンプル -->
            <div class="button-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">ボタン</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                    <button class="btn-primary">プライマリボタン</button>
                    <button class="btn-secondary">セカンダリボタン</button>
                    <button class="btn-accent">アクセントボタン</button>
                    <button class="btn-outline-primary">アウトラインボタン</button>
                </div>
            </div>
            
            <!-- アラートサンプル -->
            <div class="alert-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">アラート</h3>
                <div class="alert alert-success">
                    <strong>成功！</strong> 操作が正常に完了しました。
                </div>
                <div class="alert alert-warning">
                    <strong>警告！</strong> 注意が必要な項目があります。
                </div>
                <div class="alert alert-error">
                    <strong>エラー！</strong> 何かが間違っています。
                </div>
                <div class="alert alert-info">
                    <strong>情報：</strong> 追加情報をお知らせします。
                </div>
            </div>
            
            <!-- カードサンプル -->
            <div class="card-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">カード</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <h4 style="margin: 0; color: var(--gof-color-heading);">カードタイトル</h4>
                        </div>
                        <p>カードの本文です。カスタムカラーが適用されています。</p>
                        <a href="#" class="btn-primary">詳細を見る</a>
                    </div>
                    
                    <div class="card">
                        <h4 style="color: var(--gof-color-heading);">シンプルカード</h4>
                        <p>ヘッダーなしのシンプルなカードです。</p>
                        <a href="#" class="btn-accent">アクション</a>
                    </div>
                </div>
            </div>
            
            <!-- フォームサンプル -->
            <div class="form-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">フォーム</h3>
                <form style="max-width: 500px;">
                    <div style="margin-bottom: 15px;">
                        <label for="name" style="display: block; margin-bottom: 5px; color: var(--gof-color-text);">お名前</label>
                        <input type="text" id="name" name="name" placeholder="山田太郎" style="width: 100%;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="email" style="display: block; margin-bottom: 5px; color: var(--gof-color-text);">メールアドレス</label>
                        <input type="email" id="email" name="email" placeholder="example@example.com" style="width: 100%;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="message" style="display: block; margin-bottom: 5px; color: var(--gof-color-text);">メッセージ</label>
                        <textarea id="message" name="message" rows="4" placeholder="メッセージを入力してください" style="width: 100%;"></textarea>
                    </div>
                    <input type="submit" value="送信する" class="btn-primary">
                </form>
            </div>
            
            <!-- テキストカラーサンプル -->
            <div class="text-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">テキストカラー</h3>
                <p class="text-primary">プライマリカラーのテキスト</p>
                <p class="text-secondary">セカンダリカラーのテキスト</p>
                <p class="text-accent">アクセントカラーのテキスト</p>
                <p class="text-success">成功メッセージのテキスト</p>
                <p class="text-warning">警告メッセージのテキスト</p>
                <p class="text-error">エラーメッセージのテキスト</p>
            </div>
            
            <!-- 背景カラーサンプル -->
            <div class="bg-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">背景カラー</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <div class="bg-primary" style="padding: 20px; border-radius: 8px; text-align: center;">
                        プライマリ背景
                    </div>
                    <div class="bg-secondary" style="padding: 20px; border-radius: 8px; text-align: center;">
                        セカンダリ背景
                    </div>
                    <div class="bg-accent" style="padding: 20px; border-radius: 8px; text-align: center;">
                        アクセント背景
                    </div>
                    <div class="bg-light" style="padding: 20px; border-radius: 8px; text-align: center;">
                        ライト背景
                    </div>
                    <div class="bg-dark" style="padding: 20px; border-radius: 8px; text-align: center;">
                        ダーク背景
                    </div>
                </div>
            </div>
            
            <!-- テーブルサンプル -->
            <div class="table-demo" style="margin-bottom: 30px;">
                <h3 style="color: var(--gof-color-heading);">テーブル</h3>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>項目</th>
                            <th>値</th>
                            <th>説明</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>プライマリカラー</td>
                            <td style="color: var(--gof-color-primary);">#007cba</td>
                            <td>メインとなる色</td>
                        </tr>
                        <tr>
                            <td>セカンダリカラー</td>
                            <td style="color: var(--gof-color-secondary);">#666666</td>
                            <td>サブとなる色</td>
                        </tr>
                        <tr>
                            <td>アクセントカラー</td>
                            <td style="color: var(--gof-color-accent);">#ff6b35</td>
                            <td>アクセントとなる色</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </section>
        
        <!-- 通常のコンテンツ -->
        <section class="content-area">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
            ?>
                    <article <?php post_class(); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title" style="color: var(--gof-color-heading);">
                                <?php the_title(); ?>
                            </h1>
                        </header>
                        
                        <div class="entry-content" style="color: var(--gof-color-text);">
                            <?php the_content(); ?>
                        </div>
                    </article>
            <?php
                endwhile;
            endif;
            ?>
        </section>
        
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content" style="padding: 40px 0; text-align: center;">
            <p style="margin: 0;">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>

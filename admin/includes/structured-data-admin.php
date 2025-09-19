<?php
/**
 * 構造化データ管理画面
 * 組織情報とローカルビジネス情報の設定画面
 *
 * @package GOF_Template_Ver2
 */

// Direct access prevention
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 構造化データ管理画面クラス
 */
class GOF_Structured_Data_Admin {
    
    private static $instance = null;
    
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
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * 管理メニューの追加
     */
    public function add_admin_menu() {
        add_options_page(
            '構造化データ設定',
            '構造化データ',
            'manage_options',
            'gof-structured-data',
            array($this, 'admin_page')
        );
    }
    
    /**
     * 管理画面の初期化
     */
    public function admin_init() {
        // 組織情報セクション
        add_settings_section(
            'gof_organization_section',
            '組織情報',
            array($this, 'organization_section_callback'),
            'gof-structured-data-org'
        );
        
        register_setting('gof_structured_data_org', 'gof_organization_data');
        
        // ローカルビジネスセクション
        add_settings_section(
            'gof_local_business_section',
            'ローカルビジネス情報',
            array($this, 'local_business_section_callback'),
            'gof-structured-data-local'
        );
        
        register_setting('gof_structured_data_local', 'gof_local_business_data');
    }
    
    /**
     * 管理画面用スクリプト読み込み
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_gof-structured-data') {
            return;
        }
        
        wp_enqueue_style(
            'gof-structured-data-admin',
            get_template_directory_uri() . '/admin/assets/css/admin-structured-data.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'gof-structured-data-admin',
            get_template_directory_uri() . '/admin/assets/js/admin-structured-data.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    /**
     * 管理画面の表示
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>構造化データ設定</h1>
            <p>Google検索結果に表示される組織情報やローカルビジネス情報を設定できます。</p>
            
            <div class="gof-structured-data-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#organization" class="nav-tab nav-tab-active" data-tab="organization">組織情報</a>
                    <a href="#local-business" class="nav-tab" data-tab="local-business">ローカルビジネス</a>
                </nav>
                
                <div id="organization" class="tab-content active">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('gof_structured_data_org');
                        do_settings_sections('gof-structured-data-org');
                        $this->render_organization_fields();
                        submit_button('設定を保存');
                        ?>
                    </form>
                </div>
                
                <div id="local-business" class="tab-content">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('gof_structured_data_local');
                        do_settings_sections('gof-structured-data-local');
                        $this->render_local_business_fields();
                        submit_button('設定を保存');
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * 組織情報セクションのコールバック
     */
    public function organization_section_callback() {
        echo '<p>一般的な組織・企業情報を設定します。ローカルビジネス情報が設定されている場合、こちらは表示されません。</p>';
    }
    
    /**
     * ローカルビジネスセクションのコールバック
     */
    public function local_business_section_callback() {
        echo '<p>実店舗やローカルビジネス向けの詳細情報を設定します。営業時間や住所などの詳細な情報を含めることができます。</p>';
    }
    
    /**
     * 組織情報フィールドの表示
     */
    private function render_organization_fields() {
        $data = get_option('gof_organization_data', array());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">組織名 <span class="required">*</span></th>
                <td>
                    <input type="text" name="gof_organization_data[name]" value="<?php echo esc_attr($data['name'] ?? ''); ?>" class="regular-text" />
                    <p class="description">会社名・団体名を入力してください。</p>
                </td>
            </tr>
            <tr>
                <th scope="row">公式URL</th>
                <td>
                    <input type="url" name="gof_organization_data[url]" value="<?php echo esc_attr($data['url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">公式ウェブサイトのURLを入力してください。</p>
                </td>
            </tr>
            <tr>
                <th scope="row">ロゴURL</th>
                <td>
                    <input type="url" name="gof_organization_data[logo]" value="<?php echo esc_attr($data['logo'] ?? ''); ?>" class="regular-text" />
                    <p class="description">ロゴ画像のURLを入力してください。</p>
                </td>
            </tr>
            <tr>
                <th scope="row">説明</th>
                <td>
                    <textarea name="gof_organization_data[description]" rows="3" class="large-text"><?php echo esc_textarea($data['description'] ?? ''); ?></textarea>
                    <p class="description">組織の説明を入力してください。</p>
                </td>
            </tr>
            <tr>
                <th scope="row">メールアドレス</th>
                <td>
                    <input type="email" name="gof_organization_data[email]" value="<?php echo esc_attr($data['email'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">電話番号</th>
                <td>
                    <input type="tel" name="gof_organization_data[telephone]" value="<?php echo esc_attr($data['telephone'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: +81-3-1234-5678</p>
                </td>
            </tr>
        </table>
        
        <h3>住所情報</h3>
        <table class="form-table">
            <tr>
                <th scope="row">住所</th>
                <td>
                    <input type="text" name="gof_organization_data[street_address]" value="<?php echo esc_attr($data['street_address'] ?? ''); ?>" class="regular-text" />
                    <p class="description">番地・建物名を入力してください。</p>
                </td>
            </tr>
            <tr>
                <th scope="row">市区町村</th>
                <td>
                    <input type="text" name="gof_organization_data[address_locality]" value="<?php echo esc_attr($data['address_locality'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">都道府県</th>
                <td>
                    <input type="text" name="gof_organization_data[address_region]" value="<?php echo esc_attr($data['address_region'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">郵便番号</th>
                <td>
                    <input type="text" name="gof_organization_data[postal_code]" value="<?php echo esc_attr($data['postal_code'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: 100-0001</p>
                </td>
            </tr>
            <tr>
                <th scope="row">国</th>
                <td>
                    <select name="gof_organization_data[address_country]">
                        <option value="JP" <?php selected($data['address_country'] ?? '', 'JP'); ?>>日本 (JP)</option>
                        <option value="US" <?php selected($data['address_country'] ?? '', 'US'); ?>>アメリカ (US)</option>
                        <option value="GB" <?php selected($data['address_country'] ?? '', 'GB'); ?>>イギリス (GB)</option>
                        <option value="FR" <?php selected($data['address_country'] ?? '', 'FR'); ?>>フランス (FR)</option>
                        <option value="DE" <?php selected($data['address_country'] ?? '', 'DE'); ?>>ドイツ (DE)</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h3>その他の情報</h3>
        <table class="form-table">
            <tr>
                <th scope="row">創業者名</th>
                <td>
                    <input type="text" name="gof_organization_data[founder_name]" value="<?php echo esc_attr($data['founder_name'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">設立日</th>
                <td>
                    <input type="date" name="gof_organization_data[founding_date]" value="<?php echo esc_attr($data['founding_date'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">従業員数（最小）</th>
                <td>
                    <input type="number" name="gof_organization_data[employees_min]" value="<?php echo esc_attr($data['employees_min'] ?? ''); ?>" class="small-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">従業員数（最大）</th>
                <td>
                    <input type="number" name="gof_organization_data[employees_max]" value="<?php echo esc_attr($data['employees_max'] ?? ''); ?>" class="small-text" />
                </td>
            </tr>
        </table>
        
        <h3>ソーシャルメディア</h3>
        <table class="form-table">
            <tr>
                <th scope="row">Facebook URL</th>
                <td>
                    <input type="url" name="gof_organization_data[facebook_url]" value="<?php echo esc_attr($data['facebook_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.facebook.com/yourpage</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Twitter URL</th>
                <td>
                    <input type="url" name="gof_organization_data[twitter_url]" value="<?php echo esc_attr($data['twitter_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://twitter.com/youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">LinkedIn URL</th>
                <td>
                    <input type="url" name="gof_organization_data[linkedin_url]" value="<?php echo esc_attr($data['linkedin_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.linkedin.com/company/yourcompany</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Instagram URL</th>
                <td>
                    <input type="url" name="gof_organization_data[instagram_url]" value="<?php echo esc_attr($data['instagram_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.instagram.com/youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">YouTube URL</th>
                <td>
                    <input type="url" name="gof_organization_data[youtube_url]" value="<?php echo esc_attr($data['youtube_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.youtube.com/channel/yourchannel</p>
                </td>
            </tr>
            <tr>
                <th scope="row">TikTok URL</th>
                <td>
                    <input type="url" name="gof_organization_data[tiktok_url]" value="<?php echo esc_attr($data['tiktok_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.tiktok.com/@youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Pinterest URL</th>
                <td>
                    <input type="url" name="gof_organization_data[pinterest_url]" value="<?php echo esc_attr($data['pinterest_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://www.pinterest.com/youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">GitHub URL</th>
                <td>
                    <input type="url" name="gof_organization_data[github_url]" value="<?php echo esc_attr($data['github_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://github.com/youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">note URL</th>
                <td>
                    <input type="url" name="gof_organization_data[note_url]" value="<?php echo esc_attr($data['note_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://note.com/youraccount</p>
                </td>
            </tr>
            <tr>
                <th scope="row">LINE公式アカウント URL</th>
                <td>
                    <input type="url" name="gof_organization_data[line_url]" value="<?php echo esc_attr($data['line_url'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: https://lin.ee/yourcode</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * ローカルビジネスフィールドの表示
     */
    private function render_local_business_fields() {
        $data = get_option('gof_local_business_data', array());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">ビジネス名 <span class="required">*</span></th>
                <td>
                    <input type="text" name="gof_local_business_data[name]" value="<?php echo esc_attr($data['name'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">ビジネスタイプ <span class="required">*</span></th>
                <td>
                    <select name="gof_local_business_data[business_type]">
                        <option value="">選択してください</option>
                        <option value="Restaurant" <?php selected($data['business_type'] ?? '', 'Restaurant'); ?>>レストラン</option>
                        <option value="Store" <?php selected($data['business_type'] ?? '', 'Store'); ?>>小売店</option>
                        <option value="LocalBusiness" <?php selected($data['business_type'] ?? '', 'LocalBusiness'); ?>>一般的なローカルビジネス</option>
                        <option value="MedicalBusiness" <?php selected($data['business_type'] ?? '', 'MedicalBusiness'); ?>>医療機関</option>
                        <option value="ProfessionalService" <?php selected($data['business_type'] ?? '', 'ProfessionalService'); ?>>専門サービス</option>
                        <option value="BeautySalon" <?php selected($data['business_type'] ?? '', 'BeautySalon'); ?>>美容院・サロン</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">公式URL</th>
                <td>
                    <input type="url" name="gof_local_business_data[url]" value="<?php echo esc_attr($data['url'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">電話番号</th>
                <td>
                    <input type="tel" name="gof_local_business_data[telephone]" value="<?php echo esc_attr($data['telephone'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: +81-3-1234-5678</p>
                </td>
            </tr>
            <tr>
                <th scope="row">画像URL</th>
                <td>
                    <textarea name="gof_local_business_data[images]" rows="4" class="large-text"><?php echo esc_textarea($data['images'] ?? ''); ?></textarea>
                    <p class="description">1行に1つずつ画像URLを入力してください。</p>
                </td>
            </tr>
        </table>
        
        <h3>住所・位置情報</h3>
        <table class="form-table">
            <tr>
                <th scope="row">住所</th>
                <td>
                    <input type="text" name="gof_local_business_data[street_address]" value="<?php echo esc_attr($data['street_address'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">市区町村</th>
                <td>
                    <input type="text" name="gof_local_business_data[address_locality]" value="<?php echo esc_attr($data['address_locality'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">都道府県</th>
                <td>
                    <input type="text" name="gof_local_business_data[address_region]" value="<?php echo esc_attr($data['address_region'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">郵便番号</th>
                <td>
                    <input type="text" name="gof_local_business_data[postal_code]" value="<?php echo esc_attr($data['postal_code'] ?? ''); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">国</th>
                <td>
                    <select name="gof_local_business_data[address_country]">
                        <option value="JP" <?php selected($data['address_country'] ?? '', 'JP'); ?>>日本 (JP)</option>
                        <option value="US" <?php selected($data['address_country'] ?? '', 'US'); ?>>アメリカ (US)</option>
                        <option value="GB" <?php selected($data['address_country'] ?? '', 'GB'); ?>>イギリス (GB)</option>
                        <option value="FR" <?php selected($data['address_country'] ?? '', 'FR'); ?>>フランス (FR)</option>
                        <option value="DE" <?php selected($data['address_country'] ?? '', 'DE'); ?>>ドイツ (DE)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">緯度</th>
                <td>
                    <input type="number" step="any" name="gof_local_business_data[latitude]" value="<?php echo esc_attr($data['latitude'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: 35.6762</p>
                </td>
            </tr>
            <tr>
                <th scope="row">経度</th>
                <td>
                    <input type="number" step="any" name="gof_local_business_data[longitude]" value="<?php echo esc_attr($data['longitude'] ?? ''); ?>" class="regular-text" />
                    <p class="description">例: 139.6503</p>
                </td>
            </tr>
        </table>
        
        <div id="restaurant-fields" style="<?php echo ($data['business_type'] ?? '') === 'Restaurant' ? '' : 'display: none;'; ?>">
            <h3>レストラン固有の情報</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">料理ジャンル</th>
                    <td>
                        <input type="text" name="gof_local_business_data[serves_cuisine]" value="<?php echo esc_attr($data['serves_cuisine'] ?? ''); ?>" class="regular-text" />
                        <p class="description">例: 日本料理、イタリアン、フレンチ</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">価格帯</th>
                    <td>
                        <input type="text" name="gof_local_business_data[price_range]" value="<?php echo esc_attr($data['price_range'] ?? ''); ?>" class="regular-text" />
                        <p class="description">例: ¥1000-2000</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">メニューURL</th>
                    <td>
                        <input type="url" name="gof_local_business_data[menu_url]" value="<?php echo esc_attr($data['menu_url'] ?? ''); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>
        
        <h3>営業時間</h3>
        <table class="form-table">
            <?php
            $days = array(
                'monday' => '月曜日',
                'tuesday' => '火曜日',
                'wednesday' => '水曜日',
                'thursday' => '木曜日',
                'friday' => '金曜日',
                'saturday' => '土曜日',
                'sunday' => '日曜日'
            );
            
            foreach ($days as $day => $label) {
                $opens = $data[$day . '_opens'] ?? '';
                $closes = $data[$day . '_closes'] ?? '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label); ?></th>
                    <td>
                        <input type="time" name="gof_local_business_data[<?php echo $day; ?>_opens]" value="<?php echo esc_attr($opens); ?>" />
                        〜
                        <input type="time" name="gof_local_business_data[<?php echo $day; ?>_closes]" value="<?php echo esc_attr($closes); ?>" />
                        <label>
                            <input type="checkbox" class="closed-checkbox" data-day="<?php echo $day; ?>" <?php checked($opens === 'none' || $closes === 'none'); ?> />
                            定休日
                        </label>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
}

// 構造化データ管理画面初期化
GOF_Structured_Data_Admin::get_instance();

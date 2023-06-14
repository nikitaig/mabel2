<?php
/**
 * Options Action.
 *
 * @package WOPB\Notice
 * @since v.1.0.0
 */
namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Options class.
 */
class Options{

    /**
     * Setup class.
     *
     * @since v.1.1.0
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'menu_page_callback' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_action( 'in_admin_header', array($this, 'remove_all_notices') );
        add_filter( 'plugin_row_meta', array( $this, 'plugin_settings_meta' ), 10, 2 );
        add_filter( 'plugin_action_links_'.WOPB_BASE, array( $this, 'plugin_action_links_callback' ) );
    }

    /**
     * Remove All Notification From Menu Page
     *
     * @since v.1.0.0
     * @return NULL
     */
    public static function remove_all_notices() {
        $curret_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if ($curret_page === 'wopb-settings' ||
            $curret_page === 'wopb-features' ||
            $curret_page === 'wopb-addons' ||
            $curret_page === 'wopb-option-settings' ||
            $curret_page === 'wopb-contact' ||
            $curret_page === 'wopb-initial-setup-wizard' ||
            $curret_page === 'wopb-license'
        ) {
            remove_all_actions( 'admin_notices' );
            remove_all_actions( 'all_admin_notices' );
        }
    }


    /**
     * Plugins Settings Meta Menu Add
     *
     * @since v.1.0.0
     * @return NULL
     */
    public function plugin_settings_meta( $links, $file ) {
        if ( strpos( $file, 'product-blocks.php' ) !== false ) {
            $new_links = array(
                'wopb_docs' =>  '<a href="https://docs.wpxpo.com/" target="_blank">'. esc_html__('Docs', 'product-blocks').'</a>',
                'wopb_tutorial' =>  '<a href="https://www.youtube.com/watch?v=JZxIflYKOuM&list=PLPidnGLSR4qcAwVwIjMo1OVaqXqjUp_s4" target="_blank">'.esc_html__('Tutorials', 'product-blocks').'</a>',
                'wopb_support' =>  '<a href="'.esc_url(wopb_function()->get_premium_link('https://www.wpxpo.com/contact/', 'plugin_list_wpxpo_support')).'" target="_blank">'.esc_html__('Support', 'product-blocks').'</a>'
            );

            $links = array_merge( $links, $new_links );
        }
        return $links;
    }


    /**
     * Plugins Settings Meta Pro Link Add
     *
     * @since v.1.1.0
     * @return NULL
     */
    public function plugin_action_links_callback ( $links ) {
        $upgrade_link = array();
        $setting_link = array();
        if(!defined('WOPB_PRO_VER')){
            $upgrade_link = array(
                'wopb_pro' => '<a href="'.esc_url(wopb_function()->get_premium_link("", 'plugin_list_productx_go_pro')).'" target="_blank"><span style="color: #e83838; font-weight: bold;">'.esc_html__('Upgrade to Pro', 'product-blocks').'</span></a>'
            );
        }
        $setting_link['wopb_settings'] = '<a href="'. esc_url(admin_url('admin.php?page=wopb-settings#settings')) .'">'. esc_html__('Settings', 'wp-megamenu') .'</a>';
        return array_merge( $setting_link, $links, $upgrade_link);
    }


    /**
     * Plugins Menu Page Added
     *
     * @since v.1.0.0
     * @return NULL
     */
    public static function menu_page_callback() {
        $wopb_menu_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MCA1MCI+PHBhdGggZD0iTTQxLjggMTIuMDdoLTUuMjZ2LS41NEMzNi41NCA1LjE4IDMxLjM3IDAgMjUgMGMtNi4zNiAwLTExLjU0IDUuMTgtMTEuNTQgMTEuNTR2LjU0SDguMjFjLTQuMjkgMC03LjU2IDMuODItNi44OSA4LjA2bDMuNzkgMjMuOTlDNS42NCA0Ny41IDguNTYgNTAgMTIgNTBoMjYuMDFjMy40MyAwIDYuMzYtMi41IDYuODgtNS44OGwzLjc5LTIzLjk5Yy42Ny00LjI0LTIuNjEtOC4wNi02Ljg4LTguMDZ6TTE2IDExLjU0YzAtNC45NiA0LjA0LTkgOS05czkgNC4wNCA5IDl2LjU0SDE2di0uNTR6bTExLjk5IDEyLjQ4YzAgLjE0LS4wNC4zLS4xMi40NGwtMS41MiAyLjctMS4xIDEuOTJjLS4xMS4xOS0uMzguMTktLjQ5IDBsLTIuNjMtNC42MmMtLjMzLS42LjEtMS4zMy43Ny0xLjMzaDQuMTljLjUzIDAgLjkuNDMuOS44OXptLTcuNTYgMTIuNzFjLS4zMy42LTEuMi42LTEuNTUgMGwtLjY1LTEuMTUtNS4yNS05LjIzYy0uNjUtMS4xNC0uMi0yLjUuODEtMy4xMi4yNS0uMTUuNTUtLjI3Ljg2LS4zMS4xMi0uMDIuMjMtLjAyLjM1LS4wMmgyLjE0YzEuMSAwIDIuMS41OCAyLjYzIDEuNTJsLjIzLjM5IDIuMzEgNC4wOC44OCAxLjU0Yy4yNS40NS4yNSAxLjAxIDAgMS40NmwtMiAzLjUxLS43NiAxLjMzem02LjY3IDIuNDVoLTQuMmMtLjUxIDAtLjg5LS40My0uODktLjg5IDAtLjE1LjA0LS4zLjEyLS40NGwxLjU0LTIuNjkgMS4xLTEuOTNjLjExLS4xOS4zOC0uMTkuNDkgMGwyLjYyIDQuNjJhLjg5My44OTMgMCAwMS0uNzggMS4zM3ptOS45NC0xMi44M2wtNS45MiAxMC4zOGMtLjMzLjYtMS4yLjYtMS41NSAwbC0uNzUtMS4zMi0xLjk5LTMuNTFjLS4yNi0uNDUtLjI2LTEuMDEgMC0xLjQ2bC44Ny0xLjU0IDIuMzItNC4wOC4yMS0uMzlhMy4wNCAzLjA0IDAgMDEyLjYzLTEuNTJoMi4xNWMuNDUgMCAuODYuMTIgMS4yLjMzYTIuMjkgMi4yOSAwIDAxLjgzIDMuMTF6IiBmaWxsPSIjYTdhYWFkIi8+PC9zdmc+';

        add_menu_page(
            esc_html__( 'ProductX', 'product-blocks' ),
            esc_html__( 'ProductX', 'product-blocks' ),
            'manage_options',
            'wopb-settings',
            array(self::class, 'tab_page_content'),
            $wopb_menu_icon,
            58.5
        );
        add_submenu_page(
            'wopb-settings',
            esc_html__( 'Getting Started', 'product-blocks' ),
            esc_html__( 'Getting Started', 'product-blocks' ),
            'manage_options',
            'wopb-settings'
        );

        if (wopb_function()->get_setting('wopb_templates') == 'true') {
            add_submenu_page(
                'wopb-settings',
                esc_html__( 'Saved Templates', 'product-blocks' ),
                esc_html__( 'Saved Templates', 'product-blocks' ),
                'manage_options',
                'edit.php?post_type=wopb_templates',
                ''
            );
        }

        if (wopb_function()->get_setting('wopb_builder') == 'true') {
            add_submenu_page(
                'wopb-settings',
                esc_html__( 'WooCommerce Builder', 'product-blocks' ),
                esc_html__( 'WooCommerce Builder', 'product-blocks' ),
                'manage_options',
                'admin.php?page=wopb-settings#builder',
                ''
            );
        }

        $menu_lists = array(
            array(
                'name' => esc_html__( 'Template Kit', 'product-blocks' ),
                'slug' => 'templatekit'
            ),
            array(
                'name' => esc_html__( 'Addons', 'product-blocks' ),
                'slug' => 'addons'
            ),
            array(
                'name' => esc_html__( 'Blocks', 'product-blocks' ),
                'slug' => 'blocks'
            ),
            array(
                'name' => esc_html__( 'Settings', 'product-blocks' ),
                'slug' => 'settings'
            ),
            array(
                'name' => esc_html__( 'Tutorials', 'product-blocks' ),
                'slug' => 'tutorials'
            ),
        );
        foreach ($menu_lists as $key => $val) {
            add_submenu_page(
                'wopb-settings',
                $val['name'],
                $val['name'],
                'manage_options',
                'wopb-settings#' . $val['slug'],
                array(__CLASS__, 'render_main')
            );
        }

        if( !function_exists('wopb_pro_function') ){
            global $submenu;
            $submenu['wopb-settings'][] = array( '<span class="wopb-dashboard-upgrade"><span class="dashicons dashicons-update"></span> Upgrade to Pro</span>', 'manage_options', esc_url(wopb_function()->get_premium_link("", 'main_menu_go_pro')) );
        }
    }


    /**
     * Register a setting and its sanitization callback.
     *
     * @since v.1.0.0
     * @return NULL
     */
    public static function register_settings() {
       register_setting( 'wopb_options', 'wopb_options', array( self::class, 'sanitize' ) );
    }

    /**
     * Sanitization Callback Add.
     *
     * @since v.1.0.0
     * @return NULL
     */
    public static function sanitize( $options ) {
        if ($options) {
            $settings = self::get_option_settings();
            foreach ($settings as $key => $setting) {
                if (!empty($key)) {
                    $options[$key] = sanitize_text_field($options[$key]);
                }
            }
        }
        return $options;
    }


    /**
     * General Option Settings
     *
     * @since v.1.0.0
     * @return NULL
     */
    public static function get_option_settings(){
        return array(
            'css_save_as' => array(
                'type' => 'select',
                'label' => __('CSS Save Method', 'product-blocks' ),
                'options' => array(
                    'wp_head'   => __( 'Header','product-blocks' ),
                    'filesystem' => __( 'File System','product-blocks' ),
                ),
                'default' => 'wp_head',
                'desc' => __('Select where you want to save CSS.', 'product-blocks' )
            ),
            'container_width' => array(
                'type' => 'number',
                'label' => __('Container Width', 'product-blocks' ),
                'default' => '1140',
                'desc' => __('Change Container Width.', 'product-blocks' )
            ),
            'hide_import_btn' => array(
                'type' => 'switch',
                'label' => __('Hide Import Button', 'product-blocks'),
                'default' => '',
                'desc' => __('Hide Import Layout Button from the Gutenberg Editor.', 'product-blocks')
            ),
        );
    }

    /**
     * Content of Tab Page
     *
     * @return STRING
     */
    public static function tab_page_content() {
        $current = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if ($current) {
            $pro_active = wopb_function()->is_lc_active();
            echo '<div class="wopb-settings-tab-wrap">';
                if (date('U') < strtotime('28-04-2023')) {
                    echo '<div class="wopb-setting-hellobar"><span class="dashicons dashicons-bell wopb-ring"></span><strong> Eid Sale: </strong> Enjoy Up to <b>50% Off</b> on ProductX Pro! <a href="'.esc_url(wopb_function()->get_premium_link( 'https://www.wpxpo.com/productx/pricing', 'dashboard-topbar')).'" target="_blank">'.esc_html__('GET IT NOW', 'product-blocks').'</a></div>';
                }else {
                    if (!$pro_active) {
                        echo '<div class="wopb-setting-hellobar"><span class="dashicons dashicons-bell wopb-ring"></span><strong> WordPress Birthday Offer: </strong> Up to <b>55% OFF</b> on ProductX Pro <a href="' . esc_url(wopb_function()->get_premium_link('', 'menu_topbar')) . '" target="_blank">' . esc_html__('Get It Now', 'product-blocks') . '</a></div>';
                    }
                }
                echo '<div class="wopb-setting-header">';
                    echo '<div class="wopb-setting-logo">';
                        echo '<img class="wopb-setting-header-img" loading="lazy" src="'.esc_url(WOPB_URL.'assets/img/logo-option.svg').'" alt="'.esc_html__('ProductX', 'product-blocks').'">';
                        echo '<span>v'. esc_html(WOPB_VER).'</span>';
                    echo '</div>';
                    echo '<ul class="wopb-settings-tab">';
                        echo '<li class="current"><a href="#home">'.esc_html__('Getting Started', 'product-blocks').'</a></li>';
                        if (wopb_function()->get_setting('wopb_templates') != 'false') {
                            echo '<li><a href="'.esc_url(admin_url( 'edit.php?post_type=wopb_templates' )).'">'.esc_html__('Saved Template', 'product-blocks').'</a></li>';
                        }
                        if (wopb_function()->get_setting('wopb_builder') != 'false') {
                            echo '<li><a href="#builder">'.esc_html__('WooCommerce Builder', 'product-blocks').'</a></li>';
                        }
                        echo '<li><a href="#templatekit">'.esc_html__('Template kit', 'product-blocks').'</a></li>';
                        echo '<li><a href="#addons">'.esc_html__('Addons', 'product-blocks').'</a></li>';
                        echo '<li><a href="#blocks">'.esc_html__('Blocks', 'product-blocks').'</a></li>';
                        echo '<li><a href="#settings">'.esc_html__('Settings', 'product-blocks').'</a></li>';
                        echo '<li><a href="#tutorials">'.esc_html__('Tutorials', 'product-blocks').'</a></li>';
                    echo '</ul>';
                echo '</div>';
                echo '<ul class="wopb-settings-content">';
                    echo '<li class="current" data-tab="#home">';
                        require_once WOPB_PATH . 'classes/options/Getting_Started.php';
                    echo '</li>';
                    echo '<li data-tab="#addons">';
                        require_once WOPB_PATH . 'classes/options/Addons.php';
                        new \WOPB\Options_Addons();
                    echo '</li>';
                    echo '<li data-tab="#blocks">';
                        require_once WOPB_PATH . 'classes/options/Blocks.php';
                        new \WOPB\Option_Blocks();
                    echo '</li>';
                    if (wopb_function()->get_setting('wopb_builder') != 'false') {
                        echo '<li data-tab="#builder">';
                            echo '<div id="wopb-builder-condition"></div>';
                        echo '</li>';
                    }
                    echo '<li data-tab="#templatekit">';
                        echo '<div id="wopb-dashboard-templatekit"></div>';
                    echo '</li>';

                    echo '<li data-tab="#settings">';
                        require_once WOPB_PATH . 'classes/options/Settings.php';
                        $obj = new \WOPB\Options_Settings();
                        $obj->create_admin_page();
                    echo '</li>';
                    echo '<li data-tab="#tutorials">';
                        require_once WOPB_PATH . 'classes/options/Tutorials.php';
                    echo '</li>';
                echo '<ul>';
            echo '</div>';
        }
    }
}
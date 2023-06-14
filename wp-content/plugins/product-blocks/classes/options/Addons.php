<?php
namespace WOPB;

defined('ABSPATH') || exit;

class Options_Addons{
    public function __construct() {
        $this->create_admin_page();
    }

    // For Preview Data
    public function wopb_productx_tab_data( $product_data_tabs ) {
        $product_data_tabs['productx'] = array(
            'label'  => __( 'ProductX Preorder', 'product-blocks' ),
            'class'  => array( 'show_if_simple', 'hidden' ),
            'target' => 'productx_tab_data',
        );
        return $product_data_tabs;
    }
    public function wopb_productx_custom_field_simple(){
        global $post;
        echo '<div class="panel woocommerce_options_panel hidden" id="productx_tab_data">';
            echo '<div class="wopb-productx-options-tab-wrap">';
                echo '<div id="wopb-preorder-field-group-pro-instruction">';
                    echo '<a href="https://www.wpxpo.com/productx" target="_blank">';
                        echo '<img style="max-width: 100%;" src="'.esc_url(WOPB_URL.'/assets/img/addons/preorder-pro.png').'">';
                    echo '</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * Contents of Addons Configure
     */
    public function create_admin_page() { ?>
        <div class="wopb-dashboard-container">

             <!-- addons content -->
                <div class="wopb-dashboard-body wopb-addons-content">

                    <!-- Pro Popup Container -->
                    <?php
                        wopb_function()->pro_popup_html();
                        wopb_function()->pro_updated_feature_popup();
                    ?>

                    <h3 class="wopb-md-heading wopb-mb25">
                        <?php _e('All Addons', 'product-blocks'); ?>
                    </h3>

                    <div class="wopb-addons-grid">
                        <?php
                            $settings = apply_filters('wopb_settings', []);
                            $option_value = wopb_function()->get_setting();
                            $addons_data = wopb_function()->all_addons();
                            foreach ($addons_data as $key => $val) {
                                $require_plugin = '';
                                if (isset($val['required'])) {
                                    $active_plugins = get_option( 'active_plugins', array() );
                                    if (is_multisite()) {
                                        $active_plugins = array_merge($active_plugins, array_keys(get_site_option( 'active_sitewide_plugins', array() )));
                                    }
                                    if ( !in_array($val['required']['slug'], apply_filters('active_plugins', $active_plugins)) ) {
                                        $require_plugin = $val['required']['name'];
                                    }
                                }
                                ?>
                                <div class="wopb-content-addon wopb-card wopb-p25">
                                    <div class="wopb-content-addon-container">
                                        <div class="wopb-content-meta">
                                            <img src="<?php echo esc_url($val['img']); ?>" alt="<?php echo esc_attr($val['name']); ?>">
                                        </div>
                                        <div class="wopb-addons-option-wrapper">
                                            <div class="wopb-addons-option-control">
                                                <h4 class="wopb-xs-heading"><?php echo esc_html($val['name']); ?></h4>
                                                <div class="wopb-control-option">
                                                    <?php
                                                    if (isset($val['docs'])) { ?>
                                                        <a href="<?php echo esc_url($val['docs']); ?>" class="wopb-option-tooltip" target="_blank">
                                                            <span><?php esc_html_e('Documentation', 'product-blocks'); ?></span>
                                                            <div class="dashicons dashicons-book"></div>
                                                        </a>
                                                    <?php } ?>

                                                    <?php
                                                    if (isset($val['live'])) { ?>
                                                        <a href="<?php echo esc_url($val['live']); ?>" class="wopb-option-tooltip" target="_blank">
                                                            <span><?php esc_html_e('Live View', 'product-blocks'); ?></span>
                                                            <div class="dashicons dashicons-visibility"></div>
                                                        </a>
                                                    <?php } ?>

                                                    <input type="checkbox" data-type="<?php echo esc_attr($key); ?>" class="wopb-addons-enable <?php echo (($val['is_pro'] && (!defined('WOPB_PRO_VER'))) ? 'disabled' : ''); echo isset($val['is_exist']) && !$val['is_exist'] && wopb_function()->is_lc_active() ? ' wopb-updated-pro-feature' : ''; ?>" id="<?php echo esc_attr($key); ?>" <?php echo ( isset($option_value[$key]) && $option_value[$key] == 'true' ? ($val['is_pro'] && !wopb_function()->is_lc_active() || (isset($val['is_exist']) && !$val['is_exist']) ? '' : 'checked') : '' ); ?>/>
                                                    <label class="wopb-toggle-switch" for="<?php echo esc_attr($key); ?>">
                                                        <?php if ($val['is_pro'] && (!defined('WOPB_PRO_VER'))) { ?>
                                                            <span class="dashicons dashicons-lock"></span>
                                                        <?php } ?>
                                                    </label>

                                                    <?php
                                                    if (isset($settings[$key])) { ?>
                                                        <span class="wopb-block-settings dashicons dashicons-admin-generic"></span>
                                                        <div class="wopb-popup-container blocks-settings">
                                                            <div class="wopb-unlock-popup">
                                                                <form action="">
                                                                    <?php
                                                                        require_once WOPB_PATH.'classes/options/Settings.php';
                                                                        $obj = new \WOPB\Options_Settings();
                                                                        $obj->get_settings_data($settings[$key]['attr']);
                                                                    ?>
                                                                    <div class="wopb-data-message"></div>
                                                                    <div>
                                                                        <button class="wopb-addons-setting-save"><?php esc_html_e('Save Settings', 'product-blocks'); ?></button>
                                                                    </div>
                                                                </form>
                                                                <button class="wopb-popup-close"></button>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="wopb-addon-desc wopb-sm-text"><?php echo esc_html($val['desc']); ?></div>
                                        </div>
                                    </div>
                                    <?php if ($require_plugin) { ?>
                                        <div class="wopb-plugin-required">
                                            <?php esc_html_e('This addon required this plugin:', 'product-blocks'); ?> <b><?php echo wp_kses($require_plugin, 'post'); ?></b>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                    </div>
            </div>
        </div>

    <?php }

}
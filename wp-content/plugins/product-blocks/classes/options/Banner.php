<?php
/**
 * Notice Action.
 * 
 * @package ULTP\Notice
 * @since v.2.6.0
 */
namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Functions class.
 */
class Banner{
    public function sidebar_additional_feature($tab = null) {
        $explore_more_link = wopb_function()->get_premium_link("https://www.wpxpo.com/productx/addons/", '');
        if($tab == 'getting_started') {
            $explore_more_link = wopb_function()->get_premium_link("https://www.wpxpo.com/productx/addons/", 'menu_started_explore_more');
        }elseif($tab == 'settings') {
            $explore_more_link = wopb_function()->get_premium_link("https://www.wpxpo.com/productx/addons/", 'menu_settings_explore_more');
        }
        ?>
        <div class="wopb-sidebar wopb-admin-card">
                                <h3><?php esc_html_e('ProductX Additional Features', 'product-blocks'); ?> <span class="wopb-free-text"><?php esc_html_e('(Free)'); ?></span></h3>
                                <ul class="wopb-sidebar-list">
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Variations Swatches', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Product Quick View', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Wishlist', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Product Compare', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Product Image Flipper', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Saved Template', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('Elementor, Divi, and Shortcode Support', 'product-blocks'); ?></li>
                                </ul>
                                <a href="<?php echo esc_url($explore_more_link); ?>" target="_blank" class="wopb-btn wopb-btn-transparent"><?php esc_html_e('Explore More', 'product-blocks'); ?></a>
                            </div>
    <?php }

    public function sidebar_content_rate() { ?>
        <div class="wopb-sidebar wopb-admin-card wopb-aside-content-rate">
            <div class="wopb-aside-heading wopb-getstart-title">
                <span class="dashicons dashicons-star-filled"></span>
                <?php esc_html_e('Show your love', 'product-blocks'); ?>
            </div>
            <p class="wopb-support-desc">
                <?php esc_html_e('Enjoying ProductX? Give us a review to support our endless work.', 'product-blocks'); ?>
            </p>
            <a href="https://wordpress.org/plugins/product-blocks/#reviews" class="wopb-start-btn" target="_blank">
                <?php esc_html_e('Rate it now', 'product-blocks'); ?>
            </a>
        </div>
    <?php }
}
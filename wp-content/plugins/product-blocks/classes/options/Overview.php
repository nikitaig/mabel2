<?php
namespace WOPB;

defined('ABSPATH') || exit;

class Options_Overview{
    public function __construct() {
        add_submenu_page('wopb-settings', __('Getting Started', 'product-blocks'), __('Getting Started', 'product-blocks'), 'manage_options', 'wopb-settings', array( self::class, 'create_admin_page'), 0);
    }
    /**
     * Settings page output
     */
    public static function create_admin_page() { ?>
        <style>
            .style-css{
                background-color: #f2f2f2;
                -webkit-font-smoothing: subpixel-antialiased;
            }
        </style>

        <div class="wopb-option-body">
            
            <?php require_once WOPB_PATH . 'classes/options/Heading.php'; ?>
            
            <div class="wopb-tab-wrap">
                <div class="wopb-content-wrap">
                    <div class="wopb-overview-wrap">
                        <div class="wopb-tab-content-wrap">
                            <div class="wopb-overview wopb-admin-card">
                                <iframe width="650" height="350" src="https://www.youtube.com/embed/bR2RPDtrFq4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                <div class="wopb-overview-btn">
                                    <a href="https://wopb.wpxpo.com/" target="_blank" class="wopb-btn wopb-btn-primary"><?php esc_html_e('Live Demo', 'product-blocks'); ?></a>
                                    <a class="wopb-btn wopb-btn-transparent" target="_blank" href="<?php echo esc_url(wopb_function()->get_premium_link("https://www.wpxpo.com/productx/", 'productx_dashboard_details')); ?>"><?php esc_html_e('Plugin Details', 'product-blocks'); ?></a>
                                </div>
                            </div>

                            <div class="wopb-features">
                                <div class="wopb-text-center"><h2 class="wopb-admin-title"><?php esc_html_e('Why Choose ProductX?', 'product-blocks'); ?></h2></div>
                                <div class="wopb-feature-items">
                                    <div class="wopb-feature-item">    
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/product_filter.jpg'); ?>" alt="Product Filter">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('Product Filter', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('The Ajax-Powered filtering feature helps you to let shoppers filter products by categories, on sale, top sale, most rated, featured, new arrival, etc.'); ?></div>
                                        </div>
                                    </div>
                                    <div class="wopb-feature-item">    
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/product_sorting.jpg'); ?>" alt="Product Sorting">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('Product Sorting', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('Sort Products by multiple criteria including most popular, recent viewed, random, most rated most sold, etc using the advanced '); ?> <strong><?php esc_html_e('Query Builder.'); ?></strong></div>
                                        </div>
                                    </div>
                                    <div class="wopb-feature-item">    
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/product_pagination.jpg'); ?>" alt="Product Pagination">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('Product Pagination', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('Enable two types of paginations on shop, categories, or any product archive pages. You can also customize it according to your needs.'); ?></div>
                                        </div>
                                    </div>
                                    <div class="wopb-feature-item">    
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/variation_swatches.jpg'); ?>" alt="Variation Swatches">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('Variation Swatches', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('Convert dropdown variation selection options to beautiful buttons and you can also create images, labels, and color swatches from scratch.'); ?></div>
                                        </div>
                                    </div>
                                    <div class="wopb-feature-item">    
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/wooCommerce_builder.jpg'); ?>" alt="WooCommerce Builder">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('WooCommerce Builder', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('Sort the posts and pages based on multiple criteria that include categories, tags, popular, recent, related, most commented, and much more.', 'product-blocks'); ?></div>
                                        </div>
                                    </div>
                                    <div class="wopb-feature-item">
                                        <div class="wopb-feature-image">    
                                            <img loading="lazy" src="<?php echo esc_url(WOPB_URL.'assets/img/admin/dashboard/design_library.jpg'); ?>" alt="Design Library">        
                                        </div>
                                        <div class="wopb-feature-content">    
                                            <h4><?php esc_html_e('Design Library', 'product-blocks'); ?></h4> 
                                            <div><?php esc_html_e('ProductX offers multiple starter packs and numerous variations of Blocks that can be imported from the Block Library.'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wopb-text-center">
                                    <a class="wopb-btn wopb-btn-primary" target="_blank" href="<?php echo esc_url(wopb_function()->get_premium_link("https://www.wpxpo.com/productx/", 'productx_dashboard_explore_features')); ?>"><?php esc_html_e('Explore All Features', 'product-blocks'); ?></a>
                                </div>

                            </div>

                            <div class="wopb-getstart-documentation">
                                <div>
                                    <div class="wopb-getstart-title">
                                        <?php esc_html_e('Easy Documentation', 'product-blocks'); ?>
                                    </div>
                                    <div class="wopb-documentation-desc">
                                        <?php esc_html_e('Get step-by-step guides on the best ways to use ProductX.', 'product-blocks'); ?>
                                    </div>
                                </div>
                                <a href="https://docs.wpxpo.com/docs/productx/" target="_blank"><?php esc_html_e('Documentation', 'product-blocks'); ?></a>
                            </div>

                        </div><!--/overview-->

                        <div class="wopb-admin-sidebar">
                        <?php if (!wopb_function()->isActive()) { ?>
                            <div class="wopb-sidebar wopb-admin-card">
                                <h3><?php esc_html_e('Why Upgrade to ProductX Premium', 'product-blocks'); ?></h3>
                                <ul class="wopb-sidebar-list">
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Single Product Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Shop Page Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Cart Page Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Checkout Page Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Thank You Page Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('My Account Page Builder', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Access Unlimited Design Library', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Partial Payment', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Back-order', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Pre-order', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Stock Progress Bar', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Product Deals', 'product-blocks'); ?></li>
                                    <li><span class="dashicons dashicons-plus"></span><?php esc_html_e('Fast & Priority Support', 'product-blocks'); ?></li>
                                </ul>
                                <a href="<?php echo esc_url(wopb_function()->get_premium_link("https://www.wpxpo.com/productx/pricing/", 'productx_sidebar_upgrade_pro')); ?>" target="_blank" class="wopb-btn wopb-btn-primary wopb-btn-pro"><?php esc_html_e('Upgrade to Pro  ➤', 'product-blocks'); ?></a>
                            </div>
                            <?php } ?>

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
                                <a href="<?php echo esc_url(wopb_function()->get_premium_link("https://www.wpxpo.com/productx/addons/", 'menu_started_explore_more')); ?>" target="_blank" class="wopb-btn wopb-btn-transparent"><?php esc_html_e('Explore More', 'product-blocks'); ?></a>
                            </div>


                            <div class="wopb-sidebar wopb-admin-card">
                                <div class="wopb-aside-heading wopb-getstart-title">
                                    <span class="dashicons dashicons-facebook"></span>
                                    <?php esc_html_e('Join Facebook Group', 'product-blocks'); ?>
                                </div>
                                <p class="wopb-support-desc">
                                    <?php esc_html_e('You can discuss anything related to ProductX. You can share your issues, tips, and idea with the group member.', 'product-blocks'); ?>
                                </p>
                                <a href="https://www.facebook.com/groups/woocommerceproductx/?ref=share" class="wopb-btn wopb-btn-transparent" target="_blank">
                                    <?php esc_html_e('Join Facebook Group', 'product-blocks'); ?>
                                </a>
                            </div>
                            <div class="wopb-sidebar wopb-admin-card">
                                <div class="wopb-aside-heading wopb-getstart-title">
                                    <span class="dashicons dashicons-media-document"></span>
                                    <?php esc_html_e('Changelog', 'product-blocks'); ?>
                                </div>
                                <p class="wopb-support-desc">
                                    <?php esc_html_e('Be up to date with all the latest features, bug, and error fixing updates of ProductX.', 'product-blocks'); ?>
                                </p>
                                <a href="<?php echo esc_url(wopb_function()->get_premium_link("https://www.wpxpo.com/productx/changelog/", 'menu_started_changelog')); ?>" class="wopb-btn wopb-btn-transparent" target="_blank">
                                    <?php esc_html_e('View Changelog', 'product-blocks'); ?>
                                </a>
                            </div>
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
                        </div><!--/sidebar-->
                    </div>
                </div><!--/overview wrapper-->

            </div>
        </div>

    <?php }
}


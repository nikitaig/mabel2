<?php
/**
 * Quickview Addons Core.
 *
 * @package WOPB\Quickview
 * @since v.1.1.0
 */

namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Quickview class.
 */
class Quickview
{

    /**
     * Setup class.
     *
     * @since v.1.1.0
     */
    public function __construct()
    {
        add_action('wp_ajax_wopb_quickview', array($this, 'wopb_quickview_callback'));
        add_action('wp_ajax_nopriv_wopb_quickview', array($this, 'wopb_quickview_callback'));
        add_action('wp_enqueue_scripts', array($this, 'add_quickview_scripts'));

        // Quickview in default woocommerce shop pages
        if ( wopb_function()->get_setting('quickview_shop_enable') == 'yes' ) {
            add_filter('woocommerce_loop_add_to_cart_link', array($this, 'wopb_show_quickview_in_shop') , 10 , 3);
        }

    }

    /**
     * Quickview Addons Initial Setup Action
     *
     * @return NULL
     * @since v.2.1.8
     */
    public function add_quickview_scripts()
    {
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-single-product');
        wp_enqueue_script('flexslider');

        wp_enqueue_style('wopb-quickview-style', WOPB_URL . 'addons/quickview/css/quickview.css', array(), WOPB_VER);
        wp_enqueue_script('wopb-quickview', WOPB_URL . 'addons/quickview/js/quickview.js', array('jquery', 'wp-api-fetch'), WOPB_VER);

        wp_localize_script('wopb-quickview', 'wopb_quickview', array(
            'ajax' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('wopb-nonce'),
            'isVariationSwitchActive' => wopb_function()->get_setting('wopb_variation_swatches')
        ));

    }


    /**
     * Quickview Addons Initial Setup Action
     *
     * @return NULL
     * @since v.1.1.0
     */
    public function initial_setup()
    {

        // Set Default Value
        $initial_data = array(
            'quickview_heading' => 'yes',
            'quickview_mobile_disable' => '',
            'quickview_text' => __('Quick View', 'product-blocks'),
            'quickview_navigation' => 'yes',
            'quickview_link' => 'yes',
            'quickview_gallery_enable' => 'yes',
            'quickview_cart_redirect' => '',
            'quickview_image_disable' => '',
            'quickview_sales_disable' => '',
            'quickview_rating_disable' => '',
            'quickview_title_disable' => '',
            'quickview_price_disable' => '',
            'quickview_excerpt_disable' => '',
            'quickview_stock_disable' => '',
            'quickview_sku_disable' => '',
            'quickview_cart_disable' => '',
            'quickview_category_disable' => '',
            'quickview_tag_disable' => '',
            'wopb_quickview' => 'true'
        );
        foreach ($initial_data as $key => $val) {
            wopb_function()->set_setting($key, $val);
        }
    }

    /**
     * Quickview Add Action Callback.
     *
     * @return HTML
     * @since v.1.1.0
     */
    public function wopb_quickview_callback()
    {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')) {
            return;
        }

        global $post;
        $post_id = sanitize_text_field($_POST['postid']);
        $post_list = sanitize_text_field($_POST['postList']);

        if ($post_id) {
            $args = array(
                'post_type' => 'product',
                'post__in' => array($post_id),
                'orderby' => 'date',
                'post_status' => 'publish',
                'order' => 'DESC'
            );
            $loop = new \WP_Query($args);
            $html = '';

            if ($loop->have_posts()) {
                while ($loop->have_posts()) {
                    $loop->the_post();
                    $post_id = get_the_ID();
                    $product = wc_get_product($post_id);

                    $html .= '<div class="wopb-modal-content woocommerce" data-product_id="' . esc_attr($post_id) . '">';
                    $html .= '<div class="single-product">';
                    $html .= '<div class="product">';
                    $html .= '<div class="wopb-quick-view-modal">';

                    if (wopb_function()->get_setting('quickview_image_disable') != 'yes') {
                        $html .= '<div class="wopb-quick-view-image">';
//                                        $html .= '<div class="wopb-thumbnails">';
                        ob_start();
                        woocommerce_show_product_images();
                        $html .= ob_get_clean();
//                                        $html .= '</div>';

                        // Sales Percentage
                        if (wopb_function()->get_setting('quickview_sales_disable') != 'yes') {
                            $save_percentage = ($product->get_sale_price() && $product->get_regular_price()) ? round(($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price() * 100) . '%' : '';
                            if ($save_percentage) {
                                $html .= '<span class="wopb-quick-view-sale">-';
                                    $html .= $save_percentage;
//                                    $html .=  '<span>' . __('Sale!', 'product-blocks') . '</span>';
                                $html .=  '</span>';
                            }
                        }

                        $html .= '</div>';
                    }

                    $html .= '<div class="wopb-quick-view-content' . (wopb_function()->get_setting('quickview_stock_disable') == 'yes' ? ' wopb-quick-view-disable-stock' : '') . '">';
                    $html .= '<div class="woocommerce-message wopb-d-none" role="alert"></div>';
                    ob_start();
                        woocommerce_template_single_title();
                    $product_title = ob_get_clean();
                    if (wopb_function()->get_setting('quickview_link') == 'yes') {
                        $product_title = '<a class="wopb-quick-title" href="' . $product->get_permalink() . '">' . $product_title . '</a>';
                    }

                    ob_start();
                    if (wopb_function()->get_setting('quickview_rating_disable') != 'yes') {
                        woocommerce_template_single_rating();
                    }

                    if (wopb_function()->get_setting('quickview_title_disable') != 'yes') {
                        echo $product_title;
                        echo '<div class="wopb-quick-divider"></div>';
                    }

                    if (wopb_function()->get_setting('quickview_price_disable') != 'yes') {
                        woocommerce_template_single_price();
                    }

                    if (wopb_function()->get_setting('quickview_excerpt_disable') != 'yes') {
                        woocommerce_template_single_excerpt();
                    }

                    if (wopb_function()->get_setting('quickview_cart_disable') != 'yes') {
                        add_action('woocommerce_before_quantity_input_field', [$this, 'quick_view_before_add_to_cart_quantity']);
                        add_action('woocommerce_after_quantity_input_field', [$this, 'quick_view_after_add_to_cart_quantity']);
                        woocommerce_template_single_add_to_cart();
                    }

                    if (wopb_function()->get_setting('quickview_meta_disable') != 'yes') {
                        woocommerce_template_single_meta();
                    }
                    $quickview_content = ob_get_clean();
                    $html .= $quickview_content;
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="wopb-modal-close"></div>';
                    $html .= '</div>';

                    $html .= '<div class="wopb-quick-view-navigation">';
                    if (wopb_function()->get_setting('quickview_navigation') == 'yes') {
                        // Previous Link
                        if ($post_list) {
                            $p_id = explode(',', $post_list);
                            $key = array_search($post_id, $p_id);
                            $key = isset($p_id[$key - 1]) ? $p_id[$key - 1] : '';
                            if ($key) {
                                $html .= '<div class="wopb-quick-view-previous wopb-quickview-btn" data-list="' . $post_list . '" data-postid="' . $key . '">';
                                $thumbnail = get_post_thumbnail_id($key);
                                $html .= '<span><svg class="icon" viewBox="0 0 64 64"><path id="arrow-left-1" d="M46.077 55.738c0.858 0.867 0.858 2.266 0 3.133s-2.243 0.867-3.101 0l-25.056-25.302c-0.858-0.867-0.858-2.269 0-3.133l25.056-25.306c0.858-0.867 2.243-0.867 3.101 0s0.858 2.266 0 3.133l-22.848 23.738 22.848 23.738z"></path></svg></span>';
                                $html .= '<div class="wopb-quick-view-btn-image">';
                                if ($thumbnail) {
                                    $t_img = wp_get_attachment_image_src($thumbnail, 'thumbnail');
                                    if (isset($t_img[0])) {
                                        $html .= '<img src="' . $t_img[0] . '" alt="' . get_the_title($key) . '" />';
                                    }
                                }
                                $html .= '<h4>' . get_the_title($key) . '</h4>';
                                $html .= '</div>';//wopb-quick-view-btn-image
                                $html .= '</div>';
                            }
                        }
                        // Next Link
                        if ($post_list) {
                            $p_id = explode(',', $post_list);
                            $key = array_search($post_id, $p_id);
                            $key = isset($p_id[$key + 1]) ? $p_id[$key + 1] : '';
                            if ($key) {
                                $html .= '<div class="wopb-quick-view-next wopb-quickview-btn" data-list="' . $post_list . '" data-postid="' . $key . '">';
                                $thumbnail = get_post_thumbnail_id($key);
                                $html .= '<span><svg class="icon" viewBox="0 0 64 64"><path id="arrow-right-1" d="M17.919 55.738c-0.858 0.867-0.858 2.266 0 3.133s2.243 0.867 3.101 0l25.056-25.302c0.858-0.867 0.858-2.269 0-3.133l-25.056-25.306c-0.858-0.867-2.243-0.867-3.101 0s-0.858 2.266 0 3.133l22.848 23.738-22.848 23.738z"></path></svg></span>';
                                $html .= '<div class="wopb-quick-view-btn-image">';
                                if ($thumbnail) {
                                    $t_img = wp_get_attachment_image_src($thumbnail, 'thumbnail');
                                    if (isset($t_img[0])) {
                                        $html .= '<img src="' . $t_img[0] . '" alt="' . get_the_title($key) . '" />';
                                    }
                                }
                                $html .= '<h4>' . get_the_title($key) . '</h4>';
                                $html .= '</div>';//wopb-quick-view-btn-image
                                $html .= '</div>';
                            }
                        }
                    }
                    $html .= '</div>';//wopb-quick-view-navigation
                }
                wp_reset_postdata();
                echo $html;
            }
        }
        die();
    }

    public function quick_view_before_add_to_cart_quantity()
    {
        echo '<span class="wopb-add-to-cart-minus">-</span>';
    }

    public function quick_view_after_add_to_cart_quantity()
    {
        echo '<span class="wopb-add-to-cart-plus">+</span>';
    }

    public function add_quickview_html($post_ids, $post_id) {
        $html = '';
        if ( wopb_function()->get_setting('quickview_mobile_disable') == 'yes' && wp_is_mobile() ) {} else {
            $html .= '<a data-list="'.esc_attr(implode(',', $post_ids )).'" data-postid="'.esc_attr($post_id).'" class="wopb-quickview-btn" href="#" defaultWooPage ="yes">';
                $quickview_text = wopb_function()->get_setting('quickview_text');
                $html .= esc_html($quickview_text);
            $html .= '</a>';
        }
        return $html;
    }
    // Wishlist show in default shop page
    public function wopb_show_quickview_in_shop($add_to_cart_html, $products, $args) {

        $post_id = $args["attributes"]["data-product_id"];
        $post_ids = get_posts( array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ));

        if(!wopb_function()->wopb_shop_builder_check() && is_shop()) {
            wp_enqueue_style('wopb-slick-style', WOPB_URL.'assets/css/slick.css', array(), WOPB_VER);
            wp_enqueue_style('wopb-slick-theme-style', WOPB_URL.'assets/css/slick-theme.css', array(), WOPB_VER);
        
            wp_enqueue_script('wopb-slick-script', WOPB_URL.'assets/js/slick.min.js', array('jquery'), WOPB_VER, true);
        
            wp_enqueue_style('wopb-style', WOPB_URL.'assets/css/blocks.style.css', array(), WOPB_VER );
            wp_enqueue_style('wopb-css', WOPB_URL.'assets/css/wopb.css', array(), WOPB_VER);

            $content_data = '';
            $content_data .= '<div class="wopb-quickview-btn">';
                $content_data .= $this->add_quickview_html($post_ids, $post_id);
            $content_data .= '</div>';

            $add_to_cart_html = $add_to_cart_html.$content_data;
        }
        
        return $add_to_cart_html;
    }
}

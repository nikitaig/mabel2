<?php
/**
 * Compatibility Action.
 * 
 * @package WOPB\Notice
 * @since v.1.1.0
 */
namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Blocks class.
 */
class Blocks {

    /**
	 * Setup class.
	 *
	 * @since v.1.1.0
	 */
    private $all_blocks;
    private $api_endpoint = 'https://demo.wpxpo.com/wp-json/restapi/v2/';

    /**
	 * Setup class.
	 *
	 * @since v.1.1.0
	 */
    public function __construct(){
        $this->blocks();
		add_action('wp_ajax_wopb_load_more',                    array($this, 'wopb_load_more_callback'));       // Next Previous AJAX Call
		add_action('wp_ajax_nopriv_wopb_load_more',             array($this, 'wopb_load_more_callback'));       // Next Previous AJAX Call
        add_action('wp_ajax_wopb_filter',                       array($this, 'wopb_filter_callback'));          // Next Previous AJAX Call
        add_action('wp_ajax_nopriv_wopb_filter',                array($this, 'wopb_filter_callback'));          // Next Previous AJAX Call
        add_action('wp_ajax_wopb_pagination',                   array($this, 'wopb_pagination_callback'));      // Page Number AJAX Call
        add_action('wp_ajax_nopriv_wopb_pagination',            array($this, 'wopb_pagination_callback'));      // Page Number AJAX Call
        add_action('wp_ajax_get_all_layouts',                   array($this, 'get_all_layouts_callback'));      // All Layout AJAX Call
        add_action('wp_ajax_nopriv_get_all_layouts',            array($this, 'get_all_layouts_callback'));      // All Layout AJAX Call
        add_action('wp_ajax_get_all_sections',                  array($this, 'get_all_sections_callback'));     // All Section AJAX Call
        add_action('wp_ajax_nopriv_get_all_sections',           array($this, 'get_all_sections_callback'));    // All Section AJAX Call
        add_action('wp_ajax_get_single_section',                array($this, 'get_single_section_callback'));   // Page Number AJAX Call
        add_action('wp_ajax_nopriv_get_single_section',         array($this, 'get_single_section_callback'));// Page Number AJAX Call
        add_action('wp_ajax_wopb_addcart',                      array($this, 'wopb_addcart_callback'));         // Add To Cart
        add_action('wp_ajax_nopriv_wopb_addcart',               array($this, 'wopb_addcart_callback'));         // Add To Cart
        add_action('wp_ajax_wopb_checkout_login',        array($this, 'wopb_checkout_login_callback'));       // Checkout Login AJAX Call
		add_action('wp_ajax_nopriv_wopb_checkout_login', array($this, 'wopb_checkout_login_callback'));       // Checkout Login AJAX Call
        add_action('wp_ajax_wopb_share_count',        array($this, 'wopb_share_count_callback'));       // Checkout Login AJAX Call
		add_action('wp_ajax_nopriv_wopb_share_count', array($this, 'wopb_share_count_callback'));       // Checkout Login AJAX Call
    }

    public function wopb_addcart_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local) {
            return ;
        }
        $product_id = sanitize_text_field($_POST['postid']);
        $quantity = sanitize_text_field($_POST['quantity']);
        $variationId = sanitize_text_field($_POST['variationId']);
        $variation = sanitize_text_field($_POST['variation']);
        if ($product_id) {
            global $woocommerce;
            WC()->cart->add_to_cart( $product_id, $quantity, $variationId, $variation );
            ob_start();

            woocommerce_mini_cart();

            $mini_cart = ob_get_clean();

            $data = array(
                'message' => wc_add_to_cart_message($product_id, $quantity, true),
                'fragments' => apply_filters(
                    'woocommerce_add_to_cart_fragments',
                    array(
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
                    )
                ),
                'cart_hash' => WC()->cart->get_cart_hash(),
            );
            wp_send_json($data);
        }
    }


    /**
	 * Require Blocks.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function blocks() {
        require_once WOPB_PATH.'blocks/Heading.php';
        require_once WOPB_PATH.'blocks/Product_Grid_1.php';
        require_once WOPB_PATH.'blocks/Product_Grid_2.php';
        require_once WOPB_PATH.'blocks/Product_Grid_3.php';
        require_once WOPB_PATH.'blocks/Product_Grid_4.php';
        require_once WOPB_PATH.'blocks/Product_List_1.php';
        require_once WOPB_PATH.'blocks/Product_Category_1.php';
        require_once WOPB_PATH.'blocks/Product_Category_2.php';
        require_once WOPB_PATH.'blocks/Product_Category_3.php';
        require_once WOPB_PATH.'blocks/Image.php';
        require_once WOPB_PATH.'blocks/Filter.php';
        require_once WOPB_PATH.'blocks/Currency_Switcher.php';
        require_once WOPB_PATH.'blocks/Product_Search.php';
        $this->all_blocks['product-blocks_heading'] = new \WOPB\blocks\Heading();
        $this->all_blocks['product-blocks_product-grid-1'] = new \WOPB\blocks\Product_Grid_1();
        $this->all_blocks['product-blocks_product-grid-2'] = new \WOPB\blocks\Product_Grid_2();
        $this->all_blocks['product-blocks_product-grid-3'] = new \WOPB\blocks\Product_Grid_3();
        $this->all_blocks['product-blocks_product-grid-4'] = new \WOPB\blocks\Product_Grid_4();
        $this->all_blocks['product-blocks_product-list-1'] = new \WOPB\blocks\Product_List_1();
        $this->all_blocks['product-blocks_product-category-1'] = new \WOPB\blocks\Product_Category_1();
        $this->all_blocks['product-blocks_product-category-2'] = new \WOPB\blocks\Product_Category_2();
        $this->all_blocks['product-blocks_product-category-2'] = new \WOPB\blocks\Product_Category_3();
        $this->all_blocks['product-blocks_image'] = new \WOPB\blocks\Image();
        $this->all_blocks['product-blocks_price-filter'] = new \WOPB\blocks\Filter();
        $this->all_blocks['product-blocks_currency_switcher'] = new \WOPB\blocks\Currency_Switcher();
        $this->all_blocks['product-blocks_product-search'] = new \WOPB\blocks\Product_Search();
        $settings = wopb_function()->get_setting('wopb_builder');
        require_once WOPB_PATH.'addons/builder/blocks/my_account/My_Account.php';
        $this->all_blocks['product-blocks_my-account'] = new \WOPB\blocks\My_Account();
        if ($settings == 'true') {
            require_once WOPB_PATH.'addons/builder/blocks/Archive_Title.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Title.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Short.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Price.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Description.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Stock.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Image.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Meta.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Additional_Info.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Cart.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Review.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Breadcrumb.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Rating.php';
            require_once WOPB_PATH.'addons/builder/blocks/Product_Tab.php';
            require_once WOPB_PATH.'addons/builder/blocks/cart_table/Cart_Table.php';
            require_once WOPB_PATH.'addons/builder/blocks/cart_total/Cart_Total.php';
            require_once WOPB_PATH.'addons/builder/blocks/free_shipping_progress_bar/Free_Shipping_Progress_Bar.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/coupon/Checkout_Coupon.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/billing/Checkout_Billing.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/shipping/Checkout_Shipping.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/order_review/Checkout_Order_Review.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/payment_method/Checkout_Payment_Method.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/additional_information/Checkout_Additional_Information.php';
            require_once WOPB_PATH.'addons/builder/blocks/checkout/login/Checkout_Login.php';
            require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_conformation/Order_Conformation.php';
            require_once WOPB_PATH.'addons/builder/blocks/thank_you/address/Thankyou_Address.php';
            require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_details/Thankyou_Order_Details.php';
            require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_payment/Order_Payment.php';
            require_once WOPB_PATH.'addons/builder/blocks/Social_Share.php';
            $this->all_blocks['product-blocks_archive-title'] = new \WOPB\blocks\Archive_Title();
            $this->all_blocks['product-blocks_product-title'] = new \WOPB\blocks\Product_Title();
            $this->all_blocks['product-blocks_product-short'] = new \WOPB\blocks\Product_Short();
            $this->all_blocks['product-blocks_product-price'] = new \WOPB\blocks\Product_Price();
            $this->all_blocks['product-blocks_product-description'] = new \WOPB\blocks\Product_Description();
            $this->all_blocks['product-blocks_product-stock'] = new \WOPB\blocks\Product_Stock();
            $this->all_blocks['product-blocks_product-image'] = new \WOPB\blocks\Product_Image();
            $this->all_blocks['product-blocks_product-meta'] = new \WOPB\blocks\Product_Meta();
            $this->all_blocks['product-blocks_product-additional-info'] = new \WOPB\blocks\Product_Additional_Info();
            $this->all_blocks['product-blocks_product-cart'] = new \WOPB\blocks\Product_Cart();
            $this->all_blocks['product-blocks_product-review'] = new \WOPB\blocks\Product_Review();
            $this->all_blocks['product-blocks_product-breadcrumb'] = new \WOPB\blocks\Product_Breadcrumb();
            $this->all_blocks['product-blocks_product-rating'] = new \WOPB\blocks\Product_Rating();
            $this->all_blocks['product-blocks_product-tab'] = new \WOPB\blocks\Product_Tab();
            $this->all_blocks['product-blocks_cart-table'] = new \WOPB\blocks\Cart_Table();
            $this->all_blocks['product-blocks_cart-total'] = new \WOPB\blocks\Cart_Total();
            $this->all_blocks['product-blocks_cart-shipping'] = new \WOPB\blocks\Free_Shipping_Progress_Bar();
            $this->all_blocks['product-blocks_checkout-coupon'] = new \WOPB\blocks\Checkout_Coupon();
            $this->all_blocks['product-blocks_checkout-billing'] = new \WOPB\blocks\Checkout_Billing();
            $this->all_blocks['product-blocks_checkout-shipping'] = new \WOPB\blocks\Checkout_Shipping();
            $this->all_blocks['product-blocks_checkout-review'] = new \WOPB\blocks\Checkout_Order_Review();
            $this->all_blocks['product-blocks_checkout-payment'] = new \WOPB\blocks\Checkout_Payment_Method();
            $this->all_blocks['product-blocks_checkout-info'] = new \WOPB\blocks\Checkout_Additional_Information();
            $this->all_blocks['product-blocks_checkout-login'] = new \WOPB\blocks\Checkout_Login();
            $this->all_blocks['product-blocks_thankyou-order-conformation'] = new \WOPB\blocks\Order_Conformation();
            $this->all_blocks['product-blocks_thankyou-address'] = new \WOPB\blocks\Thankyou_Address();
            $this->all_blocks['product-blocks_thankyou-order-details'] = new \WOPB\blocks\Thankyou_Order_Details();
            $this->all_blocks['product-blocks_thankyou-order-payment'] = new \WOPB\blocks\Order_Payment();
            $this->all_blocks['product-blocks_social-share'] = new \WOPB\blocks\Social_Share();
        }
    }

    
    /**
	 * Load More Action.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function wopb_load_more_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local){
            return ;
        }

        $paged      = sanitize_text_field($_POST['paged']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $builder    = isset($_POST['builder']) ? sanitize_text_field($_POST['builder']) : '';
        $blockName  = str_replace('_','/', $blockRaw);
        $widgetBlockId  = $_POST['widgetBlockId'];
        $params = [
          'filterAttributes' => $_POST['filterAttributes']
        ];

        if( $paged && $blockId && $postId && $blockName ) {
            $post = get_post($postId); 
    
            if($widgetBlockId) {
                $blocks = parse_blocks(get_option('widget_block')[$widgetBlockId]['content']);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName, $builder, $params);
            }elseif (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName, $builder, $params);
            }
        }
    }

    /**
     * Filter Callback of the Blocks
     *
     * @param $blocks
     * @param $paged
     * @param $blockId
     * @param $blockRaw
     * @param $blockName
     * @param $builder
     * @param string $params
     * @return STRING
     * @since v.2.1.4
     */
    public function block_return($blocks, $paged, $blockId, $blockRaw, $blockName, $builder, $params = []) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName']) {
                if($value['attrs']['blockId'] == $blockId) {
                    $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                    $value['attrs']['paged'] = $paged;
                    if ($builder) {
                        $value['attrs']['builder'] = $builder;
                    }
                    if($params['filterAttributes']) {
                        $attr = array_merge($attr, $params['filterAttributes']);
                    }
                    $attr = array_merge($attr, $value['attrs']);
                    if($params['ajax_source']) {
                        $attr['ajax_source'] = $params['ajax_source'];
                    }
                    wopb_function()->currency_switcher_require();
                    echo  $this->all_blocks[$blockRaw]->content($attr, true);
                    die();
                }
            }
            if(!empty($value['innerBlocks'])){
                $this->block_return($value['innerBlocks'], $paged, $blockId, $blockRaw, $blockName, $builder, $params);
            }
        }
    }


    public function filter_block_return($blocks, $blockId, $blockRaw, $blockName, $params = []) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName']) {
                if($value['attrs']['blockId'] == $blockId) {
                    $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                    $attr = array_merge($attr, $params);
                    $attr = array_merge($attr, $value['attrs']);
                    wopb_function()->currency_switcher_require();
                    echo  $this->all_blocks[$blockRaw]->content($attr, true);
                    die();
                }
            }
            if(!empty($value['innerBlocks'])){
                $this->filter_block_return($value['innerBlocks'], $blockId, $blockRaw, $blockName, $params);
            }
        }
    }

    /**
	 * Filter Callback.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function wopb_filter_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local){
            return ;
        }
     
        $taxtype    = sanitize_text_field($_POST['taxtype']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $taxonomy   = sanitize_text_field($_POST['taxonomy']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $blockName  = str_replace('_','/', $blockRaw);
        $widgetBlockId  = $_POST['widgetBlockId'];
        $params = [
            'page_post_id' => $postId,
            'current_url' => $_POST['currentUrl'],
            'queryTax' => $taxtype,
            'ajax_source' => 'filter',
        ];

        if($taxtype == 'product_cat' && $taxonomy) {
            $params['queryCatAction'] = array($taxonomy);
        }
        if($taxtype == 'product_tag' && $taxonomy) {
            $params['queryTagAction'] = array($taxonomy);
        }
        if ($taxonomy) {
            if (strpos($taxonomy, 'custom_action#') !== false) {
                $params['custom_action'] = $taxonomy;
            }
        }

        if( $taxtype ) {
            $post = get_post($postId);
            if($widgetBlockId) {
                $blocks = parse_blocks(get_option('widget_block')[$widgetBlockId]['content']);
                $this->filter_block_return($blocks, $blockId, $blockRaw, $blockName, $params);
            }elseif (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->filter_block_return($blocks, $blockId, $blockRaw, $blockName, $params);
            }
        }
    }


    /**
	 * Pagination Callback.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function wopb_pagination_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local) {
            return ;
        }

        $paged      = sanitize_text_field($_POST['paged']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $builder    = isset($_POST['builder']) ? sanitize_text_field($_POST['builder']) : '';
        $blockName  = str_replace('_','/', $blockRaw);
        $widgetBlockId  = $_POST['widgetBlockId'];
        $params = [
          'filterAttributes' => $_POST['filterAttributes'],
          'ajax_source' => 'pagination',
        ];

        if($paged) {
            $post = get_post($postId);
            if($widgetBlockId) {
                $blocks = parse_blocks(get_option('widget_block')[$widgetBlockId]['content']);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName, $builder, $params);
            }elseif (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName, $builder, $params);
            }
        }
    }


    /**
	 * All Layout Callback.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function get_all_layouts_callback() {
        $request_data = wp_remote_post($this->api_endpoint.'layouts', array('timeout' => 150, 'body' => array('request_from' => 'product-blocks' )));
        if (!is_wp_error($request_data)) {
            return wp_send_json_success(json_decode($request_data['body'], true));
        } else {
			wp_send_json_error(array('messages' => $request_data->get_error_messages()));
        }
    }


    /**
	 * All Sections Callback.
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function get_all_sections_callback() {
        $request_data = wp_remote_post($this->api_endpoint.'sections', array('timeout' => 150, 'body' => array('request_from' => 'product-blocks' )));
        if (!is_wp_error($request_data)) {
            return wp_send_json_success(json_decode($request_data['body'], true));
        } else {
			wp_send_json_error(array('messages' => $request_data->get_error_messages()));
        }
    }


    /**
	 * Single Sections REST API Callback
     * 
     * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
    public function get_single_section_callback(){        
        $template_id = (int) sanitize_text_field($_REQUEST['template_id']);
        if (!$template_id) {
			return false;
        }
        $request_data = wp_remote_post( $this->api_endpoint.'single-section', array('timeout' => 150, 'body' => array('request_from' => 'product-blocks', 'template_id' => $template_id)));
        if (!is_wp_error($request_data)) {
            return wp_send_json_success(json_decode($request_data['body'], true));
        } else {
			wp_send_json_error(array('messages' => $request_data->get_error_messages()));
        }
    }

    /**
     * Filter Callback of the Blocks
     *
     * @param $blocks
     * @param $blockName
     * @param $blockRaw
     * @param $params
     * @param $block_list
     * @return STRING
     * @since v.2.1.4
     */
    public function product_filter_block_target($blocks, $blockName , $blockRaw, $params, &$block_list) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName']) {
                $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                $attr = array_merge($attr, $params);
                $attr = array_merge($attr, $value['attrs']);
                $block_list[] = [
                    'blockId' => $value['attrs']['blockId'],
                    'content' => $this->all_blocks[$blockRaw]->content($attr, true),
                ];
                remove_filter( 'posts_where', 'title_filter', 1000 );
                remove_filter( 'posts_join', 'custom_join_product_filter', 1000 );
            }
            if(!empty($value['innerBlocks'])) {
                $this->product_filter_block_target($value['innerBlocks'], $blockName, $blockRaw, $params, $block_list);
            }
        }
        return $block_list;
    }

    public function wopb_checkout_login_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local) {
            return ;
        }
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        $remember = sanitize_text_field($_POST['rememberme']);
        $errors = [];

        if ( isset( $username, $password ) ) {
			try {
				$creds = array(
					'user_login'    => trim( wp_unslash( $username ) ),
					'user_password' => $password,
					'remember'      => isset( $remember ),
				);

				$validation_error = new \WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );
				if ( $validation_error->get_error_code() ) {
				    return wp_send_json( $validation_error->get_error_message(), 422 );
				}

				if ( empty( $creds['user_login'] ) ) {
				    $errors['username'] = 'Username or Email is required';
				    return wp_send_json( $errors, 422 );
				}
				if ( empty( $creds['user_password'] ) ) {
				    $errors['password'] = 'Password is required';
				    return wp_send_json( $errors, 422 );
				}

				// On multisite, ensure user exists on current site, if not add them before allowing login.
				if ( is_multisite() ) {
					$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

					if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
						add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
					}
				}

				// Perform the login.
				$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

				if ( is_wp_error( $user ) ) {
				    $errors['default'] = $user->get_error_message();
				    return wp_send_json( $errors, 422 );
				} else {
				    return wp_send_json_success( ['success' => true] );
					exit;
				}
			} catch ( \Exception $e ) {
			    return wp_send_json( $e->getMessage(), 500 );
				do_action( 'woocommerce_login_failed' );
			}
		}
    }
    /**
	 * share Count callback
     * 
     * @since v.1.0.0
	 * @return STRING
	 */
    public function wopb_share_count_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local) {
            return ;
        }
            $id = sanitize_text_field($_POST['postId']);
            $count = sanitize_text_field($_POST['shareCount']);
            $post_id = $id;
            $new_count = $count+1; 
            update_post_meta($post_id, 'wopb_share_count', $new_count);
    }

    /**
     * Get Product Search Block Search Data
     *
     * @since v.2.6.8
     * @param $blocks
     * @param $blockId
     * @param $blockRaw
     * @param $blockName
     * @return array
     */
    public function search_block_attr($blocks, $blockId, $blockRaw, $blockName, &$params) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName'] && $value['attrs']['blockId'] == $blockId) {
                $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                $params['attr'] = array_merge($attr, $value['attrs']);
                break;
            }
            if(!empty($value['innerBlocks'])){
                $this->search_block_attr($value['innerBlocks'], $blockId, $blockRaw, $blockName, $params);
            }
        }

        return $params;
    }


    /**
	 * Get Product Search Param
     *
	 *@since v.2.6.8
     * @param $params
	 * @return array
     */
    public function search_block_param($params = []) {
        $query_args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];
        $tax_args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ];
        $tax_terms = '';

        if(isset($params['search']) && $params['search']) {
            $query_args['filter_search_key'] = $params['search'];
            add_filter( 'posts_where', [wopb_function(), 'custom_query_product_filter'], 1000,2 );
            $tax_args['search'] = $params['search'];
        }
        if(isset($params['category']) && $params['category']) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $params['category'],
                'operator' => 'IN'
            ];
        }else {
            $tax_terms = get_terms($tax_args);
        }

        $products = new \WP_Query( $query_args);
        $params = [
            'search' => $params['search'],
            'attr' => isset($params['attr']) ? $params['attr'] : '',
            'products' => $products,
            'total_product' => count($products->posts),
            'tax_terms' => $tax_terms,
        ];
        return $params;
    }

}
<?php
/**
 * Settings Action.
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * Settings Class.
 */
class Settings {
	/**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'settings_submenu_page_callback' ) );
		add_action( 'rest_api_init', array( $this, 'save_settings_callback' ) );
		add_filter( 'option_woocommerce_tax_display_shop', array( $this, 'display_price_shop_including_tax' ) );
		add_filter( 'option_woocommerce_tax_display_cart', array( $this, 'display_price_cart_including_tax' ) );
		add_filter( 'woocommerce_get_price_suffix', array( $this, 'price_suffix_handler' ), 10, 2 );
		add_filter( 'wholesalex_recaptcha_minimum_score_allow', array( $this, 'recaptcha_minimum_score_allow' ) );
		add_filter( 'option_woocommerce_myaccount_page_id', array( $this, 'separate_my_account_page_for_b2b' ), 10, 1 );
		add_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_fields' ) );
		/**
		 * Force Redirect Logged Out User to Specific Page
		 *
		 * @since 1.0.8
		 */
		add_action( 'plugins_loaded', array( $this, 'force_redirect_guest_users' ) );

		/**
		 * Allow Hidden Product to checkout
		 *
		 * @since 1.1.0
		 */
		add_filter( 'wholesalex_allow_hidden_filter_to_checkout', array( $this, 'allow_hidden_product_to_checkout' ) );
	}


	/**
	 * Save Wholesalex Settings Actions
	 *
	 * @since 1.0.0
	 */
	public function save_settings_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/settings_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'settings_action_callback' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}


	/**
	 * Save Wholesalex Settings
	 *
	 * @param object $server Server.
	 * @since 1.0.0
	 */
	public function settings_action_callback( $server ) {
		$post = $server->get_params();
		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( $post['nonce'], 'wholesalex-registration' ) ) ) {
			return;
		}

		$type = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';

		if ( 'set' === $type ) {
			$post = wholesalex()->sanitize( $post );

			if ( 'email_confirmation_require' === $post['settings']['_settings_user_status_option'] ) {
				$__confirmation_email_status = get_option( 'wholesalex_email_verification_email_status' );
				if ( 'yes' !== $__confirmation_email_status ) {
					wp_send_json_error( __( 'To Active "Email Confirmation" Please Enable WholesaleX Email Template from "Dashboard > WholesaleX > Emails".', 'wholesalex' ) );
				}
			}
			wholesalex()->set_setting_multiple( $post['settings'] );
			wp_send_json_success( __( 'Successfully Saved.', 'wholesalex' ) );
		} elseif ( 'get' === $type ) {
			$data            = array();
			$data['default'] = $this->get_option_settings();
			$data['value']   = wholesalex()->get_setting();
			wp_send_json_success( $data );
		}
	}


	/**
	 * Settings Menu callback
	 *
	 * @return void
	 */
	public function settings_submenu_page_callback() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'Settings', 'wholesalex' ),
			__( 'Settings', 'wholesalex' ),
			'manage_options',
			'wholesalex-settings',
			array( $this, 'settings_html_callback' )
		);
	}


	/**
	 * Settings Sub Menu Page Callback
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function settings_html_callback() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_settings' );
		?>
		<div id="_wholesalex_settings"></div> 
			<?php
	}


	/**
	 * Settings Field Return
	 *
	 * @since 1.0.0
	 * @since 1.0.4 DragList Option Error Fixed.
	 */
	public function get_option_settings() {

		$__pages        = get_pages();
		$__pages_option = array();
		foreach ( $__pages as $page ) {
			$__pages_option[ esc_attr( $page->ID ) ] = esc_html( $page->post_title );
		}
		$my_account_id = get_option( 'woocommerce_myaccount_page_id' );

		return apply_filters(
			'wholesalex_setting_fields',
			array(
				'general'              => array(
					'label' => __( 'General Settings', 'wholesalex' ),
					'attr'  => array(
						'_settings_status'                 => array(
							'type'    => 'radio',
							'label'   => __( 'Plugin Status', 'wholesalex' ),
							'options' => array(
								'b2b'       => __( 'B2B (Wholesale Only)', 'wholesalex' ),
								'b2c'       => __( 'B2C (Public Only)', 'wholesalex' ),
								'b2b_n_b2c' => __( 'B2B & B2C Hybrid (Wholesale and Public)', 'wholesalex' ),
							),
							'default' => 'b2b',
							// 'help_popup' => true,
							// 'popup_gif_link' => 'https://plugins.svn.wordpress.org/wholesalex/assets/Screenshot-1.jpg',
						),
						'_settings_show_table'             => array(
							'type'    => 'switch',
							'label'   => __( 'Show Tiered Pricing Table', 'wholesalex' ),
							'desc'    => __( 'Product Single Page', 'wholesalex' ),
							'default' => 'yes',
							// 'help_popup' => true,
							// 'popup_gif_link' => 'https://plugins.svn.wordpress.org/wholesalex/assets/Screenshot-1.jpg',
						),
						// '_settings_pricing_table'        => array(
						// 'type'    => 'switch',
						// 'label'   => __( 'Merge Tierd Pricing Table', 'wholesalex' ),
						// 'desc'    => __( 'Merge All Tiers', 'wholesalex' ),
						// 'help'    => __( 'By default, It will display the table which is currently applied. If you enable this option, It will display a merged table of all tiers.', 'wholesalex' ),
						// 'default' => '',
						// ),
						'_settings_quantity_based_discount_priority' => array(
							'type'    => 'DragList',
							'label'   => __( 'Pricing / Discount Priority', 'wholesalex' ),
							'desc'    => __( 'Set the priority to declare which will be applied if discounts are assigned in multiple ways.', 'wholesalex' ),
							'is_pro'  => true,
							'options' => array( 'profile', 'single_product', 'category', 'dynamic_rule' ),
							'default' => array( 'profile', 'single_product', 'category', 'dynamic_rule' ),
						),
						'_settings_disable_coupon'         => array(
							'type'    => 'switch',
							'label'   => __( 'Disable Coupons', 'wholesalex' ),
							'desc'    => __( 'Hide coupon form of cart and checkout pages from wholesale users.', 'wholesalex' ),
							'help'    => '',
							'default' => '',
							// 'help_popup' => true,
							// 'popup_gif_link' => 'https://plugins.svn.wordpress.org/wholesalex/assets/Screenshot-1.jpg',
						),
						'_settings_hide_products_from_b2c' => array(
							'type'    => 'switch',
							'label'   => __( 'Hide All Products From B2C Users', 'wholesalex' ),
							'desc'    => __( 'Click on the checkbox to hide all products from B2C users.', 'wholesalex' ),
							'help'    => __( 'Once you click on this check box all products will be hidden from b2c users.', 'wholesalex' ),
							'default' => '',
							// 'help_popup' => true,
							// 'popup_gif_link' => 'https://plugins.svn.wordpress.org/wholesalex/assets/Screenshot-1.jpg',
						),
						'_settings_hide_all_products_from_guest' => array(
							'type'    => 'switch',
							'label'   => __( 'Hide All Products From Guest Users', 'wholesalex' ),
							'desc'    => __( 'Click on the check box if you want to hide all products from guest users.', 'wholesalex' ),
							'help'    => __( 'Once you click on this check box all products will be hidden from guest users.', 'wholesalex' ),
							'default' => '',
							// 'tooltip' => __('Once you click on this check box all products will be hidden from guest users.','wholesalex'),
							// 'doc_link' => 'https://getwholesalex.com/pricing/?utm_source=wholesalex_plugin&utm_medium=support&utm_campaign=wholesalex-DB',
						),
						'_settings_display_price_shop_page' => array(
							'type'    => 'select',
							'label'   => __( 'Display Prices in the Shop', 'wholesalex' ),
							'options' => array(
								'woocommerce_default_tax' => __( 'Use WooCommerce default', 'wholesalex' ),
								'incl'                    => __( 'Including Tax', 'wholesalex' ),
								'excl'                    => __( 'Excluding Tax', 'wholesalex' ),
							),
							'help'    => __( 'Display prices including or excluding taxes on the shop page.', 'wholesalex' ),
							'default' => 'woocommerce_default_tax',
							// 'tooltip' => __('Display prices including or excluding taxes on the shop page.','wholesalex'),
							// 'doc_link' => 'https://getwholesalex.com/pricing/?utm_source=wholesalex_plugin&utm_medium=support&utm_campaign=wholesalex-DB',

						),
						'_settings_display_price_cart_checkout' => array(
							'type'    => 'select',
							'label'   => __( 'Display Prices in Cart and Checkout Page', 'wholesalex' ),
							'options' => array(
								'woocommerce_default_tax' => __( 'Use WooCommerce default', 'wholesalex' ),
								'incl'                    => __( 'Including Tax', 'wholesalex' ),
								'excl'                    => __( 'Excluding Tax', 'wholesalex' ),
							),
							'help'    => __( 'Display prices including or excluding taxes on the cart and checkout pages.', 'wholesalex' ),
							'default' => 'woocommerce_default_tax',
							// 'tooltip' => __('Display prices including or excluding taxes on the cart and checkout pages.','wholesalex'),
							// 'doc_link' => 'https://getwholesalex.com/pricing/?utm_source=wholesalex_plugin&utm_medium=support&utm_campaign=wholesalex-DB',

						),
						'_settings_regular_price_suffix'   => array(
							'type'        => 'text',
							'label'       => __( 'Override Regular Price Suffix', 'wholesalex' ),
							'placeholder' => '',
							'help'        => __( 'Display desired text after regular prices on shop and single products pages.', 'wholesalex' ),
							'default'     => '',
						),
						'_settings_wholesalex_price_suffix' => array(
							'type'        => 'text',
							'label'       => __( 'WholesaleX Price Suffix', 'wholesalex' ),
							'placeholder' => '',
							'help'        => __( 'Display desired text after wholesale prices on shop and single products pages.', 'wholesalex' ),
							'default'     => '',
						),
						'_settings_private_store'          => array(
							'type'    => 'switch',
							'label'   => __( 'Make The Store Private', 'wholesalex' ),
							'desc'    => __( 'Click the check box to make the store private from logged out users', 'wholesalex' ),
							'help'    => '',
							'default' => '',
						),
						'_settings_private_store_redirect_url' => array(
							'type'       => 'text',
							'label'      => __( 'Force Redirect URL', 'wholesalex' ),
							'depends_on' => array(
								array(
									'key'   => '_settings_private_store',
									'value' => 'yes',
								),
							),
							'help'       => __( 'Enter an url where you want to force redirect logged out users.', 'wholesalex' ),
							'default'    => get_permalink( $my_account_id ),
						),
						'_settings_allow_hidden_product_checkout' => array(
							'type'    => 'switch',
							'label'   => __( 'Allow Hidden Product to Checkout', 'wholesalex' ),
							'desc'    => __( 'Click the check box if you want to allow hidden product to checkout', 'wholesalex' ),
							'help'    => '',
							'default' => '',
						),
					),
				),
				'registration_n_login' => array(
					'label' => __( 'Registration & Login', 'wholesalex' ),
					'attr'  => array(
						'_settings_user_login_option'  => array(
							'type'    => 'select',
							'label'   => __( 'User Login Option', 'wholesalex' ),
							'options' => array(
								'manual_login' => __( 'Manual Login After Registration', 'wholesalex' ),
								'auto_login'   => __( 'Auto Login After Registration', 'wholesalex' ),
							),
							'help'    => __( 'Auto login after registration will work only if the user status option is set to “Auto Approve”. ', 'wholesalex' ),
							'link'    => 'https://getwholesalex.com/docs/wholesalex/registration-form-builder/',
							'default' => 'manual_login',
						),
						'_settings_user_status_option' => array(
							'type'    => 'select',
							'label'   => __( 'User Status Option', 'wholesalex' ),
							'options' => array(
								'email_confirmation_require' => __( 'Email Confirmation to Active Account', 'wholesalex' ),
								'auto_approve'  => __( 'Auto Approval Account', 'wholesalex' ),
								'admin_approve' => __( 'Admin Approval Require to Active Account', 'wholesalex' ),
							),
							'default' => 'admin_approve',
						),
						'_settings_seperate_page_b2b'  => array(
							'type'    => 'select',
							'label'   => __( 'Separate My Account Page for B2B Users', 'wholesalex' ),
							'options' => $__pages_option,
							'help'    => __( 'Select your desired page if you want to separate the My Account Page for B2B users.', 'wholesalex' ),
							'default' => $my_account_id,

						),
						'_settings_show_form_for_logged_in' => array(
							'type'    => 'switch',
							'label'   => __( 'Show Registration Form For Logged In User', 'wholesalex' ),
							'desc'    => __( 'Click on the check box if you want to show registration form form logged in users.', 'wholesalex' ),
							'help'    => __( 'Once you click on this check box the regular price will be hidden if the wholesale price is present.', 'wholesalex' ),
							'default' => '',
						),
						'_settings_redirect_url_registration' => array(
							'type'        => 'text',
							'label'       => __( 'Redirect Page URL (After Registration)', 'wholesalex' ),
							'placeholder' => __( 'http://', 'wholesalex' ),
							'help'        => '',
							'default'     => get_permalink( $my_account_id ),
						),
						'_settings_redirect_url_login' => array(
							'type'        => 'text',
							'label'       => __( 'Redirect Page URL (After Login)', 'wholesalex' ),
							'placeholder' => __( 'http://', 'wholesalex' ),
							'help'        => '',
							'default'     => get_permalink( get_option( 'woocommerce_shop_page_id' ) ),
						),
						'_settings_registration_success_message' => array(
							'type'    => 'textarea',
							'label'   => __( 'Registration Successful Message', 'wholesalex' ),
							'help'    => '',
							'default' => __( 'Thank you for registering. Your account will be reviewed by us & approve manually. Please wait to be approved.', 'wholesalex' ),
						),
						'_settings_message_for_logged_in_user' => array(
							'type'    => 'textarea',
							'label'   => __( 'Registration Form Message For Logged In User', 'wholesalex' ),
							'help'    => '',
							'default' => __( 'Sorry You Are Not Allowed To View This Form', 'wholesalex' ),
						),
						'_settings_disable_woocommerce_new_user_email' => array(
							'type'    => 'switch',
							'label'   => __( 'Disable WooCommerce New Customer Registration Email', 'wholesalex' ),
							'desc'    => __( 'Disable', 'wholesalex' ),
							'help'    => __( 'Click on the check box if you want to disable WooCommerce New Customer Registration Email For The users who registered through the wholesalex registration form.', 'wholesalex' ),
							'default' => '',
						),
					),
				),
				'price'                => array(
					'label' => __( 'Price', 'wholesalex' ),
					'attr'  => array(
						'_settings_price_text'            => array(
							'type'        => 'text',
							'label'       => __( 'Wholesale Price Text for Product Pages', 'wholesalex' ),
							'placeholder' => __( 'Wholesale Price:', 'wholesalex' ),
							'help'        => __( 'The text is shown immediately before the wholesale price in product single page. The default text is “WholesaleX Price:”', 'wholesalex' ),
							'default'     => __( 'Wholesale Price:', 'wholesalex' ),
						),
						'_settings_price_text_product_list_page' => array(
							'type'        => 'text',
							'label'       => __( 'Wholesale Price Text for Product Listing Page', 'wholesalex' ),
							'placeholder' => __( 'Wholesale Price:', 'wholesalex' ),
							'help'        => __( 'The text is shown immediately before the wholesale price in product listing page. The default text is “WholesaleX Price:”.', 'wholesalex' ),
							'default'     => __( 'Wholesale Price:', 'wholesalex' ),
						),
						'_settings_price_product_list_page' => array(
							'type'        => 'select',
							'label'       => __( 'Wholesale Price On Product Listing Page', 'wholesalex' ),
							'options'     => array(
								'pricing_range'   => __( 'Pricing Range', 'wholesalex' ),
								'minimum_pricing' => __( 'Minimum Pricing', 'wholesalex' ),
								'maximum_pricing' => __( 'Maximum Pricing', 'wholesalex' ),
							),
							'placeholder' => __( 'Pricing Range, Minimum Pricing, Maximum Pricing', 'wholesalex' ),
							'help'        => __( 'Select whether you want to display wholesale price range, minimum price, or maximize price on the product listing page.', 'wholesalex' ),
							'default'     => 'pricing_range',
						),
						'_settings_hide_retail_price'     => array(
							'type'    => 'switch',
							'label'   => __( 'Hide Retail Price', 'wholesalex' ),
							'desc'    => __( 'Click on the check box if you want to hide the retail price.', 'wholesalex' ),
							'help'    => __( 'Once you click on this check box the regular price will be hidden if the wholesale price is present.', 'wholesalex' ),
							'default' => '',
						),
						'_settings_hide_wholesalex_price' => array(
							'type'    => 'switch',
							'label'   => __( 'Hide Wholesale Price', 'wholesalex' ),
							'desc'    => __( 'Hide wholesale price for all users.', 'wholesalex' ),
							'help'    => __( 'This option will hide wholesale price in price-column of product-listing page.', 'wholesalex' ),
							'default' => '',
						),
						'_settings_login_to_view_price_product_list' => array(
							'type'    => 'switch',
							'label'   => __( 'Show Login to view price on Product Listing Page', 'wholesalex' ),
							'desc'    => __( 'Login to view price', 'wholesalex' ),
							'help'    => __( 'Display logging option on the product listing page to view price.', 'wholesalex' ),
							'default' => '',
						),
						'_settings_login_to_view_price_product_page' => array(
							'type'    => 'switch',
							'label'   => __( 'Show Login to view price on Single Product Page', 'wholesalex' ),
							'desc'    => __( 'Login to view price', 'wholesalex' ),
							'help'    => __( 'Display logging option on single product pages to view price.', 'wholesalex' ),
							'default' => '',
						),
					),
				),
				'language_n_text'      => array(
					'label' => __( 'Language and Text', 'wholesalex' ),
					'attr'  => array(
						'_language_registraion_from_combine_login_text' => array(
							'type'        => 'text',
							'label'       => __( 'Login Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'Login', 'wholesalex' ),
						),
						'_language_registraion_from_combine_registration_text' => array(
							'type'        => 'text',
							'label'       => __( 'Registration Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'Registration', 'wholesalex' ),
						),
						'_language_logout_to_see_this_form' => array(
							'type'        => 'text',
							'label'       => __( 'Logout To See this form Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'Logout To See this form', 'wholesalex' ),
						),
						'_language_login_to_see_prices' => array(
							'type'        => 'text',
							'label'       => __( 'Login To See Price Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'Login to see prices', 'wholesalex' ),
						),
						'_language_price_is_hidden'     => array(
							'type'        => 'text',
							'label'       => __( 'Price is hidden Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'Price is hidden!', 'wholesalex' ),
						),
						'_language_profile_force_free_shipping_text'     => array(
							'type'        => 'text',
							'label'       => __( 'WholesaleX Free Shipping Text', 'wholesalex' ),
							'placeholder' => '',
							'help'        => '',
							'default'     => __( 'WholesaleX Free Shipping', 'wholesalex' ),
						)
					),
				),
				'design'               => array(
					'label' => __( 'Design', 'wholesalex' ),
					'attr'  => array(
						'_settings_tier_layout'         => array(
							'type'    => 'choosebox',
							'label'   => __( 'Tier Table Layout in Product Single Page', 'wholesalex' ),
							'options' => apply_filters(
								'wholesalex_settings_product_tier_layout',
								array(
									'layout_one'   => WHOLESALEX_URL . '/assets/img/layout_one.png',
									'layout_two'   => WHOLESALEX_URL . '/assets/img/layout_two.png',
									'layout_three' => WHOLESALEX_URL . '/assets/img/layout_three.png',
								)
							),
							'default' => 'layout_one',
						),
						'_settings_tier_position'       => array(
							'type'    => 'switch',
							'label'   => __( 'Tier Position in Product Single Page', 'wholesalex' ),
							'desc'    => __( 'Above Cart Button', 'wholesalex' ),
							'help'    => __( 'Display tiered pricing above the “Add to Cart” button.', 'wholesalex' ),
							'default' => '',
						),
						'_settings_primary_color'       => array(
							'type'    => 'color',
							'label'   => __( 'Primary Color', 'wholesalex' ),
							'desc'    => '#5a40e8',
							'default' => '#5a40e8',
						),
						'_settings_primary_button_color'       => array(
							'type'    => 'color',
							'label'   => __( 'Primary Button Color', 'wholesalex' ),
							'desc'    => '#FFFFFF',
							'default' => '#ffffff',
						),
						'_settings_primary_hover_color' => array(
							'type'    => 'color',
							'label'   => __( 'Primary Hover Color', 'wholesalex' ),
							'desc'    => '#24A88F',
							'default' => '#24A88F',
						),
						'_settings_text_color'          => array(
							'type'    => 'color',
							'label'   => __( 'Text Color', 'wholesalex' ),
							'desc'    => '#272727',
							'default' => '#272727',
						),
						'_settings_border_color'        => array(
							'type'    => 'color',
							'label'   => __( 'Border', 'wholesalex' ),
							'desc'    => '#E5E5E5',
							'default' => '#E5E5E5',
						),
						'_settings_active_tier_color'   => array(
							'type'    => 'color',
							'label'   => __( 'Active Tier', 'wholesalex' ),
							'desc'    => '#E5E5E5',
							'default' => '#E5E5E5',
						),
					),
				),
				'recaptcha'            => array(
					'label' => __( 'reCAPTCHA', 'wholesalex' ),
					'attr'  => array(
						'_settings_google_recaptcha_v3_site_key'   => array(
							'type'        => 'text',
							'label'       => __( 'Site Key', 'wholesalex' ),
							'placeholder' => __( 'Site Key...', 'wholesalex' ),
							'default'     => '',
							'help'        => __( 'For Gettings reCAPTCHA Site Key, You have to create an project on google recaptcha. ', 'wholesalex' ),
							'link'        => 'https://getwholesalex.com/add-on/recaptcha/',
						),
						'_settings_google_recaptcha_v3_secret_key' => array(
							'type'        => 'text',
							'label'       => __( 'Secret Key', 'wholesalex' ),
							'placeholder' => __( 'Secret Key...', 'wholesalex' ),
							'default'     => '',
							'help'        => __( 'For Gettings reCAPTCHA secret Key, You have to create an project on google recaptcha. ', 'wholesalex' ),
							'link'        => 'https://getwholesalex.com/add-on/recaptcha/',
						),
						'_settings_google_recaptcha_v3_allowed_score' => array(
							'type'        => 'text',
							'label'       => __( 'Minimum Allowed Score', 'wholesalex' ),
							'placeholder' => '',
							'default'     => __( '0.5', 'wholesalex' ),
							'help'        => __( 'Set minimum allowed score for reCAPTCHA. Default Range: 0.0 - 1.00', 'wholesalex' ),
						),
					),
				),
				'_save'                => array(
					'type'  => 'button',
					'label' => __( 'Save Changes', 'wholesalex' ),
				),

			),
		);
	}

	/**
	 * Display Price Including Tax in Shop
	 *
	 * @param String $option Include tax or Exclude Tax in Shop.
	 * @since 1.0.0
	 * @access public
	 */
	public function display_price_shop_including_tax( $option ) {
		$__display_price_shop = wholesalex()->get_setting( '_settings_display_price_shop_page' ) ? wholesalex()->get_setting( '_settings_display_price_shop_page' ) : '';

		if ( ! empty( $__display_price_shop ) && 'woocommerce_default_tax' !== $__display_price_shop ) {
			$option = $__display_price_shop;
		}
		return $option;
	}

	/**
	 * Display Price Including Tax in Cart Page
	 *
	 * @param String $option Include tax or Exclude Tax in Cart Page.
	 * @since 1.0.0
	 * @access public
	 */
	public function display_price_cart_including_tax( $option ) {
		$__display_price_cart_checkout = wholesalex()->get_setting( '_settings_display_price_cart_checkout' ) ? wholesalex()->get_setting( '_settings_display_price_cart_checkout' ) : '';
		if ( ! empty( $__display_price_cart_checkout ) && 'woocommerce_default_tax' !== $__display_price_cart_checkout ) {
			$option = $__display_price_cart_checkout;
		}
		return $option;
	}

	/**
	 * WholesaleX Price Suffix Handler
	 *
	 * @param String     $price_suffix Default Price Suffix.
	 * @param WC_Product $product Woocommerce Product.
	 * @since 1.0.0
	 * @access public
	 * @return String $price_suffix
	 */
	public function price_suffix_handler( $price_suffix, $product ) {
		$wholesalex_regular_price_suffix = wholesalex()->get_setting( '_settings_regular_price_suffix' );
		$wholesalex_price_suffix         = wholesalex()->get_setting( '_settings_wholesalex_price_suffix' );
		if ( isset( $wholesalex_regular_price_suffix ) && ! empty( $wholesalex_regular_price_suffix ) && ! $product->is_on_sale() ) {
			if ( '{price_including_tax}' === $wholesalex_regular_price_suffix ) {
				$wholesalex_regular_price_suffix = wc_price( wc_get_price_including_tax( $product ) );
			} elseif ( '{price_excluding_tax}' === $wholesalex_regular_price_suffix ) {
				$wholesalex_regular_price_suffix = wc_price( wc_get_price_excluding_tax( $product ) );
			}
			return '<small class="woocommerce-price-suffix">' . $wholesalex_regular_price_suffix . '</small>';
		} elseif ( isset( $wholesalex_price_suffix ) && ! empty( $wholesalex_price_suffix ) && $product->is_on_sale() ) {
			if ( '{price_including_tax}' === $wholesalex_price_suffix ) {
				$wholesalex_price_suffix = wc_price( wc_get_price_including_tax( $product ) );
			} elseif ( '{price_excluding_tax}' === $wholesalex_price_suffix ) {
				$wholesalex_price_suffix = wc_price( wc_get_price_excluding_tax( $product ) );
			}
			return '<small class="woocommerce-price-suffix">' . $wholesalex_price_suffix . '</small>';
		}
		return $price_suffix;
	}


	/**
	 * Separate My Account Page For B2B Users
	 *
	 * @param string $option Option.
	 * @return string
	 */
	public function separate_my_account_page_for_b2b( $option ) {
		$__user_role = wholesalex()->get_current_user_role();

		$__is_b2b = ( 'wholesalex_guest' !== $__user_role && 'wholesalex_b2c_users' !== $__user_role && ! empty( $__user_role ) ) ? true : false;

		$__my_account_page = wholesalex()->get_setting( '_settings_seperate_page_b2b' );
		if ( $__is_b2b && ! empty( $__my_account_page ) ) {
			return $__my_account_page;
		}

		return $option;
	}

	/**
	 * Hide Coupon Fields For WholesaleX User
	 *
	 * @param bool $enabled Coupon Fields Enable Status.
	 * @return bool
	 * @since 1.0.0
	 */
	public function hide_coupon_fields( $enabled ) {
		if ( is_user_logged_in() ) {
			$current_user_id   = get_current_user_id();
			$current_user_role = get_the_author_meta( '__wholesalex_role', $current_user_id );
			$__status          = wholesalex()->get_setting( '_settings_disable_coupon' );

			if ( isset( $current_user_role ) && ! empty( $current_user_role ) && 'yes' === $__status ) {
				return false;
			}
		}
		return $enabled;
	}

	/**
	 * Recaptcha Minimum Score Allowed
	 *
	 * @return string
	 * @since 1.0.0 Reposition on v1.0..7
	 */
	public function recaptcha_minimum_score_allow() {
		$__score = wholesalex()->get_setting( '_settings_google_recaptcha_v3_allowed_score' ) ?? 0.5;
		return $__score;
	}


	/**
	 * Force Redirect Guest Users
	 *
	 * @return void
	 * @since 1.0.8
	 */
	public function force_redirect_guest_users() {
		if ( ! is_user_logged_in() ) {
			$is_private = wholesalex()->get_setting( '_settings_private_store' );
			if ( 'yes' === $is_private ) {
				add_action(
					'wp',
					function () {
						$protocol = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
						$cur_url  = $protocol . '' . $_SERVER['HTTP_HOST'] . '' . $_SERVER['REQUEST_URI'];

						$redirect_url = apply_filters( 'wholesalex_force_redirect_guest_user_url', wholesalex()->get_setting( '_settings_private_store_redirect_url' ) );
						$redirect_url = esc_url_raw( $redirect_url );

						$default_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

						if ( '' === $redirect_url ) {
							$redirect_url = $default_url;
						}

						if ( filter_var( $redirect_url, FILTER_VALIDATE_URL ) ) {
							if ( $default_url !== $cur_url && $redirect_url != $cur_url ) {
								wp_safe_redirect( $redirect_url );
								exit;
							}
						} else {
							auth_redirect();
						}
					}
				);
			}
		}
	}

	/**
	 * Allow Hidden Product to Checkout
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function allow_hidden_product_to_checkout() {
		$is_allow = ( 'yes' === wholesalex()->get_setting( '_settings_allow_hidden_product_checkout' ) );
		return $is_allow;
	}
}

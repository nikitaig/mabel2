<?php

/**
 * WholesaleX Dynamic Rules
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WC_Shipping_Free_Shipping;
use WOPB_PRO\Currency_Switcher_Action;



/**
 * WholesaleX Dynamic Rules Class
 */
class WHOLESALEX_Dynamic_Rules {


	/**
	 * Contain Currently Applied Discount Source
	 *
	 * @var string
	 */
	public $discount_src = '';

	/**
	 * Contain The tier id, which is currently applied
	 *
	 * @var integer
	 */
	public $active_tier_id = 0;

	/**
	 * Contains sale price function name which run first
	 *
	 * @var string
	 */
	public $first_sale_price_generator = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wholesalex_dynamic_rules_submenu_page' ) );
		add_action( 'rest_api_init', array( $this, 'dynamic_rule_restapi_callback' ) );
		add_action( 'plugins_loaded', array( $this, 'dynamic_rules_handler' ) );
		add_action( 'woocommerce_package_rates', array( $this, 'filter_shipping_methods' ), 10, 2 );
		add_filter( 'woocommerce_checkout_update_order_review', array( $this, 'clear_shipping_fee_cache' ) );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'add_custom_meta_on_wholesale_order' ), 10 );
		add_action( 'woocommerce_update_cart_action_cart_updated', array( $this, 'update_discounted_product' ) );

		add_filter( 'wholesalex_single_product_discount_action', array( $this, 'single_product_discounts_action' ) );
		add_filter( 'wholesalex_profile_discount_action', array( $this, 'profile_discounts_action' ), 1 );
		add_filter( 'wholesalex_category_discount_action', array( $this, 'category_discounts_action' ),1 );
		add_filter('ppom_product_price', array($this, 'product_price'),10,2);
		add_filter('ppom_product_price_on_cart', array($this, 'set_price_on_ppom' ),10,2);
	}

	/**
	 * WholesaleX Dynamic Rule Submenu Page
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function wholesalex_dynamic_rules_submenu_page() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'Dynamic Rules', 'wholesalex' ),
			__( 'Dynamic Rules', 'wholesalex' ),
			'manage_options',
			'wholesalex_dynamic_rules',
			array( $this, 'wholesalex_dynamic_rules_content' )
		);
	}

	/**
	 * Dynamic Rule Rest API Callback
	 *
	 * @since 1.0.0
	 */
	public function dynamic_rule_restapi_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/dynamic_rule_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'dynamic_rule_restapi_action' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get Category actions
	 *
	 * @param object $server Server.
	 * @return void
	 * @since 1.0.0
	 */
	public function dynamic_rule_restapi_action( $server ) {
		$post = $server->get_params();

		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( sanitize_key( $post['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}

		$type = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';

		if ( 'get' === $type ) {
			$__dynamic_rules = array_values( wholesalex()->get_dynamic_rules() );
			if ( empty( $__dynamic_rules ) ) {
				$__dynamic_rules = array(
					array(
						'id'    => 1,
						'label' => __( 'New Rule', 'wholesalex' ),
					),
				);
			}

			wp_send_json_success(
				array(
					'default' => $this->get_dynamic_rules_field(),
					'value'   => $__dynamic_rules,
				)
			);
		} elseif ( 'post' === $type ) {
			$_id   = isset( $post['id'] ) ? sanitize_text_field( $post['id'] ) : '';
			$_rule = isset( $post['rule'] ) ? wp_unslash( $post['rule'] ) : '';
			$_rule = json_decode( $_rule, true );
			$_rule = wholesalex()->sanitize( $_rule );
			$_flag = true;

			if ( isset( $post['check'] ) && empty( wholesalex()->get_dynamic_rules( $_id ) ) ) {
				$_flag = false;
			}
			if ( $_flag ) {
				wholesalex()->set_dynamic_rules( $_id, $_rule, ( isset( $post['delete'] ) && $post['delete'] ) ? 'delete' : '' );
				if ( isset( $post['delete'] ) && $post['delete'] ) {
					wp_send_json_success(
						array(
							'message' => __( 'Sucessfully Deleted.', 'wholesalex' ),
						)
					);
				} else {
					wp_send_json_success(
						array(
							'message' => __( 'Successfully Saved.', 'wholesalex' ),
						)
					);
				}
			} else {
				wp_send_json_error(
					array(
						'message' => __( 'Before Status Update, You Have to Save Rule Status.', 'wholesalex' ),
					)
				);
			}
		}
	}

	/**
	 * WholesaleX Dynamic Rules Content
	 *
	 * @since 1.0.0
	 * @since 1.0.1 _conditions_operator Option Key Changed.
	 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
	 * @access public
	 */
	public function wholesalex_dynamic_rules_content() {   
		wp_enqueue_script('wholesalex_dynamic_rules');
		?>
		<div id="_wholesalex_dynamic_rules"></div>
		<?php
	}

	/**
	 * Get Dynamic Rules Fields
	 */
	public function get_dynamic_rules_field() {
		$__roles_options = wholesalex()->get_roles( 'roles_option' );
		$__users_options = wholesalex()->get_users()['user_options'];

		// Category Options.
		$categories         = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
		$categories_options = array();

		foreach ( $categories as $category ) {
			$categories_options[] = array(
				'value' => $category->term_id,
				'name'  => $category->name,
			);
		}

		// Product Options.
		$product_ids = get_posts(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);

		$products = array();
		foreach ( $product_ids as $product_id ) {
			$product    = array(
				'value' => $product_id,
				'name'  => wc_get_product( $product_id )->get_name(),
			);
			$products[] = $product;
		}

		// Variation products.....
		$variation_products = array();
		// Get all products.
		$product_variations = get_posts(
			array(
				'post_type'   => 'product_variation',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);

		foreach ( $product_variations as $product ) {
			$productobj = wc_get_product( $product );

			$productobjname = $productobj->get_name();

			if ( is_a( $productobj, 'WC_Product_Variation' ) ) {
				$attributes           = $productobj->get_variation_attributes();
				$number_of_attributes = count( $attributes );
				if ( $number_of_attributes > 2 ) {
					$productobjname .= ' - ';
					foreach ( $attributes as $attribute ) {
						$productobjname .= $attribute . ', ';
					}
					$productobjname = substr( $productobjname, 0, -2 );
				}
			}
			$variation_products[] = array(
				'value' => $product,
				'name'  => $productobjname,
			);
		}

		return apply_filters(
			'wholesalex_dynamic_rules_field',
			array(
				'create_n_save_btn' => array(
					'type' => 'buttons',
					'attr' => array(
						'create' => array(
							'type'  => 'button',
							'label' => __( 'Create Dynamic Rule', 'wholesalex' ),
						),
					),
				),
				'_new_rule'         => array(
					'label' => __( 'New Dynamic Rule', 'wholesalex' ),
					'type'  => 'rule',
					'attr'  => array(
						'_rule_title_n_status_section' => array(
							'label' => '',
							'type'  => 'title_n_status',
							'_id'   => 1,
							'attr'  => array(
								'_rule_title'  => array(
									'type'        => 'text',
									'label'       => __( 'Rule Title', 'wholesalex' ),
									'placeholder' => __( 'Rule Title', 'wholesalex' ),
									'default'     => '',
									'help'        => '',
								),
								'_rule_status' => array(
									'type'    => 'switch',
									'label'   => __( 'Rule Status', 'wholesalex' ),
									'default' => false,
									'help'    => '',
								),
								'save_rule'    => array(
									'type'  => 'button',
									'label' => __( 'Save', 'wholesalex' ),
								),
								'cancel_rule'  => array(
									'type'  => 'button',
									'label' => __( 'Reset', 'wholesalex' ),
								),
							),
						),
						'_rule_section'                => array(
							'label' => '',
							'type'  => 'rules',
							'attr'  => array(
								'_rule_type'            => array(
									'type'    => 'select',
									'label'   => __( 'Rule Type', 'wholesalex' ),
									'options' => apply_filters(
										'wholesalex_dynamic_rules_rule_type_options',
										array(
											''             => __( 'Choose Rule...', 'wholesalex' ),
											'product_discount' => __( 'Product Discount', 'wholesalex' ),
											'pro_quantity_based' => __( 'Quantity Based Discount (Pro)', 'wholesalex' ),
											'pro_cart_discount' => __( 'Cart Discount (Pro)', 'wholesalex' ),
											'pro_payment_discount' => __( 'Payment Discount (Pro)', 'wholesalex' ),
											'pro_tax_rule' => __( 'Tax Rule (Pro)', 'wholesalex' ),
											'pro_shipping_rule' => __( 'Shipping Rule (Pro)', 'wholesalex' ),
											'pro_payment_order_qty' => __( 'Payment Order Quantity (Pro)', 'wholesalex' ),
											'pro_extra_charge' => __( 'Extra Charge (Pro)', 'wholesalex' ),
											'pro_buy_x_get_y' => __( 'Buy X Get Y (Pro)', 'wholesalex' ),
											'pro_buy_x_get_one' => __( 'BOGO Discounts (Buy X Get One Free) (Pro)', 'wholesalex' ),
											'pro_min_order_qty' => __( 'Minimum Order Quantity (Pro)', 'wholesalex' ),
											'pro_max_order_qty' => __( 'Maximum Order Quantity (Pro)', 'wholesalex' ),
											'pro_restrict_product_visibility' => __( 'Restrict Product Visibility (Pro)', 'wholesalex' ),
										),
										'rule_type'
									),
									'default' => '',
									'help'    => '',
								),
								'_rule_for'             => array(
									'type'    => 'select',
									'label'   => __( 'Select User/Role', 'wholesalex' ),
									'options' => apply_filters(
										'wholesalex_dynamic_rules_rule_for_options',
										array(
											''          => __( 'Select Users/Role...', 'wholesalex' ),
											'all_users' => __( 'All Users', 'wholesalex' ),
											'all_roles' => __( 'All Roles', 'wholesalex' ),
											'specific_users' => __( 'Specific Users', 'wholesalex' ),
											'specific_roles' => __( 'Specific Roles', 'wholesalex' ),
										),
										'rule_for'
									),
									'default' => '',
									'help'    => '',
								),
								'_product_filter'       => array(
									'type'    => 'select',
									'label'   => __( 'Product Filter', 'wholesalex' ),
									'options' => apply_filters(
										'wholesalex_dynamic_rules_product_filter_options',
										array(
											''             => __( 'Choose Filter...', 'wholesalex' ),
											'all_products' => __( 'All Products', 'wholesalex' ),
											'products_in_list' => __( 'Product in list', 'wholesalex' ),
											'products_not_in_list' => __( 'Product not in list', 'wholesalex' ),
											'cat_in_list'  => __( 'Categories in list', 'wholesalex' ),
											'cat_not_in_list' => __( 'Categories not in list', 'wholesalex' ),
											'attribute_in_list' => __( 'Attribute in list', 'wholesalex' ),
											'attribute_not_in_list' => __( 'Attribute not in list', 'wholesalex' ),
										),
										'product_filter'
									),
									'default' => '',
								),
								'specific_users'        => array(
									'label'       => __( 'Select Users', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_rule_for',
											'value' => 'specific_users',
										),
									),
									'options'     => $__users_options,
									'placeholder' => __( 'Choose Users...', 'wholesalex' ),
									'default'     => array(),
								),
								'specific_roles'        => array(
									'label'       => __( 'Select Roles', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_rule_for',
											'value' => 'specific_roles',
										),
									),
									'options'     => $__roles_options,
									'placeholder' => __( 'Choose Roles...', 'wholesalex' ),
									'default'     => array(),
								),
								'products_in_list'      => array(
									'label'       => __( 'Select Multiple Products', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'products_in_list',
										),
									),
									'options'     => $products,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_product_in_list_placeholder', __( 'Choose Products to apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
								'products_not_in_list'  => array(
									'label'       => __( 'Select Multiple Products', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'products_not_in_list',
										),
									),
									'options'     => $products,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_product_not_in_list_placeholder', __( 'Choose Products that wont apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
								'cat_in_list'           => array(
									'label'       => __( 'Select Multiple Categories', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'cat_in_list',
										),
									),
									'options'     => $categories_options,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_cat_in_list_placeholder', __( 'Choose Categories to apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
								'cat_not_in_list'       => array(
									'label'       => __( 'Select Multiple Categories', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'cat_not_in_list',
										),
									),
									'options'     => $categories_options,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_cat_not_in_list_placeholder', __( 'Choose Categories that wont apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
								'attribute_in_list'     => array(
									'label'       => __( 'Select Multiple Attributes', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'attribute_in_list',
										),
									),
									'options'     => $variation_products,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_attribute_in_list_placeholder', __( 'Choose Product Variations to apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
								'attribute_not_in_list' => array(
									'label'       => __( 'Select Multiple Attributes', 'wholesalex' ),
									'type'        => 'multiselect',
									'depends_on'  => array(
										array(
											'key'   => '_product_filter',
											'value' => 'attribute_not_in_list',
										),
									),
									'options'     => $variation_products,
									'placeholder' => apply_filters( 'wholesalex_dynamic_rules_attribute_not_in_list_placeholder', __( 'Choose Product Variations that wont apply discounts', 'wholesalex' ) ),
									'default'     => array(),
								),
							),
						),
						'product_discount'             => array(
							'label'      => __( 'Manage Discount', 'wholesalex' ),
							'type'       => 'manage_discount',
							'depends_on' => array(
								array(
									'key'   => '_rule_type',
									'value' => 'product_discount',
								),
							),
							'attr'       => array(
								'_discount_type'   => array(
									'type'    => 'select',
									'label'   => __( 'Discount Type', 'wholesalex' ),
									'options' => array(
										'percentage' => __( 'Percentage', 'wholesalex' ),
										'amount'     => __( 'Amount', 'wholesalex' ),
										'fixed'      => __( 'Fixed Price', 'wholesalex' ),
									),
									'default' => 'percentage',
								),
								'_discount_amount' => array(
									'type'        => 'number',
									'label'       => __( 'Amount', 'wholesalex' ),
									'default'     => '',
									'placeholder' => '',
									'help'        => '',
								),
								'_discount_name'   => array(
									'type'        => 'text',
									'label'       => __( 'Disc. name(optional)', 'wholesalex' ),
									'default'     => '',
									'placeholder' => __( 'Add disc. Name herer', 'wholesalex' ),
									'help'        => '',
								),
							),
						),
						'conditions'                   => array(
							'label' => __( 'Conditions: (optional)', 'wholesalex' ),
							'type'  => 'tiers',
							'attr'  =>
							array(
								'_quantity_based_tier' => array(
									'type'   => 'tier',
									'_tiers' => array(
										'data' => array(
											'_conditions_for' => array(
												'type'    => 'select',
												'label'   => '',
												'options' => apply_filters(
													'wholesalex_dynamic_rules_condition_options',
													array(
														'' => __( 'Choose Conditions...', 'wholesalex' ),
														'cart_total_qty' => __( 'Cart - Total Qty', 'wholesalex' ),
														'cart_total_value' => __( 'Cart - Total Value', 'wholesalex' ),
														'cart_total_weight' => __( 'Cart - Total Weight', 'wholesalex' ),
														'pro_order_count' => __( 'User Order Count (Pro)', 'wholesalex' ),
														'pro_total_purchase' => __( 'Total Purchase Amount (Pro)', 'wholesalex' ),
													),
													'conditions'
												),
												'default' => '',
												'placeholder' => '',
												'help'    => '',
											),
											'_conditions_operator' => array(
												'type'    => 'select',
												'label'   => '',
												'options' => array(
													''     => __( 'Choose Operators...', 'wholesalex' ),
													'less' => __( 'Less than (<)', 'wholesalex' ),
													'less_equal' => __( 'Less than or equal (<=)', 'wholesalex' ),
													'greater_equal' => __( 'Greater than or equal (>=)', 'wholesalex' ),
													'greater' => __( 'Greater than (>)', 'wholesalex' ),
												),
												'default' => '',
												'placeholder' => '',
												'help'    => '',
											),
											'_conditions_value' => array(
												'type'    => 'number',
												'label'   => '',
												'default' => '',
												'placeholder' => __( 'Amount', 'wholesalex' ),
												'help'    => '',
											),
										),
									),
								),
							),
						),
						'limit'                        => array(
							'label'          => __( 'Date & Limit Rule', 'wholesalex' ),
							'type'           => 'date_n_usages_limit',
							'not_visible_on' => array(
								'_rule_type' => array( 'max_order_qty', 'min_order_qty' ),
							),
							'attr'           => array(
								'_usage_limit' => array(
									'type'           => 'number',
									'label'          => __( 'Usages Limit', 'wholesalex' ),
									'default'        => '',
									'placeholder'    => '',
									'help'           => '',
									'not_visible_on' => array(
										'_rule_type' => array( 'restrict_product_visibility' ),
									),
								),
								'_start_date'  => array(
									'type'        => 'date',
									'label'       => __( 'Start Date', 'wholesalex' ),
									'default'     => '',
									'placeholder' => '',
									'help'        => '',
								),
								'_end_date'    => array(
									'type'        => 'date',
									'label'       => __( 'End Data', 'wholesalex' ),
									'default'     => '',
									'placeholder' => '',
									'help'        => '',
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Dynamic Rules Handler
	 *
	 * @since 1.0.0
	 */
	public function dynamic_rules_handler() {

		/**
		 * In Admin Interface Dynamic Rule Will Not apply.
		 *
		 * @since 1.0.8
		 */
		/**
		 * Add Ajax Check
		 *
		 * @since 1.1.5
		 */
		if ( is_admin() && ! (defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}
		// Payment Rules.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_tax_exemption' ) );
		add_action( 'woocommerce_before_checkout_billing_form', array( $this, 'set_tax_exemption' ) );

		// Cart Total Discounts.
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'set_cart_discounts' ) );

		$__user_id = apply_filters( 'wholesalex_dynamic_rule_user_id', get_current_user_id() );

		delete_transient( 'wholesalex_tax_exemption_' . $__user_id );
		delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );
		delete_transient( 'wholesalex_payment_order_quantity_discounts_' . $__user_id );

		$__show_table = wholesalex()->get_setting( '_settings_show_table' );
		if ( isset( $__show_table ) && 'yes' === $__show_table ) {
			add_filter( 'wholesalex_single_product_quantity_based_table', array( $this, 'quantity_based_pricing_table' ), 10, 2 );
			add_filter( 'wholesalex_variation_product_quantity_based_table', array( $this, 'quantity_based_pricing_table' ), 10, 2 );
			add_filter( 'woocommerce_available_variation', array( $this, 'wholesalex_product_variation_price_table' ), 10, 3 );
		}

		$__tier_position = wholesalex()->get_setting( '_settings_tier_position' );
		if ( isset( $__tier_position ) && 'yes' === $__tier_position ) {
			add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'wholesalex_product_price_table' ) );
		} else {
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'wholesalex_product_price_table' ) );
		}

		/**
		 * Remove add to cart button on both price hidden products.
		 */
		$__hide_regular_price = wholesalex()->get_setting( '_settings_hide_retail_price' ) ?? '';

		$__hide_wholesale_price = wholesalex()->get_setting( '_settings_hide_wholesalex_price' ) ?? '';

		if ( 'yes' === (string) $__hide_wholesale_price && 'yes' === (string) $__hide_regular_price ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			// If Both Price hidden, then make products not purchasable.
			add_filter( 'woocommerce_is_purchasable', '__return_false' );
		}

		delete_transient( 'wholesalex_force_free_shipping_' . $__user_id );
		delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );
		delete_transient( 'wholesalex_tax_exemption_' . $__user_id );

		$__priorities = wholesalex()->get_quantity_based_discount_priorities();

		$this->discount_src = $__priorities[0];
		$__priorities       = array_reverse( $__priorities );

		foreach ( $__priorities as $key => $priority ) {
			if ( 0 == $key ) {
				$this->first_sale_price_generator = $priority . '_discounts';
			}

			delete_transient( 'wholesalex_pricing_tiers_' . $priority . '_' . $__user_id );

			$this->discounts_init( $priority . '_discounts' );

		}
		//  add_filter('woocommerce_product_get_price', array($this, 'update_get_price'),10,2);
	}

	/**
	 * Apply WholesaleX Discount By WooCommerce Filter
	 *
	 * @param string $sale_price_generator WholesaleX Sale Price Generator.
	 * @since 1.0.0
	 */
	private function discounts_init( $sale_price_generator ) {
		// get regular price single product.
		add_filter( 'woocommerce_product_get_regular_price', array( $this, 'get_regular_price' ), 9, 2 );
		// get regular price variable product.
		add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'get_regular_price' ), 9, 2 );
		// get single product sale price.
		add_filter( 'woocommerce_product_get_sale_price', array( $this, $sale_price_generator ), 9, 2 );
		// get variable product sale price.
		add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, $sale_price_generator ), 9, 2 );
		add_filter( 'woocommerce_variation_prices_sale_price', array( $this, $sale_price_generator ), 9, 2 );
		add_filter( 'woocommerce_variation_prices_price', array( $this, $sale_price_generator ), 9, 2 );
		// variation product hash.
		add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'variation_price_hash' ), 9, 2 );
		// display sale and regular price.
		add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 9, 2 );
		// Set sale price in Cart.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'display_price_in_cart' ), 9, 1 );
		// Function to make this work for MiniCart.
		/**
		 * Remove woocommerce_cart_item_price this filter to work with minicart
		 *
		 * @since 1.0.10
		 */
		// add_filter( 'woocommerce_cart_item_price', array( $this, 'display_price_in_cart_item' ), 9, 2 );
	}



	/**
	 * Get WholesaleX Regular Price
	 *
	 * @param string|int $product_id Product ID.
	 * @since 1.0.0
	 */
	public function get_wholesalex_regular_price( $product_id ) {
		$__user_role = wholesalex()->get_current_user_role();
		$__discounts = wholesalex()->get_single_product_discount( $product_id );
		if ( isset( $__discounts[ $__user_role ]['wholesalex_base_price'] ) && ! empty( $__discounts[ $__user_role ]['wholesalex_base_price'] ) ) {			
			return max( 0, $__discounts[ $__user_role ]['wholesalex_base_price'] );
		}
	}

	/**
	 * Get Product Regular Price
	 *
	 * @param WC_Price|float $regular_price Woocommerce Product Regular Price.
	 * @param object         $product Product Object.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_regular_price( $regular_price, $product ) {
		$__product_id = $product->get_id();

		$__regular_price = $this->get_wholesalex_regular_price( $__product_id );		
		if ( isset( $__regular_price ) && ! empty( $__regular_price ) ) {
			return $__regular_price;
		}

		if ( empty( $regular_price ) || (float) 0.0 === (float) $regular_price ) {
			return;
		} else {
			return max( 0, $regular_price );
		}
	}

	/**
	 * Single Product Discounts
	 *
	 * @param mixed      $sale_price Sale Price.
	 * @param WC_Product $product Product.
	 * @return mixed Sale Price.
	 * @since 1.0.0
	 * @since 1.0.4 Set Initial Sale Price to Session Added.
	 */
	public function single_product_discounts( $sale_price, $product ) {
		$__product_id = $product->get_id();
		/**
		 * Initial Sale Price Added to session For Further Processing
		 *
		 * @since 1.0.4
		 */
		$this->set_initial_sale_price_to_session( __FUNCTION__, $__product_id, $sale_price );
		$__single_product_show_tier = wholesalex()->get_single_product_setting( $product->get_ID(), '_settings_show_tierd_pricing_table' );
		if ( 'yes' !== $__single_product_show_tier ) {
			remove_filter( 'wholesalex_single_product_quantity_based_table', array( $this, 'quantity_based_pricing_table' ), 10, 2 );
			remove_filter( 'wholesalex_variation_product_quantity_based_table', array( $this, 'quantity_based_pricing_table' ), 10, 2 );
		}

		$__discounts_result = apply_filters(
			'wholesalex_single_product_discount_action',
			array(
				'sale_price' => $sale_price,
				'product'    => $product,
			)
		);

		$sale_price = $__discounts_result['sale_price'];
		if ( isset( $__discounts_result['discount_src'] ) ) {
			$this->discount_src = $__discounts_result['discount_src'];
		}
		if ( isset( $__discounts_result['active_tier_id'] ) ) {
			$this->active_tier_id = $__discounts_result['active_tier_id'];
		}

		if ( empty( $sale_price ) ) {
			return;
		} else {
			$this->set_discounted_product( $__product_id );
			return $sale_price;
		}
	}
	/**
	 * Profile Discounts
	 *
	 * @param mixed      $sale_price Sale Price.
	 * @param WC_Product $product Product.
	 * @return mixed Sale Price.
	 * @since 1.0.0
	 * @since 1.0.4 Set Initial Sale Price to Session Added.
	 */
	public function profile_discounts( $sale_price, $product ) {
		$__user_id    = apply_filters( 'wholesalex_dynamic_rule_user_id', get_current_user_id() );
		$__product_id = $product->get_id();
		/**
		 * Initial Sale Price Added to session For Further Processing
		 *
		 * @since 1.0.4
		 */
		$this->set_initial_sale_price_to_session( __FUNCTION__, $__product_id, $sale_price );

		if ( ! ( 'active' === wholesalex()->get_user_status( $__user_id ) ) ) {
			if ( empty( $sale_price ) ) {
				return;
			} else {
				return $sale_price;
			}
		}

		// delete_transient( 'wholesalex_force_free_shipping_' . $__user_id );
		// delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );

		// Profile Discounts.
		$__discounts_result = apply_filters(
			'wholesalex_profile_discount_action',
			array(
				'sale_price' => $sale_price,
				'product'    => $product,
			)
		);

		$sale_price = $__discounts_result['sale_price'];
		if ( isset( $__discounts_result['discount_src'] ) ) {
			$this->discount_src = $__discounts_result['discount_src'];
		}
		if ( isset( $__discounts_result['active_tier_id'] ) ) {
			$this->active_tier_id = $__discounts_result['active_tier_id'];
		}

		// Retrieve User Profile Dynamic Rules Settings.
		$__profile_settings = get_user_meta( $__user_id, '__wholesalex_profile_settings', true );

		// Profile Tax Section.

		// If User has tax exemption status, then set tax exemption transient true. Which will be used to set tax exemption for current user.
		if ( isset( $__profile_settings['_wholesalex_profile_override_tax_exemption'] ) && 'yes' === $__profile_settings['_wholesalex_profile_override_tax_exemption'] ) {
			set_transient( 'wholesalex_tax_exemption_' . $__user_id, true );
		}

		// Profile Shipping Section.

		if ( isset( $__profile_settings['_wholesalex_profile_override_shipping_method'] ) && 'yes' === $__profile_settings['_wholesalex_profile_override_shipping_method'] ) {

			if ( isset( $__profile_settings['_wholesalex_profile_shipping_method_type'] ) ) {

				switch ( $__profile_settings['_wholesalex_profile_shipping_method_type'] ) {

					case 'force_free_shipping':
						set_transient( 'wholesalex_force_free_shipping_' . $__user_id, true );
						break;
					case 'specific_shipping_methods':
						if ( ! isset( $__profile_settings['_wholesalex_profile_shipping_zone'] ) || ! isset( $__profile_settings['_wholesalex_profile_shipping_zone_methods'] ) ) {
							break;
						}
						delete_transient( 'wholesalex_profile_shipping_methods_' . $__user_id );
						delete_transient( 'wholesalex_shipping_methods_' . $__user_id );

						$__zone_id               = $__profile_settings['_wholesalex_profile_shipping_zone'];
						$__shipping_zone_methods = $__profile_settings['_wholesalex_profile_shipping_zone_methods'];

						$__available_methods = array();

						if ( ! empty( $__shipping_zone_methods ) && is_array( $__shipping_zone_methods ) ) {
							foreach ( $__shipping_zone_methods as $method ) {
								$__available_methods[ $method['value'] ] = true;
							}
						}

						$__shipping_method_transient = get_transient( 'wholesalex_profile_shipping_methods_' . $__user_id );

						if ( ! $__shipping_method_transient && ! empty( $__zone_id ) ) {
							$__temp_shipping_data                                = array();
							$__temp_shipping_data[ $__zone_id ][ $__product_id ] = $__available_methods;
							set_transient( 'wholesalex_profile_shipping_methods_' . $__user_id, $__temp_shipping_data );
						}
						break;
					default:
						// code...
						break;
				}
			}
		}

		// Profile Payment Gateway Section.

		if ( isset( $__profile_settings['_wholesalex_profile_override_payment_gateway'] ) && 'yes' === $__profile_settings['_wholesalex_profile_override_payment_gateway'] ) {
			if ( isset( $__profile_settings['_wholesalex_profile_payment_gateways'] ) && ! empty( $__profile_settings['_wholesalex_profile_payment_gateways'] ) ) {
				$__profile_gateways = $__profile_settings['_wholesalex_profile_payment_gateways'];

				remove_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), 99999 );
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_profile_payment_gateway' ), 99999 );

				set_transient( 'wholesalex_profile_payment_gateways_' . $__user_id, $__profile_gateways );
			}
		}

		if ( empty( $sale_price ) ) {
			return;
		} else {
			$this->set_discounted_product( $__product_id );

			return $sale_price;
		}
	}
	/**
	 * Category Discounts
	 *
	 * @param mixed      $sale_price Sale Price.
	 * @param WC_Product $product Product.
	 * @return mixed Sale Price.
	 * @since 1.0.0
	 * @since 1.0.4 Set Initial Sale Price to Session Added.
	 */
	public function category_discounts( $sale_price, $product ) {
		$__product_id = $product->get_id();
		/**
		 * Initial Sale Price Added to session For Further Processing
		 *
		 * @since 1.0.4
		 */
		$this->set_initial_sale_price_to_session( __FUNCTION__, $__product_id, $sale_price );

		$__discounts_result = apply_filters(
			'wholesalex_category_discount_action',
			array(
				'sale_price' => $sale_price,
				'product'    => $product,
			)
		);

		$sale_price = $__discounts_result['sale_price'];
		if ( isset( $__discounts_result['discount_src'] ) ) {
			$this->discount_src = $__discounts_result['discount_src'];
		}
		if ( isset( $__discounts_result['active_tier_id'] ) ) {
			$this->active_tier_id = $__discounts_result['active_tier_id'];
		}

		if ( empty( $sale_price ) ) {
			return;
		} else {
			$this->set_discounted_product( $__product_id );

			return $sale_price;
		}
	}
	/**
	 * Dynamic Rule Discounts
	 *
	 * @param mixed      $sale_price Sale Price.
	 * @param WC_Product $product Product.
	 * @return mixed Sale Price.
	 * @since 1.0.0
	 * @since 1.0.4 Set Initial Sale Price to Session Added.
	 */
	public function dynamic_rule_discounts( $sale_price, $product ) {
		/**
		 * Initial Sale Price Added to session For Further Processing
		 *
		 * @since 1.0.4
		 */
		$this->set_initial_sale_price_to_session( __FUNCTION__, $product->get_id(), $sale_price );

		remove_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_profile_payment_gateway' ), 99999 );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), 99999 );
		$__override_sale_price = false;
		$__user_id             = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );

		/**
		 * Activation Status Check if user is logged in
		 *
		 * @since 1.0.10
		 */
		if ( is_user_logged_in() ) {
			if ( ! ( $__user_id && 'active' === wholesalex()->get_user_status( $__user_id ) ) ) {
				if ( empty( $sale_price ) ) {
					return;
				} else {
					return $sale_price;
				}
			}
		}

		// Remove All function associated with woocommerce_sale_flash filter.
		remove_all_filters( 'woocommerce_sale_flash' );

		// delete_transient( 'wholesalex_pricing_tiers_dynamic_rule_' . $__user_id );

		$__discounts = wholesalex()->get_dynamic_rules();
		$__role      = wholesalex()->get_current_user_role();

		if ( empty( $__discounts ) ) {
			if ( empty( $sale_price ) ) {
				return;
			} else {

				return $sale_price;
			}
		}

		$__discounts_for_me = array();

		foreach ( $__discounts as $discount ) {
			$__has_discount  = false;
			$__product_id    = $product->get_id();
			$__parent_id     = $product->get_parent_id();
			$__cat_ids       = wc_get_product_term_ids( 0 === $__parent_id ? $__product_id : $__parent_id, 'product_cat' );
			$__regular_price = $product->get_regular_price();
			$__for           = '';
			$__src_id        = '';

			if ( isset( $discount['_rule_status'] ) && $discount['_rule_status'] && ! empty( $discount['_rule_status'] ) && isset( $discount['_product_filter'] ) ) {

				switch ( $discount['_product_filter'] ) {
					case 'all_products':
						$__has_discount = true;
						$__for          = 'all_products';
						$__src_id       = -1;
						break;
					case 'products_in_list':
						if ( ! isset( $discount['products_in_list'] ) ) {
							break;
						}
						foreach ( $discount['products_in_list'] as $list ) {
							if(!isset($list['value'])) {
								continue;
							}
							if ( (int) $__product_id === (int) $list['value'] || (int)$__parent_id === (int)$list['value'] ) {
								$__has_discount = true;
								$__for          = 'product';
								$__src_id       = $__product_id;
								break;
							}
						}
						break;
					case 'products_not_in_list':
						if ( ! isset( $discount['products_not_in_list'] ) ) {
							break;
						}
						$__flag = true;
						foreach ( $discount['products_not_in_list'] as $list ) {
							if ( isset( $list['value'] ) && ((int) $__product_id === (int) $list['value'] || (int)$__parent_id === (int)$list['value']) ) {
								$__flag = false;
							}
						}
						if ( $__flag ) {
							$__has_discount = true;
							$__for          = 'product';
							$__src_id       = $__product_id;
						}
						break;
					case 'cat_in_list':
						if ( ! isset( $discount['cat_in_list'] ) ) {
							break;
						}
						foreach ( $discount['cat_in_list'] as $list ) {
							if (in_array($list['value'], $__cat_ids)) { //phpcs:ignore
								$__has_discount = true;
								$__for          = 'cat';
								$__src_id       = $list['value'];
								break;
							}
						}

						break;

					case 'cat_not_in_list':
						if ( ! isset( $discount['cat_not_in_list'] ) ) {
							break;
						}
						$__flag = true;
						foreach ( $discount['cat_not_in_list'] as $list ) {
							if (in_array($list['value'], $__cat_ids)) { //phpcs:ignore
								$__flag = false;
							}
						}
						if ( $__flag ) {
							$__has_discount = true;
							$__for          = 'cat';
							$__src_id       = $__cat_ids[0];
						}
						break;
					case 'attribute_in_list':
						if ( ! isset( $discount['attribute_in_list'] ) ) {
							break;
						}
						if ( 'product_variation' === $product->post_type ) {
							foreach ( $discount['attribute_in_list'] as $list ) {
								if ( isset( $list['value'] ) && (int) $__product_id === (int) $list['value'] ) {
									$__has_discount = true;
									$__for          = 'variation';
									$__src_id       = $__product_id;
									break;
								}
							}
						}
						break;
					case 'attribute_not_in_list':
						if ( ! isset( $discount['attribute_not_in_list'] ) ) {
							break;
						}
						if ( 'product_variation' === $product->post_type ) {
							$__flag = true;
							foreach ( $discount['attribute_not_in_list'] as $list ) {
								if ( isset( $list['value'] ) && (int) $__product_id === (int) $list['value'] ) {
									$__flag = false;
								}
							}
							if ( $__flag ) {
								$__has_discount = true;
								$__for          = 'variation';
								$__src_id       = $__product_id;
							}
						}
						break;
				}
			}
			if ( ! $__has_discount ) {
				continue;
			}
			if ( isset( $discount['limit'] ) && ! empty( $discount['limit'] ) ) {
				if ( ! self::has_limit( $discount['limit'] ) ) {
					continue;
				}
			}

			if ( ! isset( $discount['_rule_for'] ) ) {
				continue;
			}

			$__role_for = $discount['_rule_for'];
			$__for_me   = false;
			switch ( $__role_for ) {
				case 'specific_roles':
					foreach ( $discount['specific_roles'] as $role ) {
						// Check The Discounts Rules In Valid or not.
						if ( (string) $role['value'] === (string) $__role || 'role_' . $__role === $role['value'] ) {
							array_push( $__discounts_for_me, $discount );
							$__for_me = true;
							break;
						}
					}
					break;
				case 'specific_users':
					foreach ( $discount['specific_users'] as $user ) {
						if ( ( is_numeric( $user['value'] ) && (int) $user['value'] === (int) $__user_id ) || 'user_' . $__user_id === $user['value'] ) {
							array_push( $__discounts_for_me, $discount );
							$__for_me = true;
							break;
						}
					}
					break;
				case 'all_roles':
					$__exclude_roles = apply_filters( 'wholesalex_dynamic_rules_exclude_roles', array( 'wholesalex_guest', 'wholesalex_b2c_users' ) );
					if ( is_array( $__exclude_roles ) && ! empty( $__exclude_roles ) ) {
						if (!in_array($__role, $__exclude_roles)) { //phpcs:ignore
							array_push( $__discounts_for_me, $discount );
							$__for_me = true;
							break;
						}
					} else {
						array_push( $__discounts_for_me, $discount );
						$__for_me = true;
					}
					break;
				case 'all_users':
					$__exclude_users = apply_filters( 'wholesalex_dynamic_rules_exclude_users', array() );
					if ( is_array( $__exclude_users ) && ! empty( $__exclude_users ) ) {
						if (!in_array($__user_id, $__exclude_users)) { //phpcs:ignore
							array_push( $__discounts_for_me, $discount );
							$__for_me = true;
							break;
						}
					} else {
						if ( 0 !== $__user_id ) {
							array_push( $__discounts_for_me, $discount );
							$__for_me = true;
						}
					}
					break;
			}

			if ( isset( $discount['conditions']['tiers'] ) ) {
				$__conditions = $discount['conditions'];
				if ( ! self::is_conditions_fullfiled( $__conditions['tiers'] ) ) {
					continue;
				}
			}

			$__product_settings       = wholesalex()->get_single_product_setting( $__product_id );
			$__override_tax_exemption = true;
			$__override_shipping_rule = true;

			if ( isset( $__product_settings['_settings_override_tax_extemption'] ) && 'disable' === $__product_settings['_settings_override_tax_extemption'] ) {
				$__override_tax_exemption = false;
			}
			if ( isset( $__product_settings['_settings_override_shipping_role'] ) && 'disable' === $__product_settings['_settings_override_shipping_role'] ) {
				$__override_shipping_rule = false;
			}

			if ( ! $__for_me ) {
				continue;
			}

			if ( ! isset( $discount['_rule_type'] ) || ! isset( $discount[ $discount['_rule_type'] ] ) ) {
				continue;
			}

			switch ( $discount['_rule_type'] ) {
				case 'product_discount':
					$sale_price            = wholesalex()->calculate_sale_price( $discount['product_discount'], $__regular_price );
					$__override_sale_price = true;
					wholesalex()->set_usages_dynamic_rule_id( $discount['id'] );
					$__discount_name = isset( $discount['product_discount']['_discount_name'] ) ? $discount['product_discount']['_discount_name'] : '';
					// If discount has any name then discount name will replace the default woocommerce sale flash text.
					if ( ! empty( $__discount_name ) ) {
						add_filter(
							'woocommerce_sale_flash',
							function () use ( $__discount_name ) {
								return '<span class="onsale">' . $__discount_name . '</span>';
							}
						);
					}

					break;
				case 'quantity_based':
					if ( ! isset( $discount['quantity_based']['tiers'] ) ) {
						break;
					}
					$data = apply_filters(
						'wholesalex_dynamic_rule_quantity_based_action',
						array(
							'id'            => $discount['id'],
							'tiers'         => $discount['quantity_based']['tiers'],
							'product_id'    => $__product_id,
							'cat_ids'       => $__cat_ids,
							'regular_price' => $__regular_price,
							'for'           => $__for,
							'src_id'        => $__src_id,
						)
					);
					if ( isset( $data['override_sale_price'] ) && $data['override_sale_price'] ) {
						$__override_sale_price = true;
					}
					if ( isset( $data['active_tier_id'] ) ) {
						$this->active_tier_id = $data['active_tier_id'];
					}
					if ( isset( $data['sale_price'] ) ) {
						$sale_price = $data['sale_price'];
					}

					break;

				case 'payment_discount':
					delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );

					$__payment_discount = $discount['payment_discount'];
					$__rule_id          = $discount['id'];
					if ( ! empty( $__payment_discount ) ) {
						do_action( 'wholesalex_dynamic_rules_payment_discount_action', $__payment_discount, $__rule_id, $__product_id );
					}
					break;
				case 'payment_order_qty':
					delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );

					$__payment_gateways = isset( $discount['payment_order_qty']['_payment_gateways'] ) ? $discount['payment_order_qty']['_payment_gateways'] : array();
					do_action( 'wholesalex_dynamic_rules_payment_order_qty_discount_action', $__payment_gateways, $discount['payment_order_qty']['_order_quantity'], $__product_id );

					break;
				case 'tax_rule':
					if ( ! $__override_tax_exemption ) {
						break;
					}
					$__rules = $discount['tax_rule'];
					do_action( 'wholesalex_dynamic_rules_tax_rules_action', $__rules, $discount['id'], $__product_id );

					break;
				case 'extra_charge':
					delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );

					$__discounts = $discount['extra_charge'];
					$__rule_id   = $discount['id'];

					do_action( 'wholesalex_dynamic_rules_extra_charge_action', $discount['extra_charge'], $discount['id'], $__product_id );

					break;
				case 'cart_discount':
					$__cart_total_discounts = $discount['cart_discount'];
					if ( ! isset( $__cart_total_discounts['_discount_type'] ) || ! isset( $__cart_total_discounts['_discount_amount'] ) ) {
						break;
					}

					$__id = $discount['id'];
					do_action( 'wholesalex_dynamic_rules_cart_discount_action', $__cart_total_discounts, $__id, $__product_id );

					break;
				case 'shipping_rule':
					delete_transient( 'wholesalex_force_free_shipping_' . $__user_id );
					delete_transient( 'wholesalex_profile_shipping_methods_' . $__user_id );

					if ( ! $__override_shipping_rule ) {
						break;
					}

					$__shipping_discounts = $discount['shipping_rule'];
					if ( ! isset( $__shipping_discounts['__shipping_zone'] ) || ! isset( $__shipping_discounts['_shipping_zone_methods'] ) ) {
						break;
					}

					do_action( 'wholesalex_dynamic_rules_shipping_rule_action', $__shipping_discounts, $discount['id'], $__product_id );
					break;
			}
		}

		if ( $__override_sale_price ) {
			$this->discount_src = 'dynamic_rule';
		}

		if ( empty( $sale_price ) ) {
			return;
		} else {
			$this->set_discounted_product( $__product_id );
			return $sale_price;
		}
	}

	/**
	 * Generate Hash for Variation Product
	 *
	 * @param array $hash Hash for Variation Products.
	 * @since 1.0.0
	 * @access public
	 */
	public function variation_price_hash( $hash ) {
		$hash[] = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		return $hash;
	}
	/**
	 * Show Formated WholesaleX Prices
	 *
	 * @param WC_Price|string $price_html Product Sale Price HTML.
	 * @param WC_Product      $product Woocommerce Product Object.
	 * @since 1.0.0
	 * @access public
	 * @return WC_Price $price_html Formated Regular and Sale Price.
	 * @since 1.0.7 is_wholesalex_sale_price_applied Condition Added
	 * @since 1.0.8 Variable Product Pricing, Include, Exclude Tax Issue Fixed.
	 * @since 1.1.4 Duplicate Price (Empty Sale Price) Issue Fixed.
	 */
	public function get_price_html( $price_html, $product ) {
		
		$__product_id         = $product->get_id();
		$__wholesale_products = array();

		if ( ! ( is_object( $product ) && is_a( $product, 'WC_Product' ) ) ) {
			return $price_html;
		}

		/** Login To View Price Section */
		$__view_price_product_list   = wholesalex()->get_setting( '_settings_login_to_view_price_product_list' );
		$__view_price_product_single = wholesalex()->get_setting( '_settings_login_to_view_price_product_page' );

		$__hide_login_to_see_price_message = false;

		if ( 'yes' === $__view_price_product_list && ! is_user_logged_in() && ! ( is_single() ) ) {
			$price_html = '<div><a href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '">' . esc_html( wholesalex()->get_language_n_text( '_language_login_to_see_prices', __( 'Login to see prices', 'wholesalex' ) ) ) . '</a></div>';

			// hide add to cart button also.
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

			remove_all_actions( 'woocommerce_' . $product->get_type() . '_add_to_cart' );

			add_filter( 'woocommerce_is_purchasable', '__return_false' );

			if ( $__hide_login_to_see_price_message ) {
				return;
			}
			return $price_html;

		}

		if ( 'yes' === $__view_price_product_single && ! is_user_logged_in() && is_single() ) {
			$price_html = '<div><a href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '">' . esc_html( wholesalex()->get_language_n_text( '_language_login_to_see_prices', __( 'Login to see prices', 'wholesalex' ) ) ) . '</a></div>';

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			// hide add to cart button also.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

			remove_all_actions( 'woocommerce_' . $product->get_type() . '_add_to_cart' );

			add_filter( 'woocommerce_is_purchasable', '__return_false' );

			if ( $__hide_login_to_see_price_message ) {
				return;
			}
			return $price_html;
		}

		$is_wholesalex_sale_price_applied = true;
		if ( isset( WC()->session ) && ! is_admin() ) {
			$__wholesale_products = WC()->session->get( '__wholesalex_wholesale_products' );
			if ( $product->is_type( 'simple' ) || $product->is_type( 'variation' ) ) {
				if ( $__wholesale_products[ $__product_id ] == $product->get_sale_price() ) {
					$is_wholesalex_sale_price_applied = false;
				}
			}
		}

		if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
			$variations               = $product->get_children();
			$regular_prices           = array();
			$regular_price            = '';
			$sale_prices              = array();
			$sale_price               = '';
			$has_sale_price           = false;
			$has_wholesale_sale_price = false;

			foreach ( $variations as $variation_id ) {
				$single_variation = wc_get_product( $variation_id );
				if ( ! ( is_object( $single_variation ) && is_a( $single_variation, 'WC_Product' ) ) ) {
					return $price_html;
				}
				array_push( $regular_prices, wc_get_price_to_display( $single_variation, array( 'price' => $single_variation->get_regular_price() ) ) );

				if ( isset( $__wholesale_products[ $variation_id ] ) && $__wholesale_products[ $variation_id ] != $single_variation->get_sale_price() ) {
					$has_wholesale_sale_price = true;
				}

				if ( ! empty( $single_variation->get_sale_price() ) ) {
					$has_sale_price = true;
					array_push( $sale_prices, wc_get_price_to_display( $single_variation, array( 'price' => $single_variation->get_sale_price() ) ) );
				} else {
					array_push( $sale_prices, wc_get_price_to_display( $single_variation, array( 'price' => $single_variation->get_regular_price() ) ) );
				}
			}

			sort( $sale_prices );
			sort( $regular_prices );

			if ( empty( $sale_prices ) || empty( $regular_prices ) ) {
				return $price_html;
			}

			if ( $regular_prices[0] === $regular_prices[ count( $regular_prices ) - 1 ] ) {
				$regular_price = wc_price( $regular_prices[0] );
			} else {
				$regular_price = wc_format_price_range( $regular_prices[0], $regular_prices[ count( $regular_prices ) - 1 ] );
			}
			if ( $sale_prices[0] === $sale_prices[ count( $sale_prices ) - 1 ] ) {
				$sale_price = wc_price( $sale_prices[0] );
			} else {
				$sale_price = wc_format_price_range( $sale_prices[0], $sale_prices[ count( $sale_prices ) - 1 ] );
			}

			if ( $has_sale_price ) {
				if ( ! is_single() ) {
					$__product_list_page_price = wholesalex()->get_setting( '_settings_price_product_list_page' );
					$__product_list_page_price = isset( $__product_list_page_price ) ? $__product_list_page_price : '';
					switch ( $__product_list_page_price ) {
						case 'pricing_range':
							$price_html = $this->format_sale_price( $regular_price, $sale_price, $has_wholesale_sale_price ) . $product->get_price_suffix();
							break;
						case 'minimum_pricing':
							$price_html = $this->format_sale_price( $regular_price, $sale_prices[0], $has_wholesale_sale_price ) . $product->get_price_suffix();
							break;
						case 'maximum_pricing':
							$price_html = $this->format_sale_price( $regular_price, $sale_prices[ count( $sale_prices ) - 1 ], $has_wholesale_sale_price ) . $product->get_price_suffix();
							break;
						default:
							$price_html = $this->format_sale_price( $regular_price, $sale_price, $has_wholesale_sale_price ) . $product->get_price_suffix();
							break;
					}
				}
				if ( is_single() ) {
					$price_html = $this->format_sale_price( $regular_price, $sale_price, $has_wholesale_sale_price ) . $product->get_price_suffix();
				}
			} else {
				$price_html = $regular_price;
			}

			/**
			 * Hide Prices on Variable Products.
			 *
			 * @since 1.0.2
			 */
			$__hide_regular_price = wholesalex()->get_setting( '_settings_hide_retail_price' ) ?? '';

			$__hide_wholesale_price = wholesalex()->get_setting( '_settings_hide_wholesalex_price' ) ?? '';

			if ( ! is_admin() ) {
				if ( 'yes' === (string) $__hide_wholesale_price && 'yes' === (string) $__hide_regular_price ) {
					return apply_filters( 'wholesalex_regular_sale_price_hidden_text', wholesalex()->get_language_n_text( '_language_price_is_hidden', 'Price is hidden!' ) );
				}
			}

			return $price_html;
		}

		$product_sale_price = $product->get_sale_price();

		if(( empty($product_sale_price) || (float) $product->get_sale_price() === (float) 0.0 ) && $product->get_regular_price() ) {
			$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), '', $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();
			return $price_html;
		}

		if ( empty( $product_sale_price ) || (float) $product->get_sale_price() === (float) 0.0 ) {
			return $price_html;
		}

		if ( is_shop() || is_product_category() ) {
			$__product_list_page_price = wholesalex()->get_setting( '_settings_price_product_list_page' );
			$__product_list_page_price = isset( $__product_list_page_price ) ? $__product_list_page_price : '';

			$__min_sale_price = wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) );
			$__max_sale_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );

			switch ( $__product_list_page_price ) {
				case 'pricing_range':
					if ( $__min_sale_price === $__max_sale_price ) {
						$__max_sale_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
					}
					$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_format_price_range( $__min_sale_price, $__max_sale_price ), $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();
					break;
				case 'minimum_pricing':
					$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), $__min_sale_price, $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();
					break;
				case 'maximum_pricing':
					$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), $__max_sale_price, $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();
					break;
				default:
					$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) ), $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();
					break;
			}

			return $price_html;
		}
		$price_html = $this->format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) ), $is_wholesalex_sale_price_applied ) . $product->get_price_suffix();

		return $price_html;
	}
	/**
	 * Formate Wholesale Price.
	 *
	 * @param mixed $regular_price Regular Price.
	 * @param mixed $sale_price Sale Price.
	 * @param bool  $is_wholesalex_sale_price_applied Is Applied.
	 * @return mixed Formatted Price
	 * @since 1.0.0
	 * @since 1.0.7 Is WholesaleX Price Applied Condition Added
	 */
	public function format_sale_price( $regular_price, $sale_price, $is_wholesalex_sale_price_applied ) {
		$sale_text = '';
		if ( is_shop() || is_product_category() ) {
			$sale_text = wholesalex()->get_setting( '_settings_price_text_product_list_page' );
		} else {
			$sale_text = wholesalex()->get_setting( '_settings_price_text' );
		}
		$sale_text = empty( $sale_text ) ? __( 'Wholesale Price: ', 'wholesalex' ) : $sale_text;

		$__hide_regular_price = wholesalex()->get_setting( '_settings_hide_retail_price' ) ?? '';

		$__hide_wholesale_price = wholesalex()->get_setting( '_settings_hide_wholesalex_price' ) ?? '';

		if ( ! $is_wholesalex_sale_price_applied ) {
			$sale_text = '';
		}
		if ( ! is_admin() ) {
			if ( 'yes' === (string) $__hide_wholesale_price && 'yes' === (string) $__hide_regular_price ) {
				return apply_filters( 'wholesalex_regular_sale_price_hidden_text', wholesalex()->get_language_n_text( '_language_price_is_hidden', 'Price is hidden!' ) );
			}
			if ( 'yes' === (string) $__hide_regular_price && ! empty( $sale_price ) ) {
				return $sale_text . ( ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) );
			}
			if ( 'yes' === (string) $__hide_wholesale_price && ! empty( $regular_price ) ) {
				return ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price );
			}
		}

		if ( ! empty( $sale_price ) && ! empty( $regular_price ) ) {
			return '<del aria-hidden="true">' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins>' . $sale_text . ( ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) ) . '</ins>';
		}

		if ( ! empty( $sale_price ) ) {
			return '<ins>' . $sale_text . ( ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) ) . '</ins>';
		}
		if ( ! empty( $regular_price ) ) {
			return ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price );
		}
	}

	/**
	 * Set and display WholesaleX Discounted/ Sale price in cart
	 *
	 * @param WC $cart Woocommerce Cart Object.
	 * @since 1.0.0
	 * @access public
	 */
	public function display_price_in_cart( $cart ) {
		if ( is_admin() && ! (defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		if ( is_object( $cart ) ) {
			foreach ( $cart->get_cart() as $cart_item ) {

				
				$__product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
				$__product    = wc_get_product( $__product_id );

				// PPOM Plugin Active
				if(isset($cart_item['ppom']) && wholesalex()->is_ppop_active() ) {
					continue;
				}
				
				

				// If product has any discounts or sale price was alter using any discount rules,
				// Then updated sale price will be display on cart page.

				
				if ( $cart_item['data']->is_on_sale() ) {
					if ( ! empty( $__product->get_sale_price() ) ) {
						$_sale_price = $this->price_after_currency_changed( $__product->get_sale_price() );
						$cart_item['data']->set_price( max( 0, $_sale_price ) );
					}
				} else {
					// If regular product price altered using any discount rules,
					// then updated regular price will be displayed on cart page.
					if ( ! empty( $__product->get_regular_price() ) ) {
						// $_regular_price = $this->price_after_currency_changed( wc_get_price_to_display( $__product, array( 'price' => $__product->get_regular_price() ) ) );
						$_regular_price = $this->price_after_currency_changed( $__product->get_regular_price() );
						// $cart_item['data']->set_price( max( 0, $_regular_price ) );                 }
						$cart_item['data']->set_price( max( 0, $_regular_price ) );
					}
				}
			}
		}
	}

	/**
	 * Set WholesaleX Discounted/ Sale Price for each cart item
	 *
	 * @param float $price Price of current product.
	 * @param array $cart_item Woocommerce Cart Item.
	 * @since 1.0.0
	 * @access public
	 */
	public function display_price_in_cart_item( $price, $cart_item ) {
		return $price;
	}

	/**
	 * Wholesalex Product Price Tablew
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wholesalex_product_price_table() {
		global $post;
		$product_id = $post->ID;
		$product    = wc_get_product( $post->ID );
		if ( ! $product->is_type( 'simple' ) ) {
			return;
		}
		$product_price = $product->get_regular_price();
		$table_data    = apply_filters( 'wholesalex_single_product_quantity_based_table', '', $product_id, $product_price );

		echo wp_kses(
			$table_data,
			array(
				'table' => array(),
				'thead' => array(),
				'tbody' => array(),
				'th'    => array(),
				'tr'    => array( 'id' => array() ),
				'td'    => array(),
				'div'   => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'span'  => array(
					'class' => array(),
					'id'    => array(),
				),
				'style' => array(),
				'pre'   => array(),
			)
		);
	}

	/**
	 * WholesaleX product variation price table
	 *
	 * @param Object $data WC Product Data.
	 * @param Object $product WC Product Object.
	 * @param Object $variation WC Variation Product Data.
	 * @since 1.0.0
	 * @since 1.0.5 In Stock Message Display Multiple Times Issue Fixed.
	 */
	public function wholesalex_product_variation_price_table( $data, $product, $variation ) {
		$product      = $product;
		$variation_id = $variation->get_id();

		$tier_table = $this->quantity_based_pricing_table( '', $variation_id );

		$data['availability_html'] .= $tier_table;
		return $data;
	}

	/**
	 * Quantity Based Pricing Table
	 *
	 * @param array $data Quantity Based Prices.
	 * @param mixed $id Product Id.
	 * @return mixed Table Data.
	 * @since 1.0.0
	 */
	public function quantity_based_pricing_table( $data, $id ) {
		$product = wc_get_product( $id );

		$regular_price = $product->get_regular_price();

		if ( ! empty( $this->get_wholesalex_regular_price( $id ) ) ) {
			$regular_price = $this->get_wholesalex_regular_price( $id );
		}

		ob_start();
		$this->wholesalex_price_table_generator( $regular_price, $product );
		$data .= ob_get_clean();

		return $data;
	}

	/**
	 * Remove Empty Tiers
	 *
	 * @param array $tiers Tiers.
	 * @since 1.0.0
	 */
	private function filter_empty_tier( $tiers ) {
		$__tiers = array();
		if ( ! ( is_array( $tiers ) && ! empty( $tiers ) ) ) {
			return array();
		}
		foreach ( $tiers as $tier ) {
			if ( isset( $tier['_discount_type'] ) && ! empty( $tier['_discount_type'] ) && isset( $tier['_discount_amount'] ) && ! empty( $tier['_discount_amount'] ) && isset( $tier['_min_quantity'] ) && ! empty( $tier['_min_quantity'] ) ) {
				array_push( $__tiers, $tier );
			}
		}
		return $__tiers;
	}

	/**
	 * Price Table Layout CSS
	 *
	 * @return void
	 */
	public function price_table_layout_css() {
		$__primary_color                  = wholesalex()->get_setting( '_settings_primary_color' );
		$__primary_hover_color            = wholesalex()->get_setting( 'settings_primary_hover_color' );
		$__text_color                     = wholesalex()->get_setting( '_settings_text_color' );
		$__border_color                   = wholesalex()->get_setting( '_settings_border_color' );
		$__active_tier_color              = wholesalex()->get_setting( '_settings_active_tier_color' );
		$__tire_layout_before_add_to_cart = 'yes' === wholesalex()->get_setting( '_settings_tier_position' );
		?>
		<style>
			#_active_row {
				color: white;
				background-color: <?php echo esc_attr( $__active_tier_color ); ?>;
			}

			.wholesalex-price-table {
				color: <?php echo esc_attr( $__text_color ); ?>;
				padding-top: <?php echo esc_attr( ! $__tire_layout_before_add_to_cart ? '25px' : '0px' ); ?>;
				padding-bottom: <?php echo esc_attr( $__tire_layout_before_add_to_cart ? '25px' : '0px' ); ?>;
			}

			.wholesalex-price-table tbody,thead {
				text-align: center;
			}

			.wholesalex-price-table table tr {
				background-color: #fdfdfd;
			}

			.wholesalex-price-table table th {
				background-color: #f8f8f8;
			}

			.wholesalex-price-table table th,
			.wholesalex-price-table table td {
				padding: 15px;
			}

			.wholesalex-price-table table,
			.wholesalex-price-table tr,
			.wholesalex-price-table td {
				border-collapse: collapse;
			}

			/**
			 * Tire Layout Two, Five
			 */


			.wholesalex-price-table.layout-two .layout_two_title,
			.wholesalex-price-table.layout-five .layout_five_title,
			.wholesalex-price-table.layout-six .layout_six_title,
			.wholesalex-price-table.layout-seven .layout_seven_title {
				font-size: 20px;
				color: <?php echo esc_attr( $__text_color ); ?>;
				font-weight: 500;
				line-height: 1.4;
			}

			.wholesalex-price-table.layout-two .layout-two-tiers,
			.wholesalex-price-table.layout-five .layout-five-tiers,
			.wholesalex-price-table.layout-seven .layout-seven-tiers {
				display: flex;
				padding: 20px;
				gap: 20px;
				flex-wrap: wrap;
				background-color: #f8f8f8;
			}

			.wholesalex-price-table.layout-two .prices_heading,
			.wholesalex-price-table.layout-five .prices_heading,
			.wholesalex-price-table.layout-six .prices_heading,
			.wholesalex-price-table.layout-seven .prices_heading {
				font-size: 16px;
				font-weight: bold;
				line-height: 1.75;
				color: <?php echo esc_attr( $__text_color ); ?>;
			}

			.wholesalex-price-table.layout-two .quantities_heading,
			.wholesalex-price-table.layout-five .quantities_heading,
			.wholesalex-price-table.layout-six .quantities_heading,
			.wholesalex-price-table.layout-seven .quantities_heading {
				font-weight: bold;
				font-size: 14px;
				line-height: 2;
				color: <?php echo esc_attr( $__text_color ); ?>;
			}

			.wholesalex-price-table.layout-two .price,
			.wholesalex-price-table.layout-five .price,
			.wholesalex-price-table.layout-six .price,
			.wholesalex-price-table.layout-seven .price {
				font-size: 16px;
				font-weight: 500;
				color: #f4a019;
				line-height: 1.75;
			}

			.wholesalex-price-table.layout-two,
			.wholesalex-price-table.layout-five,
			.wholesalex-price-table.layout-six,
			.wholesalex-price-table.layout-seven {
				display: flex;
				flex-direction: column;
				gap: 15px;
				padding-bottom: <?php echo esc_attr( $__tire_layout_before_add_to_cart ? '25px' : '0px' ); ?>;
			}

			.wholesalex-price-table.layout-two .sale_amount,
			.wholesalex-price-table.layout-five .sale_amount,
			.wholesalex-price-table.layout-six .sale_amount,
			.wholesalex-price-table.layout-seven .sale_amount {
				background-color: black;
				color: white;
				font-size: 12px;
				font-weight: 500;
				line-height: 2.33;
				padding: 2px 5px;
				border-radius: 2px;
			}

			.layout-two-tiers .tier:not(:last-child),
			.layout-five-tiers .tier:not(:last-child) {
				padding-right: 15px;
				border-right: solid 1px <?php echo esc_attr( $__border_color ); ?>;
			}

			.wholesalex-price-table.layout-two .product_quantity,
			.wholesalex-price-table.layout-five .product_quantity,
			.wholesalex-price-table.layout-six .product_quantity,
			.wholesalex-price-table.layout-seven .product_quantity,
			.layout-three-tiers .product_quantity,
			.layout-eight-tiers .product_quantity
			{
				font-size: 14px;
				line-height: 28px;
				color: <?php echo esc_attr( $__text_color ); ?>;
				display: flex;
				gap:5px;
			}

			.wholesalex-price-table.layout-two .quantities,
			.wholesalex-price-table.layout-five .quantities,
			.wholesalex-price-table.layout-six .quantities,
			.wholesalex-price-table.layout-seven .quantities,
			.layout-three-tiers .quantities,
			.layout-eight-tiers .quantities {
				color: <?php echo esc_attr( $__text_color ); ?>;
				font-weight: bold;
			}

			/** Layout Three */

			.wholesalex-price-table.layout-three{
				margin-bottom: <?php echo esc_attr( $__tire_layout_before_add_to_cart ? '25px' : '0px' ); ?>;
			}

			.layout-three-tiers .tier .price,.layout-eight-tiers .tier .price {
				color: #f4a019;
				font-size: 24px;
				font-weight: 500;
				line-height: 1.17;
			}

			.layout-three-tiers .product_quantity::before,.layout-eight-tiers .product_quantity::before {
				content: "/";
				color: <?php echo esc_attr( $__text_color ); ?>;
				padding-right: 5px;
			}

			.layout-three-tiers .tier,.layout-eight-tiers .tier {
				display: flex;
				gap: 15px;
			}

			.wholesalex-price-table.layout-three, .wholesalex-price-table.layout-eight {
				border-top: 1px solid <?php echo esc_attr( $__border_color ); ?>;
				border-bottom: 1px solid <?php echo esc_attr( $__border_color ); ?>;
				padding-top: 20px;
				padding-bottom: 20px;
			}

			/** Tire Layout Four */
			.wholesalex-price-table.layout_four table tr {
				background-color: #f9f9f9;
			}

			.wholesalex-price-table.layout_four table tr:nth-child(even) {
				background-color: #f0f0f0;
			}

			.wholesalex-price-table.layout_four table th {
				background-color: #e2e2e2;
			}


			/** Layout Six */

			.wholesalex-price-table.layout-six .heading,
			.wholesalex-price-table.layout-six .tier {
				background-color: white;
			}

			/* .wholesalex-price-table.layout-six .layout-six-tiers {
				padding: 0px;
				gap: 0px;
			} */

			.wholesalex-price-table.layout-six .layout-six-tiers {
				display: flex;
				flex-wrap: wrap;
			}
			.wholesalex-price-table.layout-six .heading,
			.wholesalex-price-table.layout-six .tier{ 
				border:1px solid <?php echo esc_attr( wholesalex()->get_setting( '_settings_border_color' ) ); ?>;
			}

			/* .heading,.tier:not(:last-child){
				border-right: none;
			} */
			.wholesalex-price-table.layout-six .quantities_heading,
				.wholesalex-price-table.layout-six .product_quantity {
				border-top:1px solid <?php echo esc_attr( wholesalex()->get_setting( '_settings_border_color' ) ); ?>;
			}

			.wholesalex-price-table.layout-six .prices_heading,
			.wholesalex-price-table.layout-six .quantities_heading,.wholesalex-price-table.layout-six .quantities,.wholesalex-price-table.layout-six .quantity_text,.wholesalex-price-table.layout-six .price {
				padding:15px;
			}
			.wholesalex-price-table.layout-six .quantities {
				padding-right: 0px;
			}
			.wholesalex-price-table.layout-six .quantity_text{
				padding-left: 5px;
			}

			/** Layout Seven */
			.wholesalex-price-table.layout-seven .layout-seven-tiers{
				background-color: white;
				border-top: 1px solid <?php echo esc_attr( wholesalex()->get_setting( '_settings_border_color' ) ); ?>;
				border-bottom: 1px solid <?php echo esc_attr( wholesalex()->get_setting( '_settings_border_color' ) ); ?>;
				width: fit-content;
			}
			/* .wholesalex-price-table.layout-seven .product_quantity,
			.wholesalex-price-table.layout-eight .product_quantity{
				gap:5px;
			} */

			/** Layout Eight */

			.wholesalex-price-table.layout-eight .layout-eight-tiers {
				display: flex;
				column-gap: 20px;
				row-gap: 15px;
				flex-wrap: wrap;
			}

			.wholesalex-price-table.layout-eight {
				display: flex;
				flex-direction: column;
				gap: 15px;
				margin-bottom: <?php echo esc_attr( $__tire_layout_before_add_to_cart ? '25px' : '0px' ); ?>;
			}
		</style>
		<?php
	}
	/**
	 * WholesaleX Price Table generator
	 *
	 * @param float  $regular_price Product Regular Price.
	 * @param object $product Product Object.
	 * @return void
	 * @since 1.0.0
	 */
	public function wholesalex_price_table_generator( $regular_price, $product ) {
		$__user_id         = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		$__merge_all_table = wholesalex()->get_setting( '_settings_pricing_table' );
		/**
		 * Tier Layout Style Deprecated Warning Fixed
		 *
		 * @since 1.0.4
		 */
		if ( null !== ( $this->price_table_layout_css() ) ) {
			wp_add_inline_style( 'wholesalex', $this->price_table_layout_css() );
		}

		$quantity_prices = array();

		if ( ! empty( $__merge_all_table ) && 'yes' === $__merge_all_table ) {
			$__priorities = wholesalex()->get_quantity_based_discount_priorities();

			foreach ( $__priorities as $priority ) {
				$__temp_quantity_prices = get_transient( 'wholesalex_pricing_tiers_' . $priority . '_' . $__user_id );
				if ( $__temp_quantity_prices && ! empty( $__temp_quantity_prices ) ) {
					$quantity_prices = array_merge( $quantity_prices, $__temp_quantity_prices );
				}
			}
		} else {
			$__priorities = wholesalex()->get_quantity_based_discount_priorities();

			$quantity_prices = get_transient( 'wholesalex_pricing_tiers_' . $this->discount_src . '_' . $__user_id );

			if ( empty( $quantity_prices ) ) {
				foreach ( $__priorities as $priority ) {
					$__temp_quantity_prices = get_transient( 'wholesalex_pricing_tiers_' . $priority . '_' . $__user_id );
					if ( $__temp_quantity_prices && ! empty( $__temp_quantity_prices ) ) {
						$quantity_prices = $__temp_quantity_prices;
						break;
					}
				}
			}
		}
		if ( empty( $quantity_prices ) ) {
			return;
		}
		if ( isset( $quantity_prices['_min_quantity'] ) ) {
			$__sort_colum = array_column( $quantity_prices, '_min_quantity' );
			array_multisort( $__sort_colum, SORT_ASC, $quantity_prices );
		}
		$__show_source = apply_filters( 'wholesalex_pricing_tier_source', WP_DEBUG );

		$__wc_currency = get_option( 'woocommerce_currency' );

		$__show_heading = apply_filters( 'wholesalex_tier_layout_six_show_heading', true );

		$__tier_layout = '';

		$__global_tier_layout = wholesalex()->get_setting( '_settings_tier_layout' );
		if ( $__global_tier_layout ) {
			$__tier_layout = apply_filters( 'wholesalex_tier_layout', $__global_tier_layout );
		}

		/**
		 * Variable Product Tier Table Layout Not Working (Fixed)
		 *
		 * @since 1.0.3
		 */
		$__parent_id = $product->get_parent_id();

		$__single_product_tier_layout = wholesalex()->get_single_product_setting( $__parent_id ? $__parent_id : $product->get_ID(), '_settings_tier_layout' );

		if ( $__single_product_tier_layout ) {
			$__tier_layout = apply_filters( 'wholesalex_tier_layout', $__single_product_tier_layout );
		}

		switch ( $__tier_layout ) {
			case 'layout_one':
				?>
				<div class="wholesalex-price-table">
					<table>
						<thead>
							<tr>
								<th>
									<?php esc_html_e( 'Product Quantity', 'wholesalex' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Discount', 'wholesalex' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Price per Unit', 'wholesalex' ); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$__tier_size = count( $quantity_prices );

							for ( $i = 0; $i < $__tier_size; $i++ ) {
								$__current_tier = $quantity_prices[ $i ];
								$__next_tier    = '';
								if ( ( $__tier_size ) - 1 !== $i ) {
									$__next_tier = $quantity_prices[ $i + 1 ];
								}

								$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
								$__discount   = $regular_price - $__sale_price;

								$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

								?>
								<tr id=<?php echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' ); ?>>
									<td>
										<?php
										if ( isset( $__current_tier['_min_quantity'] ) ) {
											if ( ! empty( $__next_tier ) && ( $__next_tier['_min_quantity'] - 1 ) > $__current_tier['_min_quantity'] ) {

												if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
													echo esc_html( $__current_tier['_min_quantity'] );
												} else {
													echo ( esc_html( $__current_tier['_min_quantity'] ) ) . '-' . esc_html( $__next_tier['_min_quantity'] - 1 );
												}
											} else {
												echo ( esc_html( $__current_tier['_min_quantity'] ) ) . '+';
											}
										}

										?>
									</td>
									<td>
										<?php echo wp_kses_post( wc_price( $__discount ) ); ?>
									</td>
									<td>
										<?php echo wp_kses_post( wc_price( $__sale_price ) ); ?>
									</td>
									<?php
									if ( isset( $__current_tier['src'] ) && $__show_source && current_user_can( 'manage_options' ) ) {
										echo '<td>' . esc_html( $__current_tier['src'] ) . '</td>';
									}

									?>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php

				break;
			case 'layout_two':
				$__show_discount_amount = apply_filters( 'wholesalex_tier_layout_two_show_discount_amount', true );
				?>
				<div class="wholesalex-price-table layout-two">
					<div class="layout_two_title"><?php esc_html_e( 'Tier Purchase', 'wholesalex' ); ?></div>
					<div class="layout-two-tiers">
						<?php

						$__tier_size    = count( $quantity_prices );
						$__show_heading = false;

						if ( $__show_heading ) {
							$__prices_heading     = apply_filters( 'wholesalex_tier_layout_two_prices_heading', __( 'Price per Unit', 'wholesalex' ) );
							$__quantities_heading = apply_filters( 'wholesalex_tier_layout_two_quantities_heading', __( 'Quantity (Pieces)', 'wholesalex' ) );
							?>
							<div class="heading">
								<div class="prices_heading">
									<?php echo esc_html( $__prices_heading ); ?>
								</div>
								<div class="quantities_heading">
									<?php echo esc_html( $__quantities_heading ); ?>
								</div>
							</div>
							<?php
						}

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
							<div class="tier" id=
							<?php
							echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
							?>
													>
								<div class="price">
									<?php
									if ( $__show_discount_amount ) {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' <span class="sale_amount">' . - ( (float) $__discount / (float) $regular_price ) * 100.00 . '% </span>' );
									} else {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' ' );
									}
									?>
								</div>
								<div class="product_quantity">
									<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
									</span>
									<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
									</span>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php

				break;
			case 'layout_three':
				?>
				<div class="wholesalex-price-table layout-three">
					<div class="layout-three-tiers">
						<?php

						$__tier_size = count( $quantity_prices );

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
							<div class="tier" id=
							<?php
							echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
							?>
													>
								<div class="price">
									<?php echo wp_kses_post( wc_price( $__sale_price ) ); ?>
								</div>
								<div class="product_quantity">
									<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
									</span>
									<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
									</span>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				break;
			case 'layout_four':
				?>
				<div class="wholesalex-price-table layout_four">
					<table>
						<thead>
							<tr>
								<th>
									<?php esc_html_e( 'Product Quantity', 'wholesalex' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Discount', 'wholesalex' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Price per Unit', 'wholesalex' ); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$__tier_size = count( $quantity_prices );

							for ( $i = 0; $i < $__tier_size; $i++ ) {
								$__current_tier = $quantity_prices[ $i ];
								$__next_tier    = '';
								if ( ( $__tier_size ) - 1 !== $i ) {
									$__next_tier = $quantity_prices[ $i + 1 ];
								}

								$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
								$__discount   = $regular_price - $__sale_price;

								$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

								?>
								<tr id=<?php echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' ); ?>>
									<td>
										<?php
										if ( isset( $__current_tier['_min_quantity'] ) ) {
											if ( ! empty( $__next_tier ) && ( $__next_tier['_min_quantity'] - 1 ) > $__current_tier['_min_quantity'] ) {

												if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
													echo esc_html( $__current_tier['_min_quantity'] );
												} else {
													echo ( esc_html( $__current_tier['_min_quantity'] ) ) . '-' . esc_html( $__next_tier['_min_quantity'] - 1 );
												}
											} else {
												echo ( esc_html( $__current_tier['_min_quantity'] ) ) . '+';
											}
										}

										?>
									</td>
									<td>
										<?php echo wp_kses_post( wc_price( $__discount ) ); ?>
									</td>
									<td>
										<?php echo wp_kses_post( wc_price( $__sale_price ) ); ?>
									</td>
									<?php
									if ( isset( $__current_tier['src'] ) && $__show_source && current_user_can( 'manage_options' ) ) {
										echo '<td>' . esc_html( $__current_tier['src'] ) . '</td>';
									}

									?>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php

				break;
			case 'layout_five':
				$__show_discount_amount = apply_filters( 'wholesalex_tier_layout_five_show_discount_amount', false );
				?>
				<div class="wholesalex-price-table layout-five">
					<div class="layout_five_title"><?php esc_html_e( 'Tier Purchase', 'wholesalex' ); ?></div>
					<div class="layout-five-tiers">
						<?php

						$__tier_size    = count( $quantity_prices );
						$__show_heading = false;

						if ( $__show_heading ) {
							$__prices_heading     = apply_filters( 'wholesalex_tier_layout_five_prices_heading', __( 'Price per Unit', 'wholesalex' ) );
							$__quantities_heading = apply_filters( 'wholesalex_tier_layout_five_quantities_heading', __( 'Quantity (Pieces)', 'wholesalex' ) );
							?>
							<div class="heading">
								<div class="prices_heading">
									<?php echo esc_html( $__prices_heading ); ?>
								</div>
								<div class="quantities_heading">
									<?php echo esc_html( $__quantities_heading ); ?>
								</div>
							</div>
							<?php
						}

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
							<div class="tier" id=
							<?php
							echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
							?>
													>
								<div class="price">
									<?php
									if ( $__show_discount_amount ) {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' <span class="sale_amount">' . - ( (float) $__discount / (float) $regular_price ) * 100.00 . '% </span>' );
									} else {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' ' );
									}
									?>
								</div>
								<div class="product_quantity">
									<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
									</span>
									<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
									</span>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php

				break;
			case 'layout_six':
				$__show_discount_amount = apply_filters( 'wholesalex_tier_layout_six_show_discount_amount', false );
				?>
				<div class="wholesalex-price-table layout-six">
					<div class="layout_six_title"><?php esc_html_e( 'Tier Purchase', 'wholesalex' ); ?></div>
					<div class="layout-six-tiers">
						<?php

						$__tier_size = count( $quantity_prices );

						if ( $__show_heading ) {
							$__prices_heading     = apply_filters( 'wholesalex_tier_layout_six_prices_heading', __( 'Price per Unit', 'wholesalex' ) );
							$__quantities_heading = apply_filters( 'wholesalex_tier_layout_six_quantities_heading', __( 'Quantity (Pieces)', 'wholesalex' ) );
							?>
							<div class="heading">
								<div class="prices_heading">
									<?php echo esc_html( $__prices_heading ); ?>
								</div>
								<div class="quantities_heading">
									<?php echo esc_html( $__quantities_heading ); ?>
								</div>
							</div>
							<?php
						}

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
							<div class="tier" id=
							<?php
							echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
							?>
													>
								<div class="price">
									<?php
									if ( $__show_discount_amount ) {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' <span class="sale_amount">' . - ( (float) $__discount / (float) $regular_price ) * 100.00 . '% </span>' );
									} else {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' ' );
									}
									?>
								</div>
								<div class="product_quantity">
									<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
									</span>
									<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
									</span>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php

				break;
			case 'layout_seven':
				$__show_discount_amount = apply_filters( 'wholesalex_tier_layout_seven_show_discount_amount', false );
				?>
					<div class="wholesalex-price-table layout-seven">
						<div class="layout_seven_title"><?php esc_html_e( 'Tier Purchase', 'wholesalex' ); ?></div>
						<div class="layout-seven-tiers">
						<?php

						$__tier_size = count( $quantity_prices );

						$__show_heading = false;

						if ( $__show_heading ) {
							$__prices_heading     = apply_filters( 'wholesalex_tier_layout_seven_prices_heading', __( 'Price per Unit', 'wholesalex' ) );
							$__quantities_heading = apply_filters( 'wholesalex_tier_layout_seven_quantities_heading', __( 'Quantity (Pieces)', 'wholesalex' ) );
							?>
								<div class="heading">
									<div class="prices_heading">
									<?php echo esc_html( $__prices_heading ); ?>
									</div>
									<div class="quantities_heading">
									<?php echo esc_html( $__quantities_heading ); ?>
									</div>
								</div>
							<?php
						}

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
								<div class="tier" id=
								<?php
								echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
								?>
														>
									<div class="price">
									<?php
									if ( $__show_discount_amount ) {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' <span class="sale_amount">' . - ( (float) $__discount / (float) $regular_price ) * 100.00 . '% </span>' );
									} else {
										echo wp_kses_post( $__wc_currency . ' ' . wc_price( $__sale_price ) . ' ' );
									}
									?>
									</div>
									<div class="product_quantity">
										<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
										</span>
										<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
										</span>
									</div>
								</div>
							<?php
						}
						?>
						</div>
					</div>
				<?php

				break;
			case 'layout_eight':
				?>
					<div class="wholesalex-price-table layout-eight">
						<div class="layout-eight-tiers">
						<?php

						$__tier_size = count( $quantity_prices );

						for ( $i = 0; $i < $__tier_size; $i++ ) {
							$__current_tier = $quantity_prices[ $i ];
							$__next_tier    = '';
							if ( ( $__tier_size ) - 1 !== $i ) {
								$__next_tier = $quantity_prices[ $i + 1 ];
							}

							$__sale_price = wholesalex()->calculate_sale_price( $__current_tier, $regular_price );
							$__discount   = $regular_price - $__sale_price;

							$__sale_price = wc_get_price_to_display( $product, array( 'price' => $__sale_price ) );

							$__quantities = '';

							if ( isset( $__current_tier['_min_quantity'] ) ) {
								if ( ! empty( $__next_tier ) ) {

									if ( $__current_tier['_min_quantity'] === $__next_tier['_min_quantity'] ) {
										$__quantities = $__current_tier['_min_quantity'];
									} else {
										$__quantities = $__current_tier['_min_quantity'] . '-' . ( (int) $__next_tier['_min_quantity'] - 1 );
									}
								} else {
									$__quantities = $__current_tier['_min_quantity'] . '+';
								}
							}

							?>
								<div class="tier" id=
							<?php
							echo esc_attr( ( $__current_tier['_id'] === $this->active_tier_id ) ? '_active_row' : '' );
							?>
														>
									<div class="price">
									<?php echo wp_kses_post( wc_price( $__sale_price ) ); ?>
									</div>
									<div class="product_quantity">
										<span class="quantities">
										<?php echo esc_html( $__quantities ); ?>
										</span>
										<span class="quantity_text">
										<?php esc_html_e( 'Pieces', 'wholesalex' ); ?>
										</span>
									</div>
								</div>
								<?php
						}
						?>
						</div>
					</div>
					<?php
				break;

			default:
				// code...
				break;
		}

		?>

		<?php

	}

	/**
	 * Check the dynamic rule is valid or not
	 *
	 * @param array|empty $__limits Limits.
	 * @return boolean
	 * @since 1.0.0
	 * @since 1.0.8 Make Static To WholesaleX Pro
	 */
	public static function has_limit( $__limits ) {
		if ( is_array( $__limits ) && ! empty( $__limits ) ) {
			// Check if the discount has any usages limit, if have , then check is any remaining.
			$__is_remaining = true;
			if ( isset( $__limits['_usage_limit'] ) && ! empty( $__limits['_usage_limit'] ) && isset( $__limits['usages_count'] ) ) {
				$__limit        = (int) $__limits['_usage_limit'];
				$__usages_count = (int) $__limits['usages_count'];
				$__remaining    = $__limit - $__usages_count;
				if ( $__remaining < 1 ) {
					$__is_remaining = false;
				}
			}

			// Check if rule has any start and end date limit or not. If have then check is the rule is valid for today.
			$__today        = gmdate( 'Y-m-d' );
			$__has_duration = true;
			if ( isset( $__limits['_start_date'] ) && ! empty( $__limits['_start_date'] ) ) {

				$__start_date = gmdate( 'Y-m-d', strtotime( $__limits['_start_date'] . ' +1 day' ) );

				if ( $__today < $__start_date ) {
					$__has_duration = false;
				}
			}
			if ( isset( $__limits['_end_date'] ) && ! empty( $__limits['_end_date'] ) ) {

				$__end_date = gmdate( 'Y-m-d', strtotime( $__limits['_end_date'] . ' +1 day' ) );

				if ( $__today > $__end_date ) {
					$__has_duration = false;
				}
			}

			return $__has_duration && $__is_remaining;
		}
		return true;
	}

	/**
	 * Check Single Conditions
	 *
	 * @param float  $conditions_value Conditions Value.
	 * @param string $operator Compare Operator.
	 * @param float  $value Value.
	 * @return boolean
	 * @since 1.0.0
	 * @since 1.0.1 Fixed Less Than Not Working Issue With Backward Compability.
	 */
	public static function is_condition_passed( $conditions_value, $operator, $value ) {
		if ( '>' === $operator || 'greater' === $operator ) {
			if ( $value > $conditions_value ) {
				return true;
			}
		} elseif ( '<' === $operator || 'less' === $operator ) {
			if ( $value < $conditions_value ) {
				return true;
			}
		} elseif ( '>=' === $operator || 'greater_equal' === $operator ) {
			if ( $value >= $conditions_value ) {
				return true;
			}
		} elseif ( '<=' === $operator || 'less_equal' === $operator ) {
			if ( $value <= $conditions_value ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check the dynamic rule has any conditions and if has then check the rules meets the conditions or not
	 *
	 * @param array $conditions Conditions Array.
	 * @since 1.0.0
	 * @since 1.0.4 Order Count Coditions Added.
	 * @since 1.0.4 Total Purchase Amount Added.
	 * @since 1.0.8 Make Static to Use on WholesaleX Pro
	 */
	public static function is_conditions_fullfiled( $conditions ) {
		if ( is_admin() || null === WC()->cart ) {
			return true;
		}

		$__status               = true;
		$__total_cart_counts    = 0;
		$__is_unique_cart_count = apply_filters( 'wholesalex_is_unique_cart_count', false );
		if ( $__is_unique_cart_count ) {
			$__total_cart_counts = count( WC()->cart->get_cart() );
		} else {
			$__total_cart_counts = null !== WC()->cart->get_cart_contents_count() ? WC()->cart->get_cart_contents_count() : 0;
		}
		$__total_cart_weight = wholesalex()->get_cart_total_weight();

		$__total_cart_total = wholesalex()->get_cart_total();
		$__number_of_orders = 0;
		$__total_spent      = 0;
		if ( is_user_logged_in() ) {
			$user_id            = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
			$__number_of_orders = wc_get_customer_order_count( $user_id );
			$__total_spent      = wc_get_customer_total_spent( $user_id );
		}

		foreach ( $conditions as $condition ) {
			$__conditions_value = isset( $condition['_conditions_value'] ) ? (float) $condition['_conditions_value'] : 0;
			$__conditions_for   = isset( $condition['_conditions_for'] ) ? $condition['_conditions_for'] : '';

			if ( ! isset( $condition['_conditions_operator'] ) ) {
				continue;
			}

			if ( 'order_count' === $__conditions_for && ! wholesalex()->is_pro_active() ) {
				continue;
			}
			if ( 'total_purchase' === $__conditions_for && ! wholesalex()->is_pro_active() ) {
				continue;
			}
			switch ( $__conditions_for ) {
				case 'cart_total_value':
					$__status = self::is_condition_passed( $__conditions_value, $condition['_conditions_operator'], $__total_cart_total );
					break;
				case 'cart_total_qty':
					$__status = self::is_condition_passed( $__conditions_value, $condition['_conditions_operator'], $__total_cart_counts );
					break;
				case 'cart_total_weight':
					$__status = self::is_condition_passed( $__conditions_value, $condition['_conditions_operator'], $__total_cart_weight );
					break;
				case 'order_count':
					$__status = self::is_condition_passed( $__conditions_value, $condition['_conditions_operator'], $__number_of_orders );
					break;
				case 'total_purchase':
					$__status = self::is_condition_passed( $__conditions_value, $condition['_conditions_operator'], $__total_spent );
					break;
			}
		}
		return $__status;
	}
	/**
	 * Set Available Payment Gateways
	 *
	 * @param array $gateways Payment Gateways.
	 * @since 1.0.0
	 */
	public function available_payment_gateways( $gateways ) {
		$__override_status         = false;
		$__user_id                 = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		$__payment_order_discounts = get_transient( 'wholesalex_payment_order_quantity_discounts_' . $__user_id );

		if ( ! $__payment_order_discounts || empty( $__payment_order_discounts ) ) {
			return $gateways;
		}

		$__available_gateways = $gateways;

		foreach ( $__payment_order_discounts as $discount ) {

			$__quantity     = (int) wholesalex()->cart_count( $discount['product_id'] );
			$__min_quantity = isset( $discount['min_quantity'] ) ? (int) $discount['min_quantity'] : 999999999;

			if ( $__quantity < $__min_quantity && isset( $discount['gateways'] ) && ! empty( $discount['gateways'] ) ) {
				foreach ( $discount['gateways'] as $gateway ) {
					if ( isset( $gateways[ $gateway['value'] ] ) ) {
						unset( $__available_gateways[ $gateway['value'] ] );
					}
				}
				$__override_status = true;
			}
		}
		if ( empty( $__available_gateways ) ) {
			$__available_gateways = $gateways;
		}
		set_transient( 'wholesalex_payment_order_quantity_discounts_' . $__user_id, array() );

		$__available_gateways = apply_filters( 'wholesalex_available_payment_gateways', $__available_gateways, $__override_status );

		return $__available_gateways;
	}

	/**
	 * Available Profile Payment Gateways
	 *
	 * @param array $gateways Payment Gateways.
	 * @since 1.0.0
	 */
	public function available_profile_payment_gateway( $gateways ) {
		if ( ! is_admin() && ( is_checkout() || is_cart() ) ) {
			$__user_id          = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
			$__profile_gateways = get_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );
			delete_transient( 'wholesalex_profile_payment_gateways_' . $__user_id );

			if ( ! $__profile_gateways || empty( $__profile_gateways ) ) {
				return $gateways;
			}
			$__available_gateways = array();
			foreach ( $__profile_gateways as $profile_gateway ) {
				if ( null !== $gateways[ $profile_gateway['value'] ] ) {
					$__available_gateways[ $profile_gateway['value'] ] = $gateways[ $profile_gateway['value'] ];
				}
			}

			$__available_gateways = apply_filters( 'wholesalex_available_payment_gateways', $__available_gateways, true );

			if ( $__available_gateways && ! empty( $__available_gateways ) ) {
				return $__available_gateways;
			}
		}

		return $gateways;
	}

	/**
	 * Set Tax Exemption
	 *
	 * @since 1.0.0
	 */
	public function set_tax_exemption() {
		if ( is_admin() || null === WC()->customer ) {
			return;
		}
		$__user_id          = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		$__is_tax_exemption = get_transient( 'wholesalex_tax_exemption_' . $__user_id );

		if ( ! $__is_tax_exemption || empty( $__is_tax_exemption ) ) {
			WC()->customer->set_is_vat_exempt( false );
			return;
		}
		if ( $__is_tax_exemption ) {
			WC()->customer->set_is_vat_exempt( true );
		} else {
			WC()->customer->set_is_vat_exempt( false );
		}
		delete_transient( 'wholesalex_tax_exemption_' . $__user_id, '' );
	}

	/**
	 * Set Cart Discounts
	 *
	 * @param object $cart Cart Object.
	 * @return void
	 * @since 1.0.0
	 */
	public function set_cart_discounts( $cart ) {
		if ( is_admin() ) {
			return;
		}
		$__user_id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );

		$__cart_discounts = get_transient( 'wholesalex_cart_total_discounts_' . $__user_id );

		if ( isset( $__cart_discounts['data'] ) ) {
			$__discount_value = 0.0;
			foreach ( $__cart_discounts['data'] as $key => $value ) {
				$__discount_value = $__discount_value + $value;
			}
			$cart->add_fee( $__cart_discounts['title'], -$__discount_value );
		}
		set_transient( 'wholesalex_cart_total_discounts_' . $__user_id, '' );
	}

	/**
	 * Filter Shipping Methods
	 *
	 * @param object $package_rates Package Rates.
	 * @param object $package Package.
	 * @return object shipping methods.
	 * @since 1.0.0
	 */
	public function filter_shipping_methods( $package_rates, $package ) {		
		$__user_id                   = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		$__override_status           = false;
		$__shipping_method_transient = get_transient( 'wholesalex_shipping_methods_' . $__user_id );
		delete_transient( 'wholesalex_shipping_methods_' . $__user_id );

		if ( ! $__shipping_method_transient || empty( $__shipping_method_transient ) ) {
			$__shipping_method_transient = get_transient( 'wholesalex_profile_shipping_methods_' . $__user_id );
		}

		$__available_shipping_methods = array();
		$shipping_zone                = wc_get_shipping_zone( $package );
		$zone_id                      = $shipping_zone->get_id(); // Get the zone ID.

		$__shipping_method_transient = isset( $__shipping_method_transient[ $zone_id ] ) ? $__shipping_method_transient[ $zone_id ] : '';

		if ( $__shipping_method_transient && ! empty( $__shipping_method_transient ) ) {
			$__shipping_method_ids = array();
			foreach ( $__shipping_method_transient as $shipping_methods ) {
				foreach ( $shipping_methods as $id => $value ) {
					$__shipping_method_ids[ $id ] = $value;
				}
			}

			$__shipping_method_ids = array_keys( $__shipping_method_ids );

			foreach ( $package_rates as $rate_key => $rate ) {
				if (in_array($rate->instance_id, $__shipping_method_ids)) { //phpcs:ignore
					$__available_shipping_methods[ $rate_key ] = $rate;
					$__override_status                         = true;
				}
			}
		}

		$__available_shipping_methods = apply_filters( 'wholesalex_available_shipping_methods', $__available_shipping_methods, $__override_status );

		// Force Free Shipping Section.
		$__force_free_shipping_status = get_transient( 'wholesalex_force_free_shipping_' . $__user_id );
		if ( $__force_free_shipping_status && ! empty( $__force_free_shipping_status ) ) {

			// Delete Transient.
			delete_transient( 'wholesalex_force_free_shipping_' . $__user_id );
			// Create WholesaleX Free Shipping Method.
			$__free_shipping        = new WC_Shipping_Free_Shipping( 'wholesalex_free_shipping' );
			$__free_shipping->title = apply_filters( 'wholesalex_free_shipping_title', __( 'WholesaleX Free Shipping', 'wholesalex' ) );

			$__free_shipping->calculate_shipping( $package );

			$available_shipping_methods = array();
			if ( ! empty( $__free_shipping->rates ) && is_array( $__free_shipping->rates ) ) {
				foreach ( $__free_shipping->rates as $rate ) {
					$available_shipping_methods[ $rate->id ] = $rate;
				}
			}
			return $available_shipping_methods;
		}

		if ( ! empty( $__available_shipping_methods ) ) {
			return $__available_shipping_methods;
		}

		return $package_rates;
	}

	/**
	 * Clear Shipping Fee Cache
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function clear_shipping_fee_cache() {
		if ( is_admin() || null === WC()->session ) {
			return;
		}
		$packages = WC()->cart->get_shipping_packages();
		foreach ( $packages as $key => $value ) {
			$shipping_session = "shipping_for_package_$key";
			unset( WC()->session->$shipping_session );
		}
	}

	/**
	 * Set WholesaleX Discounted Products in WC Session
	 *
	 * @param int $product_id Product ID.
	 * @return void
	 */
	public function set_discounted_product( $product_id ) {
		if ( is_admin() || null === WC()->session ) {
			return;
		}
		$__discounted_product = null !== WC()->session ? WC()->session->get( '__wholesalex_discounted_products' ) : '';

		if ( ! ( isset( $__discounted_product ) && is_array( $__discounted_product ) ) ) {
			$__discounted_product = array();
		}
		$__discounted_product[ $product_id ] = true;

		WC()->session->set( '__wholesalex_discounted_products', $__discounted_product );
	}

	/**
	 * Add Custom Meta on Wholesale Order
	 *
	 * @param object $order Order Data.
	 * @since 1.0.0
	 * @since 1.0.1 Dynamic Rule Usages Count and Discounted Product Empty Bug Fixed.
	 */
	public function add_custom_meta_on_wholesale_order( $order ) {
		if ( is_admin() || null === WC()->session ) {
			return;
		}
		$__discounted_product = WC()->session->get( '__wholesalex_discounted_products' );
		$__dynamic_rule_id    = WC()->session->get( '__wholesalex_used_dynamic_rule' );

		if ( ! empty( $__dynamic_rule_id ) ) {
			$order->update_meta_data( '__wholesalex_dynamic_rule_ids', $__dynamic_rule_id );
			if ( is_array( $__dynamic_rule_id ) ) {
				foreach ( $__dynamic_rule_id as $key => $value ) {
					if ( 1 == $value ) {
						$__rule                          = wholesalex()->get_dynamic_rules( $key );
						$__rule['limit']['usages_count'] = isset( $__rule['limit']['usages_count'] ) ? (int) $__rule['limit']['usages_count'] + 1 : 1;
						wholesalex()->set_dynamic_rules( $key, $__rule );
					}
				}
			}
		}
		$__ordered_discounted_product = array();
		$items                        = $order->get_items();
		foreach ( $items as $item ) {
			$product_id           = $item->get_product_id();
			$product_variation_id = $item->get_variation_id();
			if ( $__discounted_product[ $product_id ] ) {
				$__ordered_discounted_product[] = $product_id;
			}
			if ( $__discounted_product[ $product_variation_id ] ) {
				$__ordered_discounted_product[] = $product_variation_id;
			}
		}

		if ( ! empty( $__ordered_discounted_product ) ) {
			$order->update_meta_data( '__wholesalex_discounted_products', $__ordered_discounted_product );
		}

		$__user_role = wholesalex()->get_current_user_role();

		if (in_array($__user_role, array('', 'wholesalex_guest', 'wholesalex_b2c_users'))) { //phpcs:ignore
			$order->update_meta_data( '__wholesalex_order_type', 'b2c' );
		} else {
			$order->update_meta_data( '__wholesalex_order_type', 'b2b' );
		}
		WC()->session->set( '__wholesalex_discounted_products', array() );
	}


	/**
	 * Reset Wholesale Discount Product on Cart update
	 */
	public function update_discounted_product() {
		if ( is_admin() || null === WC()->session ) {
			return;
		}
		WC()->session->set( '__wholesalex_discounted_products', array() );
		WC()->session->set( '__wholesalex_used_dynamic_rule', array() );
	}

	/**
	 * Price After Currency Changed
	 * For Any Currency Switcher Compability Issue, Add Necessary Codes In This Function.
	 *
	 * @param float $price Price.
	 * @return float $price.
	 * @since 1.0.3
	 */
	public function price_after_currency_changed( $price ) {
		// ProductX Currency Switcher Compatibility.
		if ( defined( 'WOPB_VER' ) && defined( 'WOPB_PRO_VER' ) && class_exists( 'WOPB_PRO\Currency_Switcher_Action' ) ) {
			$current_currency_code = wopb_function()->get_setting( 'wopb_current_currency' );
			$default_currency      = wopb_function()->get_setting( 'wopb_default_currency' );
			$current_currency      = Currency_Switcher_Action::get_currency( $current_currency_code );
			if ( ! $current_currency ) {
				$current_currency = $default_currency;
			}

			if ( $current_currency_code !== $default_currency ) {

				$wopb_current_currency_rate = ( isset( $current_currency['wopb_currency_rate'] ) && $current_currency['wopb_currency_rate'] > 0 && ! ( $current_currency['wopb_currency_rate'] == '' ) ) ? $current_currency['wopb_currency_rate'] : 1;
				$wopb_current_exchange_fee  = ( isset( $current_currency['wopb_currency_exchange_fee'] ) && $current_currency['wopb_currency_exchange_fee'] >= 0 && ! ( $current_currency['wopb_currency_exchange_fee'] == '' ) ) ? $current_currency['wopb_currency_exchange_fee'] : 0;
				$total_rate                 = ( $wopb_current_currency_rate + $wopb_current_exchange_fee );
				return $price / $total_rate;
			}
		}
		return $price;
	}

	/**
	 * Set Initial Sell Price to session
	 *
	 * @param string              $__function_name Function Name.
	 * @param float|double|string $id Product ID.
	 * @param float|double|string $sale_price Product Initital Sale Price.
	 * @return void
	 * @since 1.0.4
	 */
	public function set_initial_sale_price_to_session( $__function_name, $id, $sale_price ) {
		if ( isset( WC()->session ) && ! is_admin() ) {
			if ( $__function_name === $this->first_sale_price_generator ) {
				$__wholesale_products        = WC()->session->get( '__wholesalex_wholesale_products' );
				$__wholesale_products[ $id ] = $sale_price;

				WC()->session->set( '__wholesalex_wholesale_products', $__wholesale_products );
			}
		}
	}




	/**
	 * Single Product Discounts
	 *
	 * @param array $data Data.
	 * @return array
	 * @since 1.0.0
	 * @since 1.0.3 ProductX Currency Switcher Compatibility Fixed
	 */
	public function single_product_discounts_action( $data ) {

		/**
		 * Hook Added For Support Bulk Order Addon (wholesalex_single_product_discounts_data)
		 *
		 * @since 1.0.4
		 */
		$data = apply_filters( 'wholesalex_single_product_discounts_data', $data );

		$sale_price = $data['sale_price'];
		$product    = $data['product'];

		$__override_sale_price = false;
		$__product_id          = $product->get_id();
		$__user_id             = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		// delete_transient( 'wholesalex_pricing_tiers_single_product_' . $__user_id );

		$__discounts = wholesalex()->get_discounts( 'product', $__product_id );
		if ( empty( $__discounts ) ) {
			return $data;
		}
		$sale_price = ( isset( $__discounts['sale_price'] ) && ! empty( $__discounts['sale_price'] ) ) ? max( 0, $__discounts['sale_price'] ) : $sale_price;
		/**
		 * Rolewise Sale Price ( Single Product ) Not Working Issue Fixed
		 *
		 * @since 1.0.6
		 */
		$data['sale_price'] = $sale_price;
		/**
		 * ProductX Currency Switcher Compatibility Fixed
		 *
		 * @since 1.0.3
		 */
		$__regular_price = ( isset( $__discounts['regular_price'] ) && ! empty( $__discounts['regular_price'] ) ) ? max( 0, $__discounts['regular_price'] ) : $this->price_after_currency_changed( $product->get_regular_price() );

		$__tiers = isset( $__discounts['tiers'] ) ? $__discounts['tiers'] : '';
		set_transient( 'wholesalex_pricing_tiers_single_product_' . $__user_id, $__tiers );

		if ( ! ( is_array( $__tiers ) && ! empty( $__tiers ) ) ) {
			return $data;
		}

		$__quantity = isset( $data['quantity'] ) ? $data['quantity'] : wholesalex()->cart_count( $__product_id );

		foreach ( $__tiers as $tier ) {
			if ( ! isset( $tier['_min_quantity'] ) ) {
				continue;
			}
			if ( $__quantity >= $tier['_min_quantity'] ) {
				$sale_price             = wholesalex()->calculate_sale_price( $tier, $__regular_price );
				$__override_sale_price  = true;
				$data['active_tier_id'] = isset( $tier['_id'] ) ? $tier['_id'] : '';
			}
		}

		$data['sale_price'] = $sale_price;

		if ( $__override_sale_price ) {
			remove_all_filters( 'woocommerce_sale_flash' );
			$data['discount_src'] = 'single_product';
		}

		return $data;

	}

	/**
	 * Profile Discounts
	 *
	 * @param array $data Data.
	 * @return array
	 * @since 1.0.0
	 * @since 1.0.3 ProductX Currency Switcher Compatibility Fixed
	 */
	public function profile_discounts_action( $data ) {
		$__override_sale_price = false;
		$__user_id             = get_current_user_id();
		$sale_price            = $data['sale_price'];
		$product               = $data['product'];

		// delete_transient( 'wholesalex_pricing_tiers_profile_' . $__user_id );

		$__tiers = get_user_meta( $__user_id, '__wholesalex_profile_discounts', true );

		/**
		 * ProductX Currency Switcher Compatibility Fixed
		 *
		 * @since 1.0.3
		 */
		$__regular_price = $this->price_after_currency_changed( $product->get_regular_price() );

		if ( ! ( isset( $__tiers['_profile_discounts']['tiers'] ) && ! empty( $__tiers['_profile_discounts']['tiers'] ) ) ) {
			return $data;
		}

		$__tiers                 = $__tiers['_profile_discounts']['tiers'];
		$__product_id            = $product->get_id();
		$__tiers_for_cur_product = array();
		$__parent_id             = $product->get_parent_id();
		$__cat_ids               = wc_get_product_term_ids( 0 === $__parent_id ? $__product_id : $__parent_id, 'product_cat' );
		foreach ( $__tiers as $tier ) {
			if ( isset( $tier['_product_filter'] ) ) {
				switch ( $tier['_product_filter'] ) {
					case 'all_products':
						$__temp_tier = $tier;
						unset( $__temp_tier['_product_filter'] );
						unset( $__temp_tier[ $tier['_product_filter'] ] );
						$__temp_tier['for']    = 'all_products';
						$__temp_tier['src_id'] = -1;
						array_push( $__tiers_for_cur_product, $__temp_tier );

						break;
					case 'products_in_list':
						if ( ! isset( $tier['products_in_list'] ) ) {
							break;

						}
						foreach ( $tier['products_in_list'] as $list ) {
							if ( isset( $list['value'] ) && $__product_id == $list['value'] ) {
								$__temp_tier = $tier;
								unset( $__temp_tier['_product_filter'] );
								unset( $__temp_tier[ $tier['_product_filter'] ] );
								$__temp_tier['for']    = 'product';
								$__temp_tier['src_id'] = $__product_id;
								array_push( $__tiers_for_cur_product, $__temp_tier );
								break;
							}
						}
						break;
					case 'products_not_in_list':
						if ( ! isset( $tier['products_not_in_list'] ) ) {
							break;

						}
						$__flag = true;
						foreach ( $tier['products_not_in_list'] as $list ) {
							if ( isset( $list['value'] ) && $__product_id == $list['value'] ) {
								$__flag = false;
							}
						}
						if ( $__flag ) {
							$__temp_tier = $tier;
							unset( $__temp_tier['_product_filter'] );
							unset( $__temp_tier[ $tier['_product_filter'] ] );
							$__temp_tier['for']    = 'product';
							$__temp_tier['src_id'] = $__product_id;
							array_push( $__tiers_for_cur_product, $__temp_tier );
						}
						break;
					case 'cat_in_list':
						if ( ! isset( $tier['cat_in_list'] ) ) {
							break;

						}
						foreach ( $tier['cat_in_list'] as $list ) {
							if ( in_array( $list['value'], $__cat_ids ) ) {
								$__temp_tier = $tier;
								unset( $__temp_tier['_product_filter'] );
								unset( $__temp_tier[ $tier['_product_filter'] ] );
								$__temp_tier['for']    = 'cat';
								$__temp_tier['src_id'] = $list['value'];
								array_push( $__tiers_for_cur_product, $__temp_tier );
								break;
							}
						}

						break;

					case 'cat_not_in_list':
						if ( ! isset( $tier['cat_not_in_list'] ) ) {
							break;

						}
						$__flag = true;
						foreach ( $tier['cat_not_in_list'] as $list ) {
							if ( in_array( $list['value'], $__cat_ids ) ) {
								$__flag = false;
							}
						}
						if ( $__flag ) {
							$__temp_tier = $tier;
							unset( $__temp_tier['_product_filter'] );
							unset( $__temp_tier[ $tier['_product_filter'] ] );
							$__temp_tier['for']    = 'cat';
							$__temp_tier['src_id'] = $__cat_ids[0];
							array_push( $__tiers_for_cur_product, $__temp_tier );
						}
						break;
					case 'attribute_in_list':
						if ( ! isset( $tier['attribute_in_list'] ) ) {
							break;

						}
						if ( 'product_variation' == $product->post_type ) {
							foreach ( $tier['attribute_in_list'] as $list ) {
								if ( isset( $list['value'] ) && $__product_id == $list['value'] ) {
									$__temp_tier = $tier;
									unset( $__temp_tier['_product_filter'] );
									unset( $__temp_tier[ $tier['_product_filter'] ] );
									$__temp_tier['for']    = 'variation';
									$__temp_tier['src_id'] = $__product_id;
									array_push( $__tiers_for_cur_product, $__temp_tier );
									break;
								}
							}
						}
						break;
					case 'attribute_not_in_list':
						if ( ! isset( $tier['attribute_not_in_list'] ) ) {
							break;

						}
						if ( 'product_variation' == $product->post_type ) {
							$__flag = true;
							foreach ( $tier['attribute_not_in_list'] as $list ) {
								if ( isset( $list['value'] ) && $__product_id == $list['value'] ) {
									$__flag = false;
								}
							}
							if ( $__flag ) {
								$__temp_tier = $tier;
								unset( $__temp_tier['_product_filter'] );
								unset( $__temp_tier[ $tier['_product_filter'] ] );
								$__temp_tier['for']    = 'variation';
								$__temp_tier['src_id'] = $__product_id;
								array_push( $__tiers_for_cur_product, $__temp_tier );
							}
						}
						break;
					default:
						// code...
						break;
				}
			}
		}

		foreach ( $__tiers_for_cur_product as $tier ) {
			if ( isset( $tier['for'] ) && isset( $tier['src_id'] ) ) {
				switch ( $tier['for'] ) {
					case 'cat':
						if ( in_array( $tier['src_id'], $__cat_ids ) ) {
							/**
							 * Hook Added For Support Bulk Order Addon (wholesalex_profile_discounts_category_cart_count)
							 *
							 * @since 1.0.4
							 */
							$__quantity     = apply_filters( 'wholesalex_profile_discounts_category_cart_count', 0, $tier['src_id'], $__product_id );
							$__quantity     = $__quantity ? $__quantity : wholesalex()->category_cart_count( $tier['src_id'] );
							$__min_quantity = isset( $tier['_min_quantity'] ) ? $tier['_min_quantity'] : 999999999;
							if ( $__quantity >= $__min_quantity ) {
								$sale_price             = wholesalex()->calculate_sale_price( $tier, $__regular_price );
								$__override_sale_price  = true;
								$data['active_tier_id'] = isset( $tier['_id'] ) ? $tier['_id'] : '';
							}
						}

						break;
					case 'product':
					case 'variation':
					case 'all_products':
						/**
						 * Hook Added For Support Bulk Order Addon (wholesalex_profile_discounts_product_count)
						 *
						 * @since 1.0.4
						 */
						$__quantity     = apply_filters( 'wholesalex_profile_discounts_product_count', 0, $__product_id );
						$__quantity     = $__quantity ? $__quantity : wholesalex()->cart_count( $__product_id );
						$__min_quantity = isset( $tier['_min_quantity'] ) ? $tier['_min_quantity'] : 999999999;
						if ( $__quantity >= $__min_quantity ) {
							$sale_price             = wholesalex()->calculate_sale_price( $tier, $__regular_price );
							$__override_sale_price  = true;
							$data['active_tier_id'] = isset( $tier['_id'] ) ? $tier['_id'] : '';
						}
						break;
				}
			}
		}

		$data['sale_price'] = $sale_price;

		set_transient( 'wholesalex_pricing_tiers_profile_' . $__user_id, $__tiers_for_cur_product );

		if ( $__override_sale_price ) {
			remove_all_filters( 'woocommerce_sale_flash' );
			$data['discount_src'] = 'profile';
		}

		return $data;
	}


	/**
	 * Category Discounts
	 *
	 * @param array $data Data.
	 * @return array
	 * @since 1.0.0
	 * @since 1.0.3 ProductX Currency Switcher Compatibility Fixed
	 */
	public function category_discounts_action( $data ) {
		$sale_price = $data['sale_price'];
		$product    = $data['product'];

		$__override_sale_price = false;
		$__parent_id           = $product->get_parent_id();
		$__product_id          = $product->get_id();
		$__user_id             = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );

		// delete_transient( 'wholesalex_pricing_tiers_category_' . $__user_id );

		$__discounts = wholesalex()->get_discounts( 'category', 0 !== $__parent_id ? $__parent_id : $__product_id, );
		if ( empty( $__discounts ) ) {
			return $data;
		}
		$__tiers  = $__discounts['tiers'];
		$__cat_id = $__discounts['cat_id'];

		/**
		 * ProductX Currency Switcher Compatibility Fixed
		 *
		 * @since 1.0.3
		 */
		$__regular_price = $this->price_after_currency_changed( $product->get_regular_price() );

		// If product category has no discounts, then return as usual sale price.
		if ( empty( $__tiers ) ) {
			return $data;
		}

		/**
		 * Hook Added For Support Bulk Order Addon (wholesalex_category_discounts_cart_count)
		 *
		 * @since 1.0.4
		 */
		$__quantity = apply_filters( 'wholesalex_category_discounts_cart_count', 0, $__cat_id, ( 0 !== $__parent_id ) ? $__parent_id : $__product_id );
		$__quantity = $__quantity ? $__quantity : wholesalex()->category_cart_count( $__cat_id );

		foreach ( $__tiers as $tier ) {
			$__min_quantity = isset( $tier['_min_quantity'] ) ? $tier['_min_quantity'] : 999999999;
			if ( $__quantity >= $__min_quantity ) {
				$sale_price             = wholesalex()->calculate_sale_price( $tier, $__regular_price );
				$__override_sale_price  = true;
				$data['active_tier_id'] = isset( $tier['_id'] ) ? $tier['_id'] : '';
			}
		}

		$data['sale_price'] = $sale_price;

		set_transient( 'wholesalex_pricing_tiers_category_' . $__user_id, $__tiers );

		if ( $__override_sale_price ) {
			remove_all_filters( 'woocommerce_sale_flash' );
			$data['discount_src'] = 'category';
		}

		return $data;

	}


	public function product_price($price,$product) {
		if ( ( is_object( $product ) && is_a( $product, 'WC_Product' ) ) ) {
			if(empty($product->get_sale_price())) {
				$price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
			} else {
				$price = wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) );
			}
		}
		return $price;
	}

	public function set_price_on_ppom($price,$cart_item) {
		$__product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
		$__product    = wc_get_product( $__product_id );
		return $__product->get_sale_price();
	}
}

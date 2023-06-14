<?php
/**
 * Product
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * WholesaleX Product Class
 */
class WHOLESALEX_Product {

	/**
	 * Rule on Lists
	 *
	 * @var array
	 */
	public $rule_on_lists = array();

	/**
	 * Product Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_options_pricing', array( $this, 'wholesalex_single_product_settings' ) );
		add_filter( 'woocommerce_product_after_variable_attributes', array( $this, 'wholesalex_single_product_settings' ), 10, 3 );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'wholesalex_product_meta_save' ) );
		add_action( 'woocommerce_save_product_variation', array( $this, 'wholesalex_product_meta_save' ) );
		add_action( 'rest_api_init', array( $this, 'get_product_callback' ) );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_custom_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'wholesalex_tab_data' ) );
		add_action( 'save_post', array( $this, 'product_settings_data_save' ) );
		/**
		 * Use of Pre Get Posts Hook instead of woocommerce_product_query.
		 *
		 * @since 1.0.2
		 */
		add_filter( 'pre_get_posts', array( $this, 'control_single_product_visibility' ) );
		add_filter( 'woocommerce_product_query', array( $this, 'control_single_product_visibility' ) );
		add_action( 'template_redirect', array( $this, 'redirect_from_hidden_products' ) );
		add_action( 'woocommerce_check_cart_items', array( $this, 'prevent_checkout_hidden_products' ) );
		/**
		 * Remove Hidden Product From Related Products.
		 *
		 * @since 1.0.2
		 */
		add_filter( 'woocommerce_related_products', array( $this, 'filter_hidden_products' ) );

		/**
		 * Add WholesaleX Rule on Column on All Products Page.
		 *
		 * @since 1.0.4
		 */
		add_filter( 'manage_edit-product_columns', array( $this, 'add_wholesalex_rule_on_column_on_product_page' ) );

		add_action( 'manage_product_posts_custom_column', array( $this, 'populate_data_on_wholesalex_rule_on_column' ), 10, 2 );

		/**
		 * Add More Tier Layout
		 *
		 * @since 1.0.6 Tier layouts added on v1.0.1 But Code was refactored on v1.0.6.
		 */
		if ( wholesalex()->is_pro_active() && version_compare( WHOLESALEX_PRO_VER, '1.0.6', '>=' ) ) {
			add_filter( 'wholesalex_single_product_tier_layout', array( $this, 'add_more_tier_layouts' ), 20 );
			add_filter( 'wholesalex_settings_product_tier_layout', array( $this, 'add_more_tier_layouts' ), 20 );
		} else {
			add_filter( 'wholesalex_single_product_tier_layout', array( $this, 'add_more_tier_layouts' ), 1 );
			add_filter( 'wholesalex_settings_product_tier_layout', array( $this, 'add_more_tier_layouts' ), 1 );
		}

		add_action( 'woocommerce_process_product_meta', array( $this, 'after_product_update' ), 1 );

		/**
		 * WooCommerce Importer and Exporter Integration On Single Product WholesaleX Rolewise Price.
		 *
		 * @since 1.1.5
		 */
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_wholesale_rolewise_column_exporter' ), 99999 );
		$wholesalex_roles = wholesalex()->get_roles( 'b2b_roles_option' );
		foreach ( $wholesalex_roles as $role ) {
			$base_price_meta_key = $role['value'] . '_base_price';
			$sale_price_meta_key = $role['value'] . '_sale_price';
			add_filter( "woocommerce_product_export_product_column_{$base_price_meta_key}", array( $this, 'export_column_value' ), 99999, 3 );
			add_filter( "woocommerce_product_export_product_column_{$sale_price_meta_key}", array( $this, 'export_column_value' ), 99999, 3 );
		}

		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'import_column_mapping' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'import_column_mapping' ) );
		add_filter( 'woocommerce_product_import_inserted_product_object', array( $this, 'process_import' ), 10, 2 );
	}


	/**
	 * Process Import
	 *
	 * @param object $product Product Object.
	 * @param array  $data Data.
	 * @return void
	 * @since 1.1.5
	 */
	public function process_import( $product, $data ) {

		$product_id = $product->get_id();
		$roles      = wholesalex()->get_roles( 'b2b_roles_option' );

		foreach ( $roles as $role ) {
			$base_price_column_id = $role['value'] . '_base_price';
			$sale_price_column_id = $role['value'] . '_sale_price';
			if ( isset( $data[ $base_price_column_id ] ) && ! empty( $data[ $base_price_column_id ] ) ) {
				update_post_meta( $product_id, $base_price_column_id, $data[ $base_price_column_id ] );
			}
			if ( isset( $data[ $sale_price_column_id ] ) && ! empty( $data[ $sale_price_column_id ] ) ) {
				update_post_meta( $product_id, $sale_price_column_id, $data[ $sale_price_column_id ] );
			}
		}

	}

	/**
	 * Save Wholesalex Category
	 *
	 * @since 1.0.0
	 */
	public function get_product_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/product_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'product_action_callback' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * WholesaleX Tab in Single Product Edit Page
	 *
	 * @param array $tabs Single Product Page Tabs.
	 * @return array Updated Tabs.
	 */
	public function product_custom_tab( $tabs ) {
		$tabs['wholesalex'] = array(
			'label'    => __( 'WholesaleX', 'wholesalex' ),
			'priority' => 15,
			'target'   => 'wholesalex_tab_data',
			'class'    => array( 'hide_if_grouped' ),
		);

		return $tabs;
	}

	/**
	 * WholesaleX Custom Tab Data.
	 *
	 * @return void
	 */
	public function wholesalex_tab_data() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_product' );

		wp_localize_script(
			'wholesalex_components',
			'wholesalex_product_tab',
			array(
				'fields'   => $this->get_product_settings(),
				'settings' => wholesalex()->get_single_product_setting(),
			),
		);
		?>
		<div class="panel woocommerce_options_panel" id="wholesalex_tab_data"></div>
		<?php
	}

	/**
	 * Save Product Setting Data
	 *
	 * @param mixed $post_id Product ID.
	 */
	public function product_settings_data_save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['meta-box-order-nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['meta-box-order-nonce'] ), 'meta-box-order' ) ) {
			return;
		}

		if ( isset( $_POST['wholesalex_product_settings'] ) ) {
			$product_settings = wholesalex()->sanitize( json_decode( wp_unslash( $_POST['wholesalex_product_settings'] ), true ) );
			wholesalex()->save_single_product_settings( $post_id, $product_settings );
		}

	}



	/**
	 * Get Category actions
	 *
	 * @param object $server Server.
	 * @return void
	 */
	public function product_action_callback( $server ) {
		$post = $server->get_params();
		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( sanitize_key( $post['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}
		$type    = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';
		$post_id = isset( $post['postId'] ) ? sanitize_text_field( $post['postId'] ) : '';
		$is_tab  = isset( $post['isTab'] ) ? sanitize_text_field( $post['isTab'] ) : '';
		if ( 'get' === $type ) {

			if ( $is_tab ) {
				wp_send_json_success(
					array(
						'default' => $this->get_product_settings(),
						'value'   => wholesalex()->get_single_product_setting( $post_id ),
					),
				);
			}

			wp_send_json_success(
				array(
					'default' => $this->get_product_fields(),
					'value'   => wholesalex()->get_single_product_discount( $post_id ),
				)
			);
		}
	}

	/**
	 * WholesaleX Single Product Settings
	 */
	public function wholesalex_single_product_settings() {

		$post_id   = get_the_ID();
		$discounts = array();
		if ( $post_id ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$is_variable = 'variable' === $product->get_type();
				if ( $is_variable ) {
					if ( $product->has_child() ) {
						$childrens = $product->get_children();
						foreach ( $childrens as $key => $child_id ) {
							$discounts[ $child_id ] = wholesalex()->get_single_product_discount( $child_id );
						}
					}
				} else {
					$discounts[ $post_id ] = wholesalex()->get_single_product_discount( $post_id );
				}
			}
		}
		wp_localize_script(
			'wholesalex_components',
			'wholesalex_single_product',
			array(
				'fields'    => $this->get_product_fields(),
				'discounts' => $discounts,
			),
		);
		?>
		<div class="_wholesalex_single_product_settings" class="options-group hide_if_external"></div>
		<?php
	}

	/**
	 * Save WholesaleX Product Meta
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function wholesalex_product_meta_save( $post_id ) {

		if ( isset( $_POST['meta-box-order-nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['meta-box-order-nonce'] ), 'meta-box-order' ) ) {
			return;
		}

		if ( isset( $_POST[ 'wholesalex_single_product_tiers_' . $post_id ] ) ) {
			$product_discounts = wholesalex()->sanitize( json_decode( wp_unslash( $_POST[ 'wholesalex_single_product_tiers_' . $post_id ] ), true ) );
			wholesalex()->save_single_product_discount( $post_id, $product_discounts );
		}

	}

	/**
	 * Control Single Product Visibility
	 *
	 * @param WP_Query $q Query Object.
	 * @since 1.0.0
	 * @since 1.0.3 Added post type checking.
	 */
	public function control_single_product_visibility( $q ) {
		if ( is_admin() ) {
			return $q;
		}
		$post_type = $q->get( 'post_type' );
		if ( 'product' === $post_type && ! is_admin() ) {
			$__role = wholesalex()->get_current_user_role();
			if ( 'wholesalex_guest' === $__role ) {
				$__hide_for_guest_global = apply_filters( 'wholesalex_hide_all_products_for_guest', wholesalex()->get_setting( '_settings_hide_all_products_from_guest' ) );
				if ( 'yes' === $__hide_for_guest_global ) {
					$q->set( 'post__in', (array) array( '9999999' ) );
				}
			}
			if ( 'wholesalex_b2c_users' === $__role ) {
				$__hide_for_b2c_global = apply_filters( 'wholesalex_hide_all_products_for_b2c', wholesalex()->get_setting( '_settings_hide_products_from_b2c' ) );
				if ( 'yes' === $__hide_for_b2c_global ) {
					$q->set( 'post__in', (array) array( '9999999' ) );
				}
			}
			$q->set( 'post__not_in', (array) wholesalex()->hidden_product_ids() );

		}
		return $q;
	}

	/**
	 * WholesaleX Redirect From Hidden Products
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function redirect_from_hidden_products() {
		if ( is_product() ) {
			$__role      = wholesalex()->get_current_user_role();
			$__is_hidden = false;
			if ( 'wholesalex_guest' === $__role ) {
				$__hide_for_guest_global = apply_filters( 'wholesalex_hide_all_products_for_guest', wholesalex()->get_setting( '_settings_hide_all_products_from_guest' ) );
				if ( 'yes' === $__hide_for_guest_global ) {
					$__is_hidden = true;
				}
			}
			if ( 'wholesalex_b2c_users' === $__role ) {
				$__hide_for_b2c_global = apply_filters( 'wholesalex_hide_all_products_for_b2c', wholesalex()->get_setting( '_settings_hide_products_from_b2c' ) );
				if ( 'yes' === $__hide_for_b2c_global ) {
					$__is_hidden = true;
				}
			}
			$__id = get_the_ID();
			if ( in_array( $__id, wholesalex()->hidden_product_ids(), true ) || $__is_hidden ) {
				/* translators: %s: Product Name */
				wc_add_notice( sprintf( __( 'Sorry, you are not allowed to see %s product.', 'wholesalex' ), get_the_title( get_the_ID() ) ), 'notice' );
				$redirect_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				wp_safe_redirect( $redirect_url );
				exit();
			}
		}
	}

	/**
	 * Prevent Checkout If any cart has any wholesalex hidden product
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Allow Hidden to checkout hook added
	 */
	public function prevent_checkout_hidden_products() {
		if ( ! ( isset( WC()->cart ) && ! empty( WC()->cart ) ) ) {
			return;
		}
		$allow_hidden_product_to_checkout = apply_filters( 'wholesalex_allow_hidden_filter_to_checkout', false );
		if ( $allow_hidden_product_to_checkout ) {
			return;
		}
		$__role      = wholesalex()->get_current_user_role();
		$__is_hidden = false;
		if ( 'wholesalex_guest' === $__role ) {
			$__hide_for_guest_global = apply_filters( 'wholesalex_hide_all_products_for_guest', wholesalex()->get_setting( '_settings_hide_all_products_from_guest' ) );
			if ( 'yes' === $__hide_for_guest_global ) {
				$__is_hidden = true;
			}
		}
		if ( 'wholesalex_b2c_users' === $__role ) {
			$__hide_for_b2c_global = apply_filters( 'wholesalex_hide_all_products_for_b2c', wholesalex()->get_setting( '_settings_hide_products_from_b2c' ) );
			if ( 'yes' === $__hide_for_b2c_global ) {
				$__is_hidden = true;
			}
		}

		$__hide_regular_price = wholesalex()->get_setting( '_settings_hide_retail_price' ) ?? '';

		$__hide_wholesale_price = wholesalex()->get_setting( '_settings_hide_wholesalex_price' ) ?? '';

		if ( ! is_admin() ) {
			if ( 'yes' === (string) $__hide_wholesale_price && 'yes' === (string) $__hide_regular_price ) {
				$__is_hidden = true;
			}
		}
		foreach ( WC()->cart->get_cart() as $key => $cart_item ) {

			$__product_id = '';

			if ( $cart_item['variation_id'] ) {
				$__product    = wc_get_product( $cart_item['variation_id'] );
				$__product_id = $__product->get_parent_id();
			} elseif ( $cart_item['product_id'] ) {
				$__product_id = $cart_item['product_id'];
			}
			if ( in_array( $__product_id, wholesalex()->hidden_product_ids(), true ) || $__is_hidden ) {
				// Remove Hidden Product From Cart.
				WC()->cart->remove_cart_item( $key );
				/* translators: %s: Product Name */
				wc_add_notice( sprintf( __( 'Sorry, you are not allowed to checkout %s product.', 'wholesalex' ), get_the_title( $__product_id ) ), 'error' );
			}
		}
	}

	/**
	 * Get Product Settings Field
	 *
	 * @return array Product Settings Fields.
	 */
	public function get_product_settings() {
		$__roles_options = wholesalex()->get_roles( 'b2b_roles_option' );
		$__users_options = wholesalex()->get_users()['user_options'];

		return apply_filters(
			'wholesalex_single_product_settings_field',
			array(
				'_product_settings_tab' => array(
					'type' => 'custom_tab',
					'attr' => array(
						'_settings_tier_layout'            => array(
							'type'    => 'choosebox',
							'label'   => __( 'Tier Table Layout in Product Single Page', 'wholesalex' ),
							'options' => apply_filters(
								'wholesalex_single_product_tier_layout',
								array(
									'layout_one'   => WHOLESALEX_URL . '/assets/img/layout_one.png',
									'layout_two'   => WHOLESALEX_URL . '/assets/img/layout_two.png',
									'layout_three' => WHOLESALEX_URL . '/assets/img/layout_three.png',
								)
							),
							'default' => 'layout_one',
						),
						'_settings_show_tierd_pricing_table' => array(
							'type'    => 'switch',
							'label'   => __( 'Show Tierd Pricing Table', 'wholesalex' ),
							'help'    => '',
							'default' => 'yes',
						),
						'_settings_override_tax_extemption' => array(
							'type'    => 'select',
							'label'   => __( 'Override Tax Extemption', 'wholesalex' ),
							'options' => array(
								'enable'  => __( 'Enable', 'wholesalex' ),
								'disable' => __( 'Disable', 'wholesalex' ),
							),
							'help'    => '',
							'default' => 'disable',
						),
						'_settings_override_shipping_role' => array(
							'type'    => 'select',
							'label'   => __( 'Override Shipping Role', 'wholesalex' ),
							'options' => array(
								'enable'  => __( 'Enable', 'wholesalex' ),
								'disable' => __( 'Disable', 'wholesalex' ),
							),
							'help'    => '',
							'default' => 'disable',
						),
						'_settings_product_visibility'     => array(
							'label' => __( 'Visibility', 'wholesalex' ),
							'type'  => 'visibility_section',
							'attr'  => array(
								'_hide_for_b2c'      => array(
									'type'    => 'switch',
									'label'   => __( 'Hide product for B2C', 'wholesalex' ),
									'help'    => '',
									'default' => '',
								),
								'_hide_for_visitors' => array(
									'type'    => 'switch',
									'label'   => __( 'Hide product for Visitors', 'wholesalex' ),
									'help'    => '',
									'default' => '',
								),
								'_hide_for_b2b_role_n_user' => array(
									'type'    => 'select',
									'label'   => __( 'Hide B2B Role and Users', 'wholesalex' ),
									'options' => array(
										''              => __( 'Choose Options...', 'wholesalex' ),
										'b2b_all'       => __( 'All B2B Users', 'wholesalex' ),
										'b2b_specific'  => __( 'Specific B2B Roles', 'wholesalex' ),
										'user_specific' => __( 'Specific Register Users', 'wholesalex' ),
									),
									'help'    => '',
									'default' => '',
								),
								'_hide_for_roles'    => array(
									'type'        => 'multiselect',
									'label'       => '',
									'options'     => $__roles_options,
									'placeholder' => __( 'Choose Roles...', 'wholesalex' ),
									'default'     => array(),
								),
								'_hide_for_users'    => array(
									'type'        => 'multiselect',
									'label'       => '',
									'options'     => $__users_options,
									'placeholder' => __( 'Choose Users...', 'wholesalex' ),
									'default'     => array(),
								),
							),
						),
					),
				),
			),
		);
	}


	/**
	 * Single Product Field Return.
	 */
	public function get_product_fields() {
		$b2b_roles   = wholesalex()->get_roles( 'b2b_roles_option' );
		$b2c_roles   = wholesalex()->get_roles( 'b2c_roles_option' );
		$__b2b_roles = array();
		foreach ( $b2b_roles as $role ) {
			if ( ! ( isset( $role['value'] ) && isset( $role['value'] ) ) ) {
				continue;
			}
			$__b2b_roles[ $role['value'] ] = array(
				'label'    => $role['name'],
				'type'     => 'tiers',
				'is_pro'   => true,
				'pro_data' => array(
					'type'  => 'limit',
					'value' => 2,
				),
				'attr'     => array(
					'_prices'               => array(
						'type' => 'prices',
						'attr' => array(
							'wholesalex_base_price' => array(
								'type'    => 'number',
								'label'   => __( 'Base Price', 'wholesalex' ),
								'default' => '',
							),
							'wholesalex_sale_price' => array(
								'type'    => 'number',
								'label'   => __( 'Sale Price', 'wholesalex' ),
								'default' => '',
							),
						),
					),
					$role['value'] . 'tier' => array(
						'type'   => 'tier',
						'_tiers' => array(
							'columns'     => array(
								__( 'Discount Type', 'wholesalex' ),
								/* translators: %s: WholesaleX Role Name */
								sprintf( __( ' %s Price', 'wholesalex' ), $role['name'] ),
								__( 'Min Quantity', 'wholesalex' ),
							),
							'data'        => array(
								'_discount_type'   => array(
									'type'    => 'select',
									'options' => array(
										''            => __( 'Choose Discount Type...', 'wholesalex' ),
										'amount'      => __( 'Discount Amount', 'wholesalex' ),
										'percentage'  => __( 'Discount Percentage', 'wholesalex' ),
										'fixed_price' => __( 'Fixed Price', 'wholesalex' ),
									),
									'default' => '',
								),
								'_discount_amount' => array(
									'type'        => 'number',
									'placeholder' => '',
									'default'     => '',
								),
								'_min_quantity'    => array(
									'type'        => 'number',
									'placeholder' => '',
									'default'     => '',
								),
							),
							'add'         => array(
								'type'  => 'button',
								'label' => __( 'Add Price Tier', 'wholesalex' ),
							),
							'upgrade_pro' => array(
								'type'  => 'button',
								'label' => __( 'Go For Unlimited Price Tiers', 'wholesalex' ),
							),
						),
					),
				),
			);
		}

		$__b2c_roles = array();
		foreach ( $b2c_roles as $role ) {
			if ( ! ( isset( $role['value'] ) && isset( $role['value'] ) ) ) {
				continue;
			}
			$__b2c_roles[ $role['value'] ] = array(
				'label'    => $role['name'],
				'type'     => 'tiers',
				'is_pro'   => true,
				'pro_data' => array(
					'type'  => 'limit',
					'value' => 2,
				),
				'attr'     => array(
					$role['value'] . 'tier' => array(
						'type'   => 'tier',
						'_tiers' => array(
							'columns'     => array(
								__( 'Discount Type', 'wholesalex' ),
								/* translators: %s: WholesaleX Role Name */
								sprintf( __( ' %s Price', 'wholesalex' ), $role['name'] ),
								__( 'Min Quantity', 'wholesalex' ),
							),
							'data'        => array(
								'_discount_type'   => array(
									'type'    => 'select',
									'options' => array(
										''            => __( 'Choose Discount Type...', 'wholesalex' ),
										'amount'      => __( 'Discount Amount', 'wholesalex' ),
										'percentage'  => __( 'Discount Percentage', 'wholesalex' ),
										'fixed_price' => __( 'Fixed Price', 'wholesalex' ),
									),
									'default' => '',
								),
								'_discount_amount' => array(
									'type'        => 'number',
									'placeholder' => '',
									'default'     => '',
								),
								'_min_quantity'    => array(
									'type'        => 'number',
									'placeholder' => '',
									'default'     => '',
								),
							),
							'add'         => array(
								'type'  => 'button',
								'label' => __( 'Add Price Tier', 'wholesalex' ),
							),
							'upgrade_pro' => array(
								'type'  => 'button',
								'label' => __( 'Go For Unlimited Price Tiers', 'wholesalex' ),
							),
						),
					),
				),
			);
		}

		return apply_filters(
			'wholesalex_single_product_fields',
			array(
				'_b2c_section' => array(
					'label' => '',
					'attr'  => apply_filters( 'wholesalex_single_product_b2c_roles_tier_fields', $__b2c_roles ),
				),
				'_b2b_section' => array(
					'label' => __( 'WholesaleX B2B Special', 'wholesalex' ),
					'attr'  => apply_filters( 'wholesalex_single_product_b2b_roles_tier_fields', $__b2b_roles ),
				),
			),
		);
	}

	/**
	 * Filter Hidden Products
	 *
	 * @param array $args Related Products.
	 * @return array related products excluding hidden products.
	 * @since 1.0.2
	 */
	public function filter_hidden_products( $args ) {
		return array_diff( $args, wholesalex()->hidden_product_ids() );
	}

	/**
	 * Add WholesaleX Rule On Column On Product Page.
	 *
	 * @param array $columns Order Columns.
	 * @return array
	 * @since 1.0.4
	 */
	public function add_wholesalex_rule_on_column_on_product_page( $columns ) {
		$columns['wholesalex_rule_on'] = __( 'WholesaleX Rule On', 'wholesalex' );
		return $columns;
	}

	/**
	 * Rule on List Modals
	 *
	 * @param string|int $product_id Product Id.
	 * @return void
	 * @since 1.0.4
	 */
	public function list_modal( $product_id ) {
		?>
		<div class="wholesalex_rule_modal <?php echo 'product_' . esc_attr( $product_id ); ?>">
			<div class="modal_content">
				<div class="modal_header">
					<div class="modal-close-btn">
						<span class="close-modal-icon dashicons dashicons-no-alt" ></span>
					</div>
				</div>
				<div class="wholesalex_rule_on_lists">
					<?php
					foreach ( $this->rule_on_lists[ $product_id ] as $rule_on ) {
						echo wp_kses_post( $rule_on );
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Populate Data on WholesaleX Rule On Column on Products page
	 *
	 * @param string $column Products Page Column.
	 * @param int    $product_id Product ID.
	 * @since 1.0.4
	 */
	public function populate_data_on_wholesalex_rule_on_column( $column, $product_id ) {

		if ( 'wholesalex_rule_on' === $column ) {

			$product = wc_get_product( $product_id );
			// Single Products.
			if ( $product->has_child() ) {
				$childrens = $product->get_children();
				foreach ( $childrens as $key => $child_id ) {
					$__discounts = wholesalex()->get_single_product_discount( $child_id );
					$status      = $this->wholesalex_rule_on( $__discounts, $child_id, 'Single Product' );
				}
			} else {

				$__discounts = wholesalex()->get_single_product_discount( $product_id );

				$status = $this->wholesalex_rule_on( $__discounts, $product_id, 'Single Product' );
			}

			// Profile.
			$users = get_users(
				array(
					'fields'   => 'ids',
					'meta_key' => '__wholesalex_profile_discounts',
				)
			);

			$__parent_id = $product->get_parent_id();

			$__cat_ids = wc_get_product_term_ids( 0 === $__parent_id ? $product_id : $__parent_id, 'product_cat' );

			foreach ( $users as $user_id ) {
				$discounts = get_user_meta( $user_id, '__wholesalex_profile_discounts', true );
				$discounts = wholesalex()->filter_empty_tier( $discounts['_profile_discounts']['tiers'] );

				if ( ! empty( $discounts ) ) {
					foreach ( $discounts as $discount ) {
						if ( ! isset( $discount['_product_filter'] ) ) {
							continue;
						}
						$__has_discount = true;
						switch ( $discount['_product_filter'] ) {
							case 'all_products':
								$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
								break;
							case 'products_in_list':
								if ( ! isset( $discount['products_in_list'] ) ) {
									break;
								}
								foreach ( $discount['products_in_list'] as $list ) {
									if ( (int) $product_id === (int) $list['value'] ) {
										$__has_discount = true;
										break;
									}
								}
								if ( $__has_discount ) {
									$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
								}
								break;
							case 'products_not_in_list':
								if ( ! isset( $discount['products_not_in_list'] ) ) {
									break;
								}
								$__flag = true;
								foreach ( $discount['products_not_in_list'] as $list ) {
									if ( isset( $list['value'] ) && (int) $product_id === (int) $list['value'] ) {
										$__flag = false;
									}
								}
								if ( $__flag ) {
									$__has_discount                       = true;
									$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
								}
								break;
							case 'cat_in_list':
								if ( ! isset( $discount['cat_in_list'] ) ) {
									break;
								}
								foreach ( $discount['cat_in_list'] as $list ) {
									if (in_array($list['value'], $__cat_ids)) { //phpcs:ignore
											$__has_discount = true;
											break;
									}
								}
								if ( $__has_discount ) {
									$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
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
									if ( $__has_discount ) {
										$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
									}
								}
								break;
							case 'attribute_in_list':
								if ( ! isset( $discount['attribute_in_list'] ) ) {
									break;
								}
								if ( 'product_variation' === $product->post_type ) {
									foreach ( $discount['attribute_in_list'] as $list ) {
										if ( isset( $list['value'] ) && (int) $product_id === (int) $list['value'] ) {
											$__has_discount = true;
											break;
										}
									}
								}
								if ( $__has_discount ) {
									$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
								}
								break;
							case 'attribute_not_in_list':
								if ( ! isset( $discount['attribute_not_in_list'] ) ) {
									break;
								}
								if ( 'product_variation' === $product->post_type ) {
									$__flag = true;
									foreach ( $discount['attribute_not_in_list'] as $list ) {
										if ( isset( $list['value'] ) && (int) $product_id === (int) $list['value'] ) {
											$__flag = false;
										}
									}
									if ( $__flag ) {
										$__has_discount = true;
										if ( $__has_discount ) {
											$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( sprintf( 'User ID: %s ', $user_id ), 'Profile' );
										}
									}
								}
								break;
						}
					}
				}
			}

			// Category.

			foreach ( $__cat_ids as $cat_id ) {
				$__discounts = wholesalex()->get_category_discounts( $cat_id );
				$status      = $this->wholesalex_rule_on( $__discounts, $product_id, 'Category' );
				if ( $status ) {
					break;
				}
			}

			// Dynamic Rules.
			$__discounts = wholesalex()->get_dynamic_rules();
			foreach ( $__discounts as $discount ) {
				$__has_discount  = false;
				$__product_id    = $product->get_id();
				$__parent_id     = $product->get_parent_id();
				$__cat_ids       = wc_get_product_term_ids( 0 === $__parent_id ? $__product_id : $__parent_id, 'product_cat' );
				$__regular_price = $product->get_regular_price();
				$__for           = '';
				$__src_id        = '';

				if ( isset( $discount['_rule_status'] ) && ! empty( $discount['_rule_status'] ) && isset( $discount['_product_filter'] ) ) {
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
								if ( (int) $__product_id === (int) $list['value'] ) {
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
								if ( isset( $list['value'] ) && (int) $__product_id === (int) $list['value'] ) {
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

				if ( ! isset( $discount['_rule_type'] ) || ! isset( $discount[ $discount['_rule_type'] ] ) ) {
					continue;
				}

				$__rule_type = '';

				switch ( $discount['_rule_type'] ) {
					case 'product_discount':
						$__rule_type = 'Product Discount';

						break;
					case 'quantity_based':
						if ( ! isset( $discount['quantity_based']['tiers'] ) ) {
							break;
						}
						$__rule_type = 'Quantity Based';

						break;

					case 'payment_discount':
						$__rule_type = 'Payment Discount';
						break;
					case 'payment_order_qty':
						$__rule_type = 'Payment Order Quantity';
						break;
					case 'tax_rule':
						$__rule_type = 'Tax Rule';
						break;
					case 'extra_charge':
						$__rule_type = 'Extra Charge';
						break;
					case 'cart_discount':
						$__rule_type = 'Cart Discount';
						break;
					case 'shipping_rule':
						$__rule_type = 'Shipping Rule';
						break;
					case 'buy_x_get_y':
						$__rule_type = 'Buy X Get Y';
						break;
					case 'buy_x_get_one':
						$__rule_type = __( 'BOGO Discounts (Buy X Get One Free)', 'wholesalex' );
						break;
				}

				if ( ! isset( $discount['_rule_for'] ) ) {
					continue;
				}

				$__role_for = $discount['_rule_for'];
				switch ( $__role_for ) {
					case 'specific_roles':
						foreach ( $discount['specific_roles'] as $role ) {
							if ( '' != $__rule_type ) {
								$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( $role['name'], sprintf( 'Dynamic Rules( %s )', $__rule_type ) );
							}
						}
						break;
					case 'specific_users':
						foreach ( $discount['specific_users'] as $user ) {
							$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( $user['value'], sprintf( 'Dynamic Rules( %s )', $__rule_type ) );
						}
						break;
					case 'all_roles':
						$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( 'All Roles', sprintf( 'Dynamic Rules( %s )', $__rule_type ) );
						break;
					case 'all_users':
						$this->rule_on_lists[ $product_id ][] = $this->rule_on_message( 'All Users', sprintf( 'Dynamic Rules( %s )', $__rule_type ) );
						break;
				}
			}

			if ( isset( $this->rule_on_lists[ $product_id ] ) ) {
				$this->rule_on_lists[ $product_id ] = array_unique( $this->rule_on_lists[ $product_id ] );
				if ( count( $this->rule_on_lists[ $product_id ] ) > 3 ) {
					$__count = 0;
					foreach ( $this->rule_on_lists[ $product_id ] as $key => $value ) {
						echo wp_kses_post( $value );
						$__count++;
						if ( 3 == $__count ) {
							break;
						}
					}
					echo '<span class="wholesalex_rule_on_more" id="product_' . esc_attr( $product_id ) . '">More+</span>';
					$this->list_modal( $product_id );
				} else {
					foreach ( $this->rule_on_lists[ $product_id ] as $key => $value ) {
						echo wp_kses_post( $value );
					}
				}
			}
		}

	}


	/**
	 * Check Product Has Any WholesaleX Rule
	 *
	 * @param array      $__discounts Discounts.
	 * @param int|string $product_id Product ID.
	 * @param string     $rule_src Rule Src.
	 * @return boolean
	 * @since 1.0.4
	 */
	public function wholesalex_rule_on( $__discounts, $product_id, $rule_src ) {
		$has_rule = false;

		foreach ( $__discounts as $role_id => $discount ) {

			$_temp          = $discount;
			$_temp['tiers'] = wholesalex()->filter_empty_tier( $_temp['tiers'] );

			if ( ! empty( $_temp['wholesalex_base_price'] ) || ! empty( $_temp['wholesalex_sale_price'] ) || ! empty( $_temp['tiers'] ) ) {
				$product   = wc_get_product( $product_id );
				$parent_id = $product->get_parent_id();
				$suffix    = '';
				if ( $parent_id ) {
					$suffix = $product->get_name();
				}
				$_role_name                           = wholesalex()->get_role_name_by_role_id( $role_id );
				$this->rule_on_lists[ $product_id ][] = '<span class="wholesalex_rule_on_list">' . $_role_name . ' -> ' . $rule_src . $suffix . '</span>';
				$has_rule                             = true;
			}
		}
		return $has_rule;
	}

	/**
	 * Rule on Message
	 *
	 * @param string $name Role/Src Name.
	 * @param string $rule_src Rule Src.
	 * @param string $suffix If Have Any Suffix.
	 * @return string
	 * @since 1.0.4
	 */
	public function rule_on_message( $name, $rule_src, $suffix = '' ) {
		return '<span class="wholesalex_rule_on_list">' . $name . ' -> ' . $rule_src . $suffix . '</span>';
	}

	/**
	 * Add More Tier Layouts
	 *
	 * @param array $existing_layouts Existing Layout.
	 * @return array
	 * @since 1.0.6 Tier Layouts added on v1.0.1 but Refactored on v1.0.6
	 */
	public function add_more_tier_layouts( $existing_layouts ) {
		$new_layouts = array(
			'pro_layout_four'  => WHOLESALEX_URL . '/assets/img/layout_four.png',
			'pro_layout_five'  => WHOLESALEX_URL . '/assets/img/layout_five.png',
			'pro_layout_six'   => WHOLESALEX_URL . '/assets/img/layout_six.png',
			'pro_layout_seven' => WHOLESALEX_URL . '/assets/img/layout_seven.png',
			'pro_layout_eight' => WHOLESALEX_URL . '/assets/img/layout_eight.png',
		);
		return array_merge( $existing_layouts, $new_layouts );
	}


	/**
	 * After Product Update : ProductX Filter Integration.
	 *
	 * @param string|int $post_id Post ID.
	 * @return void
	 * @since 1.1.5
	 */
	public function after_product_update( $post_id ) {
		$product = wc_get_product( $post_id );
		if ( $product->is_type( 'variable' ) ) {
			$role_ids = wholesalex()->get_roles( 'ids' );
			foreach ( $role_ids as $role_id ) {
				$base_price_meta_key = $role_id . '_base_price';
				$sale_price_meta_key = $role_id . '_sale_price';
				$price_meta_key      = $role_id . '_price';
				delete_post_meta( $post_id, $price_meta_key );
				foreach ( $product->get_available_variations() as $variation ) {
					$base_price = get_post_meta( $variation['variation_id'], $base_price_meta_key, true );
					$sale_price = get_post_meta( $variation['variation_id'], $sale_price_meta_key, true );
					if ( $sale_price ) {
						add_post_meta( $post_id, $price_meta_key, $sale_price );
					} elseif ( $base_price ) {
						add_post_meta( $post_id, $price_meta_key, $base_price );
					}
				}
			}
		}
	}



	/**
	 * Import Column Mapping: WC Importer and Exporter Plugin Integration
	 *
	 * @param array $columns Columns.
	 * @return void
	 * @since 1.1.5
	 */
	public function import_column_mapping( $columns ) {
		$roles = wholesalex()->get_roles( 'b2b_roles_option' );

		foreach ( $roles as $role ) {
			$columns[ $role['value'] . '_base_price' ] = $role['name'] . ' Base Price';
			$columns[ $role['value'] . '_sale_price' ] = $role['name'] . ' Sale Price';
		}
		return $columns;
	}

	/**
	 * Export Column Value
	 *
	 * @since 1.1.5
	 */
	public function export_column_value( $value, $product, $column_name ) {
		$id    = $product->get_id();
		$value = get_post_meta( $id, $column_name, true );

		return $value;
	}


	/**
	 * Add WholesaleX Rolewise Column to WC Exporter
	 *
	 * @param array $columns Columns.
	 * @return array
	 * @since 1.1.5
	 */
	public function add_wholesale_rolewise_column_exporter( $columns ) {
		$roles = wholesalex()->get_roles( 'b2b_roles_option' );

		foreach ( $roles as $role ) {
			$columns[ $role['value'] . '_base_price' ] = $role['name'] . ' Base Price';
			$columns[ $role['value'] . '_sale_price' ] = $role['name'] . ' Sale Price';
		}
		return $columns;
	}


}

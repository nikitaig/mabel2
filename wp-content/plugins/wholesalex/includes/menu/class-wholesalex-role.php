<?php
/**
 * Role Action.
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

use WC_Data_Store;
use WC_Shipping_Zone;

/**
 * WholesaleX Role Class.
 */
class WHOLESALEX_Role {
	/**
	 * Constructor
	 *
	 * @since v.1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'role_add_submenu_page' ) );
		add_action( 'rest_api_init', array( $this, 'save_role_callback' ) );
		// add_filter( 'wholesalex_available_payment_gateways', array( $this, 'available_payment_gateways' ) );
		// add_filter( 'wholesalex_available_shipping_methods', array( $this, 'available_shipping_methods' ) );
		add_filter( 'option_woocommerce_tax_display_shop', array( $this, 'tax_display' ) );
		add_filter( 'option_woocommerce_tax_display_cart', array( $this, 'tax_display' ) );

		add_action( 'woocommerce_package_rates', array( $this, 'filter_shipping_methods' ), 9, 2 );

		/**
		 * Available Payment Gateways Set For Specific Roles.
		 *
		 * @since 1.0.3
		 */
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), 99999 );

		/**
		 * Rolewise Hide/Disable Coupons
		 *
		 * @since 1.0.4
		 */
		add_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_fields' ) );

		/**
		 * Auto Role Migration
		 *
		 * @since 1.0.4
		 */
		add_action( 'plugins_loaded', array( $this, 'auto_wholesalex_role_migration' ) );

		add_filter( 'editable_roles', array( $this, 'make_wholesalex_roles_not_editable' ) );

	}

	/**
	 * Save WholesaleX Role Actions
	 *
	 * @return void
	 */
	public function save_role_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/role_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'role_action_callback' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Role Action Callback : Get and Save Role
	 *
	 * @param object $server Server.
	 * @return mixed
	 */
	public function role_action_callback( $server ) {
		$post = $server->get_params();
		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( $post['nonce'], 'wholesalex-registration' ) ) ) {
			return;
		}

		$type = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';

		if ( 'post' === $type ) {

			$_id   = sanitize_text_field( $post['id'] );
			$_role = wp_unslash( $post['role'] );
			$_role = json_decode( $_role, true );
			$_role = wholesalex()->sanitize( $_role );
			$_flag = true;
			if ( isset( $post['check'] ) ) {
				if ( empty( wholesalex()->get_roles( 'by_id', $_id ) ) ) {
					$_flag = false;
				}
			}
			$_flag && wholesalex()->set_roles( $_id, $_role, ( isset( $post['delete'] ) && $post['delete'] ) ? 'delete' : '' );
			wp_send_json_success(
				array(
					'message' => $_flag ? __( 'Successfully Saved.', 'wholesalex' ) : __( 'Before Status Update, You Have to save role.', 'wholesalex' ),
				)
			);

		} elseif ( 'get' === $type ) {
			$__roles = array_values( wholesalex()->get_roles() );
			if ( empty( $__roles ) ) {
				$__roles = array(
					array(
						'id'    => 1,
						'label' => 'New Role',
					),
				);
			}
			$data            = array();
			$data['default'] = $this->get_role_fields();
			$data['value']   = $__roles;
			wp_send_json_success( $data );
		} elseif ( 'get_users_by_role_id' === $type ) {
			$_role_id = isset( $post['id'] ) ? sanitize_text_field( $post['id'] ) : '';
			if ( ! $_role_id ) {
				wp_send_json_success( array() );
			} else {
				$__users_options = $this->get_users_by_role_id( $_role_id );

				wp_send_json_success( $__users_options );

			}
		}
	}

	/**
	 * Role Menu callback
	 *
	 * @return void
	 */
	public function role_add_submenu_page() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'User Roles', 'wholesalex' ),
			__( 'User Roles', 'wholesalex' ),
			'manage_options',
			'wholesalex_role',
			array( $this, 'role_content_callback' )
		);
	}

	/**
	 * WholesaleX Role Sub Menu Page Callback
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function role_content_callback() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_roles' );
		?>
		<div id="_wholesalex_role"></div>
		<?php
	}

	/**
	 * Roles Fields
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Role Settings Section Added.
	 */
	public function get_role_fields() {
		$available_payment_gateways = WC()->payment_gateways->payment_gateways();
		$payment_gateways           = array();
		foreach ( $available_payment_gateways as $key => $gateway ) {
			if ( 'yes' === $gateway->enabled ) {
				$payment_gateways[ $key ] = $gateway->get_title();
			}
		}
		$__shipping_sections = array();

		$data_store         = WC_Data_Store::load( 'shipping-zone' );
		$raw_zones          = $data_store->get_zones();
		$zones              = array();
		$__shipping_zones   = array();
		$__shipping_methods = array();
		foreach ( $raw_zones as $raw_zone ) {
			$zone                  = new WC_Shipping_Zone( $raw_zone );
			$zone_id               = $zone->get_id();
			$zone_name             = $zone->get_zone_name();
			$zone_shipping_methods = $zone->get_shipping_methods();
			$shipping_methods      = array();
			foreach ( $zone_shipping_methods as $key => $method ) {
				if ( $method->is_enabled() ) {
					$method_instance_id                      = $method->get_instance_id();
					$method_title                            = $method->get_title();
					$shipping_methods[ $method_instance_id ] = $method_title;
					$__shipping_methods[ $zone_id ][]        = array(
						'value' => $method_instance_id,
						'name'  => $method_title,
					);
					$__shipping_method_options[ $zone_id . ':' . $method_instance_id ] = $zone_name . ' : ' . $method_title;
				}
			}
			$__shipping_sections[ $zone_id ] = array(
				'type'  => 'shipping_zone',
				'label' => $zone_name,
				'attr'  => array(
					'_shipping_methods' => array(
						'type'    => 'checkbox',
						'label'   => '',
						'options' => $shipping_methods,
						'default' => array( '' ),
						'help'    => 'If no methods are selected, all methods are available for this role.',
					),
				),
			);
			$__shipping_zones['']            = __( 'Choose Shipping Zone...', 'wholesalex' );
			$__shipping_zones[ $zone_id ]    = $zone_name;
			$zones[]                         = array(
				'name'            => $zone_name,
				'value'           => $zone_id,
				'shipping_method' => $shipping_methods,
			);
		}
		return apply_filters(
			'wholesalex_role_fields',
			array(
				'create_n_save_btn' => array(
					'type' => 'buttons',
					'attr' => array(
						'create' => array(
							'type'  => 'button',
							'label' => __( 'Add New B2B Role', 'wholesalex' ),
						),
					),
				),
				'_role'             => array(
					'label' => __( 'Create New Role', 'wholesalex' ),
					'type'  => 'role',
					'attr'  => array(
						'_rule_title_n_status_section' => array(
							'label' => '',
							'type'  => 'title_n_status',
							'_id'   => 1,
							'attr'  => array(
								'_role_title' => array(
									'type'        => 'text',
									'label'       => __( 'Name', 'wholesalex' ),
									'placeholder' => __( 'Name', 'wholesalex' ),
									'default'     => '',
									'help'        => '',
								),
								'save_role'   => array(
									'type'  => 'button',
									'label' => __( 'Save', 'wholesalex' ),
								),
								'cancel_role' => array(
									'type'  => 'button',
									'label' => __( 'Reset', 'wholesalex' ),
								),
							),
						),
                        'role_id_section' => array(
                            'label' => '',
                            'type'  => 'role_id',
                            '_id'   => 1,
                            'attr'  => array(
                                'role_id' => array(
                                    'type'        => 'text',
                                    'label'       => wholesalex()->get_language_n_text( version_compare( WHOLESALEX_VER, '1.0.2', '>=' ) ? '_language_wholesalex_role_id' : wholesalex()->get_setting( '_language_wholesalex_role_id' ), __( 'Role ID', 'wholesalex' ) ),
                                    'placeholder' => '',
                                    'default'     => '',
                                    'help'        => '',
                                ),
                            ),
                        ),
						'display_prices_section'       => array(
							'label' => '',
							'type'  => 'display_price',
							'_id'   => 1,
							'attr'  => array(
								'_display_price' => array(
									'type'    => 'radio',
									'label'   => __( 'Display Prices', 'wholesalex' ),
									'options' => array(
										''     => __( 'Default', 'wholesalex' ),
										'incl' => __( 'Include Tax', 'wholesalex' ),
										'excl' => __( 'Exclude Tax', 'wholesalex' ),
									),
									'default' => array( '' ),
									'help'    => '',
								),
							),
						),
						'payment_methods_section'      => array(
							'label' => '',
							'type'  => 'payment_method',
							'_id'   => 1,
							'attr'  => array(
								'_payment_methods' => array(
									'type'    => 'checkbox',
									'label'   => __( 'Payment Methods', 'wholesalex' ),
									'options' => $payment_gateways,
									'default' => array( '' ),
									'help'    => '',
								),
							),
						),
						'shipping_methods_section'     => array(
							'label' => 'Shipping Methods',
							'type'  => 'shipping_section',
							'_id'   => 1,
							'attr'  => $__shipping_sections,
						),
						'settings_section'             => array(
							'label' => __( 'Role Setting', 'wholesalex' ),
							'type'  => 'role_setting',
							'attr'  => array(
								'_disable_coupon'      => array(
									'type'    => 'switch',
									'label'   => __( 'Disable Coupons', 'wholesalex' ),
									'help'    => 'Disable Coupons For This Role',
									'default' => '',
								),
								'_auto_role_migration' => array(
									'type'     => 'switch',
									'label'    => __( 'Enable Auto Role Migration', 'wholesalex' ),
									'help'     => '',
									'default'  => '',
									'excludes' => apply_filters( 'wholesalex_exclude_auto_role_migration_field', array( 'wholesalex_guest', 'wholesalex_b2c_users' ) ),
								),
								'_role_migration_threshold_value' => array(
									'type'       => 'number',
									'label'      => __( 'Minimum Purchase Amount to Migrate to This Role', 'wholesalex' ),
									'depends_on' => array(
										array(
											'key'   => '_auto_role_migration',
											'value' => 'yes',
										),
									),
									'help'       => '',
									'default'    => '',
									'excludes'   => apply_filters( 'wholesalex_exclude_auto_role_migration_field', array( 'wholesalex_guest', 'wholesalex_b2c_users' ) ),
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Available Payment Gateways
	 *
	 * @param object $gateways Payment Gateways.
	 * @since 1.0.0
	 * @since 1.0.3 Updated
	 */
	public function available_payment_gateways( $gateways ) {

		$__role_id         = wholesalex()->get_current_user_role();
		$__role_content    = wholesalex()->get_roles( 'by_id', $__role_id );
		$__payment_methods = array();
		if ( isset( $__role_content['_payment_methods'] ) && ! empty( $__role_content['_payment_methods'] ) ) {
			$__payment_methods = $__role_content['_payment_methods'];
		}
		$__payment_methods = array_filter( $__payment_methods );

		$__available_gateways = array();

		foreach ( $__payment_methods as $method ) {
			if ( isset( $gateways[ $method ] ) && null !== $gateways[ $method ] ) {
				$__available_gateways[ $method ] = $gateways[ $method ];
			}
		}

		if ( empty( $__payment_methods ) ) {
			return $gateways;
		}

		return $__available_gateways;
	}

	/**
	 * Available Shipping Methods
	 *
	 * @param object $shipping_methods Shipping Methods.
	 */
	public function available_shipping_methods( $shipping_methods ) {
		$__role_id          = wholesalex()->get_current_user_role();
		$__role_content     = wholesalex()->get_roles( 'by_id', $__role_id );
		$__shipping_methods = array();
		if ( isset( $__role_content['_shipping_methods'] ) && ! empty( $__role_content['_shipping_methods'] ) ) {
			$__shipping_methods = $__role_content['_shipping_methods'];
		}
		$__shipping_methods = array_filter( $__shipping_methods );

		$__available_shipping_methods = array();

		foreach ( $shipping_methods as $rate_key => $rate ) {

			if ( in_array( $rate->instance_id, $__shipping_methods ) ) { //phpcs:ignore
				$__available_shipping_methods[ $rate_key ] = $rate;
			}
		}

		if ( empty( $__shipping_methods ) || empty( $__available_shipping_methods ) ) {
			return $shipping_methods;
		}

		return $__available_shipping_methods;

	}


	/**
	 * Filter Rolewise Shipping Methods
	 *
	 * @param object $package_rates Package Rates.
	 * @param object $package Package.
	 * @return object shipping methods.
	 * @since 1.0.4
	 */
	public function filter_shipping_methods( $package_rates, $package ) {
		$__role_id          = wholesalex()->get_current_user_role();
		$__role_content     = wholesalex()->get_roles( 'by_id', $__role_id );
		$__shipping_methods = array();
		if ( isset( $__role_content['_shipping_methods'] ) && ! empty( $__role_content['_shipping_methods'] ) ) {
			$__shipping_methods = $__role_content['_shipping_methods'];
		}

		$__shipping_methods = array_filter( $__shipping_methods );

		$__available_shipping_methods = array();

		foreach ( $package_rates as $rate_key => $rate ) {
			if (in_array($rate->instance_id, $__shipping_methods)) { //phpcs:ignore
				$__available_shipping_methods[ $rate_key ] = $rate;
			}
		}

		if ( ! empty( $__available_shipping_methods ) ) {
			return $__available_shipping_methods;
		}

		return $package_rates;
	}

	/**
	 * Display Prices Including Taxes
	 *
	 * @param string $option Include tax or Exclude Tax in Shop.
	 */
	public function tax_display( $option ) {
		$__role_id      = wholesalex()->get_current_user_role();
		$__role_content = wholesalex()->get_roles( 'by_id', $__role_id );
		if ( isset( $__role_content['_display_price'] ) && ! empty( $__role_content['_display_price'] ) ) {
			$option = $__role_content['_display_price'];
		}
		return $option;
	}

	/**
	 * Rolewise Hide/Disable Coupons
	 *
	 * @param bool $enabled Coupon Fields Enable Status.
	 * @return bool
	 * @since 1.0.4
	 */
	public function hide_coupon_fields( $enabled ) {
		$status = 'no';
		if ( is_user_logged_in() ) {
			$__role_id      = wholesalex()->get_current_user_role();
			$__role_content = wholesalex()->get_roles( 'by_id', $__role_id );
			if ( isset( $__role_content['_disable_coupon'] ) && ! empty( $__role_content['_disable_coupon'] ) ) {
				$status = $__role_content['_disable_coupon'];
			}

			if ( isset( $__role_id ) && ! empty( $__role_id ) && 'yes' === $status ) {
				return false;
			}
		}
		return $enabled;
	}

	/**
	 * Auto WholesaleX Role Migration
	 *
	 * @since 1.0.4
	 */
	public function auto_wholesalex_role_migration() {
		$__user_id     = get_current_user_id();
		$__total_spent = wc_get_customer_total_spent( $__user_id );

		$__roles = array_values( wholesalex()->get_roles() );
		if ( empty( $__roles ) ) {
			$__roles = array(
				array(
					'id'    => 1,
					'label' => 'New Role',
				),
			);
		}
		$__current_user_role = wholesalex()->get_current_user_role();

		foreach ( $__roles as $role ) {
			if ( $__current_user_role === $role['id'] ) {
				continue;
			}
			if ( ! isset( $role['_auto_role_migration'] ) || ! isset( $role['_role_migration_threshold_value'] ) ) {
				continue;
			}
			if ( 'yes' === $role['_auto_role_migration'] && $role['_role_migration_threshold_value'] && $__total_spent ) {
				if ( $role['_role_migration_threshold_value'] <= $__total_spent ) {
					wholesalex()->change_role( $__user_id, $role['id'], $__current_user_role );
					do_action( 'wholesalex_role_auto_migrate', $role['id'], $__current_user_role );
				}
			}
		}

	}


	/**
	 * Get Users By Role ID
	 *
	 * @param int|string $role_id Role ID.
	 *
	 * @since 1.0.9
	 */
	public function get_users_by_role_id( $role_id ) {
		$users        = get_users(
			array(
				'fields'     => array( 'ID', 'user_login' ),
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '__wholesalex_role',
						'value'   => $role_id,
						'compare' => '=',
					),
					array(
						'key'     => '__wholesalex_status',
						'value'   => 'active',
						'compare' => '=',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => '__wholesalex_account_type',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'     => '__wholesalex_account_type',
							'value'   => 'subaccount',
							'compare' => '!=',
						),
					),
				),
			)
		);
		$user_options = array();
		foreach ( $users as $user ) {
			$user_options[] = array(
				'name'  => $user->user_login,
				'value' => 'user_' . $user->ID,
			);
		}
		return $user_options;
	}


	/**
	 * Make WholesaleX Role Not Editable.
	 *
	 * @param array $roles WP Roles.
	 * @return array
	 * @since 1.0.10
	 */
	public function make_wholesalex_roles_not_editable( $roles ) {
		foreach ( $roles as $key => $value ) {
			if ( wholesalex()->start_with( $key, 'wholesalex' ) || is_numeric( $key ) ) {
				unset( $roles[ $key ] );
			}
		}
		return $roles;
	}
}

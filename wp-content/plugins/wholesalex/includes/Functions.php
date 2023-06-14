<?php
/**
 * Common Functions.
 *
 * @package WHOLESALEX\Functions
 * @since v.1.0.0
 */

namespace WHOLESALEX;

use WP_Query;
use WP_User;

defined( 'ABSPATH' ) || exit;

/**
 * Functions class.
 */
class Functions {

	/**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
	public function __construct() {

		if ( ! isset( $GLOBALS['wholesalex_settings'] ) ) {
			$GLOBALS['wholesalex_settings'] = get_option( 'wholesalex_settings' );
		}
		if ( ! isset( $GLOBALS['wholesalex_single_product_settings'] ) ) {
			$GLOBALS['wholesalex_single_product_settings'] = get_option( '__wholesalex_single_product_settings', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_single_product_discounts'] ) ) {
			$GLOBALS['wholesalex_single_product_discounts'] = get_option( '__wholesalex_single_product_discounts', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_dynamic_rules'] ) ) {
			$GLOBALS['wholesalex_dynamic_rules'] = get_option( '__wholesalex_dynamic_rules', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_roles'] ) ) {
			$GLOBALS['wholesalex_roles'] = get_option( '_wholesalex_roles', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_category_settings'] ) ) {
			$GLOBALS['wholesalex_category_settings'] = get_option( '__wholesalex_category_settings', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_category_discounts'] ) ) {
			$GLOBALS['wholesalex_category_discounts'] = get_option( '__wholesalex_category_discounts', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_profile_settings'] ) ) {
			$GLOBALS['wholesalex_profile_settings'] = get_option( '__wholesalex_profile_settings', array() );
		}
		if ( ! isset( $GLOBALS['wholesalex_profile_discounts'] ) ) {
			$GLOBALS['wholesalex_profile_discounts'] = get_option( '__wholesalex_profile_discounts', array() );
		}

		/**
		 * Settings Default Field Backward Compatibility.
		 *
		 * @since 1.0.1
		 */
		if ( ! isset( $GLOBALS['wholesalex_settings']['_settings_show_form_for_logged_in'] ) ) {
			$this->set_setting( '_settings_show_form_for_logged_in', 'no' );
		}
		if ( ! isset( $GLOBALS['wholesalex_settings']['_settings_message_for_logged_in_user'] ) ) {
			$this->set_setting( '_settings_message_for_logged_in_user', __( 'Sorry You Are Not Allowed To View This Form', 'wholesalex' ) );
		}

		/**
		 * Registration Form Default Value Added
		 *
		 * @since 1.0.4
		 */
		if ( version_compare( WHOLESALEX_VER, '1.0.4', '>' ) ) {
			$__registration_form_data = get_option( '__wholesalex_registration_form', false );
			if ( ! $__registration_form_data ) {
				$__registration_form_data = array(
					array(
						'id'                    => 2,
						'type'                  => 'email',
						'title'                 => 'Email',
						'name'                  => 'user_email',
						'required'              => true,
						'isLabelHide'           => false,
						'placeholder'           => 'example@example.com',
						'help_message'          => '',
						'exclude_roles'         => array(),
						'enableForRegistration' => true,
						'excludeRoles'          => array(),
					),
					array(
						'id'                    => 3,
						'type'                  => 'password',
						'title'                 => 'Password',
						'name'                  => 'user_pass',
						'required'              => true,
						'isLabelHide'           => false,
						'placeholder'           => 'pass',
						'help_message'          => '',
						'excludeRoles'          => array(),
						'enableForRegistration' => true,
					),
				);
				update_option( '__wholesalex_registration_form', wp_json_encode( $__registration_form_data ) );
			}
		}

		if ( version_compare( WHOLESALEX_VER, '1.0.6', '>=' ) && ! isset( $GLOBALS['wholesalex_pricing_plans'] ) ) {
			$pricing_plans = get_option( '__wholesalex_pricing_plans', false );
			if ( ! $pricing_plans ) {
				$pricing_plans = array(
					''             => 'Free',
					'starter'      => 'Starter',
					'professional' => 'Professional',
					'business'     => 'Business',
					'enterprise'   => 'Pro',
				);

				update_option( '__wholesalex_pricing_plans', $pricing_plans );
			}

			$GLOBALS['wholesalex_pricing_plans'] = $pricing_plans;
		}
		if ( ( version_compare( WHOLESALEX_VER, '1.0.6', '>=' ) && ! isset( $GLOBALS['wholesalex_feature_plan'] ) ) ) {
			$plans = get_option( '__wholesalex_feature_pricing_plans', false );
			if ( ! $plans ) {
				$plans = array(
					'dynamic_rules'   => array(
						'rule_type'      => array(
							'product_discount'  => '',
							'quantity_based'    => 'enterprise',
							'cart_discount'     => 'enterprise',
							'tax_rule'          => 'enterprise',
							'payment_discount'  => 'enterprise',
							'shipping_rule'     => 'enterprise',
							'extra_charge'      => 'enterprise',
							'payment_order_qty' => 'enterprise',
							'buy_x_get_y'       => 'enterprise',
							'min_order_qty'     => 'enterprise',
							'max_order_qty'     => 'enterprise',
						),
						'rule_for'       => array(
							'all_users'      => '',
							'all_roles'      => '',
							'specific_users' => '',
							'specific_roles' => '',
						),
						'product_filter' => array(
							'all_products'          => '',
							'products_in_list'      => '',
							'products_not_in_list'  => '',
							'cat_in_list'           => '',
							'cat_not_in_list'       => '',
							'attribute_in_list'     => '',
							'attribute_not_in_list' => '',
						),
						'conditions'     => array(
							'cart_total_qty'    => '',
							'cart_total_value'  => '',
							'cart_total_weight' => '',
							'order_count'       => 'enterprise',
							'total_purchase'    => 'enterprise',
						),
					),
					'addons'          => array(
						'wsx_addon_wallet'       => 'enterprise',
						'wsx_addon_conversation' => 'enterprise',
						'wsx_addon_raq'          => 'enterprise',
					),
					'email_templates' => array(
						'new_wholesalex_user'             => '',
						'wholesalex_registration_approve' => '',
						'wholesalex_registration_decline' => 'enterprise',
						'wholesalex_registration_pending' => 'enterprise',
						'wholesalex_email_verification'   => '',
					),
					'profile'         => array(
						'profile_discount' => 'enterprise',
					),
					'category'        => array(
						'tier' => 'enterprise',
					),
					'single_product'  => array(
						'tier' => 'enterprise',
					),
					'tier_layouts'    => array(
						'layout_one'   => 'enterprise',
						'layout_two'   => 'enterprise',
						'layout_three' => 'enterprise',
						'layout_four'  => 'enterprise',
						'layout_five'  => 'enterprise',
						'layout_six'   => 'enterprise',
						'layout_seven' => 'enterprise',
						'layout_eight' => 'enterprise',
					),
					'settings'        => array(
						'general' => array( '_settings_quantity_based_discount_priority' => 'enterprise' ),
					),
				);
				update_option( '__wholesalex_feature_pricing_plans', $plans );
			}
			$GLOBALS['wholesalex_feature_plan'] = $plans;
		}

		/**
		 * Set WholesaleX License Type
		 *
		 * @since 1.0.6 Added for new pricing system.
		 */
		if ( ! isset( $GLOBALS['wholesalex_pro_license_type'] ) ) {
			if ( 'valid' === get_option( 'edd_wholesalex_license_status', false ) ) {
				update_option( '__wholesalex_license_type', 'enterprise' );
				$GLOBALS['wholesalex_pro_license_type'] = 'enterprise';
			}
		}

		$this->update_single_product_database();

		$this->set_default_color();


	}
	/**
	 * Get All WholesaleX Roles
	 *
	 * @param string $type Type.
	 * @param mixed  $id ID.
	 * @return array $roles_options.
	 */
	public function get_roles( $type = '', $id = '' ) {
		$__roles = $GLOBALS['wholesalex_roles'];

		$__plugin_status = wholesalex()->get_setting( '_settings_status' );
		$__plugin_status = empty( $__plugin_status ) ? 'b2b' : $__plugin_status;

		$__enable_guest = apply_filters( 'wholesalex_enable_guest', true );
		if ( 'ids' === $type ) {
			return array_keys( $__roles );
		}
		if ( '' === $id && ( '' === $type || 'all' === $type ) ) {
			return isset( $__roles ) ? $__roles : array();
		} else {
			$roles_option      = array();
			$mapped_roles      = array();
			$b2b_roles_option  = array();
			$b2b_mapped_roles  = array();
			$b2c_roles_option  = array();
			$b2c_mapped_roles  = array();
			$guest_role_option = array(
				'value' => 'wholesalex_guest',
				'name'  => 'Guest Users',
			);
			if ( isset( $GLOBALS['wholesalex_roles'] ) ) {
				foreach ( $__roles as $role ) {
					if ( ! ( isset( $role['id'] ) && isset( $role['_role_title'] ) ) ) {
						continue;
					}
					$roles_option[]              = array(
						'value' => $role['id'],
						'name'  => $role['_role_title'],
					);
					$mapped_roles[ $role['id'] ] = $role['_role_title'];

					if ( ! ( 'wholesalex_b2c_users' === $role['id'] || 'wholesalex_guest' === $role['id'] ) ) {
						$b2b_roles_option[]              = array(
							'value' => $role['id'],
							'name'  => $role['_role_title'],
						);
						$b2b_mapped_roles[ $role['id'] ] = $role['_role_title'];
					} elseif ( ( 'wholesalex_b2c_users' === $role['id'] || ( $__enable_guest && 'wholesalex_guest' === $role['id'] ) ) ) {
						$b2c_roles_option[]              = array(
							'value' => $role['id'],
							'name'  => $role['_role_title'],
						);
						$b2c_mapped_roles[ $role['id'] ] = $role['_role_title'];
					}
				}
			}
			if ( '' === $id ) {
				switch ( $type ) {
					case 'roles_option':
						return $roles_option;
					case 'mapped_roles':
						return $mapped_roles;
					case 'b2b_roles_option':
						if ( 'b2b' === $__plugin_status || 'b2b_n_b2c' === $__plugin_status ) {
							return $b2b_roles_option;
						} else {
							return array();
						}
					case 'b2b_mapped_roles':
						if ( 'b2b' === $__plugin_status || 'b2b_n_b2c' === $__plugin_status ) {
							return $b2b_mapped_roles;
						} else {
							return array();
						}
					case 'b2c_roles_option':
						if ( 'b2c' === $__plugin_status || 'b2b_n_b2c' === $__plugin_status ) {
							return $b2c_roles_option;
						} else {
							return array( $guest_role_option );
						}
					case 'b2c_mapped_roles':
						if ( 'b2c' === $__plugin_status || 'b2b_n_b2c' === $__plugin_status ) {
							return $b2c_mapped_roles;
						} else {
							return array();
						}
				}
			}
			if ( 'by_id' === $type && '' !== $id ) {
				return isset( $__roles[ $id ] ) ? $__roles[ $id ] : array();
			}
		}
	}

	/**
	 * Get All Users
	 *
	 * @since 1.0.0
	 * @since 1.0.9 wholesalex_get_users_query filter added
	 */
	public function get_users() {
		$users        = get_users(
			apply_filters(
				'wholesalex_get_users_query',
				array(
					'fields' => array( 'ID', 'user_login' ),
				)
			)
		);
		$user_options = array();
		$mapped_users = array();
		foreach ( $users as $user ) {
			$user_options[]                      = array(
				'name'  => $user->user_login,
				'value' => 'user_' . $user->ID,
			);
			$mapped_users[ 'user_' . $user->ID ] = $user->user_login;
		}
		return array(
			'user_options' => $user_options,
			'mapped_users' => $mapped_users,
		);
	}

	/**
	 * Assign New Role To User
	 *
	 * @param int    $user_id User ID.
	 * @param string $new_role_id New Role ID.
	 * @param string $prev_role_id Previous Role ID.
	 * @return void
	 */
	public function change_role( $user_id, $new_role_id, $prev_role_id = '' ) {
		if ( ! ( isset( $new_role_id ) && ! empty( $new_role_id ) ) ) {
			return;
		}
		$user = new WP_User( $user_id );
		do_action( 'wholesalex_before_role_update', $user_id, $new_role_id, $prev_role_id );
		if ( '' !== $prev_role_id ) {
			$user->remove_role( $prev_role_id );
			$user->remove_cap( $prev_role_id );
		}
		$user->add_role( $new_role_id );
		/**
		 * Add User Cap as wholesalex role.
		 *
		 * @since 1.1.2
		 */
		$user->add_cap( $new_role_id );

		update_user_meta( $user_id, '__wholesalex_role', $new_role_id );

		do_action( 'wholesalex_user_role_updated', $user_id, $prev_role_id, $new_role_id );
	}

	/**
	 * Set Link with the Parameters
	 *
	 * @param STRING $url Url.
	 * @since v.1.0.0
	 * @return STRING | URL with Arg
	 */
	public function get_premium_link( $url = '', $tag = 'go_premium' ) {
		$url          = $url ? $url : 'https://www.wpxpo.com/wholesalex/pricing/';
		$affiliate_id = apply_filters( 'wholesalex_affiliate_id', false );
		$arg          = array( 'utm_source' => $tag );
		if ( ! empty( $affiliate_id ) ) {
			$arg['ref'] = esc_attr( $affiliate_id );
		}
		return add_query_arg( $arg, $url );
	}

	/**
	 * Get Global Plugin Settings
	 *
	 * @since 1.0.0
	 * @param STRING $key Key of the Option.
	 * @return ARRAY | STRING
	 */
	public function get_setting( $key = '' ) {
		$data = $GLOBALS['wholesalex_settings'];
		if ( '' !== $key ) {
			return isset( $data[ $key ] ) ? $data[ $key ] : '';
		} else {
			return $data;
		}
	}


	/**
	 * Set Option Settings
	 *
	 * @since 1.0.0
	 * @param STRING $key Key of the Option .
	 * @param STRING $val Value of the Option .
	 */
	public function set_setting( $key = '', $val = '' ) {
		if ( '' !== $key ) {
			$data         = $GLOBALS['wholesalex_settings'];
			$data[ $key ] = $val;
			update_option( 'wholesalex_settings', $data );
			$GLOBALS['wholesalex_settings'] = $data;
		}
	}

	/**
	 * Set Single Product Discounts
	 *
	 * @param mixed $id Product ID.
	 * @param array $discounts Single Product Discounts.
	 * @since 1.0.0
	 * @since 1.1.5 Add rolewise price on product meta.
	 */
	public function save_single_product_discount( $id = '', $discounts = array() ) {
		if ( '' !== $id && ! empty( $discounts ) ) {
			foreach ( $discounts as $role_name => $value ) {
				$base_price_meta_name = $role_name . '_base_price';
				$sale_price_meta_name = $role_name . '_sale_price';

				// Update Base Price
				if ( isset( $value['wholesalex_base_price'] ) ) {
					update_post_meta( $id, $base_price_meta_name, $value['wholesalex_base_price'] );
				}
				// Update Sale Price
				if ( isset( $value['wholesalex_sale_price'] ) ) {
					update_post_meta( $id, $sale_price_meta_name, $value['wholesalex_sale_price'] );
				}

				if ( isset( $value['tiers'] ) ) {
					$meta_name = $role_name . '_tiers';
					update_post_meta( $id, $meta_name, $value['tiers'] );
				}
			}
		}
	}

	/**
	 * Get Single Product Discounts
	 *
	 * @param mixed $id Product ID.
	 * @return array
	 * @since 1.0.0
	 * @since 1.1.5 Updated Meta key on single product discounts
	 */
	public function get_single_product_discount( $id = '' ) {
		$is_db_updated = get_option( '__wholesalex_database_update_v2', false );
		if ( ! $is_db_updated ) {
			$this->update_single_product_database();
		}

		$data     = array();
		$role_ids = wholesalex()->get_roles( 'ids' );
		foreach ( $role_ids as $role_id ) {
			$sale_price                                = get_post_meta( $id, $role_id . '_sale_price', true );
			$base_price                                = get_post_meta( $id, $role_id . '_base_price', true );
			$tiers                                     = get_post_meta( $id, $role_id . '_tiers', true );
			$data[ $role_id ]['wholesalex_sale_price'] = $sale_price ? $sale_price : '';
			$data[ $role_id ]['wholesalex_base_price'] = $base_price ? $base_price : '';
			$data[ $role_id ]['tiers']                 = $tiers ? $tiers : array();
		}
		return $data;

	}
	/**
	 * Save Single Product Settings
	 *
	 * @param mixed $id Product ID.
	 * @param array $settings Single Product WholesaleX Setting Data.
	 * @return void
	 */
	public function save_single_product_settings( $id = '', $settings = array() ) {
		if ( '' !== $id && ! empty( $settings ) ) {
			$data        = $GLOBALS['wholesalex_single_product_settings'];
			$data[ $id ] = $settings;
			update_option( '__wholesalex_single_product_settings', $data );
			$GLOBALS['wholesalex_single_product_settings'] = $data;
		}
	}

	/**
	 * Update Single Product Rolewise Price
	 *
	 * @return void
	 */
	public function update_single_product_database() {

		if ( ! get_option( '__wholesalex_single_product_db_update_v2', false ) && is_array( $GLOBALS['wholesalex_single_product_discounts'] ) ) {
			$data = $GLOBALS['wholesalex_single_product_discounts'];

			foreach ( $data as $id => $discounts ) {

				foreach ( $discounts as $role_name => $value ) {
					if ( isset( $value['wholesalex_base_price'] ) ) {
						$meta_name = $role_name . '_base_price';
						update_post_meta( $id, $meta_name, $value['wholesalex_base_price'] );
					}
					if ( isset( $value['wholesalex_sale_price'] ) ) {
						$meta_name = $role_name . '_sale_price';
						update_post_meta( $id, $meta_name, $value['wholesalex_sale_price'] );
					}
					if ( isset( $value['tiers'] ) ) {
						$meta_name = $role_name . '_tiers';
						update_post_meta( $id, $meta_name, $value['tiers'] );
					}
				}
			}
			update_option( '__wholesalex_single_product_db_update_v2', true );
		}
	}
	/**
	 * Save Category Visibiltiy Settings
	 *
	 * @param mixed $id Category ID.
	 * @param array $settings Category WholesaleX Setting Data.
	 * @return void
	 */
	public function save_category_visibility_settings( $id = '', $settings = array() ) {
		if ( '' !== $id && ! empty( $settings ) ) {
			$data        = $GLOBALS['wholesalex_category_settings'];
			$data[ $id ] = $settings;
			update_option( '__wholesalex_category_settings', $data );
			$GLOBALS['wholesalex_category_settings'] = $data;
		}
	}
	/**
	 * Get Category Visibiltiy Settings
	 *
	 * @param mixed $id Category ID.
	 * @return array
	 */
	public function get_category_visibility_settings( $id = '' ) {
		$data = $GLOBALS['wholesalex_category_settings'];
		if ( '' !== $id ) {
			return isset( $data[ $id ] ) ? $data[ $id ] : array();
		} else {
			return $data;
		}
	}
	/**
	 * Save Category Discounts
	 *
	 * @param mixed $id Category ID.
	 * @param array $discounts Category WholesaleX Discounts Data.
	 * @return void
	 */
	public function save_category_discounts( $id = '', $discounts = array() ) {
		if ( '' !== $id && ! empty( $discounts ) ) {
			$data        = $GLOBALS['wholesalex_category_discounts'];
			$data[ $id ] = $discounts;
			update_option( '__wholesalex_category_discounts', $data );
			$GLOBALS['wholesalex_category_discounts'] = $data;
		}
	}

	/**
	 * Get Category Visibiltiy Settings
	 *
	 * @param mixed $id Category ID.
	 * @return array
	 */
	public function get_category_discounts( $id = '' ) {
		$data = $GLOBALS['wholesalex_category_discounts'];
		if ( '' !== $id ) {
			return isset( $data[ $id ] ) ? $data[ $id ] : array();
		} else {
			return $data;
		}
	}


	/**
	 * Get Single Product Settings
	 *
	 * @param mixed $id Product ID.
	 * @return array
	 * @since 1.0.0
	 */
	public function get_single_product_setting( $id = '', $key = '' ) {
		$data = $GLOBALS['wholesalex_single_product_settings'];
		if ( '' !== $id ) {
			if ( '' !== $key ) {
				return isset( $data[ $id ][ $key ] ) ? $data[ $id ][ $key ] : wholesalex()->get_single_product_default_settings( $key );
			}
			return isset( $data[ $id ] ) ? $data[ $id ] : array();
		} else {
			return $data;
		}
	}


	/**
	 * Get Dynamic Rules
	 *
	 * @param mixed $id Rule ID.
	 * @return array If Rule ID specify, return specific rule otherwise return all roles.
	 */
	public function get_dynamic_rules( $id = '' ) {
		$__rules = $GLOBALS['wholesalex_dynamic_rules'];
		if ( '' === $id ) {
			return isset( $__rules ) ? $__rules : array();
		} else {
			return isset( $__rules[ $id ] ) ? $__rules[ $id ] : array();
		}
	}

	/**
	 * Set Dynamic Rules
	 *
	 * @param string $_id Rule ID.
	 * @param array  $_rule Rule.
	 * @param string $_type Type.
	 * @return void
	 * @since 1.0.0
	 * @since 1.0.1 Usages Count Added
	 */
	public function set_dynamic_rules( $_id = '', $_rule = array(), $_type = '' ) {
		if ( '' !== $_id && ! empty( $_rule ) ) {

			$__rules          = $GLOBALS['wholesalex_dynamic_rules'];
			$__for_all        = ( ( 'all_users' === $_rule['_rule_for'] ) || ( 'all_roles' === $_rule['_rule_for'] ) ) ? true : false;
			$__previous_count = ( isset( $__rules[ $_id ]['limit']['usages_count'] ) && ! empty( $__rules[ $_id ]['limit']['usages_count'] ) ) ? $__rules[ $_id ]['limit']['usages_count'] : '';
			$__usages_count   = isset( $_rule['limit']['usages_count'] ) ? (int) $_rule['limit']['usages_count'] : ( $__previous_count ? $__previous_count : '' );
			$__formated_rule  = array(
				'id'                      => $_id,
				'_rule_status'            => $_rule['_rule_status'],
				'_rule_title'             => $_rule['_rule_title'],
				'limit'                   => array_merge( $_rule['limit'], array( 'usages_count' => $__usages_count ) ),
				'_rule_type'              => $_rule['_rule_type'],
				$_rule['_rule_type']      => $_rule[ $_rule['_rule_type'] ],
				'_rule_for'               => $_rule['_rule_for'],
				$_rule['_rule_for']       => $__for_all ? '' : $_rule[ $_rule['_rule_for'] ],
				'_product_filter'         => $_rule['_product_filter'],
				$_rule['_product_filter'] => 'all_products' === $_rule['_product_filter'] ? '' : $_rule[ $_rule['_product_filter'] ],
				'conditions'              => $_rule['conditions'],
			);
			$__rules[ $_id ]  = wholesalex()->sanitize( $__formated_rule );
			if ( 'delete' === $_type ) {
				unset( $__rules[ $_id ] );
			}
			update_option( '__wholesalex_dynamic_rules', $__rules );
			$GLOBALS['wholesalex_dynamic_rules'] = $__rules;

			do_action( 'wholesalex_dynamic_rules_updated', $_id );
		}
	}
	/**
	 * Set wholesalex roles
	 *
	 * @param string $_id Role ID.
	 * @param array  $_role Role.
	 * @param string $_type Type.
	 * @return void
	 */
	public function set_roles( $_id = '', $_role = array(), $_type = '' ) {
		if ( '' !== $_id && ! empty( $_role ) ) {
			$__roles = $GLOBALS['wholesalex_roles'];
			if ( isset( $__roles[ $_id ] ) && ! empty( $__roles[ $_id ] ) ) {
				// update.
				$__roles[ $_id ] = wholesalex()->sanitize( $_role );

			} else {
				$__roles[ $_id ] = wholesalex()->sanitize( $_role );
				add_role( $_id, $_role['_role_title'], array( 'read' => true ) );
			}
			if ( 'delete' === $_type ) {
				unset( $__roles[ $_id ] );
				if ( wp_roles()->is_role( $_id ) ) {
					remove_role( $_id );
				}
			}
			update_option( '_wholesalex_roles', $__roles );
			$GLOBALS['wholesalex_roles'] = $__roles;
		}
	}


	/**
	 * Get Form Data
	 *
	 * @param string $role_id Role ID.
	 * @param string $type Form Data Type.
	 * @return array Form Data.
	 * @since 1.0.0
	 * @since 1.0.3 Billing and Registration Field Merged At Checkout Registration Issue Fixed
	 */
	public function get_form_data( $role_id = '', $type = 'registration' ) {
		$__form_data = get_option( '__wholesalex_registration_form' );
		if ( empty( $__form_data ) ) {
			return array();
		}

		$__form_data = json_decode( $__form_data, true );

		if ( ! empty( $role_id ) ) {
			$__registration_form_data = array();
			$__billing_form_data      = array();

			foreach ( $__form_data as $field ) {
				$__exclude_status = false;
				if ( isset( $field['excludeRoles'] ) && ! empty( $field['excludeRoles'] ) ) {
					foreach ( $field['excludeRoles'] as $role ) {
						if ( isset( $role['value'] ) && $role_id === $role['value'] ) {
							$__exclude_status = true;
							break;
						}
					}
				}
				if ( $__exclude_status ) {
					continue;
				}

				if ( isset( $field['enableForRegistration'] ) && $field['enableForRegistration'] ) {
					array_push( $__registration_form_data, $field );
				}
				if ( isset( $field['enableForBillingForm'] ) && $field['enableForBillingForm'] ) {
					array_push( $__billing_form_data, $field );
				}
			}
			if ( 'registration' === $type ) {
				return $__registration_form_data;
			}
			if ( 'billing' === $type ) {
				return $__billing_form_data;
			}
		} else {
			$__registration_form_data = array();
			$__fields                 = array();
			$__billing_form_data      = array();

			$__roles        = wholesalex()->get_roles( 'roles_option' );
			$__roles_option = array();
			foreach ( $__roles as $id => $role ) {
				if ( isset( $role['value'] ) && 'wholesalex_guest' !== $role['value'] ) {
					array_push( $__roles_option, $role );
				}
			}

			foreach ( $__form_data as $field ) {
				if ( 'user_email' === $field['name'] || 'user_pass' === $field['name'] ) {
					array_push( $__registration_form_data, $field );
				} else {
					$field['dependsOn'] = isset( $field['excludeRoles'] ) ? $field['excludeRoles'] : $field['excludeRoles'];
					if ( isset( $field['enableForRegistration'] ) && $field['enableForRegistration'] ) {
						array_push( $__fields, $field );
					}
					if ( isset( $field['enableForBillingForm'] ) && $field['enableForBillingForm'] ) {
						array_push( $__billing_form_data, $field );
					}
				}
			}

			$__select_role_dropdown = array(
				'id'       => 9999999,
				'type'     => 'select',
				'title'    => apply_filters( 'wholesalex_global_registration_form_select_roles_title', __( 'Select Registration Roles', 'wholesalex' ) ),
				'name'     => 'registration_role',
				'option'   => $__roles_option,
				'empty'    => true,
				'required' => true,
			);
			array_push( $__registration_form_data, $__select_role_dropdown );
			if ( 'registration' === $type ) {
				return array_merge( $__registration_form_data, $__fields );
			}
			if ( 'billing' === $type ) {
				return $__billing_form_data;
			}
		}

	}


	/**
	 * Set Option Settings Multiple
	 *
	 * @since 1.0.0
	 * @param array $settings_data An array of settings keys and value.
	 */
	public function set_setting_multiple( $settings_data ) {
		$data = $GLOBALS['wholesalex_settings'];
		foreach ( $settings_data as $key => $val ) {
			if ( '' !== $key ) {
				$data[ $key ] = $val;
			}
		}
		update_option( 'wholesalex_settings', $data );
		$GLOBALS['wholesalex_settings'] = $data;
	}

	/**
	 * Get Current User Role
	 *
	 * @return mixed Role ID | false
	 * @since 1.0.0
	 */
	public function get_current_user_role() {
		if ( ! is_user_logged_in() ) {
			return 'wholesalex_guest';
		}

		$__current_user_id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		$__user_role       = get_user_meta( $__current_user_id, '__wholesalex_role', true );

		if ( isset( $__user_role ) && ! empty( $__user_role ) ) {
			return $__user_role;
		}
	}



	/**
	 * Get Quantity Based Discount Priority
	 *
	 * @return array Quantity Based Discount Priorities
	 */
	public function get_quantity_based_discount_priorities() {
		$__priorities = wholesalex()->get_setting( '_settings_quantity_based_discount_priority' );
		if ( isset( $__priorities ) && is_array( $__priorities ) ) {
			$__priorities = wholesalex()->get_setting( '_settings_quantity_based_discount_priority' );
		}
		if ( ! isset( $__priorities ) || empty( $__priorities ) ) {
			$__priorities = array(
				'single_product',
				'profile',
				'category',
				'dynamic_rule',
			);
		}
		return $__priorities;
	}

	/**
	 * WholesaleX Sanitizer
	 *
	 * @param array $data .
	 * @since 1.0.0
	 * @return array $data Sanitized Array
	 */
	public function sanitize( $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[ $key ] = $this->sanitize( $value );
			} else {
				$data[ $key ] = sanitize_text_field( $value );
			}
		}
		return $data;
	}

	/**
	 * Get WholesaleX Role Name by role Id
	 *
	 * @param string $role_id WholesaleX Role ID.
	 * @return string Role Name.
	 */
	public function get_role_name_by_role_id( $role_id = '' ) {
		$__role_content = wholesalex()->get_roles( 'by_id', $role_id );
		$__title        = isset( $__role_content['_role_title'] ) ? $__role_content['_role_title'] : '';
		return $__title;
	}

	/**
	 * Insert Into Array at specific position
	 *
	 * @param array $array Initial Array.
	 * @param array $insert new element of array with key.
	 * @param int   $position The Position where new elements are inserted.
	 * @return array Updated Array.
	 */
	public function insert_into_array( $array, $insert, $position = '' ) {
		if ( empty( $position ) || '' === $position ) {
			$position = count( $array );
		}
		return array_slice( $array, 0, $position, true ) + $insert + array_slice( $array, $position, null, true );
	}

	/**
	 * Get Hidden Product Ids For Current User
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Bug Fixed.
	 */
	public function hidden_product_ids() {
		$__role = wholesalex()->get_current_user_role();

		$__single_products = wholesalex()->get_single_product_setting();

		$__product_ids_hidden_for_current_user = array();
		$__product_ids_hidden_for_guest        = array();
		$__product_ids_hidden_for_b2c          = array();
		$__product_ids_hidden_for_b2b          = array();

		foreach ( $__single_products as $id => $data ) {

			if ( isset( $data['_hide_for_visitors'] ) && 'yes' === $data['_hide_for_visitors'] ) {
				array_push( $__product_ids_hidden_for_guest, $id );
			}

			if ( isset( $data['_hide_for_b2c'] ) && 'yes' === $data['_hide_for_b2c'] ) {
				array_push( $__product_ids_hidden_for_b2c, $id );
			}
			if ( is_user_logged_in() && isset( $data['_hide_for_b2b_role_n_user'] ) ) {
				$__user_id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );

				if ( 'user_specific' === $data['_hide_for_b2b_role_n_user'] ) {
					if ( isset( $data['_hide_for_users'] ) && is_array( $data['_hide_for_users'] ) ) {
						$__hide_for_users = $data['_hide_for_users'];
						foreach ( $__hide_for_users as $users ) {
							if ( isset( $users['value'] ) && 'user_' . $__user_id === $users['value'] ) {
								array_push( $__product_ids_hidden_for_current_user, $id );
								break;
							}
						}
					}
				}

				if ( 'b2b_specific' === $data['_hide_for_b2b_role_n_user'] ) {
					if ( isset( $data['_hide_for_roles'] ) && is_array( $data['_hide_for_roles'] ) ) {
						$__hide_for_roles = $data['_hide_for_roles'];
						foreach ( $__hide_for_roles as $roles ) {
							if ( isset( $roles['value'] ) && $roles['value'] === $__role ) {
								array_push( $__product_ids_hidden_for_b2b, $id );
								break;
							}
						}
					}
				}

				if ( 'b2b_all' === $data['_hide_for_b2b_role_n_user'] ) {
					array_push( $__product_ids_hidden_for_b2b, $id );
				}
			}
		}

		switch ( $__role ) {
			case 'wholesalex_guest':
				return $__product_ids_hidden_for_guest;
			case 'wholesalex_b2c_users':
				return array_unique( array_merge( $__product_ids_hidden_for_current_user, $__product_ids_hidden_for_b2c ) );
			default:
				if ( ! empty( wholesalex()->get_roles( 'by_id', $__role ) ) ) {
					return array_unique( array_merge( $__product_ids_hidden_for_current_user, $__product_ids_hidden_for_b2b ) );
				}
		}

		return array();
	}

	/**
	 * Get WholesaleX Hidden Product and Categories ID
	 *
	 * @param string $type Type of Hidden Items. product | category.
	 * @return array Hidden Items id.
	 * @since 1.0.0
	 */
	public function hidden_ids( $type = 'product' ) {
		$__role = wholesalex()->get_current_user_role();

		$__visibility_settings = array();
		switch ( $type ) {
			case 'product':
				$__visibility_settings = wholesalex()->get_single_product_setting();
				break;
			case 'category':
				$__visibility_settings = wholesalex()->get_category_visibility_settings();
				break;
		}

		$__ids_hidden_for_current_user = array();
		$__ids_hidden_for_guest        = array();
		$__ids_hidden_for_b2c          = array();
		$__ids_hidden_for_b2b          = array();

		foreach ( $__visibility_settings as $id => $data ) {

			if ( isset( $data['_hide_for_visitors'] ) && 'yes' === $data['_hide_for_visitors'] ) {
				array_push( $__ids_hidden_for_guest, $id );
			}

			if ( isset( $data['_hide_for_b2c'] ) && 'yes' === $data['_hide_for_b2c'] ) {
				array_push( $__ids_hidden_for_b2c, $id );
			}
			if ( is_user_logged_in() && isset( $data['_hide_for_b2b_role_n_user'] ) ) {
				$__user_id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );

				if ( 'user_specific' === $data['_hide_for_b2b_role_n_user'] ) {
					if ( isset( $data['_hide_for_users'] ) && is_array( $data['_hide_for_users'] ) ) {
						$__hide_for_users = $data['_hide_for_users'];
						foreach ( $__hide_for_users as $users ) {
							if ( isset( $users['value'] ) && 'user_' . $__user_id === $users['value'] ) {
								array_push( $__ids_hidden_for_current_user, $id );
								break;
							}
						}
					}
				}

				if ( 'b2b_specific' === $data['_hide_for_b2b_role_n_user'] ) {
					if ( isset( $data['_hide_for_roles'] ) && is_array( $data['_hide_for_roles'] ) ) {
						$__hide_for_roles = $data['_hide_for_roles'];
						foreach ( $__hide_for_roles as $roles ) {
							if ( isset( $roles['value'] ) && $roles['value'] === $__role ) {
								array_push( $__ids_hidden_for_b2b, $id );
								break;
							}
						}
					}
				}

				if ( 'b2b_all' === $data['_hide_for_b2b_role_n_user'] ) {
					array_push( $__ids_hidden_for_b2b, $id );
				}
			}
		}

		switch ( $__role ) {
			case 'wholesalex_guest':
				return $__ids_hidden_for_guest;
			case 'wholesalex_b2c_users':
				return array_unique( array_merge( $__ids_hidden_for_current_user, $__ids_hidden_for_b2c ) );
			default:
				if ( ! empty( wholesalex()->get_roles( 'by_id', $__role ) ) ) {
					return array_unique( array_merge( $__ids_hidden_for_current_user, $__ids_hidden_for_b2b ) );
				}
				break;
		}

		return array();
	}

	/**
	 * Filter Empty Tiers
	 *
	 * @param array $tiers Discounts Tier.
	 * @return array Updated Tiers.
	 */
	public function filter_empty_tier( $tiers ) {
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
	 * Get Discounts For Current User
	 *
	 * @param string $type Type.
	 * @param mixed  $product_id Product ID.
	 */
	public function get_discounts( $type = '', $product_id = -1 ) {

		if ( '' !== $type && -1 !== $product_id ) {

			$__role            = wholesalex()->get_current_user_role();
			$__current_user_id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
			$__discounts       = array();

			$__eligible_for_discounts = true;

			if ( ! ( ( $__current_user_id && 'active' === wholesalex()->get_user_status( $__current_user_id ) ) ) ) {
				$__eligible_for_discounts = false;
			}
			if ( 'wholesalex_guest' === wholesalex()->get_current_user_role() ) {
				$__eligible_for_discounts = true;
			}

			switch ( $type ) {
				case 'category':
					$__cat_ids           = wc_get_product_term_ids( $product_id, 'product_cat' );
					$__discounted_cat_id = '';
					// If product associated with multiple categories, take the first one which has discounts.
					foreach ( $__cat_ids as $cat_id ) {
						$__discounts = wholesalex()->get_category_discounts( $cat_id );
						if ( isset( $__discounts[ $__role ] ) && ! empty( $__discounts[ $__role ] ) ) {
							$__discounts         = $__discounts[ $__role ];
							$__discounted_cat_id = $cat_id;
							break;
						}
					}

					// Remove Empty Tiers.
					$__tiers = isset( $__discounts['tiers'] ) ? $this->filter_empty_tier( $__discounts['tiers'] ) : array();

					if ( isset( $__tiers['_min_quantity'] ) ) {
						// Sort tiers by Min Quantity.
						$__sort_column = array_column( $__tiers, '_min_quantity' );
						array_multisort( $__sort_column, SORT_ASC, $__tiers );
					}

					return array(
						'cat_id' => $__eligible_for_discounts ? $__discounted_cat_id : 0,
						'tiers'  => $__eligible_for_discounts ? $__tiers : array(),
					);
				case 'product':
					$__discounts = wholesalex()->get_single_product_discount( $product_id );
					if ( ! isset( $__discounts[ $__role ] ) ) {
						return;
					}
					$__discounts = $__discounts[ $__role ];

					$__regular_price = isset( $__discounts['wholesalex_base_price'] ) ? $__discounts['wholesalex_base_price'] : '';

					$__sale_price = isset( $__discounts['wholesalex_sale_price'] ) ? $__discounts['wholesalex_sale_price'] : '';

					$__tiers = isset( $__discounts['tiers'] ) ? $this->filter_empty_tier( $__discounts['tiers'] ) : array();

					if ( isset( $__tiers['_min_quantity'] ) ) {
						// Sort tiers by Min Quantity.
						$__sort_colum = array_column( $__tiers, '_min_quantity' );
						array_multisort( $__sort_colum, SORT_ASC, $__tiers );
					}

					return array(
						'regular_price' => $__eligible_for_discounts ? $__regular_price : '',
						'sale_price'    => $__eligible_for_discounts ? $__sale_price : '',
						'tiers'         => $__eligible_for_discounts ? $__tiers : array(),
					);

				default:
					// code...
					break;
			}
		}
	}

	/**
	 * Category Cart Count. It count how many product are in cart of given cat id.
	 *
	 * @param mixed $cat_id Category ID.
	 * @return int Product Count.
	 * @since 1.0.0
	 */
	public function category_cart_count( $cat_id ) {
		$cat_count = 0;
		if ( isset( WC()->cart ) && ! empty( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$__product_id = $cart_item['product_id'];
				$__product    = wc_get_product( $__product_id );

				if ( 'product_variation' === $__product->post_type ) {
					$__product_id = $__product->get_parent_id();
				}
				if ( has_term( $cat_id, 'product_cat', $__product_id ) ) {
					$cat_count += $cart_item['quantity'];
				}
			}
		}
		return $cat_count;
	}

	/**
	 * Get Product Count at cart
	 *
	 * @param int $product_id Product or Variation ID.
	 * @return int Product count at cart.
	 * @since 1.0.0
	 */
	public function cart_count( $product_id ) {
		$__quantity = 0;
		if ( ! is_null( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( ! empty( $cart_item['data'] ) && in_array( $product_id, array( $cart_item['product_id'], $cart_item['variation_id'] ), true ) ) {
					$__quantity = $cart_item['quantity'];
					break; // stop the loop if product is found.
				}
			}
		}

		return $__quantity;
	}

	/**
	 * Get Cart Total Amount
	 *
	 * @param string $product_id Product ID.
	 * @return int If product id specified then return cart subtotal price of specific product otherwise return total cart content price.
	 * @since 1.0.0
	 */
	public function get_cart_total( $product_id = '' ) {
		if ( ! isset( WC()->cart ) || null === WC()->cart->get_cart() ) {
			return 0;
		}

		if ( ! empty( $product_id ) && isset( WC()->cart ) ) {

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( isset($cart_item['product_id']) && isset($cart_item['variation_id']) && isset($cart_item['line_total']) &&  in_array( $product_id, array( $cart_item['product_id'], $cart_item['variation_id'] ) ) ) { //phpcs:ignore
					return $cart_item['line_total'];
				}
			}
		} else {
			$__total    = 0.0;
			$__with_tax = apply_filters( 'wholesalex_get_cart_total_with_tax', false );
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( isset( $cart_item['line_total'] ) ) {
					if ( $__with_tax && isset( $cart_item['line_tax'] ) ) {
						$__total = $__total + $cart_item['line_total'] + $cart_item['line_tax'];
					} else {
						$__total = $__total + $cart_item['line_total'];
					}
				}
			}
			return (float) $__total;
		}

		return 0;

	}

	/**
	 * Get Cart Total Weight
	 *
	 * @return float Total Weight of cart.
	 * @since 1.0.0
	 */
	public function get_cart_total_weight() {
		$__total = 0.0;
		if ( ! isset( WC()->cart ) ) {
			return $__total;
		}
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$__item_weight = $cart_item['data']->get_weight();
			if ( ! empty( $__item_weight ) ) {
				$__total = $__total + ( $__item_weight * $cart_item['quantity'] );
			}
		}
		return (float) $__total;
	}


	/**
	 * Get WholesaleX User Status
	 *
	 * @param string $id User ID.
	 * @return string user status.
	 */
	public function get_user_status( $id = '' ) {
		if ( empty( $id ) ) {
			$id = apply_filters( 'wholesalex_set_current_user', get_current_user_id() );
		}

		$__user_status = get_user_meta( $id, '__wholesalex_status', true );
		$__user_status = ( $__user_status && ! empty( $__user_status ) ) ? $__user_status : '';
		return $__user_status;
	}

	/**
	 * Get Language and Text Settings
	 *
	 * @param string $from_setting Settings Text.
	 * @param string $default Default Text.
	 * @return string
	 * @since 1.0.0
	 * @since 1.0.2 Updated and Backward Compatibility Added.
	 */
	public function get_language_n_text( $from_setting, $default ) {

		if ( version_compare( WHOLESALEX_VER, '1.0.2', '>=' ) > 0 ) {
			if ( isset( $GLOBALS['wholesalex_settings'][ $from_setting ] ) ) {
				return $this->get_setting( $from_setting );
			} else {
				return $default;
			}
		} else {
			if ( ! empty( $from_setting ) ) {
				return $from_setting;
			} else {
				return $default;
			}
		}
	}

	/**
	 * Is Pro Active.
	 */
	public function is_pro_active() {
		// For Pro Check.
		$__is_pro_active = false;
		if ( function_exists( 'wholesalex_pro' ) && wholesalex_pro()->is_active() ) {
			$__is_pro_active = true;
		}
		return $__is_pro_active;
	}


	/**
	 * Calculate Per Unit Sale Price
	 *
	 * @param array        $tier Tier Array.
	 * @param string|float $regular_price Regular Price.
	 * @since 1.0.0
	 * @since 1.0.3 Fixed Price Issue Fixed
	 */
	public function calculate_sale_price( $tier, $regular_price ) {
		if ( ! ( isset( $tier['_discount_type'] ) && isset( $tier['_discount_amount'] ) && ! empty( $regular_price ) ) ) {
			return;
		}
		switch ( $tier['_discount_type'] ) {
			case 'percentage':
				$__sale_price = max( 0, (float) $regular_price - ( ( (float) $regular_price * $tier['_discount_amount'] ) / 100.00 ) );
				return $__sale_price;
			case 'amount':
				$__sale_price = max( 0, (float) $regular_price - $tier['_discount_amount'] );
				return $__sale_price;
			case 'fixed_price':
				$__sale_price = max( 0, (float) $tier['_discount_amount'] );
				return $__sale_price;
			case 'fixed':
				$__sale_price = max( 0, (float) $tier['_discount_amount'] );
				return $__sale_price;
		}
	}

	/**
	 * Dynamic Rules Usages Count Handle
	 *
	 * @param int $rule_id Dynamic Rule ID.
	 * @return void
	 */
	public function set_usages_dynamic_rule_id( $rule_id ) {
		if ( is_admin() || null === WC()->session ) {
			return;
		}
		$__dynamic_rule_id = WC()->session->get( '__wholesalex_used_dynamic_rule' );
		if ( ! ( isset( $__dynamic_rule_id ) && is_array( $__dynamic_rule_id ) ) ) {
			$__dynamic_rule_id = array();
		}
		$__dynamic_rule_id[ $rule_id ] = true;

		WC()->session->set( '__wholesalex_used_dynamic_rule', $__dynamic_rule_id );
	}


	/**
	 * Get Single Product Default Settings
	 *
	 * @param string $key Setting name.
	 * @return string
	 */
	public function get_single_product_default_settings( $key = '' ) {
		if ( '' !== $key ) {
			switch ( $key ) {
				case '_settings_tier_layout':
					return 'layout_one';
				case '_settings_show_tierd_pricing_table':
					return 'yes';
				case '_settings_override_tax_extemption':
					return 'disable';
				case '_settings_override_shipping_role':
					return 'disable';
				case '_settings_override_tax_extemption':
					return 'disable';
				default:
					return '';
			}
		}
	}

	/**
	 * Get All Product Ids
	 *
	 * @since 1.0.2
	 */
	public function get_all_product_ids() {
		$ids = new WP_Query(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'fields'      => 'ids',
			)
		);

		return $ids;
	}

	// /**
	// * Get Pricing Plans
	// *
	// * @return array
	// * @since 1.0.6
	// */
	// public function get_pricing_plans() {
	// $plans = array();
	// if ( isset( $GLOBALS['wholesalex_pricing_plans'] ) ) {
	// $plans = $GLOBALS['wholesalex_pricing_plans'];
	// }
	// return $plans;
	// }

	/**
	 * Get Feature Pricing Plans.
	 *
	 * @param string $feature_name Feature Name.
	 * @param string $type Option Type.
	 * @param string $option Option Name.
	 * @return bool|string
	 */
	public function get_feature_pricing_plan( $feature_name, $type = '', $option = '' ) {

		$feature_pricing_plans = false;
		if ( isset( $GLOBALS['wholesalex_feature_plan'] ) ) {
			$feature_pricing_plans = $GLOBALS['wholesalex_feature_plan'];
			switch ( $feature_name ) {
				case 'dynamic_rules':
					if ( isset( $feature_pricing_plans[ $feature_name ][ $type ][ $option ] ) ) {
						return $feature_pricing_plans[ $feature_name ][ $type ][ $option ];
					} else {
						return false;
					}
				case 'addons':
				case 'email_templates':
				case 'profile':
				case 'category':
				case 'single_product':
				case 'tier_layouts':
					if ( isset( $feature_pricing_plans[ $feature_name ][ $type ] ) ) {
						return $feature_pricing_plans[ $feature_name ][ $type ];
					} else {
						return false;
					}
				default:
					// code...
					break;
			}
		}
		return false;
	}


	/**
	 * Get WholesaleX License Type
	 *
	 * @return string
	 */
	public function get_license_type() {
		$__status = get_option( 'edd_wholesalex_license_status', true );
		if ( 'valid' !== $__status ) {
			return '';
		}
		return get_option( '__wholesalex_license_type', '' );
	}

	/**
	 * Get Eligible License Plans
	 *
	 * @return array
	 */
	public function get_eligible_license_plans() {
		$license_type  = wholesalex()->get_license_type();
		$pricing_plans = wholesalex()->get_pricing_plans();

		$pricing_plans  = array_reverse( $pricing_plans, 'true' );
		$eligible_plans = array();
		$flag           = false;
		foreach ( $pricing_plans as $pricing_key => $pricing_value ) {
			if ( $pricing_key === $license_type ) {
				$flag = true;
			}
			if ( $flag ) {
				$eligible_plans[ $pricing_key ] = $pricing_value;
			}
		}
		return $eligible_plans;
	}

	/**
	 * Get Pricing Plans
	 *
	 * @return array
	 * @since 1.0.6
	 */
	public function get_pricing_plans() {
		$plans = get_option( '__wholesalex_pricing_plans', false );
		if ( $plans ) {
			return $plans;
		}
		return array();
	}


	/**
	 * Get Upgrade Pro Popup HTML
	 *
	 * @param string $heading Heading.
	 * @param string $subheading Subheading.
	 * @param string $desc Description.
	 * @return void
	 * @since 1.0.10
	 */
	public function get_upgrade_pro_popup_html( $heading = '', $subheading = '', $desc = '', $url = '' ) {
		if ( '' == $url ) {
			$url = wholesalex()->get_premium_link();
		}
		?>
		<div id="wholesalex-pro-popup" class="wholesalex-popup-container popup-center display-none">
			<div class="wholesalex-unlock-popup wholesalex-unlock-modal">
				<img src="<?php echo esc_url( WHOLESALEX_URL ) . 'assets/img/unlock.svg'; ?>" alt="Unlock Icon"/>
				<h4 class="wholesalex-md-heading wholesalex-mt25"><?php echo esc_html( $heading ); ?></h4>
				<?php
				if ( $subheading ) {
					?>
					<span class="wholesalex-unlock-subheading"><?php echo esc_html( $subheading ); ?> </span>
					<?php
				}
				?>
				<div class="wholesalex-popup-desc">
					<?php echo esc_html( $desc ); ?>
				</div>
				<a href="<?php echo esc_url( $url ); ?>" class="wholesalex-btn wholesalex-btn-warning wholesalex-mt25"><?php echo esc_html__( 'Get WholesaleX Pro', 'wholesalex' ); ?></a>
				<button class="wholesalex-popup-close pro-popup" id="wholesalex-close-pro-popup" onclick="closeWholesaleXGetProPopUp()"></button>
			</div>
		</div>
		<?php
	}


	/**
	 * Check Any String Start With
	 *
	 * @param string $str Main String.
	 * @param string $begin_with Begin With String.
	 * @return bool
	 * @since 1.0.10
	 */
	public function start_with( $str, $begin_with ) {
		$len = strlen( $begin_with );
		return ( substr( $str, 0, $len ) === $begin_with );
	}


	/**
	 * Get WholesaleX Rolewise Sale and Base Price
	 *
	 * @param string|int $user_id User Id.
	 * @param string|int $product_id Product ID.
	 * @return array
	 */
	public function get_wholesalex_rolewise_single_product_price( $user_id = '', $product_id = '' ) {
		if ( ! ( $user_id && $product_id ) ) {
			return false;
		}
		$user_role_id = get_user_meta( $user_id, '__wholesalex_role', true );
		$product      = wc_get_product( $product_id );
		if ( ! $product ) {
			return false;
		}
		$sale_price = $product->get_sale_price();
		$base_price = $product->get_regular_price();
		if ( $user_role_id && function_exists( 'wholesalex' ) ) {
			// All Roles Price.
			$prices = wholesalex()->get_single_product_discount( $product_id );

			// Price For Current User Role.
			$price = isset( $prices[ $user_role_id ] ) ? $prices[ $user_role_id ] : array();

			$sale_price = isset( $price['wholesalex_sale_price'] ) ? $price['wholesalex_sale_price'] : $sale_price;
			$base_price = isset( $price['wholesalex_base_price'] ) ? $price['wholesalex_base_price'] : $base_price;
		}

		return array(
			'base_price' => $base_price,
			'sale_price' => $sale_price,
		);
	}


	/**
	 * Check is ppop plugin active or not
	 *
	 * @return boolean
	 * @since 1.1.7
	 */
	public function is_ppop_active() {
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}
		if ( file_exists( WP_PLUGIN_DIR . '/woocommerce-product-addon/woocommerce-product-addon.php' ) && in_array( 'woocommerce-product-addon/woocommerce-product-addon.php', $active_plugins, true ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function set_default_color() {
		if(!get_transient('__wholesalex_default_color_update_v1')) {
			$this->set_setting('_settings_primary_color','#4D4DFF');
			$this->set_setting('_settings_primary_hover_color','#6C6CFF');
			set_transient('__wholesalex_default_color_update_v1', true);
		}
	}
}

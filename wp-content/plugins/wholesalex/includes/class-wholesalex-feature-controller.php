<?php
/**
 * WholesaleX Feature Controller
 *
 * @link              https://www.wpxpo.com/
 * @since             1.0.6
 * @package           WholesaleX
 */

namespace WHOLESALEX;

defined( 'ABSPATH' ) || exit;


/**
 * WholesaleX Feature Controller
 */
class WholesaleX_Feature_Controller {

	/**
	 * Pricing Plans
	 *
	 * @var array
	 */
	public $pricing_plans = array();
	/**
	 * Define Pricing Plan
	 *
	 * @since    1.0.6
	 */
	public function __construct() {
		if ( wholesalex()->is_pro_active() && version_compare( WHOLESALEX_PRO_VER, '1.0.6', '>=' ) ) {
			add_filter( 'wholesalex_dynamic_rules_rule_type_options', array( $this, 'dynamic_rule_options_pricing_plan' ), 10, 2 );
			add_filter( 'wholesalex_dynamic_rules_rule_for_options', array( $this, 'dynamic_rule_options_pricing_plan' ), 10, 2 );
			add_filter( 'wholesalex_dynamic_rules_product_filter_options', array( $this, 'dynamic_rule_options_pricing_plan' ), 10, 2 );
			add_filter( 'wholesalex_dynamic_rules_condition_options', array( $this, 'dynamic_rule_options_pricing_plan' ), 10, 2 );
			add_filter( 'wholesalex_addons_config', array( $this, 'addons_config' ), 20 );
			add_filter( 'wholesalex_email_templates', array( $this, 'email_templates_config' ) );

			$this->pricing_plans = wholesalex()->get_pricing_plans();

			add_filter( 'wholesalex_settings_product_tier_layout', array( $this, 'tier_layout_options_pricing_plan' ), 20 );
			add_filter( 'wholesalex_single_product_tier_layout', array( $this, 'tier_layout_options_pricing_plan' ), 20 );

			add_action( 'wp_ajax_profile_discount_pricing_type', array( $this, 'profile_discount_pricing_type' ) );
			add_action( 'wp_ajax_category_tier_discount_pricing_type', array( $this, 'category_tier_discount_pricing_type' ) );
			add_action( 'wp_ajax_single_product_tier_discount_pricing_type', array( $this, 'single_product_tier_discount_pricing_type' ) );
		}

	}

	/**
	 * Profile Discount Pricing Type Ajax
	 *
	 * @return void
	 */
	public function profile_discount_pricing_type() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}
		$plan = wholesalex()->get_feature_pricing_plan( 'profile', 'profile_discount' );
		if ( $plan ) {
			wp_send_json_success( $plan );
		} else {
			wp_send_json_error();
		}
	}
	/**
	 * Category Tier Discount Pricing Type.
	 *
	 * @return void
	 */
	public function category_tier_discount_pricing_type() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}
		$plan = wholesalex()->get_feature_pricing_plan( 'category', 'tier' );
		if ( $plan ) {
			wp_send_json_success( $plan );
		} else {
			wp_send_json_error();
		}
	}
	/**
	 * Single Product Tier Discount Pricing Type.
	 *
	 * @return void
	 */
	public function single_product_tier_discount_pricing_type() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}
		$plan = wholesalex()->get_feature_pricing_plan( 'single_product', 'tier' );
		if ( $plan ) {
			wp_send_json_success( $plan );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Dynamic Rule Options pricing plans
	 *
	 * @param array  $options Options.
	 * @param string $type Feature Type.
	 */
	public function dynamic_rule_options_pricing_plan( $options, $type ) {
		foreach ( $options as $key => $value ) {
			$value       = str_replace( '(Pro)', '', $value );
			$spilted_key = explode( '_', $key );
			$_is_pro     = ( 'pro' === $spilted_key[0] );
			if ( $_is_pro ) {
				array_shift( $spilted_key );
				$option_value = implode( '_', $spilted_key );
				$plan_name    = wholesalex()->get_feature_pricing_plan( 'dynamic_rules', $type, $option_value );
				if ( '' !== $plan_name ) {
					unset( $options[ $key ] );
					$option_name = $value . '(' . $this->pricing_plans[ $plan_name ] . ')';
					$options[ 'pro_' . $plan_name . '_' . $option_value ] = $option_name;
				}
			}
		}
		return $options;
	}
	/**
	 * Tier Layouts Pricing Plans
	 *
	 * @param array $options Options.
	 */
	public function tier_layout_options_pricing_plan( $options ) {
		foreach ( $options as $key => $value ) {
			$spilted_key = explode( '_', $key );
			$_is_pro     = ( 'pro' === $spilted_key[0] );
			if ( $_is_pro ) {
				array_shift( $spilted_key );
				$option_value = implode( '_', $spilted_key );
				$plan_name    = wholesalex()->get_feature_pricing_plan( 'tier_layouts', $option_value );
				if ( '' !== $plan_name ) {
					unset( $options[ $key ] );
					$options[ 'pro_' . $plan_name . '_' . $option_value ] = $value;
				}
			}
		}
		return $options;
	}


	/**
	 * Addons Config For Pricing Plans
	 *
	 * @param array $addons_config Addon Config.
	 * @return array
	 */
	public function addons_config( $addons_config ) {
		foreach ( $addons_config as $addon_id => $value ) {
			$plan_name = wholesalex()->get_feature_pricing_plan( 'addons', $addon_id );
			if ( $plan_name && '' !== $plan_name ) {
				if ( $value['is_pro'] ) {
					$addons_config[ $addon_id ]['plan'] = $plan_name;
				}
			}
		}
		return $addons_config;
	}

	/**
	 * Email Templates Config Form Pricing Plan
	 *
	 * @param array $templates Email Templates.
	 * @return array
	 */
	public function email_templates_config( $templates ) {
		foreach ( $templates as $template_id => $value ) {
			$plan_name = wholesalex()->get_feature_pricing_plan( 'email_templates', $template_id );
			if ( $plan_name && '' !== $plan_name ) {
				if ( $value['is_pro'] ) {
					$templates[ $template_id ]['plan'] = $plan_name;
				}
			}
		}
		return $templates;
	}

}

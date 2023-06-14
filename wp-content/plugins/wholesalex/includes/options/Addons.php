<?php
/**
 * Addons Page
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Setting Class
 */
class Addons {

	/**
	 * Setting Constructor
	 */
	public function __construct() {
		add_filter( 'wholesalex_addons_config', array( $this, 'pro_addons_config' ) );
		add_submenu_page(
			'wholesalex-overview',
			__( 'Addons', 'wholesalex' ),
			__( 'Addons', 'wholesalex' ),
			'manage_options',
			'wholesalex-addons',
			array( $this, 'create_admin_page' ),
			15
		);
	}

	/**
	 * Pro Addons Config
	 *
	 * @param object $config Addon Configuration.
	 * @return object $config .
	 * @since 1.0.0
	 * @since 1.0.4 Add Bulk Order.
	 */
	public function pro_addons_config( $config ) {
		$config['wsx_addon_wallet'] = array(
			'name'                => __( 'Wallet', 'wholesalex' ),
			'desc'                => __( 'This addon enables a wallet for Wholesale users. So that, they can add and manage funds.', 'wholesalex' ),
			'img'                 => WHOLESALEX_URL . 'assets/img/addons/wallet.svg',
			'docs'                => 'https://getwholesalex.com/docs/wholesalex/add-on/wallet/',
			'live'                => '',
			'is_pro'              => true,
			'is_different_plugin' => false,
			'eligible_price_ids'  => array( '1', '2', '3', '4', '5', '6', '7' ),
		);

		$config['wsx_addon_conversation'] = array(
			'name'                => __( 'Conversation', 'wholesalex' ),
			'desc'                => __( 'This addon enables functionality to the “My Account” Page. So that, the customers can communicate with the site admin.', 'wholesalex' ),
			'img'                 => WHOLESALEX_URL . 'assets/img/addons/conversation.svg',
			'docs'                => 'https://getwholesalex.com/docs/wholesalex/add-on/conversation/',
			'live'                => '',
			'is_pro'              => true,
			'is_different_plugin' => false,
			'eligible_price_ids'  => array( '1', '2', '3', '4', '5', '6', '7' ),

		);

		$config['wsx_addon_raq'] = array(
			'name'                => __( 'Request A Quote', 'wholesalex' ),
			'desc'                => __( 'This addon lets the shoppers, “Request a Quote” directly from the cart page and negotiate with you via conversation option and email.', 'wholesalex' ),
			'img'                 => WHOLESALEX_URL . 'assets/img/addons/raq.svg',
			'docs'                => 'https://getwholesalex.com/docs/wholesalex/add-on/request-a-quote/',
			'live'                => '',
			'is_pro'              => true,
			'is_different_plugin' => false,
			'depends_on'          => apply_filters( 'wholesalex_addon_raq_depends_on', array( 'wsx_addon_conversation' => __( 'Conversation', 'wholesalex' ) ) ),
			'eligible_price_ids'  => array( '1', '2', '3', '4', '5', '6', '7' ),
		);

		$config['wsx_addon_bulkorder'] = array(
			'name'                => __( 'Bulk Order', 'wholesalex' ),
			'desc'                => __( 'Display bulk order form on my account, or any other page. And, let the customers make purchase lists to order later.', 'wholesalex' ),
			'img'                 => WHOLESALEX_URL . 'assets/img/addons/bulkorder.svg',
			'docs'                => 'https://getwholesalex.com/docs/wholesalex/add-on/bulk-order/',
			'live'                => '',
			'is_pro'              => true,
			'is_different_plugin' => false,
			'eligible_price_ids'  => array( '2', '3', '5', '6', '7' ),

		);

		$config['wsx_addon_subaccount'] = array(
			'name'                => __( 'Subaccounts ', 'wholesalex' ),
			'desc'                => __( 'Create and display subaccounts with customizable and configurable permissions under the main account.', 'wholesalex' ),
			'img'                 => WHOLESALEX_URL . 'assets/img/addons/subaccount.svg',
			'docs'                => 'https://getwholesalex.com/docs/wholesalex/add-on/subaccounts/',
			'live'                => '',
			'is_pro'              => true,
			'is_different_plugin' => false,
			'eligible_price_ids'  => array( '2', '3', '5', '6', '7' ),
		);
		return $config;
	}

	/**
	 * Settings page output
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Add Dependency Check For Addon Plugins.
	 */
	public function create_admin_page() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_header' );
		$get_pro_heading = __( 'Unlock All Addons', 'wholesalex' );
		$get_pro_desc    = __( 'We are sorry, but unfortunately, only one addon is available in the free version of WholesaleX. Please get the Business Pack to unlock all addons and features.', 'wholesalex' );
		?>
			<div id='wholesalex_addons_header'></div>
			<div class="wsx-addons-dashboard">
				<div class="wholesalex-editor__row wholesalex-editor__heading">
					<?php esc_html_e( 'WholesaleX Addons', 'wholesalex' ); ?>
				</div>
				<div class="wsx-addons-grid">
					<?php
						$option_value = wholesalex()->get_setting();
						$addons_data  = apply_filters( 'wholesalex_addons_config', array() );
					foreach ( $addons_data as $key => $val ) {
						$__is_pro_active        = function_exists( 'wholesalex_pro' ) && method_exists( wholesalex_pro(), 'is_active' ) && wholesalex_pro()->is_active();
						$__is_pro               = isset( $val['is_pro'] ) ? $val['is_pro'] : '';
						$__have_dependency      = isset( $val['is_different_plugin'] ) ? $val['is_different_plugin'] : '';
						$__dependency_fulfilled = true;
						if ( $__have_dependency ) {
							$plugin_slug = $val['plugin_slug'];
							if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) || ! is_plugin_active( $plugin_slug ) ) {
								$__dependency_fulfilled = false;
							}
						}
						$__dependency_passed = $__have_dependency ? ( $__is_pro && $__is_pro_active && $__dependency_fulfilled ) : true;

						$plan              = isset( $val['plan'] ) ? $val['plan'] : '';
						$have_correct_plan = false;
						$get_addon_text    = $plan ? 'Get ' . $plan : __( 'Get Pro', 'wholesalex' );
						$pro_url           = wholesalex()->get_premium_link();
						if ( $__is_pro_active ) {
							$dir_name = explode( '_', $key );
							$dir_name = end( $dir_name );
							if ( function_exists( 'wholesalex_pro' ) && '' !== $plan ) {
								if ( method_exists( wholesalex_pro(), 'get_eligible_addon_list' ) ) {
									$eligible_addons = wholesalex_pro()->get_eligible_addon_list();
									$__is_pro_active = in_array( $dir_name, $eligible_addons, true ) ? true : false;

									if ( ! $__is_pro_active ) {
										$get_pro_heading = __( 'Unlock All Addons', 'wholesalex' );
										$get_pro_desc    = __( 'We are sorry, but unfortunately, only three addons are available in the Starter pack of WholesaleX. Please get the Business Pack to unlock all addons and features.', 'wholesalex' );
									}
								}
							}
							if ( ! file_exists( WHOLESALEX_PRO_PATH . 'addons/' . $dir_name . '/init.php' ) ) {
								$__is_pro_active = false;
								$get_addon_text  = __( 'Update Pro To Get This', 'wholesalex' );
							}
						}
						$is_lock_addon = ( $__is_pro && ! $__is_pro_active ) || ( ! $__dependency_passed );
						?>
							<div class="wsx-content-addon">
								<div class="wsx-content-addon-container">
									<div class="wsx-content-meta">
										<img src="<?php echo esc_url( $val['img'] ); ?>" alt="<?php echo esc_attr( $val['name'] ); ?>">
									</div>
									<div class="wsx-addons-option-wrapper">
										<div class="wsx-addons-option-control">
											<span class="wsx-mid-title"><?php echo esc_html( $val['name'] ); ?></span>
											<div class="wsx-control-option">
												<?php if ( isset( $val['docs'] ) && $val['docs'] ) { ?>
													<a href="<?php echo esc_url( $val['docs'] ); ?>" class="wsx-option-tooltip" target="_blank">
														<span><?php esc_html_e( 'Documentation', 'wholesalex' ); ?></span>
														<div class="dashicons dashicons-book"></div>
													</a>
												<?php } ?>
												<?php if ( isset( $val['live'] ) && $val['live'] ) { ?>
													<a href="<?php echo esc_url( $val['live'] ); ?>" class="wsx-option-tooltip" target="_blank">
														<span><?php esc_html_e( 'Live View', 'wholesalex' ); ?></span>
														<div class="dashicons dashicons-visibility"></div>
													</a>
												<?php } ?>
												<?php if ( $is_lock_addon ) { ?>
														<input disabled  type="checkbox" data-type="<?php echo esc_attr( $key ); ?>" class="wsx-addons-enable" id="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( isset( $option_value[ $key ] ) && 'yes' === $option_value[ $key ] ? '' : '' ); ?>/>
												<?php } else { ?>
														<input  type="checkbox" data-type="<?php echo esc_attr( $key ); ?>" class="wsx-addons-enable" id="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( isset( $option_value[ $key ] ) && 'yes' === $option_value[ $key ] ? 'checked' : '' ); ?>/>
												<?php } ?>
												<?php if ( ! $is_lock_addon ) { ?>
													<label for="<?php echo esc_attr( $key ); ?>">
												</label>												<?php } else { ?>
													<label for="<?php echo esc_attr( $key ); ?>" onclick="openWholesaleXGetProPopUp()">
														<span class="wholesalex-lock-icon dashicons dashicons-lock"></span>
												</label>
												<?php } ?>
											</div>
										</div>
										<div class="wsx-addon-desc wsx-addon-desc"><?php echo esc_html( $val['desc'] ); ?></div>
									</div>
								</div>
							</div>
					<?php } ?>
				</div>
				<?php wholesalex()->get_upgrade_pro_popup_html( $get_pro_heading, '', $get_pro_desc, 'https://getwholesalex.com/pricing/?utm_source=wholesalex-menu&utm_medium=addons-get_pro&utm_campaign=wholesalex-DB' ); ?>
			</div>

		<?php

	}
}

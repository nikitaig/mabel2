<?php
/**
 * WholesaleX Initialization. Initialize All Files And Dependencies
 *
 * @link              https://www.wpxpo.com/
 * @since             1.0.0
 * @package           WholesaleX
 */

use WHOLESALEX\Scripts;

defined( 'ABSPATH' ) || exit;


/**
 * WholesaleX_Initialization Class
 */
class WholesaleX_Initialization {
	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->include_addons(); // Include Addons
		// Admin Assets.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Frontend Assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );

		add_action( 'init', array( $this, 'wholesalex_process_user_email_confirmation' ) );
		add_action( 'in_admin_header', array( $this, 'add_wholesalex_header' ) );
	}

	/**
	 * Set up a div for the wholesalex header to render on it.
	 */
	public function add_wholesalex_header() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$get_data = wholesalex()->sanitize( $_GET );
		if ( isset( $get_data['post'] ) ) {
			$__post_id   = $get_data['post'];
			$__post_type = get_post_type( $__post_id );
			if ( 'wsx_conversation' === $__post_type ) {
				?>
					<div id="wholesalex_coversation_header"></div>
				<?php

				/**
				 * Enqueue Script
				 *
				 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
				 */
				wp_enqueue_script( 'wholesalex_header' );

			}
		}
		if ( isset( $get_data['post_type'] ) && 'wsx_conversation' === $get_data['post_type'] ) {
			?>
				<div id="wholesalex_coversation_header"></div>
			<?php
			/**
			 * Enqueue Script
			 *
			 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
			 */
			wp_enqueue_script( 'wholesalex_header' );

			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}

		// Remove All Notices.
		if ( isset( $get_data['page'] ) ) { //phpcs:ignore
			$page = sanitize_key( $get_data['page'] );  //phpcs:ignore
			if ( 'wholesalex-settings' === $page ||
				'wholesalex-users' === $page ||
				'wholesalex-addons' === $page ||
				'wholesalex_role' === $page ||
				'wholesalex-email' === $page ||
				'wholesalex_dynamic_rules' === $page ||
				'wholesalex-registration' === $page ||
				'wsx_conversation' === $page ||
				'wholesalex-overview' === $page || 'wholesalex-setup-wizard' === $page ) {
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );
			}
		}
	}

	/**
	 * Include Addons Directory
	 *
	 * @since v.1.0.0
	 */
	public function include_addons() {
		$addons_dir = array_filter( glob( WHOLESALEX_PATH . 'addons/*' ), 'is_dir' );
		if ( count( $addons_dir ) > 0 ) {
			foreach ( $addons_dir as $key => $value ) {
				$addon_dir_name = str_replace( dirname( $value ) . '/', '', $value );
				$file_name      = WHOLESALEX_PATH . 'addons/' . $addon_dir_name . '/init.php';
				if ( file_exists( $file_name ) ) {
					include_once $file_name;
				}
			}
		}
	}

	/**
	 * Load All Required Dependencies
	 */
	private function load_dependencies() {
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-overview.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-role.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-menu.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-profile.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-request-api.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-product.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-category.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-shortcodes.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-email.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-email-manager.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-dynamic-rules.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-settings.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-scripts.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-registration.php';
		require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-orders.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-feature-controller.php';
		require_once WHOLESALEX_PATH . 'includes/class-wholesalex-import-export.php';

		new \WHOLESALEX\WHOLESALEX_Overview();
		new \WHOLESALEX\WHOLESALEX_Category();
		new \WHOLESALEX\WHOLESALEX_Menu();
		new \WHOLESALEX\WHOLESALEX_Role();
		new \WHOLESALEX\WHOLESALEX_Profile();
		new \WHOLESALEX\WHOLESALEX_Product();
		new \WHOLESALEX\WHOLESALEX_Request_API();
		new \WHOLESALEX\WHOLESALEX_Shortcodes();
		new \WHOLESALEX\WHOLESALEX_Email();
		new \WHOLESALEX\WHOLESALEX_Email_Manager();
		new \WHOLESALEX\WHOLESALEX_Dynamic_Rules();
		new \WHOLESALEX\Settings();
		new \WHOLESALEX\WHOLESALEX_Registration();
		new \WHOLESALEX\WHOLESALEX_Orders();
		new \WHOLESALEX\WholesaleX_Feature_Controller();
		new \WHOLESALEX\ImportExport();

		add_action( 'init', array( $this, 'wholesalex_process_user_email_confirmation' ) );

	}


	/**
	 * Admin Enque Scripts
	 *
	 * @param string $slug Page Slug.
	 */
	public function admin_enqueue_scripts( $slug ) {
		Scripts::register_backend_scripts();
		Scripts::register_backend_style();

		wp_enqueue_style( 'wholesalex' );
		wp_enqueue_script( 'wholesalex' );

		wp_localize_script(
			'wholesalex',
			'wholesalex',
			apply_filters(
				'wholesalex_backend_localize_data',
				array(
					'url'                 => WHOLESALEX_URL,
					'nonce'               => wp_create_nonce( 'wholesalex-registration' ),
					'ajax'                => admin_url( 'admin-ajax.php' ),
					'wholesalex_roles'    => get_option( '_wholesalex_roles' ),
					'currency_symbol'     => get_woocommerce_currency_symbol(),
					'current_version'     => WHOLESALEX_VER,
					'wallet_status'       => wholesalex()->get_setting( 'wsx_addon_wallet' ),
					'conversation_status' => wholesalex()->get_setting( 'wsx_addon_conversation' ),
					'recaptcha_status'    => wholesalex()->get_setting( 'wsx_addon_recaptcha' ),
					'pro_link'            => wholesalex()->get_premium_link(),
					'is_pro_active'       => wholesalex()->is_pro_active(),
					'licenceType'         => wholesalex()->is_pro_active() ? wholesalex()->get_eligible_license_plans() : array(),
					'ver'                 => WHOLESALEX_VER,
					'pro_ver'             => wholesalex()->is_pro_active() ? WHOLESALEX_PRO_VER : '',
                    'settings'            => wholesalex()->get_setting(),
				)
			)
		);
	}

	/**
	 * Frontend Enque Scripts
	 */
	public function frontend_enqueue_scripts() {
		Scripts::register_frontend_scripts();
		Scripts::register_fronend_style();
		wp_enqueue_style( 'wholesalex' );
		$css = '.wholesalex-price-table table tbody td { background-color: inherit !important; }';
		wp_add_inline_style( 'wholesalex', $css );
		wp_enqueue_script( 'wholesalex' );

		do_action( 'wholesalex_after_frontend_enqueue_scripts' );

		wp_localize_script(
			'wholesalex',
			'wholesalex',
			apply_filters(
				'wholesalex_frontend_localize_data',
				array(
					'url'              => WHOLESALEX_URL,
					'nonce'            => wp_create_nonce( 'wholesalex-registration' ),
					'ajax'             => admin_url( 'admin-ajax.php' ),
					'wallet_status'    => wholesalex()->get_setting( 'wsx_addon_wallet' ),
					'recaptcha_status' => wholesalex()->get_setting( 'wsx_addon_recaptcha' ),
					'ver'              => WHOLESALEX_VER,
					'pro_ver'          => wholesalex()->is_pro_active() ? WHOLESALEX_PRO_VER : '',
                    'settings'         => wholesalex()->get_setting(),
				)
			)
		);
	}

	/**
	 * Process WholesaleX User Email Confirmation
	 */
	public function wholesalex_process_user_email_confirmation() {

		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'wholesalex_user_email_verification' ) && isset( $_REQUEST['confirmation_code'] ) ) {
			$__confirmation_data = wholesalex()->sanitize( maybe_unserialize( base64_decode( $_REQUEST['confirmation_code'] ) ) );
			$__user_id           = $__confirmation_data['id'];
			$__registration_role = $__confirmation_data['registration_role'];
			$__confirmation_code = get_user_meta( $__user_id, '__wholesalex_email_confirmation_code', true );
			$confirmation_status = get_user_meta( $__user_id, '__wholesalex_account_confirmed', true );

			if ( $confirmation_status ) {
				wc_add_notice( __( ' Your account is already confirmed!. ', 'wholesalex' ), 'notice' );
			} elseif ( $__confirmation_data['code'] === $__confirmation_code ) {
				update_user_meta( $__user_id, '__wholesalex_account_confirmed', true );
				update_user_meta( $__user_id, '__wholesalex_status', 'active' );
				wholesalex()->change_role( $__user_id, $__registration_role );
				wc_add_notice( __( '<strong>Success:</strong> Your account is successfully confirmed. ', 'wholesalex' ) );
				do_action( 'wholesalex_set_status_active', $__user_id, $__registration_role );
			}
		}
	}

}

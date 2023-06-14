<?php
/**
 * WholesaleX Registration
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

use WP_Error;

/**
 * WholesaleX Registration class
 */
class WHOLESALEX_Registration {

	/**
	 * Registration Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'form_builder_add_submenu_page' ) );
		add_action( 'rest_api_init', array( $this, 'registration_form_builder_restapi_callback' ) );
		add_filter( 'wp_authenticate_user', array( $this, 'check_status' ), 10, 2 );
		add_filter( 'wholesalex_registration_form_user_login_option', array( $this, 'user_login_option' ) );
		add_filter( 'wholesalex_registration_form_user_status_option', array( $this, 'user_status_option' ) );

		add_filter( 'wholesalex_registration_form_after_registration_redirect_url', array( $this, 'after_registration_redirect' ) );
		add_filter( 'wholesalex_registration_form_after_registration_success_message', array( $this, 'after_registration_success_message' ) );
		add_action( 'wholesalex_registration_form_user_status_email_confirmation_require', array( $this, 'confirmation_email_after_registration' ), 10, 2 );
		add_action( 'wholesalex_registration_form_user_status_auto_approve', array( $this, 'auto_approve_after_registration' ), 10, 2 );
		add_action( 'wholesalex_registration_form_user_auto_login', array( $this, 'auto_login_after_registration' ) );
		add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ) );
		add_action('wholesalex_registration_form_user_status_admin_approve', array($this, 'user_registration_admin_approval_need'));
		add_action('wholesalex_before_process_user_registration', array($this, 'disable_woocommerce_new_user_email'));
	}

	/**
	 * Role Menu callback
	 *
	 * @return void
	 */
	public function form_builder_add_submenu_page() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'Registration Form', 'wholesalex' ),
			__( 'Registration Form', 'wholesalex' ),
			'manage_options',
			'wholesalex-registration',
			array( $this, 'output' )
		);

	}

	/**
	 * User Registration Form Output
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function output() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_form_builder' ); ?>
		<div id="root"></div>
		<?php
	}

	/**
	 * WholesaleX Registration Form Builder Restapi Callback
	 *
	 * @return void
	 */
	public function registration_form_builder_restapi_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/builder_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'builder_action_callback' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Registration Form Builder Action
	 *
	 * @param object $server Server.
	 * @return mixed
	 */
	public function builder_action_callback( $server ) {
		$post = $server->get_params();
		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( $post['nonce'], 'wholesalex-registration' ) ) ) {
			return;
		}

		$type = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';

		if ( 'post' === $type ) {

			if ( isset( $post['data'] ) ) {
				update_option( '__wholesalex_registration_form', sanitize_text_field( wp_unslash( $post['data'] ) ) );
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} elseif ( 'get' === $type ) {
			$__form_data     = get_option( '__wholesalex_registration_form' );
			$__roles_options = wholesalex()->get_roles( 'roles_option' );

			wp_send_json_success(
				array(
					'roles'     => $__roles_options,
					'form_data' => $__form_data,
				)
			);
		}
	}




	/**
	 * Email Confirmation After Registration
	 *
	 * @param int|string $user_id User ID.
	 * @param string     $registration_role Registration Role.
	 * @return void
	 */
	public function confirmation_email_after_registration( $user_id, $registration_role ) {
		$confirmation_code = $this->generate_confirmation_code( 6 );
		$confirmation_link = array(
			'id'                => $user_id,
			'code'              => $confirmation_code,
			'registration_role' => $registration_role,
		);
		$confirmation_link = base64_encode( maybe_serialize( $confirmation_link ) );
		update_user_meta( $user_id, '__wholesalex_email_confirmation_code', $confirmation_code );
		update_user_meta( $user_id, '__wholesalex_account_confirmed', false );
		do_action( 'wholesalex_user_email_confirmation', $user_id, $confirmation_link );
	}


	/**
	 * Auto Approve After Registration
	 *
	 * @param int|string $user_id User ID.
	 * @param string     $registration_role Registration Role.
	 * @return void
	 */
	public function auto_approve_after_registration( $user_id, $registration_role ) {
		wholesalex()->change_role( $user_id, $registration_role );
		update_user_meta( $user_id, '__wholesalex_status', 'active' );
		do_action( 'wholesalex_set_status_active', $user_id );
		do_action( 'wholesalex_user_auto_approval', $user_id );
	}
	/**
	 * Auto Login After Registration
	 *
	 * Auto Login Will Work only if user status is auto approved.
	 *
	 * @param int|string $user_id User ID.
	 */
	public function auto_login_after_registration( $user_id ) {
		$__user_status = wholesalex()->get_user_status( $user_id );
		if ( 'pending' === $__user_status ) {
			$user_login_option = wholesalex()->get_setting( '_settings_user_status_option' );
			switch ( $user_login_option ) {
				case 'admin_approve':
					/* translators: %s: Account Status */
					$message = sprintf( esc_html__( 'Your account Status is %s. Please Contact with Site Administration to approve your account.', 'wholesalex' ), $status );
					return new WP_Error( 'admin_approval_pending', $message );
				case 'email_confirmation_require':
					$message = esc_html__( 'Please confirm your account by clicking the confirmation link, that already sent your registered email.', 'wholesalex' );
					return new WP_Error( 'email_confirmation_require', $message );

				default:
					// code...
					break;
			}
		}
		wc_set_customer_auth_cookie( $user_id );
		do_action( 'wholesalex_user_auto_login', $user_id );
	}

	/**
	 * Generate Verification Code
	 *
	 * @param int $len length of verification code.
	 */
	public function generate_confirmation_code( $len ) {
		$characters        = '0123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_-[]{}!@$';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $len; $i++ ) {
			$random_character = $characters[ wp_rand( 0, $characters_length - 1 ) ];
			$random_string   .= $random_character;
		};
		$random_string = sanitize_user( $random_string );
		if ( ( preg_match( '([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z].*[_\W])', $random_string ) === 1 ) && ( strlen( $random_string ) === $len ) ) {
			return $random_string;
		} else {
			return call_user_func( array( $this, 'generate_confirmation_code' ), $len );
		}
	}

	/**
	 * Check User Approval Status
	 *
	 * @param WP_User $user user object.
	 */
	public function check_status( $user ) {
		if ( user_can( $user->ID, 'manage_options' ) ) {
			return $user;
		}

		$user_login_option = wholesalex()->get_setting( '_settings_user_status_option' );
		$status            = get_the_author_meta( '__wholesalex_status', $user->ID );
		if ( 'admin_approve' === $user_login_option && 'active' !== $status ) {
			/* translators: %s: Account Status */
			$message = sprintf( esc_html__( 'Your account Status is %s. Please Contact with Site Administration to approve your account.', 'wholesalex' ), $status );
			return new WP_Error( 'admin_approval_pending', $message );
		} elseif ( 'email_confirmation_require' === $user_login_option && 'active' !== $status ) {
			$message = esc_html__( 'Please confirm your account by clicking the confirmation link, that already sent your registered email.', 'wholesalex' );
			return new WP_Error( 'email_confirmation_require', $message );
		}
		return $user;
	}

	/**
	 * Returns the login redirect URL.
	 *
	 * @param string $redirect Default redirect URL.
	 * @return string Redirect URL.
	 */
	public function login_redirect( $redirect ) {
		$url = esc_url_raw( wholesalex()->get_setting( '_settings_redirect_url_login' ) );

		if ( empty( $url ) ) {
			return $redirect;
		}
		return $url;
	}


	/**
	 * User Login Option Settings
	 *
	 * @since 1.0.0 Reposition on v1.0..7
	 */
	public function user_login_option() {
		$__user_login_option = wholesalex()->get_setting( '_settings_user_login_option' );
		if ( ! empty( $__user_login_option ) ) {
			return $__user_login_option;
		}
	}

	/**
	 * User Status Options
	 *
	 * @since 1.0.0 Reposition on v1.0..7
	 */
	public function user_status_option() {
		$__user_status_option = wholesalex()->get_setting( '_settings_user_status_option' );
		if ( ! empty( $__user_status_option ) ) {
			return $__user_status_option;
		}
	}


	/**
	 * After Registration Form Redirect Settings.
	 *
	 * @param string $redirect_url Redirect Url.
	 * @since 1.0.0 Reposition on v1.0..7
	 */
	public function after_registration_redirect( $redirect_url ) {
		$__redirect_url = wholesalex()->get_setting( '_settings_redirect_url_registration' );
		if ( ! empty( $__redirect_url ) ) {
			$redirect_url = sanitize_url( $__redirect_url ); //phpcs:ignore
			return $redirect_url;
		}

		return $redirect_url;
	}

	/**
	 * After Registration Success Message
	 *
	 * @since 1.0.0 Reposition on v1.0..7
	 */
	public function after_registration_success_message() {
		$__registration_success_message = wholesalex()->get_setting( '_settings_registration_success_message' );
		if ( ! empty( $__registration_success_message ) ) {
			$__registration_success_message = esc_html( $__registration_success_message ); //phpcs:ignore
			return $__registration_success_message;
		}
	}


	/**
	 * Process User Registration Admin Approval Need Settings
	 *
	 * @param string $user_id User ID.
	 * @return void
	 * @since 1.1.6
	 */
	public function user_registration_admin_approval_need($user_id) {
		add_user_meta( $user_id, '__wholesalex_status', 'pending' );
		//do_action( 'wholesalex_set_status_pending', $user_id );
		do_action('wholesalex_set_user_approval_needed',$user_id);
		
	}

	/**
	 * Disable WooCommerce new user email for the users who registered throw the wholesalex registration form.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public function disable_woocommerce_new_user_email() {
		$is_disable = wholesalex()->get_setting('_settings_disable_woocommerce_new_user_email');
		if('yes' === $is_disable) {
			add_filter( 'woocommerce_email_enabled_customer_new_account', '__return_false' );
		}
	}

}

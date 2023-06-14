<?php
/**
 * WholesaleX Email Manager
 *
 * @package WHOLELSALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * WholesaleX Email Manager
 *
 * @since 1.0.0
 */
class WHOLESALEX_Email_Manager {

	/**
	 * It contain Email Templates.
	 *
	 * @var array
	 */
	public $email_templates = '';


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->email_templates = get_option( '__wholesalex_email_templates', array() );

		add_action( 'wholesalex_set_status_active', array( $this, 'wholesalex_user_status_active_email_trigger' ) );
		add_action( 'wholesalex_user_email_confirmation', array( $this, 'wholesalex_verification_email_trigger' ), 10, 2 );

		/**
		 * 
		 * @since 1.1.6
		 */
		if(wholesalex()->is_pro_active()) {
			add_action('wholesalex_set_user_approval_needed', array($this, 'registration_pending_for_approval_email'));
		}
	}

	/**
	 * Return Super Admin Display Name
	 */
	public static function get_admin_display() {
		$super_admin = '';
		$super       = get_super_admins();
		if ( isset( $super[0] ) ) {
			$super       = get_user_by( 'login', $super[0] );
			$super_admin = isset( $super->data->display_name ) ? $super->data->display_name : '';
		}
		return $super_admin;
	}

	/**
	 * User Status : Active -> Trigger Email
	 *
	 * @param int $user_id User ID.
	 */
	public function wholesalex_user_status_active_email_trigger( $user_id ) {
		$email_template = isset( $this->email_templates['wholesalex_registration_approve'] ) ? $this->email_templates['wholesalex_registration_approve'] : '';
		if ( $user_id && ! empty( $email_template ) ) {
			$user_data = get_userdata( $user_id );
			$to        = $user_data->user_email;
			if ( ! isset( $to ) || empty( $to ) ) {
				return;
			}
			$smart_tags = array(
				'{date}'             => gmdate( 'd-m-Y' ),
				'{username}'         => $user_data->user_login,
				'{email}'            => $user_data->user_email,
				'{display_name}'     => $user_data->display_name,
				'{first_name}'       => $user_data->user_firstname,
				'{last_name}'        => $user_data->user_lastname,
				'{full_name}'        => $user_data->user_firstname . ' ' . $user_data->user_lastname,
				'{super_admin_name}' => self::get_admin_display(),
				'{admin_name}' 		 => self::get_admin_display(),
				'{site_name}'        => get_option( 'blogname' ),
			);
			if ( get_option( 'wholesalex_registration_approve_email_status' ) && ! empty( $to ) ) {
				$subject = isset( $email_template['subject'] ) ? $email_template['subject'] : '';
				$content = isset( $email_template['content'] ) ? $email_template['content'] : '';
				foreach ( $smart_tags as $key => $value ) {
					$subject = str_replace( $key, $value, $subject );
					$content = str_replace( $key, $value, $content );
				}
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				wp_mail( $to, $subject, $content, $headers );
				if ( isset( $email_template['admin_notification'] ) && $email_template['admin_notification'] ) {
					wp_mail( get_option( 'admin_email' ), $subject, $content, $headers );
				}
			}
		}

	}

	/**
	 * Email Verification Trigger Email
	 *
	 * @param int    $user_id User ID.
	 * @param string $confirmation_link Confirmation Link.
	 */
	public function wholesalex_verification_email_trigger( $user_id, $confirmation_link ) {
		$email_template = isset( $this->email_templates['wholesalex_email_verification'] ) ? $this->email_templates['wholesalex_email_verification'] : '';
		if ( $user_id && ! empty( $email_template ) ) {
			$user_data = get_userdata( $user_id );
			$to        = $user_data->user_email;
			if ( ! isset( $to ) || empty( $to ) ) {
				return;
			}
			$nonce      = wp_create_nonce( 'wholesalex_user_email_verification' );
			$smart_tags = array(
				'{date}'                    => gmdate( 'd-m-Y' ),
				'{username}'                => $user_data->user_login,
				'{email}'                   => $user_data->user_email,
				'{display_name}'            => $user_data->display_name,
				'{first_name}'              => $user_data->user_firstname,
				'{last_name}'               => $user_data->user_lastname,
				'{full_name}'               => $user_data->user_firstname . ' ' . $user_data->user_lastname,
				'{super_admin_name}'        => self::get_admin_display(),
				'{site_name}'               => get_option( 'blogname' ),
				'{admin_name}' 		 => self::get_admin_display(),
				'{email_confirmation_link}' => get_site_url() . '/my-account/?confirmation_code=' . $confirmation_link . '&_wpnonce=' . $nonce,
			);
			if ( get_option( 'wholesalex_email_verification_email_status' ) && ! empty( $to ) ) {
				$subject = isset( $email_template['subject'] ) ? $email_template['subject'] : '';
				$content = isset( $email_template['content'] ) ? $email_template['content'] : '';
				foreach ( $smart_tags as $key => $value ) {
					$subject = str_replace( $key, $value, $subject );
					$content = str_replace( $key, $value, $content );
				}
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				wp_mail( $to, $subject, $content, $headers );
				if ( isset( $email_template['admin_notification'] ) && $email_template['admin_notification'] ) {
					wp_mail( get_option( 'admin_email' ), $subject, $content, $headers );
				}
			}
		}

	}


	/**
	 * Approval Needed, pending Users Email
	 *
	 * @param int|string $user_id User ID.
	 * @return void
	 * @since 1.1.6
	 */
	public function registration_pending_for_approval_email($user_id) {
		$email_template = $this->email_templates['wholesalex_registration_pending'];
		if ( $user_id ) {
			$user_data = get_userdata( $user_id );
			$to        = $user_data->user_email;
			if ( ! isset( $to ) || empty( $to ) ) {
				return;
			}
			$smart_tags = array(
				'{date}'         => gmdate( 'd-m-Y' ),
				'{username}'     => $user_data->user_login,
				'{email}'        => $user_data->user_email,
				'{display_name}' => $user_data->display_name,
				'{first_name}'   => $user_data->user_firstname,
				'{last_name}'    => $user_data->user_lastname,
				'{full_name}'    => $user_data->user_firstname . ' ' . $user_data->user_lastname,
				'{site_name}'    => get_option( 'blogname' ),
				'{admin_name}'   => self::get_admin_display()
			);
			if ( get_option( 'wholesalex_registration_pending_email_status' ) && ! empty( $to ) ) {
				$subject = isset( $email_template['subject'] ) ? $email_template['subject'] : '';
				$content = isset( $email_template['content'] ) ? $email_template['content'] : '';
				foreach ( $smart_tags as $key => $value ) {
					$subject = str_replace( $key, $value, $subject );
					$content = str_replace( $key, $value, $content );
				}
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				wp_mail( $to, $subject, $content, $headers );
			}
		}
	}

}

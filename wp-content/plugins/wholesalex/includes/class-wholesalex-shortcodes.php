<?php
/**
 * Shortcodes
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

use Closure;
use WP_Error;

/*
 * WholesaleX Shortcodes Class
 *
 * @since 1.0.0
 */
class WHOLESALEX_Shortcodes {
	/**
	 * Shortcodes Constructor
	 */
	public function __construct() {
		add_shortcode( 'wholesalex_registration', array( $this, 'registration_shortcode' ), 10 );
		add_action( 'wp_ajax_nopriv_wholesalex_user_registration', array( $this, 'user_registration' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'add_custom_fields_on_checkout_page' ) );
		add_action( 'woocommerce_checkout_process', array( $this, 'validate_custom_checkout_fields' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_custom_fields_on_order_meta' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'show_custom_fields_value' ) );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'show_custom_fields_on_order_page' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'show_custom_fields_on_order_page' ) );
		/**
		 * Shortcode For WholesaleX Login and Registration Form (Combined)
		 *
		 * @since 1.0.1
		 */
		add_shortcode( 'wholesalex_login_registration', array( $this, 'login_registration_shortcode' ), 10 );
		/**
		 * Filters the list of CSS class names for the current post.
		 *
		 * @param string[] $classes An array of post class names.
		 * @param string[] $class   An array of additional class names added to the post.
		 * @param int      $post_id The post ID.
		 * @return string[] An array of post class names.
		 */
		add_filter(
			'post_class',
			function( array $classes, array $class, int $post_id ) : array {
				array_push( $classes, '_wholesalex' );
				return $classes;
			},
			10,
			3
		);
	}

	/**
	 * Login And Registration Form
	 *
	 * @param  array $atts Shortcode Attributes. Default Empty.
	 * @return string Shortcode output.
	 * @since 1.0.1
	 */
	public function login_registration_shortcode( $atts = array() ) {
		if ( is_admin() ) {
			return;
		}

		if ( ! wp_script_is( 'wholesalex_form', 'registered' ) ) {
			Scripts::register_frontend_scripts();
		}

		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_form' );

		if ( is_user_logged_in() && is_singular() ) {
			$__form_view_for_logged_in_user = wholesalex()->get_setting( '_settings_show_form_for_logged_in' );
			$__message_for_logged_in_user   = wholesalex()->get_setting( '_settings_message_for_logged_in_user' );
			if ( 'yes' !== $__form_view_for_logged_in_user ) {
				if ( is_admin() || ! function_exists( 'wc_add_notice' ) || ! function_exists( 'wc_print_notices' ) ) {
					return;
				}
				?> <div> 
				<?php
				wc_add_notice( $__message_for_logged_in_user, 'error' );
				wc_print_notices();
				?>
				<a href="<?php echo esc_url_raw( wp_logout_url( get_permalink() ) ); ?>"><?php echo esc_html( wholesalex()->get_language_n_text( '_language_logout_to_see_this_form', __( 'Logout to See this form', 'wholesalex' ) ) ); ?></a>
				</div>
				<?php
				return;
			}
		}

		if ( isset( $atts['registration_role'] ) ) {
			wp_localize_script(
				'wholesalex_form',
				'builder',
				array(
					'form_data'         => wholesalex()->get_form_data( $atts['registration_role'] ),
					'registration_role' => $atts['registration_role'],
				)
			);

		} else {
			wp_localize_script(
				'wholesalex_form',
				'builder',
				array(
					'form_data'         => wholesalex()->get_form_data(),
					'registration_role' => '',
				)
			);
		}
		ob_start();
		?>
		<div id="_wholesalex_form">
			<div class="wholesalex_notice_wrapper">
				<div class="wholesalex_notices">
				<?php
				if ( function_exists( 'wc_print_notices' ) && ! is_admin() ) {
					wc_print_notices();
				}
				?>
				</div>
			</div>
			<div  class="wholesalex_login_registration">
				<div id="wholesalex_login">
					<h2><?php echo esc_html( wholesalex()->get_language_n_text( '_language_registraion_from_combine_login_text', __( 'Login', 'wholesalex' ) ) ); ?></h2>
					<?php woocommerce_login_form( array( 'redirect' => wholesalex()->get_setting( '_settings_redirect_url_login' ) ) ); ?>
				</div>
				<div id="wholesalex_registration">
					<h2><?php echo esc_html( wholesalex()->get_language_n_text( '_language_registraion_from_combine_registration_text', __( 'Registration', 'wholesalex' ) ) ); ?></h2>
						<div id="_wholesalex_registration_form" class="">
						Loading..
						</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();

	}

	/**
	 * Registration Form
	 *
	 * @param array $atts    Shortcode attributes. Default empty.
	 * @return string Shortcode output.
	 * @since 1.0.0
	 */
	public function registration_shortcode( $atts = array() ) {
		do_action( 'wholesalex_before_registration_form_render' );

		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		if ( ! wp_script_is( 'wholesalex_form', 'registered' ) ) {
			Scripts::register_frontend_scripts();
		}
		wp_enqueue_script( 'wholesalex_form' );

		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		if ( is_user_logged_in() && is_singular() ) {
			$__form_view_for_logged_in_user = wholesalex()->get_setting( '_settings_show_form_for_logged_in' );
			$__message_for_logged_in_user   = wholesalex()->get_setting( '_settings_message_for_logged_in_user' );
			if ( 'yes' !== $__form_view_for_logged_in_user ) {
				if ( is_admin() || ! function_exists( 'wc_add_notice' ) || ! function_exists( 'wc_print_notices' ) ) {
					return;
				}
				?>
				<div>
				<?php
				wc_add_notice( $__message_for_logged_in_user, 'error' );
				wc_print_notices();
				?>
					<a href="<?php echo esc_url_raw( wp_logout_url( get_permalink() ) ); ?>"> <?php echo esc_html( wholesalex()->get_language_n_text( '_language_logout_to_see_this_form', __( 'Logout to See this form', 'wholesalex' ) ) ); ?></a>
					</div>
				<?php
				return;
			}
		}
		if ( isset( $atts['registration_role'] ) ) {
			wp_localize_script(
				'wholesalex_form',
				'builder',
				array(
					'form_data'         => wholesalex()->get_form_data( $atts['registration_role'] ),
					'registration_role' => $atts['registration_role'],
				)
			);

		} else {
			wp_localize_script(
				'wholesalex_form',
				'builder',
				array(
					'form_data'         => wholesalex()->get_form_data(),
					'registration_role' => '',
				)
			);
		}

		return '<div id="_wholesalex_form" class="woocommerce"><div class="wholesalex_notice_wrapper">
		<div class="wholesalex_notices"></div>
	</div></div><div id="_wholesalex_registration_form">Loading...</div>';
	}



	/**
	 * WholesaleX Registration Form : Registration Handler
	 *
	 * @since 1.0.0
	 * @access public
	 * @return String Success Message.
	 */
	public function user_registration() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wholesalex-registration' ) ) ) {
			return;
		}

		$__have_error = false;

		/**
		 * Set file fields allowed types and maximum file size.
		 */
		$__form_data       = wholesalex()->get_form_data();
		$__file_field_name = array();
		foreach ( $__form_data as $key => $field ) {
			if ( 'file' === $field['type'] ) {
				$__file_field_name[ 'file_' . $field['name'] ] = $field['title'];
			}
			if ( 'file' === $field['type'] && isset( $field['allowed_file_types'] ) && ! empty( $field['allowed_file_types'] ) ) {
				$__allowed_types = explode( ',', $field['allowed_file_types'] );
				$__allowed_types = wholesalex()->sanitize( $__allowed_types );
				add_filter(
					'wholesalex_allowed_file_types_file_' . $field['name'],
					function ( $types ) use ( $__allowed_types ) {
						return $__allowed_types;
					}
				);
			}
			if ( 'file' === $field['type'] && isset( $field['maximum_file_size'] ) && ! empty( $field['maximum_file_size'] ) ) {
				$maximum_file_size = sanitize_text_field( $field['maximum_file_size'] );
				add_filter(
					'wholesalex_allowed_file_size_file_' . $field['name'],
					function () use ( $maximum_file_size ) {
						return $maximum_file_size;
					}
				);
			}
		}

		/**
		 * Check Files Before Upload
		 */

		foreach ( $_FILES as $key => $file ) {
			// Allowed file types.
			$allowed_file_types = apply_filters( 'wholesalex_allowed_file_types_' . $key, array( 'jpg', 'jpeg', 'png', 'txt', 'pdf', 'doc', 'docx' ) );

			// Allowed file size -> 5MB.
			$allowed_file_size       = apply_filters( 'wholesalex_allowed_file_size_' . $key, 5000000 );
			$allowed_file_size_in_mb = $allowed_file_size / 1000000;
			$file_extension          = pathinfo( $file['name'], PATHINFO_EXTENSION );

			if ( ! in_array( $file_extension, $allowed_file_types ) ) {
				// translators: %s Field Name.
				// translators: %s Allowed File Types.
				wc_add_notice( sprintf( __( 'File Type Does not support for %1$s Field! Supported File Types is %2$s.', 'wholesalex' ), $__file_field_name[ $key ], implode( ',', $allowed_file_types ) ), 'error' );
				$__have_error = true;
			}
			if ( $file['size'] > $allowed_file_size ) {
				wc_add_notice( sprintf( __( 'File is too large! Max Upload Size For %1$s field is %2$s.', 'wholesalex' ), $__file_field_name[ $key ], $allowed_file_size_in_mb . 'MB' ), 'error' );
				$__have_error = true;
			}
		}

		if ( isset( $_POST['messages'] ) ) {
			$__messages = wholesalex()->sanitize( json_decode( wp_unslash( $_POST['messages'] ), true ) );
			foreach ( $__messages as $message ) {
				wc_add_notice( $message, 'error' );
				$__have_error = true;
			}
		}

		// $form_data = $this->sanitize_form_data( json_decode( wp_unslash( $_POST['data'] ), true ) );
		/**
		 * Used FormData Object Instead of Stringfy formData.
		 *
		 * @since 1.0.3
		 */
		$form_data = $this->sanitize_form_data( $_POST );
		unset( $form_data['action'] );
		unset( $form_data['nonce'] );
		unset( $form_data['messages'] );

		$userdata = array();
		$usermeta = array();

		// reCaptcha verify.

		do_action( 'wholesalex_before_process_user_registration', $this->sanitize_form_data( $_POST ) );

		if ( isset( $form_data['user_email'] ) && isset( $form_data['user_confirm_email'] ) && $form_data['user_email'] !== $form_data['user_confirm_email'] ) {
			wc_add_notice( __( 'Email and Confirm email must be same.', 'wholesalex' ), 'error' );
			$__have_error = true;
		}

		if ( isset( $form_data['user_pass'] ) && isset( $form_data['user_confirm_password'] ) && $form_data['user_pass'] !== $form_data['user_confirm_password'] ) {
			wc_add_notice( __( 'Password and Confirm password must be same.', 'wholesalex' ), 'error' );
			$__have_error = true;
		}

		if ( $__have_error ) {
			wp_send_json_error( array( 'messages' => wc_print_notices( true ) ) );
		}

		$user_name = '';
		$email     = '';
		$password  = '';
		foreach ( $form_data as $key => $value ) {
			if ( 'user_login' === $key ) {
				$user_name = $value;
				continue;
			}
			if ( 'user_pass' === $key || 'display_name' === $key || 'nickname' === $key || 'first_name' === $key || 'last_name' === $key ) {
				if ( 'user_pass' === $key ) {
					$password = $value;
					continue;
				}
				$userdata[ $key ] = $value;
				continue;
			}
			if ( 'description' === $key ) {
				$userdata[ $key ] = $value;
				continue;
			}
			if ( 'url' === $key ) {
			$userdata['user_url'] =  $value ;// phpcs:ignore
				continue;
			}
			if ( 'user_email' === $key ) {
				$email = $value;
				continue;
			}
			$exploded_key = explode( '_', $key );
			array_shift( $exploded_key );
			$field_name = implode( '_', $exploded_key );
			if ( preg_match( '#^textarea(.*)$#i', $key ) ) {
				$usermeta[ $field_name ] = $value;
			} elseif ( preg_match( '#^text(.*)$#i', $key ) ) {
				$usermeta[ $field_name ] = $value;
			} elseif ( preg_match( '#^email(.*)$#i', $key ) ) {
				$usermeta[ $field_name ] = $value;
			} else {
				if ( preg_match( '#^select(.*)$#i', $key ) || preg_match( '#^checkbox(.*)$#i', $key ) || preg_match( '#^number(.*)$#i', $key ) || preg_match( '#^radio(.*)$#i', $key ) || preg_match( '#^date(.*)$#i', $key ) ) {
					$usermeta[ $field_name ] = $value;
				}
			}
		}
		/**
		 * Remove All Third Party Plugin Registration Errors Filter.
		 *
		 * @since 1.0.2
		 */
		remove_all_filters( 'woocommerce_registration_errors' );

		$registered_user_id = wc_create_new_customer( $email, $user_name, $password, $userdata );
		if ( is_wp_error( $registered_user_id ) ) {
			$__error_messages = $registered_user_id->get_error_messages();
			foreach ( $__error_messages as $error_message ) {
				wc_add_notice( $error_message, 'error' );
			}
			wp_send_json_error( array( 'messages' => wc_print_notices( true ) ) );
		} else {
			foreach ( $usermeta as $key => $value ) {
				add_user_meta( $registered_user_id, $key, $value );
			}
			$__registration_role = isset( $form_data['registration_role'] ) ? $form_data['registration_role'] : '';
			add_user_meta( $registered_user_id, '__wholesalex_registration_role', $__registration_role );

			/**
			 * Process File Upload
			 */
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			foreach ( $_FILES as $key => $file ) {
				// Upload File.
				$file_id = media_handle_upload( $key, 0 );

				// Set file registered user as file author.
				wp_update_post(
					array(
						'ID'          => $file_id,
						'post_author' => $registered_user_id,
					)
				);

				// Update file id in user meta.
				update_user_meta( $registered_user_id, $key, $file_id );
			}

			$__user_status_option = apply_filters( 'wholesalex_registration_form_user_status_option', 'admin_approve' );
			do_action( 'wholesalex_registration_form_user_status_' . $__user_status_option, $registered_user_id, $__registration_role );

			$__user_login_option = apply_filters( 'wholesalex_registration_form_user_login_option', 'manual_login' );
			do_action( 'wholesalex_registration_form_user_' . $__user_login_option, $registered_user_id, $__registration_role );

			$__redirect_url    = apply_filters( 'wholesalex_registration_form_after_registration_redirect_url', get_home_url(), $registered_user_id, $__registration_role );
			$__success_message = apply_filters( 'wholesalex_registration_form_after_registration_success_message', __( 'Success', 'wholesalex' ), $registered_user_id, $__registration_role );

			wc_add_notice( $__success_message, 'success' );

			wp_send_json_success(
				array(
					'user_id'      => $registered_user_id,
					'redirect_url' => $__redirect_url,
					'messages'     => wc_print_notices( true ),
				)
			);

		}
	}


	/**
	 * Add Custom Fields on Checkout Page
	 *
	 * @param object $checkout Checkout Object.
	 * @return void
	 */
	public function add_custom_fields_on_checkout_page( $checkout ) {
		$__role         = wholesalex()->get_current_user_role();
		$__extra_fields = wholesalex()->get_form_data( $__role, 'billing' );

		$__fields = $checkout->get_checkout_fields( 'billing' );
		$__keys   = array_keys( $__fields );

		$__current_user = wp_get_current_user();

		$__custom_fields = array();

		foreach ( $__extra_fields as $value ) {

			if ( isset( $value['name'] ) && ! in_array( 'billing_' . $value['name'], $__keys, true ) ) {
				$__default = '';
				if ( isset( $value['enableForBillingForm'] ) && $value['enableForBillingForm'] ) {
					// $__default = isset( $__current_user->$value['name'] ) ? $__current_user->$value['name'] : '';
				}

				$__options = array();

				if ( 'select' === $value['type'] || 'checkbox' === $value['type'] || 'radio' === $value['type'] ) {
					foreach ( $value['option'] as $option ) {
						$__options[ $option['value'] ] = $option['name'];
					}

					woocommerce_form_field(
						$value['name'],
						array(
							'type'        => $value['type'],
							'class'       => array( 'form-row-wide' ),
							'label'       => isset( $value['title'] ) ? $value['title'] : '',
							'placeholder' => isset( $value['placeholder'] ) ? $value['placeholder'] : '',
							'required'    => isset( $value['required'] ) ? $value['required'] : '',
							'options'     => $__options,
							'default'     => $__default,
						),
						$checkout->get_value( $value['name'] )
					);
				} else {
					if ( 'file' != $value['type'] ) {
						woocommerce_form_field(
							$value['name'],
							array(
								'type'        => $value['type'],
								'class'       => array( 'form-row-wide' ),
								'label'       => isset( $value['title'] ) ? $value['title'] : '',
								'placeholder' => isset( $value['placeholder'] ) ? $value['placeholder'] : '',
								'required'    => isset( $value['required'] ) ? $value['required'] : '',
								'default'     => $__default,
							),
							$checkout->get_value( $value['name'] )
						);
					}
				}

				$__custom_fields[ $value['name'] ] = $value;

			}
		}

		?>
		<?php

		set_transient( 'wholesalex_custom_chekcout_fields_' . get_current_user_id(), $__custom_fields );
	}





	/**
	 * Validate Custom Fields on Checkout Page
	 */
	public function validate_custom_checkout_fields() {

		$post_data = wholesalex()->sanitize( $_POST );
		$__user_id = get_current_user_id();

		$__custom_fields = get_transient( 'wholesalex_custom_chekcout_fields_' . $__user_id );
		if ( $__custom_fields && ! empty( $__custom_fields ) ) {
			foreach ( $__custom_fields as $field ) {
				if ( 'file' === $field['type'] ) {
					continue;
				} else {
					if ( isset( $field['required'] ) && ! empty( $field['required'] ) && ! ( isset( $post_data[ $field['name'] ] ) && ! empty( $post_data[ $field['name'] ] ) ) ) {
						/* translators: %s: Field Title. */
						wc_add_notice( sprintf( '%s is Missing!', sanitize_text_field( $field['title'] ) ), 'error' );
					}
				}
			}
		}

	}


	/**
	 * Sanitize Field Data
	 *
	 * @param array      $field Field Data.
	 * @param string|int $value Field Value.
	 * @return string|int Sanitized Value.
	 */
	public function sanitize_field_data( $field, $value ) {
		switch ( $field['type'] ) {
			case 'textarea':
				return sanitize_textarea_field( $value );
			case 'url':
				return sanitize_url( $value ); // phpcs:ignore
			case 'email':
				return sanitize_email( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Save Custom Fields Data
	 *
	 * @param string|int $order_id Order ID.
	 * @return void
	 */
	public function add_custom_fields_on_order_meta( $order_id ) {

		$__user_id       = get_current_user_id();
		$__custom_fields = get_transient( 'wholesalex_custom_chekcout_fields_' . $__user_id );

		if ( $__custom_fields && ! empty( $__custom_fields ) ) {
			foreach ( $__custom_fields as $field ) {
				if ( 'file' === $field['type'] ) {
					continue;
				} else {
					if ( isset($_POST[$field['name']]) && !empty($_POST[$field['name']]) ) { // phpcs:ignore
						update_post_meta( $order_id, '__wholesalex_' . $field['name'], $this->sanitize_field_data( $field, $_POST[ $field['name'] ] ) );
					}
				}
			}
		}

	}

	/**
	 * Show Custom Fields Value
	 *
	 * @param string|int $order_id Order ID.
	 * @return void
	 */
	public function show_custom_fields_value( $order_id ) {

		$__user_id       = get_current_user_id();
		$__custom_fields = get_transient( 'wholesalex_custom_chekcout_fields_' . $__user_id );

		echo '<div class="wholesalex_custom_fields">';
		foreach ( $__custom_fields as $field ) {
			$__value = get_post_meta( $order_id, '__wholesalex_' . $field['name'], true );
			if ( $__value && ! empty( $__value ) ) {
				echo sprintf( '<label>%s</label> : <strong>%s</strong><br/>', esc_html( $field['title'] ), esc_html( $__value ) );
			}
		}
		echo '</div>';
	}


	/**
	 * Show Custom Fields On Order Details Page.
	 *
	 * @param object $order Order Object.
	 * @return void
	 */
	public function show_custom_fields_on_order_page( $order ) {
		$order_id = $order->get_id();

		$__user_id       = get_current_user_id();
		$__custom_fields = get_transient( 'wholesalex_custom_chekcout_fields_' . $__user_id );

		if ( $__custom_fields && ! empty( $__custom_fields ) ) {
			echo '<div class="wholesalex_custom_fields">';
			foreach ( $__custom_fields as $field ) {
				$__value = get_post_meta( $order_id, '__wholesalex_' . $field['name'], true );
				if ( $__value && ! empty( $__value ) ) {
					echo sprintf( '<p class="form-field form-field-wide"> <strong>%s:</strong><div>%s</div></p>', esc_html( $field['title'] ), esc_html( $__value ) );
				}
			}
			echo '</div>';
		}

	}

	/**
	 * Sanitize Form Data
	 *
	 * @param array $form_data Form Data.
	 */
	public function sanitize_form_data( $form_data ) {
		$data = array();
		foreach ( $form_data as $key => $value ) {
			$key = sanitize_key( $key );
			switch ( $key ) {
				case 'description':
					$data[ $key ] = sanitize_textarea_field( $value );
					break;
				case ( preg_match( '#^textarea(.*)$#i', $key ) ? true : false ):
					$data[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'user_pass':
				case 'display_name':
				case 'nickname':
				case 'first_name':
				case 'last_name':
				case ( preg_match( '#^text(.*)$#i', $key ) ? true : false ):
						$data[ $key ] = sanitize_text_field( $value );
					break;
				case 'url':
					$data[ $key ] = sanitize_url( $value );
					break;
				case ( preg_match( '#^email(.*)$#i', $key ) ? true : false ):
				case 'user_email':
					$data[ $key ] = sanitize_email( $value );
					break;
				case ( preg_match( '#^select(.*)$#i', $key ) ? true : false ):
				case ( preg_match( '#^checkbox(.*)$#i', $key ) ? true : false ):
				case ( preg_match( '#^number(.*)$#i', $key ) ? true : false ):
				case ( preg_match( '#^radio(.*)$#i', $key ) ? true : false ):
				case ( preg_match( '#^date(.*)$#i', $key ) ? true : false ):
					if ( is_array( $value ) ) {
						$data[ $key ] = wholesalex()->sanitize( $value );
					} else {
						$data[ $key ] = sanitize_text_field( $value );
					}
					break;
				case 'user_login':
					$data[ $key ] = sanitize_user( $value );
					break;

				default:
					if ( is_array( $value ) ) {
						$data[ $key ] = wholesalex()->sanitize( $value );
					} else {
						$data[ $key ] = sanitize_text_field( $value );
					}
					break;
			}
		}

		return $data;
	}

}

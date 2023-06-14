<?php
/**
 * Initial Setup.
 *
 * @package WOPB\Notice
 * @since v.2.4.4
 */
namespace WOPB;

use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;

defined( 'ABSPATH' ) || exit;

class InitialSetup {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page_callback' ) );
		add_action(
			'admin_menu',
			function() {
				remove_menu_page( 'wopb-initial-setup-wizard' );
			},
			99
		);

		add_action( 'wp_ajax_wopb_send_initial_plugin_data', array( $this, 'send_initial_plugin_data' ) );

		add_action( 'wp_ajax_wopb_initial_setup_complete', array( $this, 'initial_setup_complete' ) ); // Initial Setup complete

		/**
		 * Initial Setup Genenal Settings Save Ajax Request Handler
		 *
		 * @since 2.6.1
		 */
		add_action( 'wp_ajax_wopb_initial_setup_save_general_setting', array( $this, 'save_general_setting' ) );
	}

	/**
	 * Initial Setup Menu Page Added
	 *
	 * @since v.2.4.4
	 * @return NULL
	 */
	public static function menu_page_callback() {
		add_menu_page(
			esc_html__( 'Initial Setup', 'product-blocks' ),
			esc_html__( 'Initial Setup', 'product-blocks' ),
			'manage_options',
			'wopb-initial-setup-wizard',
			array( self::class, 'initial_setup' ),
			'',
			null
		);
	}

	/**
	 * Initial Plugin Setting
	 *
	 * * @since v.2.4.4
	 *
	 * @return STRING
	 */
	public static function initial_setup() {
		$html  = '';
		$html .= '<div class="wopb-initial-setting-wrap" id="wopb-initial-setting">';
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Send Plugin Data When Initial Setup
	 *
	 * * @since v.2.4.4
	 *
	 * @return STRING
	 */
	public function send_initial_plugin_data( $type ) {
		require_once WOPB_PATH . 'classes/Deactive.php';
		$obj = new \WOPB\Deactive();
		$obj->send_plugin_data( 'productx_wizard' );
	}

	/**
	 * Initial Plugin Setup Complete
	 *
	 * * @since v.2.4.4
	 *
	 * @return STRING
	 */
	public static function initial_setup_complete() {
		update_option( '_wopb_initial_setup', true );
		return wp_send_json_success(
			array(
				'redirect' => admin_url( 'admin.php?page=wopb-settings' ),
			)
		);
	}

	/**
	 * Plugin Install
	 *
	 * @param string $plugin Plugin Slug.
	 * @return boolean
	 * @since 2.6.1
	 */
	public function plugin_install( $plugin ) {

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			return $api->get_error_message();
		}

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		return $result;
	}

	/**
	 * Save General Settings Data.
	 *
	 * @return void
	 * @since 2.6.1
	 */
	public function save_general_setting() {
		if ( isset( $_POST['wpnonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpnonce'] ) ), 'wopb-nonce' ) ) {
			return;
		}

		// Set Site Icon.
		if ( isset( $_FILES['site_icon'] ) && isset( $_FILES['site_icon']['name'] ) ) {

			$file_extension     = strtolower( pathinfo( sanitize_text_field( wp_unslash( $_FILES['site_icon']['name'] ) ), PATHINFO_EXTENSION ) );
			$allowed_extenstion = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico' );
			if ( in_array( $file_extension, $allowed_extenstion ) ) { //phpcs:ignore
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				$file_id = media_handle_upload( 'site_icon', 0 );
				if ( $file_id ) {
					update_option( 'site_icon', $file_id );
				}
			}
		}

		// set website name.
		if ( isset( $_POST['website_name'] ) ) {
			$site_name = sanitize_text_field( wp_unslash( $_POST['website_name'] ) );
			update_option( 'blogname', $site_name );

		}

		$woocommerce_installed = file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );
		$wholesalex_installed  = file_exists( WP_PLUGIN_DIR . '/wholesalex/wholesalex.php' );
		if ( isset( $_POST['install_woocommerce'] ) && 'yes' === $_POST['install_woocommerce'] ) {
			if ( $woocommerce_installed ) {
					$is_wc_active = is_plugin_active( 'woocommerce/woocommerce.php' );
				if ( ! $is_wc_active ) {
					$activate_status = activate_plugin( 'woocommerce/woocommerce.php', '', false, true );
					if ( is_wp_error( $activate_status ) ) {
						wp_send_json_error( array( 'message' => __( 'WooCommerce Activation Failed!', 'wholesalex' ) ) );
					}
				}
			}
		}
		if ( isset( $_POST['install_wholesalex'] ) && 'yes' === $_POST['install_wholesalex'] ) {
			if ( ! $wholesalex_installed ) {
				$status = $this->plugin_install( 'wholesalex' );
				if ( $status && ! is_wp_error( $status ) ) {
					$activate_status = activate_plugin( 'wholesalex/wholesalex.php', '', false, true );
					if ( is_wp_error( $activate_status ) ) {
						wp_send_json_error( array( 'message' => __( 'WholesaleX Activation Failed!', 'wholesalex' ) ) );
					}
				} else {
					wp_send_json_error( array( 'message' => __( 'WholesaleX Installation Failed!', 'wholesalex' ) ) );
				}
			} else {
				$is_wc_active = is_plugin_active( 'wholesalex/wholesalex.php' );
				if ( ! $is_wc_active ) {
					$activate_status = activate_plugin( 'wholesalex/wholesalex.php', '', false, true );
					if ( is_wp_error( $activate_status ) ) {
						wp_send_json_error( array( 'message' => __( 'WholesaleX Activation Failed!', 'wholesalex' ) ) );
					}
				}
			}
		}

		wp_send_json_success( 'Success' );
	}
}

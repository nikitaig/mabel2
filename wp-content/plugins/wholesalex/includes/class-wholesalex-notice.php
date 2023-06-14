<?php
/**
 * Admin Action.
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * WholesaleX Notice Class
 */
class WHOLESALEX_Notice {

	private $notice_version = 'v4';

	/**
	 * Admin WooCommerce Installation Notice Action
	 *
	 * @since 1.0.0
	 */
	public function install_notice() {
		add_action( 'wp_ajax_wc_install', array( $this, 'wc_install_callback' ) );
		add_action( 'admin_notices', array( $this, 'wc_installation_notice_callback' ) );
	}


	/**
	 * Admin WooCommerce Activation Notice Action
	 *
	 * @since 1.0.0
	 */
	public function active_notice() {
		add_action( 'admin_notices', array( $this, 'wc_activation_notice_callback' ) );
		add_action( 'admin_action_wc_activate', array( $this, 'wc_activate_action' ) );
	}

	/**
	 * Promotional Notice Callback
	 *
	 * @since 1.0.0
	 */
	public function promotion() {
		$default_notice = array(
			// array(
			// 'start' => '09-11-2022',
			// 'end' => '03-12-2022',
			// 'type' => 'content',
			// 'content' => '<strong>WholesaleX - ⚡Flash Sale⚡</strong> is LIVE! Spend <strong style="color:#1cb53e;">25% LESS</strong> on Premium Features for a <strong>⏳LIMITED Time!</strong>',
			// 'force' => false
			// ),
			// array(
			// 	'start'   => '16-04-2023', // Date format "d-m-Y" [08-02-2019]
			// 	'end'     => '28-04-2023',
			// 	'type'    => 'content',
			// 	'content' => "<div>EID Is Here!</div> <div>Let's Celebrate Together</div> <span style='font-weight:bold;'>Enjoy Upto 60% OFF on WholesaleX Pro!</span>",
			// 	'force'   => true,
			// ),
			array(
				'start'   => '21-05-2023', // Date format "d-m-Y" [08-02-2019]
				'end'     => '02-06-2023',
				'type'    => 'banner',
				'content' => WHOLESALEX_URL.'assets/img/banner.png',
				'force'   => true,
			),
		);
		if ( count( $default_notice ) > 0 ) {
			foreach ( $default_notice as $key => $notice ) {
				$current_time = date( 'U' );
				if ( $current_time > strtotime( $notice['start'] ) && $current_time < strtotime( $notice['end'] ) ) {
					$this->type    = $notice['type'];
					$this->content = $notice['content'];
					$this->force   = $notice['force'];
					add_action( 'admin_notices', array( $this, 'promotional_notice_callback' ) );
				}
			}
		}
		add_action( 'admin_init', array( $this, 'set_dismiss_notice_callback' ) );
	}

	 /**
	  * Promotional Dismiss Notice Option Data
	  *
	  * @since v.2.0.1
	  * @param NULL
	  * @return NULL
	  */
	public function set_dismiss_notice_callback() {
		if ( ! isset( $_GET[ 'disable_wholesalex_notice_' . $this->notice_version ] ) ) {
			return;
		}
		if ( sanitize_key( $_GET[ 'disable_wholesalex_notice_' . $this->notice_version ] ) == 'yes' ) {
			set_transient( 'wholesalex_get_pro_notice_' . $this->notice_version, 'off', 2592000 ); // 30 days notice
		}
	}


	/**
	 * Promotional Banner Notice
	 */
	public function promotional_notice_callback() {
		if ( get_transient( 'wholesalex_get_pro_notice_' . $this->notice_version ) != 'off' ) {
			if ( ! wholesalex()->is_pro_active() && ( $this->force || ( date( 'U' ) > get_option( 'wholesalex_installation_date' ) ) ) ) {
				if ( ! isset( $_GET[ 'disable_wholesalex_notice_' . $this->notice_version ] ) ) {
					$this->wc_notice_css();
					?>
					<div class="wholesalex-wc-install wholesalex-pro-notice-v2">
						<?php
						switch ( $this->type ) {
							case 'banner':
								?>
									<div class="wholesalex-wc-install-body wholesalex-image-banner-v2">
										<a class="wc-dismiss-notice" href="<?php echo esc_url( add_query_arg( 'disable_wholesalex_notice_' . $this->notice_version, 'yes' ) ); ?>">
											Dismiss
										</a>
										<a class="wholesalex-btn-image" target="_blank" href="<?php echo esc_url( 'https://getwholesalex.com/pricing/?utm_source=wholesalex-ad&utm_medium=DB-banner&utm_campaign=wholesalex-DB' ); ?>">
											<img loading="lazy" src="<?php echo esc_url( $this->content ); ?>" alt="Discount Banner"/>
										</a>
									</div>
								<?php
								break;
							case 'content':
								?>
									<div class="wholesalex-wc-install-body wholesalex-content-notice">
										<div class="wholesalex-notice-content-wrapper"><?php echo $this->content; ?>
										<a class="button button-primary button-hero wholesalex-btn-notice-pro" target="_blank" href="<?php echo esc_url('https://getwholesalex.com/pricing/?utm_source=wholesalex-ad&utm_medium=DB-banner&utm_campaign=wholesalex-DB'); ?>"><span class="dashicons dashicons-image-rotate"></span><?php esc_html_e( "GET IT NOW", 'wholesalex' ); ?></a>
										</div>
										<a class="content-notice-dissmiss" href="<?php echo esc_url( add_query_arg( 'disable_wholesalex_notice_' . $this->notice_version, 'yes' ) ); ?>"><?php esc_html_e( 'Dismiss', 'wholesalex' ); ?></a> 
									</div>
								<?php
								break;
						}
						?>
					</div>
					<?php
				}
			}
		}
	}


	/**
	 * WooCommerce Installation Notice
	 *
	 * @since 1.0.0
	 */
	public function wc_installation_notice_callback() {
		if ( ! get_option( 'wholesalex_dismiss_notice' ) ) {
			$this->wc_notice_css();
			?>
			<div class="wholesalex-wc-install">
				<img loading="lazy" width="200" src="<?php echo esc_url( WHOLESALEX_URL . 'assets/img/woocommerce.png' ); ?>" alt="logo" />
				<div class="wholesalex-wc-install-body">
					<h3><?php esc_html_e( 'Welcome to WholesaleX.', 'wholesalex' ); ?></h3>
					<p><?php esc_html_e( 'WooCommerce WholesaleX is a WooCommerce plugin. To use this plugins you have to install and activate WooCommerce.', 'wholesalex' ); ?></p>
					<a class="wholesalex-wc-install-btn button button-primary button-hero" href="<?php echo esc_url( add_query_arg( array( 'action' => 'wc_install' ), admin_url( 'admin-ajax.php' ) ) ); ?>"><span class="dashicons dashicons-image-rotate"></span><?php esc_html_e( 'Install WooCommerce', 'wholesalex' ); ?></a>
					<div id="installation-msg"></div>
				</div>
			</div>
			<?php
		}
	}


	/**
	 * WooCommerce Activation Notice
	 *
	 * @since 1.0.0
	 */
	public function wc_activation_notice_callback() {
		if ( ! get_option( 'wholesalex_dismiss_notice' ) ) {
			$this->wc_notice_css();
			?>
			<div class="wholesalex-wc-install">
				<img loading="lazy" width="200" src="<?php echo esc_url( WHOLESALEX_URL . 'assets/img/woocommerce.png' ); ?>" alt="logo" />
				<div class="wholesalex-wc-install-body">
					<h3><?php esc_html_e( 'Welcome to WholesaleX.', 'wholesalex' ); ?></h3>
					<p><?php esc_html_e( 'WooCommerce WholesaleX is a WooCommerce plugin. To use this plugins you have to install and activate WooCommerce.', 'wholesalex' ); ?></p>
					<a class="button button-primary button-hero" href="<?php echo esc_url( add_query_arg( array( 'action' => 'wc_activate' ), admin_url() ) ); ?>"><?php esc_html_e( 'Activate WooCommerce', 'wholesalex' ); ?></a>
				</div>
			</div>
			<?php
		}
	}


	/**
	 * WooCommerce Notice Styles
	 *
	 * @since 1.0.0
	 */
	public function wc_notice_css() {
		?>
		<style type="text/css">

			.wholesalex-wc-install.wholesalex-pro-notice-v2 {
				padding-bottom: 0px;
			}
			.wholesalex-content-notice {
				color: white;
				background-color: #6C6CFF;
				position: relative;
				font-size: 16px;
				padding-left: 10px;
				line-height: 23px;
			}

			.wholesalex-notice-content-wrapper {
				margin-bottom: 0px !important;
				padding: 10px 5px;
			}

			.wholesalex-wc-install .wholesalex-content-notice .wholesalex-btn-notice-pro {
				margin-left: 5px;
				background-color: #3c3cb7 !important;
				border-radius: 4px;
				max-height: 30px !important;
				padding: 8px 12px !important;
				font-size: 14px;
				position: relative;
				top: -4px;
			}
			.wholesalex-wc-install .wholesalex-content-notice .wholesalex-btn-notice-pro:hover {
				background-color: #29298c !important;
			}
			
			/* .wholesalex-content-notice .content-notice-dissmiss {
				position: absolute;
				top: 0;
				right: 0;
				color: white;
				background-color: black;
				padding: 5px;
				font-size: 12px;
				line-height: 1;
				border-bottom-left-radius: 5px;
			} */

			/* .whx-new-dismiss{
				position: absolute;
				top: 0;
				right: 0;
				color: white;
				background-color: black;
				padding: 4px 5px 5px;
				font-size: 12px;
				line-height: 1;
				border-bottom-left-radius: 3px;
				text-decoration: none;
			} */

			.wholesalex-content-notice .content-notice-dissmiss {
				position: absolute;
				top: 0;
				right: 0;
				color: white;
				background-color: #3f3fa6;
				padding: 4px 5px 5px;
				font-size: 12px;
				line-height: 1;
				border-bottom-left-radius: 3px;
				text-decoration: none;
			}
			.wholesalex-image-banner-v2{
				padding:0;
			}
			.wholesalex-wc-install {
				display: -ms-flexbox;
				display: flex;
				align-items: center;
				background: #fff;
				margin-top: 40px;
				width: calc(100% - 50px);
				border: 1px solid #ccd0d4;
				padding: 4px;
				border-radius: 4px;
			}   
			.wholesalex-wc-install img {
				margin-right: 0;
				max-width: 100%; 
			}
			.wholesalex-image-banner-v2.wholesalex-wc-install-body{
				position: relative;
			}
			.wholesalex-wc-install-body {
				-ms-flex: 1;
				flex: 1;
			}
			.wholesalex-wc-install-body > div {
				max-width: 450px;
				margin-bottom: 20px;
			}
			.wholesalex-wc-install-body h3 {
				margin-top: 0;
				font-size: 24px;
				margin-bottom: 15px;
			}
			.wholesalex-install-btn {
				margin-top: 15px;
				display: inline-block;
			}
			.wholesalex-wc-install .dashicons{
				display: none;
				animation: dashicons-spin 1s infinite;
				animation-timing-function: linear;
			}
			.wholesalex-wc-install.loading .dashicons {
				display: inline-block;
				margin-top: 12px;
				margin-right: 5px;
			}
			@keyframes dashicons-spin {
				0% {
					transform: rotate( 0deg );
				}
				100% {
					transform: rotate( 360deg );
				}
			}
			.wholesalex-image-banner-v2 .wc-dismiss-notice {
				color: #fff;
				background-color: #000000;
				padding-top: 0px;
				position: absolute;
				right: 0;
				top: 0px;
				padding:5px;
				/* padding: 10px 10px 14px; */
				border-radius: 0 0 0 4px;
				display: inline-block;
				transition: 400ms;
			}
			.wholesalex-image-banner-v2 .wc-dismiss-notice:focus{
				outline: none;
				box-shadow: unset;
			}
			.wholesalex-btn-image:focus{
				outline: none;
				box-shadow: unset;
			}
			.wc-dismiss-notice {
				position: relative;
				text-decoration: none;
				float: right;
				right: 26px;
			}
			.wc-dismiss-notice .dashicons{
				display: inline-block;
				text-decoration: none;
				animation: none;
			}

			.wholesalex-pro-notice-v2 .wholesalex-wc-install-body h3 {
				font-size: 20px;
				margin-bottom: 5px;
			}
			.wholesalex-pro-notice-v2 .wholesalex-wc-install-body > div {
				max-width: 100%;
				margin-bottom: 10px;
			}
			.wholesalex-pro-notice-v2 .button-hero {
				padding: 8px 14px !important;
				min-height: inherit !important;
				line-height: 1 !important;
				box-shadow: none;
				border: none;
				transition: 400ms;
			}
			.wholesalex-pro-notice-v2 .wholesalex-btn-notice-pro {
				background: #2271b1;
				color: #fff;
			}
			.wholesalex-pro-notice-v2 .wholesalex-btn-notice-pro:hover,
			.wholesalex-pro-notice-v2 .wholesalex-btn-notice-pro:focus {
				background: #185a8f;
			}
			.wholesalex-pro-notice-v2 .button-hero:hover,
			.wholesalex-pro-notice-v2 .button-hero:focus {
				border: none;
				box-shadow: none;
			}
			.wc-dismiss-notice:hover {
				color:red;
			}
			.wc-dismiss-notice .dashicons{
				display: inline-block;
				text-decoration: none;
				animation: none;
				font-size: 16px;
			}
		</style>
		<?php
	}

	/**
	 * WooCommerce Force Install Action
	 *
	 * @since 1.0.0
	 */
	public function wc_install_callback() {
		include ABSPATH . 'wp-admin/includes/plugin-install.php';
		include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
			include ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
		}
		if ( ! class_exists( 'Plugin_Installer_Skin' ) ) {
			include ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php';
		}

		$plugin = 'woocommerce';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			wp_die( esc_html__( 'Error!', 'wholesalex' ) );
		}
		/* translators: %s: API Name and Version */
		$title = sprintf( __( 'Installing Plugin: %s', 'wholesalex' ), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url   = 'update.php?action=install-plugin&plugin=' . rawurlencode( $plugin );

		$upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
		$upgrader->install( $api->download_link );
		die();
	}

	/**
	 * WooCommerce Redirect After Active Action
	 *
	 * @since 1.0.0
	 */
	public function wc_activate_action() {
		activate_plugin( 'woocommerce/woocommerce.php' );
		wp_safe_redirect( admin_url( 'plugins.php' ) );
		exit();
	}

}

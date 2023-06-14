<?php
/**
 * Notice Action.
 *
 * @package WOPB\Notice
 * @since v.1.0.0
 */
namespace WOPB;

use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;

defined( 'ABSPATH' ) || exit;

/**
 * Notice class.
 */
class Notice {

	/**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
	private $notice_version = 'v21';

	public function __construct() {
		$this->type    = '';
		$this->content = '';
		$this->force   = false;
		add_action( 'admin_init', array( $this, 'admin_init_callback' ) );
		add_action( 'admin_init', array( $this, 'set_promotional_notice_callback' ) );
		add_action( 'wp_ajax_wc_install', array( $this, 'wc_install_callback' ) );
		add_action( 'admin_action_wc_activate', array( $this, 'wc_activate_callback' ) );
		add_action( 'wp_ajax_wopb_dismiss_notice', array( $this, 'set_dismiss_notice_callback' ) );
		/**
		 * WholesaleX Intro Banner and Remove Banner Function implementation
		 *
		 * @since 2.6.1
		 */
		add_action( 'admin_init', array( $this, 'remove_wholesalex_intro_banner' ) );
		add_action( 'admin_notices', array( $this, 'wholesalex_intro_notice' ) );
		add_action( 'wp_ajax_install_wholesalex', array( $this, 'wholesalex_installation_callback' ) );
	}

	/**
	 * Promotional Dismiss Notice Option Data
	 *
	 * @param NULL
	 * @return NULL
	 */
	public function set_promotional_notice_callback() {
		if ( ! isset( $_GET['disable_productx_notice'] ) ) {
			return;
		}
		if ( sanitize_text_field( $_GET['disable_productx_notice'] ) == 'yes' ) {
			set_transient( 'wopb_get_pro_notice_' . $this->notice_version, 'off', 2592000 ); // 30 days notice
		}
	}


	/**
	 * Dismiss Notice Option Data
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function set_dismiss_notice_callback() {
		if ( ! wp_verify_nonce( $_REQUEST['wpnonce'], 'wopb-nonce' ) ) {
			return;
		}
		update_option( 'dismiss_notice', 'yes' );
	}


	/**
	 * Admin Notice Action Add
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function admin_init_callback() {
		if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'wc_installation_notice_callback' ) );
		} elseif ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'wc_activation_notice_callback' ) );
		} else {
			$default_notice = array(
//				array(
//					'start'   => '16-04-2023',
//					'end'     => '28-04-2023',
//					'type'    => 'content',
//					'content' => "<div class='wopb-eid-notice'>
//					                <p>" . __('Eid Is Here', 'product-blocks') . "!</p>
//					                <p>" . __("Let's Celebrate Together", 'product-blocks') . "</p>
//					                <p class='wopb-eid-offer'>" .
//					                    __("Enjoy UP To 50% OFF on ProductX Pro", 'product-blocks')
//					                    . "! <a target='_blank' href='" . esc_url( wopb_function()->get_premium_link( '', 'dashboard_db_banner' ) ) . "'>GET IT NOW</a>
//					                </p>
//                                </div>",
//					'force'   => true,
//				),
				array(
					'start'   => '22-05-2023', // Date format "d-m-Y" [08-02-2019]
					'end'     => '02-06-2023',
					'type'    => 'banner',
					'content' => WOPB_URL . 'assets/img/banner.png',
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
						add_action( 'admin_notices', array( $this, 'wopb_promotional_notice_callback' ) );
					}
				}
			}
		}
	}


	/**
	 * Promotional Banner Notice
	 *
	 * @param NULL
	 * @return NULL
	 */
	public function wopb_promotional_notice_callback() {
		if ( get_transient( 'wopb_get_pro_notice_' . $this->notice_version ) != 'off' ) {
			if ( ! wopb_function()->is_lc_active() && ( $this->force || get_transient( 'wopb_initial_user_notice' ) != 'off' ) ) {
				if ( ! isset( $_GET['disable_productx_notice'] ) ) {
					$this->wc_notice_css();
					$this->wc_notice_js();
					?>
					<div class="wc-install wopb-pro-notice">
						<?php
						switch ( $this->type ) {
							case 'banner':
								?>
								<div class="wopb-install-body wopb-image-banner">
									<a href="<?php echo esc_url( add_query_arg( 'disable_productx_notice', 'yes' ) ); ?>" class="promotional-dismiss-notice">
										<?php esc_html_e( 'Dismiss', 'product-blocks' ); ?>
									</a>
									<a target="_blank" href="<?php echo esc_url( wopb_function()->get_premium_link( '', 'dashboard_db_banner' ) ); ?>">
										<img src="<?php echo esc_url( $this->content ); ?>" alt="Flash Sale" />
									</a>
								</div>
								<?php
								break;
							case 'content':
								?>
								<div class="wopb-install-body">
									<div><?php echo $this->content; ?></div>
<!--									<a class="button button-primary button-hero wopb-btn-notice-pro" target="_blank" href="--><?php //echo esc_url( wopb_function()->get_premium_link( '', 'dashboard_db_banner' ) ); ?><!--">-->
<!--										--><?php //esc_html_e( 'Upgrading to Pro', 'product-blocks' ); ?>
<!--									</a>-->
<!--									<a class="button-secondary button-large" href="--><?php //echo esc_url( add_query_arg( 'disable_productx_notice', 'yes' ) ); ?><!--">--><?php //esc_html_e( 'No Thanks / Close.', 'product-blocks' ); ?><!--</a> -->
                                    <a href="<?php echo esc_url( add_query_arg( 'disable_productx_notice', 'yes' ) ); ?>" class="promotional-dismiss-notice">
                                        <?php esc_html_e( 'Dismiss', 'product-blocks' ); ?>
									</a>
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
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function wc_installation_notice_callback() {
		if ( ! get_option( 'dismiss_notice' ) ) {
			$this->wc_notice_css();
			$this->wc_notice_js();
			?>
			<div class="wc-install">
				<img width="200" src="<?php echo esc_url( WOPB_URL . 'assets/img/woocommerce.png' ); ?>" alt="logo" />
				<div class="wopb-install-body">
					<a class="wc-dismiss-notice" data-security=<?php echo wp_create_nonce( 'wopb-nonce' ); ?>  data-ajax=<?php echo admin_url( admin_url( 'admin-ajax.php' ) ); ?> href="#"><span class="dashicons dashicons-no-alt"></span> <?php esc_html_e( 'Dismiss', 'product-blocks' ); ?></a>
					<h3><?php esc_html_e( 'Welcome to Product Blocks.', 'product-blocks' ); ?></h3>
					<p><?php esc_html_e( 'WooCommerce Product Blocks is a WooCommerce plugin. To use this plugins you have to install and activate WooCommerce.', 'product-blocks' ); ?></p>
					<a class="wc-install-btn button button-primary button-hero" href="<?php echo esc_url( add_query_arg( array( 'action' => 'wc_install' ), admin_url() ) ); ?>"><span class="dashicons dashicons-image-rotate"></span><?php esc_html_e( 'Install WooCommerce', 'product-blocks' ); ?></a>
					<div id="installation-msg"></div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * WooCommerce Activation Notice
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function wc_activation_notice_callback() {
		if ( ! get_option( 'dismiss_notice' ) ) {
			$this->wc_notice_css();
			$this->wc_notice_js();
			?>
			<div class="wc-install">
				<img width="200" src="<?php echo esc_url( WOPB_URL . 'assets/img/woocommerce.png' ); ?>" alt="logo" />
				<div class="wopb-install-body">
					<a class="wc-dismiss-notice" data-security=<?php echo wp_create_nonce( 'wopb-nonce' ); ?>  data-ajax=<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?> href="#"><span class="dashicons dashicons-no-alt"></span> <?php esc_html_e( 'Dismiss', 'product-blocks' ); ?></a>
					<h3><?php esc_html_e( 'Welcome to Product Blocks.', 'product-blocks' ); ?></h3>
					<p><?php esc_html_e( 'WooCommerce Product Blocks is a WooCommerce plugin. To use this plugins you have to install and activate WooCommerce.', 'product-blocks' ); ?></p>
					<a class="button button-primary button-hero" href="<?php echo esc_url( add_query_arg( array( 'action' => 'wc_activate' ), admin_url() ) ); ?>"><?php esc_html_e( 'Activate WooCommerce', 'product-blocks' ); ?></a>
				</div>
			</div>
			<?php
		}
	}


	/**
	 * WooCommerce Notice Styles
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function wc_notice_css() {
		?>
		<style type="text/css">
			.wc-install {
				display: flex;
				align-items: center;
				background: #fff;
				margin-top: 40px;
				width: calc(100% - 50px);
				border: 1px solid #ccd0d4;
				padding: 4px;
				border-radius: 4px;
				border-left: 3px solid #46b450;
				line-height: 0;
			}
			.wc-install img {
				margin-right: 10px;
			}
			.wopb-install-body {
				-ms-flex: 1;
				flex: 1;
				padding: 10px;
                background: linear-gradient(90deg,#F7931E 0%,#FF6245 100%);
			}
			.wopb-install-body.wopb-image-banner{
				padding: 0px;
			}
			.wopb-install-body > div {
				max-width: 450px;
				margin-bottom: 20px;
			}
			.wopb-install-body h3 {
				margin-top: 0;
				font-size: 24px;
				margin-bottom: 15px;
			}
			.wc-install-btn {
				margin-top: 15px;
				display: inline-block;
			}
			.wc-install .dashicons{
				display: none;
				animation: dashicons-spin 1s infinite;
				animation-timing-function: linear;
			}
			.wc-install.loading .dashicons {
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
			.wopb-pro-notice {
				position: relative;
                border-left: 3px solid #f79220;
			}
			.wopb-pro-notice .wopb-install-body h3 {
				font-size: 20px;
				margin-bottom: 5px;
			}
			.wopb-pro-notice .wopb-install-body > div {
				max-width: 800px;
				margin-bottom: 0;
			}
			.wopb-pro-notice .button-hero {
				padding: 8px 14px !important;
				min-height: inherit !important;
				line-height: 1 !important;
				box-shadow: none;
				border: none;
				transition: 400ms;
				background: #46b450;
			}
			.wopb-pro-notice .button-hero:hover,
			.wp-core-ui .wopb-pro-notice .button-hero:active {
				background: #389e41;
			}
			.wopb-pro-notice .wopb-btn-notice-pro {
				background: #e5561e;
				color: #fff;
			}
			.wopb-pro-notice .wopb-btn-notice-pro:hover,
			.wopb-pro-notice .wopb-btn-notice-pro:focus {
				background: #ce4b18;
			}
			.wopb-pro-notice .button-hero:hover,
			.wopb-pro-notice .button-hero:focus {
				border: none;
				box-shadow: none;
			}
			.wopb-pro-notice img {
				width: 100%;
			}
			.wopb-pro-notice .promotional-dismiss-notice {
				background-color: #000000;
				padding-top: 0px;
				position: absolute;
				right: 0;
				top: 0px;
				padding: 10px 10px 14px;
				border-radius: 0 0 0 4px;
				display: inline-block;
				color: #fff;
			}
            .wopb-eid-notice p {
                margin: 0;
                color: #f7f7f7;
                font-size: 16px;
            }
            .wopb-eid-notice p.wopb-eid-offer {
                color: #fff;
                font-weight: 700;
                font-size: 18px;
            }
            .wopb-eid-notice p.wopb-eid-offer a {
                background-color: #ffc160;
                padding: 8px 12px;
                border-radius: 4px;
                color: #000;
                font-size: 14px;
                margin-left: 3px;
                text-decoration: none;
                font-weight: 500;
                position: relative;
                top: -4px;
            }
            .wopb-eid-notice p.wopb-eid-offer a:hover {
                background-color: #edaa42;
            }
            .wopb-install-body .promotional-dismiss-notice {
                right: 4px;
                top: 4px;
                border-radius: unset !important;
                /*background-color: #cc4327;*/
                padding: 10px 8px 12px;
                text-decoration: none;
            }
		</style>
		<?php
	}


	/**
	 * WooCommerce Notice JavaScript
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function wc_notice_js() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				'use strict';
				$(document).on('click', '.wc-install-btn', function(e){
					e.preventDefault();
					const $that = $(this);
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {install_plugin: 'woocommerce', action: 'wc_install'},
						beforeSend: function(){
							$that.parents('.wc-install').addClass('loading');
						},
						success: function (data) {
							$('#installation-msg').html(data);
							$that.parents('.wc-install').remove();
						},
						complete: function () {
							$that.parents('.wc-install').removeClass('loading');
						}
					});
				});

				// Dismiss notice
				$(document).on('click', '.wc-dismiss-notice', function(e){
					e.preventDefault();
					const that = $(this);
					$.ajax({
						url: that.data('ajax'),
						type: 'POST',
						data: {
							action: 'wopb_dismiss_notice',
							wpnonce: that.data('security')
						},
						success: function (data) {
							that.parents('.wc-install').hide("slow", function() { that.parents('.wc-install').remove(); });
						},
						error: function(xhr) {
							console.log('Error occured. Please try again' + xhr.statusText + xhr.responseText );
						},
					});
				});

			});
		</script>
		<?php
	}


	/**
	 * WooCommerce Force Install Action
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
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
			wp_die( $api );
		}

		$title = sprintf( __( 'Installing Plugin: %s' ), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . esc_html( $plugin );
		$url   = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );

		$upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
		$upgrader->install( $api->download_link );
		die();
	}


	/**
	 * WooCommerce Redirect After Active Action
	 *
	 * @since v.1.0.0
	 * @param NULL
	 * @return NULL
	 */
	public function wc_activate_callback() {
		activate_plugin( 'woocommerce/woocommerce.php' );
		wp_redirect( admin_url( 'admin.php?page=wopb-option-settings' ) );
		exit();
	}

	/**
	 * WholesaleX Intro Notice
	 *
	 * @return void
	 * @since 2.6.1
	 */
	public function wholesalex_intro_notice() {
		// check wholesalex is installed or not.
		$wholesalex_installed = file_exists( WP_PLUGIN_DIR . '/wholesalex/wholesalex.php' );

		$notice_status = get_transient( '__wpxpo_wholesalex_intro_notice_status' );
		if ( ! $notice_status && ! $wholesalex_installed ) {
			ob_start();
			?>
				<style>
					/*----- WholesaleX Into Notice ------*/
					.notice.notice-success.wopb-wholesalex-notice {
						border-left-color: #4D4DFF;
						padding: 0;
					}

					.wopb-notice-container {
						display: flex;
					}

					.wopb-notice-container a{
						text-decoration: none;
					}

					.wopb-notice-container a:visited{
						color: white;
					}

					.wopb-notice-image {
						padding-top: 15px;
						padding-left: 12px;
						padding-right: 12px;
						background-color: #f4f4ff;
						max-width: 40px;
					}
					.wopb-notice-image img{
						max-width: 100%;
					}

					.wopb-notice-content {
						width: 100%;
						padding: 16px;
						display: flex;
						flex-direction: column;
						gap: 8px;
					}

					.wopb-notice-wholesalex-button {
						max-width: fit-content;
						padding: 8px 15px;
						font-size: 16px;
						color: white;
						background-color: #4D4DFF;
						border: none;
						border-radius: 2px;
						cursor: pointer;
						margin-top: 6px;
						text-decoration: none;
					}

					.wopb-notice-heading {
						font-size: 18px;
						font-weight: 500;
						color: #1b2023;
					}

					.wopb-notice-content-header {
						display: flex;
						justify-content: space-between;
						align-items: center;
					}

					.wopb-notice-close .dashicons-no-alt {
						font-size: 25px;
						height: 26px;
						width: 25px;
						cursor: pointer;
						color: #585858;
					}

					.wopb-notice-close .dashicons-no-alt:hover {
						color: red;
					}

					.wopb-notice-content-body {
						font-size: 14px;
						color: #343b40;
					}

					.wopb-notice-wholesalex-button:hover {
						background-color: #6C6CFF;
						color: white;
					}

					span.wopb-bold {
						font-weight: bold;
					}
					a.wopb-wholesalex-pro-dismiss:focus {
						outline: none;
						box-shadow: unset;
					}
					.loading {
						width: 16px;
						height: 16px;
						border: 3px solid #FFF;
						border-bottom-color: transparent;
						border-radius: 50%;
						display: inline-block;
						box-sizing: border-box;
						animation: rotation 1s linear infinite;
						margin-left: 10px;
					}

					@keyframes rotation {
						0% {
							transform: rotate(0deg);
						}

						100% {
							transform: rotate(360deg);
						}
					}
					/*----- End WholesaleX Into Notice ------*/

				</style>
				<div class="notice notice-success wopb-wholesalex-notice">
					<div class="wopb-notice-container">
						<div class="wopb-notice-image"><img src="<?php echo esc_url( WOPB_URL ) . 'assets/img/wholesalex-icon.svg'; ?>"/></div>
						<div class="wopb-notice-content">
							<div class="wopb-notice-content-header">
								<div class="wopb-notice-heading">
									<?php echo __( 'Introducing <span class="wopb-bold">WholesaleX</span> - The Most Complete <span class="wopb-bold">B2B Solution', 'product-blocks' ); //phpcs:ignore WordPress.Security.EscapeOutput ?>
								</div>
								<div class="wopb-notice-close">
									<a href="<?php echo esc_url( add_query_arg( 'close_wholesalex_promo', 'yes' ) ); ?>" class="wopb-wholesalex-pro-dismiss"><span class="dashicons dashicons-no-alt"></span></a>
								</div>
							</div>
							<div class="wopb-notice-content-body">
								<?php echo __( 'Start wholesaling in your WooCommerce store and enjoy up to <span class="wopb-bold">300% revenue</span>', 'product-blocks' ); ?>
							</div>
							<a id="wopb_install_wholesalex" class="wopb-notice-wholesalex-button " ><?php echo esc_html__( 'Get WholesaleX', 'product-blocks' ); ?></a>
						</div>
					</div>
				</div>

				<script>
					const installWholesaleX = (element)=>{
						element.innerHTML = "<?php echo esc_html_e( 'Installing WholesaleX', 'product-blocks' ); ?> <span class='loading'></span>";
						const wopb_ajax = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
						const formData = new FormData();
						formData.append('action','install_wholesalex');
						formData.append('wpnonce',"<?php echo esc_attr( wp_create_nonce( 'install_wholesalex' ) ); ?>");
						fetch(wopb_ajax, {
							method: 'POST',
							body: formData,
						})
						.then(res => res.json())
						.then(res => {
							if(res) {
								if (res.success ) {
									element.innerHTML = "<?php echo esc_html_e( 'Installed', 'product-blocks' ); ?>";
								} else {
									console.log("installation failed..");
								}
							}
							location.reload();
						})
					}
					const wopbInstallWholesaleX = document.getElementById('wopb_install_wholesalex');
					wopbInstallWholesaleX.addEventListener('click',(e)=>{
						e.preventDefault();
						installWholesaleX(wopbInstallWholesaleX);
					})
				</script>
			<?php
			echo ob_get_clean(); //phpcs:ignore
		}
	}


	/**
	 * Remove WholesaleX Intro Banner
	 *
	 * @return void
	 * @since 2.6.1
	 */
	public function remove_wholesalex_intro_banner() {
		if ( isset( $_GET['close_wholesalex_promo'] ) && 'yes' === $_GET['close_wholesalex_promo'] ) { //phpcs:ignore
			set_transient( '__wpxpo_wholesalex_intro_notice_status', true );
		}
	}


	/**
	 * WholesaleX Installation Callback From Banner.
	 *
	 * @return void
	 */
	public function wholesalex_installation_callback() {
		if ( ! isset( $_POST['wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpnonce'] ) ), 'install_wholesalex' ) ) {
			wp_send_json_error( 'Nonce Verification Failed' );
			die();
		}

		$wholesalex_installed = file_exists( WP_PLUGIN_DIR . '/wholesalex/wholesalex.php' );

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

		set_transient( '__wpxpo_wholesalex_intro_notice_status', true );

		wp_send_json_success( __( 'Successfully Installed and Activated', 'product-blocks' ) );

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

}

<?php
/**
 * WholesaleX
 *
 * @link    https://www.wpxpo.com/
 * @since   1.0.0
 * @package WholesaleX
 *
 * Plugin Name:       WholesaleX
 * Plugin URI:        https://wordpress.org/plugins/wholesalex
 * Description:       The WholesaleX plugin is a brand-new, highly-promising WooCommerce B2B solution to set up a conversion-focused B2B store for selling wholesale products. It offers everything required to operate an effective B2B store.
 * Version:           1.1.9
 * Author:            wpxpo
 * Author URI:        https://wpxpo.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wholesalex
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Plugin Defined.
define( 'WHOLESALEX_VER', '1.1.9' );
define( 'WHOLESALEX_URL', plugin_dir_url( __FILE__ ) );
define( 'WHOLESALEX_BASE', plugin_basename( __FILE__ ) );
define( 'WHOLESALEX_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load Language
 */
function wholesalex_language_load() {
	load_plugin_textdomain( 'wholesalex', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'wholesalex_language_load' );

if ( ! function_exists( 'wholesalex' ) ) {
	/**
	 * Common Function.
	 */
	function wholesalex() {
		include_once WHOLESALEX_PATH . 'includes/Functions.php';
		return new \WHOLESALEX\Functions();
	}
}

/**
 * Begins Execution of the Plugin.
 */
function wholesalex_run() {
	require_once WHOLESALEX_PATH . 'includes/class-wholesalex-scripts.php';

	require_once WHOLESALEX_PATH . 'includes/menu/class-wholesalex-setup-wizard.php';
	new \WHOLESALEX\WHOLESALEX_Setup_Wizard();

	include_once WHOLESALEX_PATH . 'includes/class-wholesalex-notice.php';
	$notice = new \WHOLESALEX\WHOLESALEX_Notice();
	if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins', array() ), true ) ) {
		include_once WHOLESALEX_PATH . 'includes/class-wholesalex-initialization.php';
		new WholesaleX_Initialization();
		$notice->promotion();
	} else {
		if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
			$notice->install_notice();
		} else {
			$notice->active_notice();
		}
	}
}

wholesalex_run();
/**
 * The code that runs during plugin activation.
 */
function wholesalex_activate_action() {
	require_once WHOLESALEX_PATH . 'includes/class-wholesalex-activator.php';
	new \WHOLESALEX\Activator();
}

/**
 * The code that runs during plugin deactivation.
 */
function wholesalex_deactivate_action() {
	require_once WHOLESALEX_PATH . 'includes/class-wholesalex-deactivator.php';
	WholesaleX_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'wholesalex_activate_action' );
register_deactivation_hook( __FILE__, 'wholesalex_deactivate_action' );
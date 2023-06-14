<?php
/**
 * Plugin Name: ProductX – Gutenberg WooCommerce Blocks
 * Description: <a href="https://www.wpxpo.com/productx/?utm_source=productx_plugin&utm_medium=productx&utm_campaign=productx-dashboard">ProductX</a> is one of the most promising plugins to expand the functionalities of the WooCommerce plugin. It offers 35+ product and store building blocks with full customization options. Moreover, the WooCommerce Builder lets you create templates dynamically and replace them with the default ones. In short, you can create idol eCommerce stores effortlessly.
 * Version:     2.7.1
 * Author:      wpxpo
 * Author URI:  https://www.wpxpo.com/?utm_source=productx_plugin&utm_medium=wpxpo&utm_campaign=productx-dashboard
 * Text Domain: product-blocks
 * License:     GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( 'ABSPATH' ) || exit;

// Define
define('WOPB_VER', '2.7.1');
define('WOPB_URL', plugin_dir_url(__FILE__));
define('WOPB_BASE', plugin_basename(__FILE__));
define('WOPB_PATH', plugin_dir_path(__FILE__));

// Language Load
add_action('init', 'wopb_language_load');
function wopb_language_load() {
    load_plugin_textdomain( 'product-blocks', false, basename(dirname(__FILE__))."/languages/" );
}

// Common Function
if(!function_exists('wopb_function')) {
    function wopb_function() {
        require_once WOPB_PATH . 'classes/Functions.php';
        return new \WOPB\Functions();
    }
}

// Plugin Initialization
require_once WOPB_PATH . 'classes/Initialization.php';
new \WOPB\Initialization();

// Template
if (wopb_function()->is_wc_ready()) {
    require_once WOPB_PATH . 'classes/Templates.php';
    new \WOPB\Templates();
}
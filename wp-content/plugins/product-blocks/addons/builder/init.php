<?php
defined( 'ABSPATH' ) || exit;

add_filter('wopb_addons_config', 'wopb_builder_config');
function wopb_builder_config( $config ) {
	$configuration = array(
		'name' => __( 'WooCommerce Builder', 'product-blocks' ),
		'desc' => __( 'Design all essential pages to make visually stunning online stores in WooCommerce.', 'product-blocks' ),
		'img' => WOPB_URL.'assets/img/addons/builder.svg',
		'is_pro' => false,
        'docs' => 'https://docs.wpxpo.com/docs/productx/woocommerce-builder/',
        'live' => 'https://www.wpxpo.com/productx/addons/woocommerce-builder/'
	);
	$config['wopb_builder'] = $configuration;
	return $config;
}

add_action('init', 'wopb_builder_init');
function wopb_builder_init() {
	$settings = wopb_function()->get_setting('wopb_builder');
	if ($settings == 'true') {
		require_once WOPB_PATH.'/addons/builder/Builder.php';
		require_once WOPB_PATH.'/addons/builder/Condition.php';
		require_once WOPB_PATH.'/addons/builder/RequestAPI.php';
		new \WOPB\Builder();
		new \WOPB\Condition();
		new \WOPB\RequestAPI();
	}
	
}

if (wopb_function()->get_setting('wopb_builder') == 'true') {
	add_action( 'after_setup_theme', 'wopb_gallery_image_support' );
	function wopb_gallery_image_support() {
		if(!class_exists('Flatsome_Default')){
            add_theme_support( 'wc-product-gallery-slider' );
        }
	}
}
<?php
defined( 'ABSPATH' ) || exit;

/**
 * Flip Image Addons Initial Configuration
 * @since v.1.1.0
 */
add_filter('wopb_addons_config', 'wopb_flipimage_config');
function wopb_flipimage_config( $config ) {
	$configuration = array(
		'name' => __( 'Product Image Flipper', 'product-blocks' ),
		'desc' => __( 'It allows you to change images when shoppers hover over a product image.', 'product-blocks' ),
		'img' => WOPB_URL.'/assets/img/addons/imageFlip.svg',
		'is_pro' => false,
        'docs' => 'https://docs.wpxpo.com/docs/productx/add-ons/flip-image-settings/',
        'live' => 'https://www.wpxpo.com/productx/addons/product-image-flipper/'
	);
	$config['wopb_flipimage'] = $configuration;
	return $config;
}

/**
 * Require Main File
 * @since v.1.1.0
 */
add_action('wp_loaded', 'wopb_flipimage_init');
function wopb_flipimage_init() {
	$settings = wopb_function()->get_setting();
	if ( isset($settings['wopb_flipimage']) ) {
		if ($settings['wopb_flipimage'] == 'true') {
			require_once WOPB_PATH.'/addons/flipimage/FlipImage.php';
			$obj = new \WOPB\FlipImage();
			if( !isset($settings['flipimage_heading']) ){
				$obj->initial_setup();
			}
		}
	}

	add_filter( 'wopb_settings', 'get_flipimage_settings', 10, 1 );
}

/**
 * FlipImage Addons Default Settings Param
 *
 * @since v.1.1.0
 * @param ARRAY | Default Filter Congiguration
 * @return ARRAY
 */
function get_flipimage_settings($config){
    $arr = array(
        'wopb_flipimage' => array(
            'label' => __('Flip Image', 'product-blocks'),
            'attr' => array(
                'flipimage_heading' => array(
                    'type' => 'heading',
                    'label' => __('Flip Image Settings', 'product-blocks'),
                ),
                'flipimage_source' => array(
                    'type' => 'radio',
                    'label' => __('Flip Image Source', 'product-blocks'),
                    'options' => array(
                        'gallery' => __( 'First Image From Gallery','product-blocks' ),
                    ),
                    'pro' => array(
                        'feature' => __( 'Extra Feature Image Sections','product-blocks' ),
                    ),
                    'default' => 'gallery'
                ),
                'wopb_flipimage' => array(
                    'type' => 'hidden',
                    'value' => 'true'
                )
            )
        )
    );

    return array_merge($config, $arr);
}
<?php
defined( 'ABSPATH' ) || exit;

/**
 * Compare Addons Initial Configuration
 * @since v.1.1.0
 */
add_filter('wopb_addons_config', 'wopb_compare_config');
function wopb_compare_config( $config ) {
	$configuration = array(
		'name' => __( 'Compare Products', 'product-blocks' ),
		'desc' => __( "Let your store's shoppers compare similar products to ensure better buying decision-making.", 'product-blocks' ),
		'img' => WOPB_URL.'/assets/img/addons/compare.svg',
		'is_pro' => false,
        'docs' => 'https://docs.wpxpo.com/docs/productx/add-ons/compare-settings/',
        'live' => 'https://www.wpxpo.com/productx/addons/product-comparison/'
	);
	$config['wopb_compare'] = $configuration;
	return $config;
}

/**
 * Require Main File
 * @since v.1.1.0
 */
add_action('wp_loaded', 'wopb_compare_init');
function wopb_compare_init(){
	$settings = wopb_function()->get_setting();
	if ( isset($settings['wopb_compare']) ) {
		if ($settings['wopb_compare'] == 'true') {
			require_once WOPB_PATH.'/addons/compare/Compare.php';
			$obj = new \WOPB\Compare();
			if( !isset($settings['compare_heading']) ){
				$obj->initial_setup();
			}
		}
	}

	add_filter('wopb_settings', 'get_compare_settings', 10, 1);
}

/**
 * Compare Addons Default Settings Param
 *
 * @since v.1.1.0
 * @param ARRAY | Default Filter Congiguration
 * @return ARRAY
 */
function get_compare_settings($config){

    $arr = array(
        'wopb_compare' => array(
            'label' => __('Compare', 'product-blocks'),
            'attr' => array(
                'compare_heading' => array(
                    'type'  => 'heading',
                    'label' => __('Compare Settings', 'product-blocks'),
                ),
                'compare_page' => array(
                    'type' => 'select',
                    'label' => __('Compare Page', 'product-blocks'),
                    'options' => wopb_function()->all_pages(true),
                    'desc' => '[wopb_compare] '.__('Use shortcode inside compare page.', 'product-blocks')
                ),
                'compare_text' => array(
                    'type' => 'text',
                    'label' => __('Button Text', 'product-blocks'),
                    'default' => __('Compare', 'product-blocks')
                ),
                'compare_added_text' => array(
                    'type' => 'text',
                    'label' => __('Browse Text', 'product-blocks'),
                    'default' => __('Added', 'product-blocks'),
                    'desc' => __('Visible text after clicking add to compare', 'product-blocks')
                ),
                'compare_single_enable' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => 'yes',
                    'desc' => __('Enable Compare in Single Page.', 'product-blocks')
                ),
                'compare_shop_enable' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => 'no',
                    'desc' => __('Enable Compare in Shop Page.', 'product-blocks')
                ),
                'compare_position' => array(
                    'type' => 'radio',
                    'label' => __('Position on Single Page', 'product-blocks'),
                    'options' => array(
                        'before_cart' => __( 'Before Cart','product-blocks' ),
                        'after_cart' => __( 'After Cart','product-blocks' ),
                    ),
                    'default' => 'after_cart'
                ),
                'compare_action_added' => array(
                    'type' => 'radio',
                    'label' => __('Action after Added', 'product-blocks'),
                    'options' => array(
                        'popup' => __( 'Popup','product-blocks' ),
                        'redirect' => __( 'Redirect to Page','product-blocks' ),
                    ),
                    'default' => 'popup'
                ),
                'wopb_compare' => array(
                    'type' => 'hidden',
                    'value' => 'true'
                )
            )
        )
    );

    return array_merge($config, $arr);
}
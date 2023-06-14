<?php
defined( 'ABSPATH' ) || exit;

/**
 * Quickview Addons Initial Configuration
 * @since v.1.1.0
 */
add_filter('wopb_addons_config', 'wopb_quickview_config');
function wopb_quickview_config( $config ) {
	$configuration = array(
		'name' => __( 'WooCommerce Quick View', 'product-blocks' ),
		'desc' => __( 'It allows your shoppers to see product details at a glance without opening the product page.', 'product-blocks' ),
		'img' => WOPB_URL.'/assets/img/addons/quickview.svg',
		'is_pro' => false,
        'docs' => 'https://docs.wpxpo.com/docs/productx/add-ons/quickview-settings/',
        'live' => 'https://www.wpxpo.com/productx/addons/quick-view/'
	);
	$config['wopb_quickview'] = $configuration;
	return $config;
}

/**
 * Require Main File
 * @since v.1.1.0
 */
add_action('wp_loaded', 'wopb_quickview_init');
function wopb_quickview_init(){
	$settings = wopb_function()->get_setting();
	if ( isset($settings['wopb_quickview']) ) {
		if ($settings['wopb_quickview'] == 'true') {
			require_once WOPB_PATH.'/addons/quickview/Quickview.php';
			$obj = new \WOPB\Quickview();
			if( !isset($settings['quickview_heading']) ){
				$obj->initial_setup();
			}
		}
	}

	add_filter('wopb_settings', 'get_quickview_settings', 10, 1);
}

/**
 * Quickview Addons Default Settings Param
 *
 * @param ARRAY | Default Filter Congiguration
 * @return ARRAY
 * @since v.1.1.0
 */
function get_quickview_settings($config)
{
    $arr = array(
        'wopb_quickview' => array(
            'label' => __('QuickView', 'product-blocks'),
            'attr' => array(
                'quickview_heading' => array(
                    'type' => 'heading',
                    'label' => __('Quick View Settings', 'product-blocks'),
                ),
                'quickview_mobile_disable' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => '',
                    'desc' => __('Disable Quickview on Mobile.', 'product-blocks')
                ),
                'quickview_navigation' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => 'yes',
                    'desc' => __('Enable Product Navigation.', 'product-blocks')
                ),
                'quickview_shop_enable' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => 'no',
                    'desc' => __('Enable Quickview in default Shop Page.', 'product-blocks')
                ),
                'quickview_link' => array(
                    'type' => 'switch',
                    'label' => __('Enable / Disable', 'product-blocks'),
                    'default' => 'yes',
                    'desc' => __('Enable Product Link.', 'product-blocks')
                ),
//                    'quickview_gallery_enable' => array(
//                        'type' => 'switch',
//                        'label' => __('Enable / Disable', 'product-blocks'),
//                        'default' => 'yes',
//                        'pro' => true,
//                        'desc' => __('Enable Gallery Images.', 'product-blocks')
//                    ),
//                    'quickview_cart_redirect' => array(
//                        'type' => 'switch',
//                        'label' => __('Enable / Disable', 'product-blocks'),
//                        'default' => '',
//                        'desc' => __('Enable Cart Redirect.', 'product-blocks')
//                    ),
                'quickview_text' => array(
                    'type' => 'text',
                    'label' => __('Quick View Text', 'product-blocks'),
                    'default' => __('Quick View', 'product-blocks'),
                ),
                'quickview_image_disable' => array(
                    'type' => 'switch',
                    'label' => __('Disable Element in QuickView', 'product-blocks'),
                    'default' => '',
                    'desc' => __('Image', 'product-blocks')
                ),
                'quickview_sales_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Sales', 'product-blocks')
                ),
                'quickview_rating_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Rating', 'product-blocks')
                ),
                'quickview_title_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Title', 'product-blocks')
                ),
                'quickview_price_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Price', 'product-blocks')
                ),
                'quickview_stock_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Stock', 'product-blocks')
                ),
                'quickview_excerpt_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Excerpt', 'product-blocks')
                ),
                'quickview_cart_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Add to Cart', 'product-blocks')
                ),
                'quickview_meta_disable' => array(
                    'type' => 'switch',
                    'label' => '',
                    'default' => '',
                    'desc' => __('Meta', 'product-blocks')
                ),
//                    'quickview_sku_disable' => array(
//                        'type' => 'switch',
//                        'label' => '',
//                        'default' => '',
//                        'desc' => __('SKU', 'product-blocks')
//                    ),
//                    'quickview_category_disable' => array(
//                        'type' => 'switch',
//                        'label' => '',
//                        'default' => '',
//                        'desc' => __('Category', 'product-blocks')
//                    ),
//                    'quickview_tag_disable' => array(
//                        'type' => 'switch',
//                        'label' => '',
//                        'default' => '',
//                        'desc' => __('Tag', 'product-blocks')
//                    ),
                'wopb_quickview' => array(
                    'type' => 'hidden',
                    'value' => 'true'
                )
            )
        )
    );

    return array_merge($config, $arr);
}
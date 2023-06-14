<?php
/**
 * Compatibility Action.
 * 
 * @package WOPB\Notice
 * @since v.1.1.0
 */
namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Compatibility class.
 */
class Compatibility{

    /**
	 * Setup class.
	 *
	 * @since v.1.1.0
	 */
    public function __construct(){
        add_action( 'upgrader_process_complete', array($this, 'plugin_upgrade_completed'), 10, 2 );
         if (class_exists('KTP_Requirements_Check')) {
            $this->handle_kadence_element();
        }
    }

    /**
	 * Compatibility to handle kadence element
	 *
	 * @since v.2.5.5
	 */
    public function handle_kadence_element() {
        $hook_lists = array(
            'replace_header'                                => 'kadence_header',
			'replace_footer'                                => 'kadence_footer',
			'replace_hero_header'                           => 'kadence_hero_header',
			'replace_404'                                   => 'kadence_404_content',
			'replace_single_content'                        => 'kadence_single_content',
			'replace_loop_content'                          => 'kadence_loop_entry',
			'woocommerce_before_single_product_image'       => 'woocommerce_before_single_product_summary',
			'woocommerce_after_single_product_image'        => 'woocommerce_before_single_product_summary',
			'replace_login_modal'                           => 'kadence_account_login_form',
			'fixed_above_trans_header'                      => 'kadence_before_wrapper',
			'fixed_above_header'                            => 'kadence_before_header',
			'fixed_on_header'                               => 'kadence_after_wrapper',
			'fixed_below_footer'                            => 'kadence_after_footer',
			'fixed_on_footer'                               => 'kadence_after_wrapper',
			'fixed_on_footer_scroll'                        => 'kadence_after_wrapper',
			'fixed_on_footer_scroll_space'                  => 'kadence_after_wrapper',
		);
        $args = array(
			'post_type'              => 'kadence_element',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'post_status'            => 'publish',
			'numberposts'            => 333,
			'order'                  => 'ASC',
			'orderby'                => 'menu_order',
			'suppress_filters'       => false,
		);

        $posts = get_posts( $args );
		foreach( $posts as $post) {
			$post_id = $post->ID;
			$post_hook_meta = get_post_meta($post_id , ( get_post_meta($post_id , '_kad_element_hook', true) == 'custom' ? '_kad_element_hook_custom' : '_kad_element_hook' ), true);
            if ( $post_hook_meta ) {
                $selected_hook = isset($hook_lists[$post_hook_meta]) ? $hook_lists[$post_hook_meta] : $post_hook_meta;
				add_action($selected_hook , function() use (&$post_id) {
					wopb_function()->register_scripts_common();
					wopb_function()->set_css_style($post_id);
				});
			}
		}
    }

    /**
	 * Compatibility Class Run after Plugin Upgrade
	 *
	 * @since v.1.1.0
	 */
    public function plugin_upgrade_completed( $upgrader_object, $options ) {
        if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ){
            foreach( $options['plugins'] as $plugin ) {
                if( $plugin == WOPB_BASE ) {
                    
                    $set_settings = array(
                        'wopb_compare'    => 'true',
                        'wopb_flipimage'  => 'true',
                        'wopb_quickview'  => 'true',
                        'wopb_templates'  => 'true',
                        'wopb_wishlist'   => 'true',
                        'wopb_builder'    => 'true',
                        'wopb_variation_swatches' => 'true',
                        'save_version' => rand(1, 1000),
                        'disable_google_font' => '',
                    );
                    $addon_data = wopb_function()->get_setting();
                    foreach ($set_settings as $key => $value) {
                        if (!isset($addon_data[$key])) {
                            wopb_function()->set_setting($key, $value);
                        }
                    }

                    // License Check And Active
                    if (defined('WOPB_PRO_VER')) {
                        $license = get_option( 'edd_wopb_license_key' );
                        $response = wp_remote_post( 
                            'https://www.wpxpo.com',
                            array(
                                'timeout' => 15,
                                'sslverify' => false,
                                'body' => array(
                                    'edd_action' => 'activate_license',
                                    'license'    => $license,
                                    'item_id'    => 1263,
                                    'url'        => home_url()
                                )
                            )
                        );
                        if ( !is_wp_error( $response ) && 200 == wp_remote_retrieve_response_code( $response ) ) {
                            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                            update_option( 'edd_wopb_license_status', $license_data->license );    
                        }
                    }
                    
                }
            }
        }
    }

}
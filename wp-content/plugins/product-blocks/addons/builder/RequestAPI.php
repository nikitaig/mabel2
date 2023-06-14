<?php
namespace WOPB;

defined('ABSPATH') || exit;

class RequestAPI{

    private $api_endpoint = 'https://wopb.wpxpo.com/wp-json/restapi/v2/';

    public function __construct() {
        add_action('rest_api_init', array($this, 'get_template_data'));
        add_action('draft_to_publish', array($this, 'builder_draft_to_publish'), 100, 1);
    }

    /**
	 * Create Builder Post Type
     *
     * @since v.2.3.9
	 * @return NULL
	 */
    public function get_template_data() {
        register_rest_route(
			'wopb/v2',
			'/get_single_premade/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array( $this, 'get_single_premade_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
        );
        register_rest_route(
			'wopb/v2',
			'/condition/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'condition_settings_action'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);
        register_rest_route(
			'wopb/v2',
			'/condition_save/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'condition_save_action'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);
        register_rest_route(
			'wopb/v2',
			'/data_builder/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'data_builder_action'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);
        register_rest_route(
			'wopb/v2',
			'/template_action/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'template_page_action'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);

    }

    /**
	 * Single Premade Data and Create Builder Posts
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return ARRAY | Data of the Premade
	 */
    public function get_single_premade_callback($server) {

        $is_active = wopb_function()->is_lc_active();
        $obj_count = wp_count_posts('wopb_builder');
        if (!$is_active) {
            $p_count = isset($obj_count->publish) ? $obj_count->publish : 0;
            $d_count = isset($obj_count->draft) ? $obj_count->draft : 0;
            if (($p_count + $d_count) > 0) {
                return array( 'success' => false );
            }
        }

        $post = $server->get_params();
        $id = isset($post['ID']) ? sanitize_text_field($post['ID']) : '';

        if ($id) {
            $response = wp_remote_post( $this->api_endpoint.'single-premade', array(
                'method' => 'POST',
                'timeout' => 120,
                'body' => array( 'section_id' => $id, 'license' => get_option('edd_wopb_license_key') )
            ));
            if (is_wp_error( $response ) ) {
                return array( 'success' => false, 'data' => "Something went wrong:" . $response->get_error_message() );
            } else {
                if (isset($response['body']) && isset($post['type'])) {
                    $body = json_decode($response['body']);
                    if (isset($body->rawData) && isset($body->success) && $body->success) {
                        $post_id = $this->set_post_content($post['type'], wp_slash($body->rawData));
                        return array( 'success' => true, 'link' => get_edit_post_link($post_id) );
                    } else {
                        return array( 'success' => false );
                    }
                }
            }
        } else {
            $post_id = $this->set_post_content($post['type'], '');
            return array( 'success' => true, 'link' => get_edit_post_link($post_id) );
        }
    }


    /**
	 * Condition Settings Actions
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return ARRAY | Data of the Condition
	 */
    public function condition_settings_action($server) {
        global $wpdb;
        $post = $server->get_params();
        $search_type = explode('###', $post['type']);

        switch ($search_type[0]) {
            case 'type':
                $args = array(
                    'post_type'         => $search_type[1],
                    'post_status'       => 'publish',
                    'orderby'           => 'title',
                    'order'             => 'ASC',
                    's'                 => $post['term'],
                    'posts_per_page'    => 10,
                );
                if ($post['title_return']){
                    unset($args['s']);
                    $args['p'] = $post['term'];
                }
                $post_results = new \WP_Query($args);
                $title = '';
                $data = [];
                if (!empty($post_results)) {
                    while ( $post_results->have_posts() ) {
                        $post_results->the_post();
                        $id = get_the_ID();
                        $title = get_the_title();
                        $data[] = array('value'=>$id, 'title'=>($title?$title:('##'.$id)));
                    }
                    wp_reset_postdata();
                }
                return ['success' => true, 'data' => ($post['title_return'] ? $title : $data )];
            break;

            case 'term':
                $args = array(
                    'taxonomy'  => $search_type[1],
                    'fields'    => 'all',
                    'orderby'   => 'id',
                    'order'     => 'ASC',
                    'name__like'=> $post['term']
                );
                if ($post['title_return']) {
                    $args['term_taxonomy_id'] = array($post['term']);
                    unset($args['name__like']);
                }
                $post_results = get_terms( $args );
                $title = '';
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $title = $val->name;
                        $data[] = array('value'=>$val->term_id, 'title'=>$title);
                    }
                }
                return ['success' => true, 'data' => ($post['title_return'] ? $title : $data )];
            break;

            case 'author':
                $term = $post['title_return'] ? $wpdb->esc_like( $post['term'] ) : '%'. $wpdb->esc_like( $post['term'] ) .'%';
                $post_results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT ID, display_name 
                        FROM $wpdb->users 
                        WHERE user_login LIKE '%s' OR ID LIKE '%s' OR user_nicename LIKE '%s' OR user_email LIKE '%s' OR display_name LIKE '%s' LIMIT 10", $term, $term, $term, $term, $term
                    )
                );
                $title = '';
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $title = $val->display_name;
                        $data[] = array('value'=>$val->ID, 'title'=>$val->display_name);
                    }
                }
                return ['success' => true, 'data' => ($post['title_return'] ? $title : $data )];
            break;
            default:
                return ['success' => false];
            break;
        }
        return ['success' => true, 'data' => 'This is Testing !!!'];
    }

    /**
	 * Save Conditions Data
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return ARRAY | Message of the Condition Success
	 */
    public function condition_save_action($server) {
        $post = $server->get_params();
        if (isset($post['settings'])) {
            update_option('wopb_builder_conditions', $post['settings']);
            return [
                    'success' => true,
                    'data' => 'Settings Saved!!!'
            ];
        }
    }


    /**
	 * Builder Post Type Data
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return ARRAY | Information of Builder Post
	 */
    public function data_builder_action($server) {
        $post = $server->get_params();
        $args = array(
            'post_type'         => 'wopb_builder',
            'post_status'       => array('publish', 'draft'),
            'orderby'           => 'title',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
        );
        $post_results = new \WP_Query($args);
        $post_list = [];
        $settings = get_option('wopb_builder_conditions', array());
        if (!empty($post_results)) {
            while ( $post_results->have_posts() ) {
                $post_results->the_post();
                $id = get_the_ID();
                $meta_type = get_post_meta( $id, '_wopb_builder_type', true );

                //condition check for existing builder version before 2.3.9
                if($meta_type === 'single-product') {
                    $type = 'single_product';
                    if($settings['archive'][$id][0] == 'filter/single-product') {
                        $settings[$type][$id] = $settings['archive'][$id];
                        if($settings[$type][$id][1] == 'include/allsingle') {
                           $settings[$type][$id][1] = 'include/single_product/product' ;
                        }
                        unset($settings[$type][$id][0]);
                        $settings[$type][$id] = array_values($settings[$type][$id]);
                    }
                }elseif($meta_type === 'shop') {
                    $type = 'shop';
                }elseif($meta_type === 'cart') {
                    $type = 'cart';
                }else {
                    $type = $meta_type ? $meta_type : 'archive';
                    if($type == 'archive' && $settings[$type][$id][0] == 'filter/archive') {
                        unset($settings[$type][$id][0]);
                        $settings[$type][$id] = array_values($settings[$type][$id]);
                    }
                }

                $post_list[] = array(
                    'id' => $id,
                    'title' => get_the_title(),
                    'author' => get_the_author_meta('display_name'),
                    'date' => get_the_date( get_option('date_format')),
                    'edit' => get_edit_post_link($id),
                    'type' => $type,
                    'label' => str_replace('_', ' ', $type),
                    'status' => get_post_status(),
                );
            }
            wp_reset_postdata();
        }

        $arg = [
            'success' => true,
            'postlist' => $post_list,
            'settings' => $settings,
            'defaults' => wopb_function()->builder_data(),
            // 'new_url' => add_query_arg(array('post_type'=>'wopb_builder'),admin_url('post-new.php'))
        ];
        if (isset($post['pid']) && $post['pid']) {
            $post_meta = get_post_meta( $post['pid'], '_wopb_builder_type', true );
            if( $post_meta == 'single-product') {
                $arg['type'] = 'single_product';
            }else {
                $arg['type'] = $post_meta ? $post_meta : 'archive';
            }
        }
        return $arg;
    }

    /**
	 * Delete Template Page
     *
     * @since v.2.3.9
     * @param STRING
	 * @return ARRAY | Success Message
	 */
    public function template_page_action($server) {
        $post = $server->get_params();
        if (isset($post['type']) && isset($post['id']) && $post['id']) {
            if ($post['type'] == 'delete') {
                wp_delete_post( $post['id'], true);
            } else if ($post['type'] == 'status') {
                if ($post['status']) {
                    wp_update_post(array(
                        'ID' => $post['id'],
                        'post_status' => $post['status']
                    ));
                }
            }
        }
        return array( 'success' => true);
    }

     /**
	 * Single Premade Data and Insert Builder Posts
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return INT | Post ID
	 */
    public function set_post_content($type, $body = '') {
        $post_id = wp_insert_post(
            array(
                'post_title'   => ucwords(str_replace('_', ' ', $type)) . ' Template',
                'post_content' => $body,
                'post_type'    => 'wopb_builder',
                'post_status'  => 'draft'
            )
        );
        $settings = get_option('wopb_builder_conditions', array());
        switch ($type) {
            case 'home_page':
                update_post_meta($post_id, '_wopb_builder_type', 'home_page');
                $settings['home_page'][$post_id] = ['filter/home_page'];
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'single_product':
                update_post_meta($post_id, '_wopb_builder_type', 'single_product');
                $settings['single_product'][$post_id] = ['include/single_product/product'];
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'shop':
                update_post_meta($post_id, '_wopb_builder_type', 'shop');
                $settings['shop'][$post_id] = ['filter/shop'];
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'cart':
                update_post_meta($post_id, '_wopb_builder_type', 'cart');
                $settings['cart'][$post_id] = ['filter/cart'];
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'checkout':
                update_post_meta($post_id, '_wopb_builder_type', 'checkout');
                $settings['checkout'][$post_id] = ['filter/checkout'];
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'my_account':
                update_post_meta($post_id, '_wopb_builder_type', 'my_account');
                $settings['my_account'][$post_id] = ['filter/my_account'];
                wp_update_post([
                    'ID'         => $post_id,
                    'post_title' => 'My Account Template'
                ]);
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'thank_you':
                update_post_meta($post_id, '_wopb_builder_type', 'thank_you');
                $settings['thank_you'][$post_id] = ['filter/thank_you'];
                wp_update_post([
                    'ID'         => $post_id,
                    'post_title' => 'Thank You Template'
                ]);
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'product_search':
                update_post_meta($post_id, '_wopb_builder_type', 'product_search');
                $settings['product_search'][$post_id] = ['filter/product_search'];
                wp_update_post([
                    'ID'         => $post_id,
                    'post_title' => 'Product Search Result Template'
                ]);
                update_option('wopb_builder_conditions', $settings);
                break;
            case 'archive':
            case 'product_cat':
            case 'product_tag':
                update_post_meta($post_id, '_wopb_builder_type', 'archive');
                $extra = $type != 'archive' ? '/'.$type : '';
                $settings['archive'][$post_id] = ['include/archive'.$extra];
                if($type == 'product_cat') {
                     wp_update_post([
                        'ID'         => $post_id,
                        'post_title' => 'Product Category Template'
                    ]);
                }else if($type == 'product_tag') {
                    wp_update_post([
                        'ID'         => $post_id,
                        'post_title' => 'Product Tag Template'
                    ]);
                }
                update_option('wopb_builder_conditions', $settings);
                break;
            default:
                break;
        }
        return $post_id;
    }

    public function builder_draft_to_publish($post) {
        if(get_post_meta( $post->ID, '_wopb_builder_type', true ) == 'home_page' && get_option('show_on_front') == 'page') {
            update_option('show_on_front', 'posts');
        }
    }
}
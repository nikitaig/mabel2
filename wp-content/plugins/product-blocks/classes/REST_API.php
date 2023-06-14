<?php
/**
 * REST API Action.
 * 
 * @package WOPB\REST_API
 * @since v.1.0.0
 */
namespace WOPB;

use WOPB\blocks\Product_Search;

defined('ABSPATH') || exit;

/**
 * Styles class.
 */
class REST_API{
    
    /**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
    public function __construct() {
        add_action( 'rest_api_init', array($this, 'wopb_register_route') );
    }


    /**
	 * REST API Action
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
    public function wopb_register_route() {
        register_rest_route( 'wopb', 'posts', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array(
                    'queryNumber'=>[], 'queryType'=>[], 'queryCat'=>[], 'queryOrderBy'=>[], 'queryOrder'=>[], 'queryOffset'=>[], 'queryInclude'=>[], 'queryExclude'=>[], 'queryStatus'=>[], 'queryQuick'=>[], 'filterCat'=>[], 'filterTag'=>[], 'filterShow'=>[], 'filterType'=>[], 'filterAction'=>[], 'filterText'=>[], 'queryExcludeStock'=>[], 'wpnonce' => []
                ),
                'callback' => array($this, 'wopb_route_post_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'category', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('queryCat' => [], 'queryNumber' => [], 'queryType' => [], 'wpnonce' => []),
                'callback' => array($this, 'wopb_route_category_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'common', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('wpnonce' => []),
                'callback' => array($this,'wopb_route_common_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'tax_info', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('taxonomy' => [], 'wpnonce' => []),
                'callback' => array($this, 'wopb_route_taxonomy_info_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'preview', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('previews'=>[], 'type'=>[], 'wpnonce' => []),
                'callback' => array($this, 'wopb_route_preview_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'stock-status', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('queryNumber' => [], 'wpnonce' => []),
                'callback' => array($this, 'wopb_route_stock_status_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route( 'wopb', 'get-product-attributes', array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array('queryNumber' => [], 'wpnonce' => []),
                'callback' => array($this, 'wopb_route_product_attributes_data'),
                'permission_callback' => '__return_true'
            )
        );
        register_rest_route(
			'wopb/v2',
			'/template_page_insert/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'template_page_insert'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);
        register_rest_route(
			'wopb/v1',
			'/search/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'search_settings_action'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);
        register_rest_route(
			'wopb',
			'/product-filter/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'product_filter'),
					'permission_callback' => '__return_true',
					'args' => array()
				)
			)
		);
//        register_rest_route(
//			'wopb',
//			'/quick-view/',
//			array(
//				array(
//					'methods'  => 'POST',
//					'callback' => array($this, 'quick_view'),
//                    'permission_callback' => '__return_true',
//					'args' => array()
//				)
//			)
//		);

        register_rest_route( 'wopb', 'product-search', array(
                'methods'  => 'POST',
                'callback' => array($this, 'product_search'),
                'permission_callback' => '__return_true'
            )
        );
    }


    /**
	 * Taxonomy Data Response of REST API
     * 
     * @since v.2.3.7
     * @param ARRAY | Parameter (ARRAY)
	 * @return ARRAY | \WP_REST_Response Taxonomy List as Array
	 */
    public function wopb_route_common_data($prams) {
        if (!wp_verify_nonce(wp_unslash($_REQUEST['wpnonce']), 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        // Global Customizer
        $global = get_option('productx_global', []);
        // Image Size
        $image_sizes = wopb_function()->get_image_size();

        $post_type_data = array();
        $post_types = wopb_function()->get_post_type();
        foreach ($post_types as $post_type_slug => $post_type ) {
            $taxonomies = $post_type_slug == 'product' ? array_diff(get_object_taxonomies($post_type_slug), ['product_type', 'product_visibility', 'product_shipping_class']) : get_object_taxonomies($post_type_slug);
            $taxonomy_array = array();
            foreach ($taxonomies as $key => $taxonomy_slug) {
                $taxonomy = get_taxonomy($taxonomy_slug);
                $terms = get_terms(array(
                    'taxonomy' => $taxonomy_slug,
                    'hide_empty' => false
                ));
                $term_array = array();
                if (!is_wp_error($terms)) {
                    foreach ($terms as $k => $term) {
                        $term_array[urldecode_deep($term->slug)] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                        ];
                    }
                }
                $taxonomy_array[] = [
                    'name' => $taxonomy->name,
                    'label' => $taxonomy->label,
                    'terms' => $term_array,
                ];
            }
            $post_type_data[$post_type_slug] = $taxonomy_array;
        }

        return rest_ensure_response([
            'tag' => wopb_function()->taxonomy('product_tag'),
            'cat' => wopb_function()->taxonomy('product_cat'),
            'global' => $global,
            'image' => $image_sizes,
            'post_type_taxonomy' => $post_type_data,
            'post_type' => json_encode($post_types),
            'stock_status' => wc_get_product_stock_status_options(),
        ]);
    }

    /**
	 * Builder Preview Data
     * 
     * @since v.2.3.7
     * @param ARRAY | Parameters of the REST API
	 * @return ARRAY | Response as Array
	 */
    public function wopb_route_preview_data($prams){
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        global $product;
        $post_id = isset($prams['previews']) ? $prams['previews'] : '';
        if ($post_id) {
            $post_data = array();
            $products = wc_get_product($post_id);
            switch ($prams['type']) {
                case 'title':
                    $post_data['title'] = $products->get_title();
                    break;
                case 'description':
                    $post_data['description'] = $products->get_description();
                    break;
                case 'short':
                    $post_data['short'] = $products->get_short_description();
                    break;
                case 'image':
                    $url = $this->get_images($products);
                    $sales = $products->get_sale_price();
                    $regular = $products->get_regular_price();
                    $post_data['images'] = $url;
                    $post_data['images_thumb'] = $url;
                    $post_data['percentage'] = ($regular && $sales) ? round((($regular - $sales) / $regular) * 100) : 0;
                    break;
                case 'meta':
                    $post_data['sku'] = $products->get_sku();
                    $post_data['category'] = '<div className="meta-block__cat">'.wp_kses_post($this->list_items($products->get_category_ids(), 'product_cat')).'</div>';
                    $post_data['tag'] = '<div className="meta-block__tag">'.wp_kses_post($this->list_items($products->get_tag_ids(), 'product_tag')).'</div>';
                    break;
                case 'price':
                    $sales = $products->get_sale_price();
                    $regular = $products->get_regular_price();
                    $post_data['sales_price'] = $sales;
                    $post_data['regular_price'] = $regular;
                    $post_data['percentage'] = ($regular && $sales) ? round((($regular - $sales) / $regular) * 100) : 0;
                    $post_data['symbol'] = get_woocommerce_currency_symbol();
                    break;
                case 'rating':
                    $post_data['sales'] = $products->get_total_sales();
                    $post_data['rating'] = $products->get_review_count();
                    $post_data['average'] = ( ( $products->get_average_rating() / 5 ) * 100 ).'%';
                    break;
            }
            return array('type' => 'data', 'data' => $post_data);
        }

		$data = array( 
            array(
                'value' => '', 
                'label' => '-- Select Product --'
            )
        );
        $products = wc_get_products(['posts_per_page' => 10, 'post_status' => 'publish']);
		foreach ($products as $key => $val) {
			$data[] = array('value' => $val->get_id(), 'label' => $val->get_title());
		}
        return array('type' => 'list', 'list' => $data);
    }


    public function get_images($val) {
        $data = array();
        $feature_image = $val->get_image_id();
        if (!empty($feature_image)) {
            $data[] = esc_url(wp_get_attachment_image_url($feature_image, 'full'));
        }
        $galleries = $val->get_gallery_image_ids();
        if (!empty($galleries)) {
            foreach ($galleries as $key => $val) {
                $data[] = esc_url(wp_get_attachment_image_url($val, 'full'));
            }
        }
        return $data;
    }


    public function list_items($terms, $type) {
        $inc = 1;
        $content = '';
        foreach ($terms as $term_id) {
            $term = get_term_by('id', $term_id, $type);
            if ($inc > 1) {
                $content .= ', ';
            }
            $content .= '<a class="meta-block__value" href="#">'.esc_html($term->name).'</a>';
            $inc++;
        }
        return $content;
    }


    /**
	 * REST API Action
     * 
     * @since v.1.0.0
     * @param ARRAY | Parameters of the REST API
	 * @return ARRAY | Response as Array
	 */
    public function wopb_route_category_data($prams){
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        $data = wopb_function()->get_category_data(json_decode($prams['queryCat']), $prams['queryNumber'], $prams['queryType']);
        return rest_ensure_response( $data );
    }


    /**
	 * Post Data Response of REST API
     * 
     * @since v.1.0.0
     * @param MIXED | Pram (ARRAY), Local (BOOLEAN)
	 * @return ARRAY | Response Image Size as Array
	 */
    public function wopb_route_post_data($prams,$local=false) {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce') && $local){
            return ;
        }
    
        $data = [];
        $loop = new \WP_Query( wopb_function()->get_query( $prams ) );

        if($loop->have_posts()){
            while($loop->have_posts()) {
                $loop->the_post();
                $var                = array();
                $post_id            = get_the_ID();
                $product            = wc_get_product($post_id);
                $user_id            = get_the_author_meta('ID');
                $var['title']       = get_the_title();
                $var['permalink']   = get_permalink();
                $var['excerpt']     = $product->get_short_description();
                $var['time']        = get_the_date();
                $var['price_sale']  = $product->get_sale_price();
                $var['price_regular']= $product->get_regular_price();
                $var['discount']    = ($var['price_sale'] && $var['price_regular']) ? round( ( $var['price_regular'] - $var['price_sale'] ) / $var['price_regular'] * 100 ).'%' : '';
                $var['sale']        = $product->is_on_sale();
                $var['price_html']  = $product->get_price_html();
                $var['stock']       = $product->get_stock_status();
                $var['featured']    = $product->is_featured();
                $var['rating_count']= $product->get_rating_count();
                $var['rating_average']= $product->get_average_rating();
                $var['type']= $product->get_type();
                $var['wishlist'] = wopb_function()->get_setting('wopb_wishlist') == 'true' ? true : false;
                $var['flipimage'] = wopb_function()->get_flip_image($post_id, $var['title'], 'full', false);

                $time = current_time('timestamp');
		        $time_to = strtotime($product->get_date_on_sale_to());
                $var['deal'] = ($var['price_sale'] && $time_to > $time) ? date('Y/m/d', $time_to) : '';


                // image
                if( has_post_thumbnail() ){
                    $thumb_id = get_post_thumbnail_id($post_id);
                    $image_sizes = wopb_function()->get_image_size();
                    $image_src = array();
                    foreach ($image_sizes as $key => $value) {
                        $image_src[$key] = esc_url(wp_get_attachment_image_src($thumb_id, $key, false)[0]);
                    }
                    $var['image'] = $image_src;
                } else {
                    $var['image']['full'] = esc_url(wc_placeholder_img_src('full'));
                }

                // tag
                $tag = get_the_terms($post_id, (isset($prams['tag'])?esc_attr($prams['tag']):'product_tag'));
                if(!empty($tag)){
                    $v = array();
                    foreach ($tag as $val) {
                        $v[] = array('slug' => $val->slug, 'name' => $val->name, 'url' => esc_url(get_term_link($val->term_id)));
                    }
                    $var['tag'] = $v;
                }

                // cat
                $cat = get_the_terms($post_id, (isset($prams['cat'])?esc_attr($prams['cat']):'product_cat'));
                if(!empty($cat)){
                    $v = array();
                    foreach ($cat as $val) {
                        $v[] = array('slug' => $val->slug, 'name' => $val->name, 'url' => esc_url(get_term_link($val->term_id)));
                    }
                    $var['category'] = $v;
                }
                $data[] = $var;
            }
            wp_reset_postdata();
        }
        return rest_ensure_response( $data );
    }

    
    /**
	 * Specific Taxonomy Data Response of REST API
     * 
     * @since v.1.0.0
     * @param ARRAY | Parameter (ARRAY)
	 * @return ARRAY | Response Taxonomy List as Array
	 */
    public function wopb_route_taxonomy_info_data($prams) {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        $data = array();
        $terms = get_terms( $prams, array(
            'hide_empty' => true,
        ));
        if( !empty($terms) ){
            foreach ($terms as $val) {
                $data['name'] = $val->name;
                $data['count'] = $val->count;
                $data['url'] = get_term_link($val->term_id);
                $data['color'] = get_term_meta($val->term_id, '_background', true);
            }
        }
        return rest_ensure_response($data);
    }

    /**
	 * REST API Action
     *
     * @since v.1.0.0
     * @param ARRAY | Parameters of the REST API
	 * @return ARRAY | Response as Array
	 */
    public function wopb_route_stock_status_data($params){
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        $data = wopb_function()->get_stock_status_data();
        return rest_ensure_response( $data );
    }

    /**
	 * REST API Action
     *
     * @since v.1.0.0
     * @param ARRAY | Parameters of the REST API
	 * @return ARRAY | Response as Array
	 */
    public function wopb_route_product_attributes_data($params){
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'wopb-nonce')){
            return rest_ensure_response([]);
        }

        $data = wopb_function()->get_product_attributes_data();
        return rest_ensure_response( $data );
    }

    /**
	 * Insert Post For Imported Template
     *
     * @since v.2.3.8
     * @param STRING
	 * @return ARRAY | Inserted Post Url
	 */
    public function template_page_insert($server) {
        $post = $server->get_params();
        $new_page = array(
            'post_title' => $post['title'],
            'post_type' => 'page',
            'post_content' => wp_slash($post['blockCode']),
            'post_status' => 'draft',
        );
        $post_id = wp_insert_post( $new_page );
        return array( 'success' => true, 'link' => get_edit_post_link($post_id));
    }

    /**
	 * Search from editor settings
     *
     * @since v.2.6.0
     * @param STRING
	 * @return ARRAY | Inserted Post Url
	 */
    public function search_settings_action($server) {
		global $wpdb;
        $post = $server->get_params();
        switch ($post['type']) {
            case 'posts':
            case 'allpost':
            case 'postExclude':
                $post_type = array('post');
                if ($post['type'] == 'allpost') {
                    $post_type = array_keys(wopb_function()->get_post_type());
                } else if ($post['type'] == 'postExclude') {
                    $post_type = array($post['condition']);
                }
                $args = array(
                    'post_type'         => $post_type,
                    'post_status'       => 'publish',
                    'posts_per_page'    => 10,
                );
                if (is_numeric($post['term'])) {
                    $args['p'] = $post['term'];
                } else {
                    $args['s'] = $post['term'];
                }

                $post_results = new \WP_Query($args);
                $data = [];
                if (!empty($post_results)) {
                    while ( $post_results->have_posts() ) {
                        $post_results->the_post();
                        $id = get_the_ID();
                        $title = html_entity_decode(get_the_title());
                        $data[] = array('value'=>$id, 'title'=>($title?'[ID: '.$id.'] '.$title:('[ID: '.$id.']')));
                    }
                    wp_reset_postdata();
                }
                return ['success' => true, 'data' => $data];
                break;

            case 'product':
            case 'allproduct':
            case 'productInclude':
            case 'productExclude':
                $post_type = array('product');
                if ($post['type'] == 'allproduct') {
                    $post_type = array_keys(wopb_function()->get_post_type());
                } else if ($post['type'] == 'productInclude' || $post['type'] == 'productExclude') {
                    $post_type = array($post['condition']);
                }
                $args = array(
                    'post_type'         => $post_type,
                    'post_status'       => 'publish',
                    'posts_per_page'    => 10,
                );
                if (is_numeric($post['term'])) {
                    $args['p'] = $post['term'];
                } else {
                    $args['s'] = $post['term'];
                }

                $post_results = new \WP_Query($args);
                $data = [];
                if (!empty($post_results)) {
                    while ( $post_results->have_posts() ) {
                        $post_results->the_post();
                        $id = get_the_ID();
                        $title = strip_tags(html_entity_decode(get_the_title()));
                        $data[] = array('value'=>$id, 'title'=>($title?'[ID: '.$id.'] '.$title:('[ID: '.$id.']')));
                    }
                    wp_reset_postdata();
                }
                return ['success' => true, 'data' => $data];
                break;

            case 'author':
                $term = '%'. $wpdb->esc_like( $post['term'] ) .'%';
                $post_results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT ID, display_name 
                        FROM $wpdb->users 
                        WHERE user_login LIKE '%s' OR ID LIKE '%s' OR user_nicename LIKE '%s' OR user_email LIKE '%s' OR display_name LIKE '%s' LIMIT 10", $term, $term, $term, $term, $term
                    )
                );
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $data[] = array('value'=>$val->ID, 'title'=>'[ID: '.$val->ID.'] '.$val->display_name);
                    }
                }
                return ['success' => true, 'data' => $data];
                break;

            case 'taxValue':
                $split = explode('###', $post['condition']);
                $condition = $split[1] != 'multiTaxonomy' ? array($split[1]) : get_object_taxonomies($split[0]);
                $args = array(
                    'taxonomy'  => $condition,
                    'fields'    => 'all',
                    'orderby'   => 'id',
                    'order'     => 'ASC',
                    'name__like'=> $post['term']
                );
                if (is_numeric($post['term'])) {
                    unset($args['name__like']);
                    $args['include'] = array($post['term']);
                }

                $post_results = get_terms( $args );
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $taxonomy = get_taxonomy($val->taxonomy);
                        if ($split[1] == 'multiTaxonomy') {
                            $data[] = array('value'=>$val->taxonomy.'###'.$val->slug, 'title'=> '[ID: '.$val->term_id.'] '.$taxonomy->label.': '.$val->name);
                        } else {
                            $data[] = array('value'=>$val->slug, 'title'=>'[ID: '.$val->term_id.'] '.$val->name);
                        }
                    }
                }
                return ['success' => true, 'data' => $data];
                break;

            case 'taxExclude':
                $condition = get_object_taxonomies($post['condition']);
                $args = array(
                    'taxonomy'  => $condition,
                    'fields'    => 'all',
                    'orderby'   => 'id',
                    'order'     => 'ASC',
                    'name__like'=> $post['term']
                );
                if (is_numeric($post['term'])) {
                    unset($args['name__like']);
                    $args['include'] = array($post['term']);
                }
                $post_results = get_terms( $args );
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $data[] = array('value'=>$val->taxonomy.'###'.$val->slug, 'title'=> '[ID: '.$val->term_id.'] '.$val->taxonomy.': '.$val->name);
                    }
                }
                return ['success' => true, 'data' => $data];
                break;

            case 'product_cat':
            case 'product_tag':
                $split = explode('###', $post['condition']);
                $condition = $split[1] != 'multiTaxonomy' ? array($split[1]) : get_object_taxonomies($split[0]);
                $args = array(
                    'taxonomy'  => $post['type'],
                    'fields'    => 'all',
                    'orderby'   => 'id',
                    'order'     => 'ASC',
                    'name__like'=> $post['term']
                );
                if (is_numeric($post['term'])) {
                    unset($args['name__like']);
                    $args['include'] = array($post['term']);
                }

                $post_results = get_terms( $args );
                $data = [];
                if (!empty($post_results)) {
                    foreach ($post_results as $key => $val) {
                        $taxonomy = get_taxonomy($val->taxonomy);
                        if ($split[1] == 'multiTaxonomy') {
                            $data[] = array('value'=>$val->taxonomy.'###'.$val->slug, 'title'=> '[ID: '.$val->term_id.'] '.$taxonomy->label.': '.$val->name);
                        } else {
                            $data[] = array('value'=>$val->slug, 'title'=>'[ID: '.$val->term_id.'] '.$val->name);
                        }
                    }
                }
                return ['success' => true, 'data' => $data];
                break;

            default:
                return ['success' => true, 'data' => [['value'=>'', 'title'=>'- Select -']]];
                break;
        }
	}

	/**
	 * Get block list by product filtering
     *
     * @since v.2.6.5
     * @param STRING
	 * @return array
	 */
    public function product_filter($server) {
        $params = $server->get_params();
        $product_filters = $params['product_filters'];
        $post_id = sanitize_text_field($params['post_id']);
        $post = get_post($post_id);
        $blockRaw   = sanitize_text_field($params['block_name']);
        $blockName  = str_replace('_','/', $blockRaw);
        $blocks = parse_blocks($post->post_content);
        $params = [
            'page_post_id' => $post_id,
            'current_url' => $params['current_url'],
            'product_filters' => $product_filters,
            'ajax_source' => 'filter',
        ];
        $block_list = [];
        $block = new Blocks();
        $block_list = $block->product_filter_block_target($blocks, $blockName, $blockRaw, $params, $block_list);
        return [
            'blockList' => $block_list
        ];
    }

	/**
	 * Open Quick View
     *
     * @since v.2.6.5
     * @param STRING
	 * @return HTML
	 */
    public function quick_view($server) {
        $params = $server->get_params();
        $quick_view = New Quickview();
        return $quick_view->quickview_content($params);
    }

	/**
	 * Get Product Search Block Search Content
     *
     * @since v.2.6.8
     * @param $server
	 * @return HTML
	 */
	public function product_search($server) {
        $_POST = $server->get_params();
        $blockId    = sanitize_text_field($_POST['blockId']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $blockName  = str_replace('_','/', $blockRaw);
        $widgetBlockId = $_POST['widgetBlockId'];
        $postId = sanitize_text_field($_POST['postId']);
        $params = [
            'search' => sanitize_text_field($_POST['search']),
            'category' => sanitize_text_field($_POST['category']),
        ];

        $post = get_post($postId);
        if($widgetBlockId) {
            $blocks = parse_blocks(get_option('widget_block')[$widgetBlockId]['content']);
        }elseif (has_blocks($post->post_content)) {
            $blocks = parse_blocks($post->post_content);
        }
        $block = new Blocks();
        $block->search_block_attr($blocks, $blockId, $blockRaw, $blockName, $params);
        if(isset($_POST['attr']) && $_POST['attr']) {
            $params['attr'] = $_POST['attr'];
        }
        $search_param = $block->search_block_param($params);
        if(isset($_POST['source']) && $_POST['source'] == 'block_editor') {
            $search_param['products'] = $this->product_format($search_param);
            return $search_param;
        }
        $product_search = new Product_Search();
        ob_start();
            $product_search->search_item_content($search_param);
        return ob_get_clean();

    }

    public function product_format($params) {
	    $data = [];
	    $loop = $params['products'];
	    $product_search = new Product_Search();
	    if($loop->have_posts()){
            while($loop->have_posts()) {
                $loop->the_post();
                $var                = array();
                $post_id            = get_the_ID();
                $product            = wc_get_product($post_id);
                $var['title']       = $product_search->highlightSearchKey(get_the_title(), $params['search']);
                $var['permalink']       = $product->get_permalink();
                $var['price_html']  = $product->get_price_html();
                $var['rating_average']= $product->get_average_rating();

                if (has_post_thumbnail()) {
                    $var['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'large')[0];
                }else {
                    $var['image'] = esc_url(WOPB_URL . 'assets/img/wopb-placeholder.jpg');
                }

                // tag
                $tag = get_the_terms($post_id, (isset($prams['tag'])?esc_attr($prams['tag']):'product_tag'));
                if(!empty($tag)){
                    $v = array();
                    foreach ($tag as $val) {
                        $v[] = array('slug' => $val->slug, 'name' => $val->name, 'url' => esc_url(get_term_link($val->term_id)));
                    }
                    $var['tag'] = $v;
                }

                // cat
                $cat = get_the_terms($post_id, (isset($prams['cat'])?esc_attr($prams['cat']):'product_cat'));
                if(!empty($cat)){
                    $v = array();
                    foreach ($cat as $val) {
                        $v[] = array('slug' => $val->slug, 'name' => $val->name, 'url' => esc_url(get_term_link($val->term_id)));
                    }
                    $var['category'] = $v;
                }
                $data[] = $var;
            }
            wp_reset_postdata();
        }
	    return $data;
    }
}
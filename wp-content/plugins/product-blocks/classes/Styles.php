<?php
/**
 * Styles Add and Style REST API Action.
 * 
 * @package WOPB\Styles
 * @since v.1.0.0
 */

namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Styles class.
 */
class Styles {

	/**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
    public function __construct(){
		$this->require_block_css();
		add_action('rest_api_init', array($this, 'save_block_css_callback'));
		add_action('wp_ajax_disable_google_font', array($this, 'disable_google_font_callback'));
	}

	/**
     * Disable Google Font Callback
     *
     * * @since v.2.5.5
     * @return STRING
     */
    public function disable_google_font_callback($type) {
		if (!wp_verify_nonce(wp_unslash($_REQUEST['wpnonce']), 'wopb-nonce') && $local){
            return ;
        }

		global $wp_filesystem;
		if (! $wp_filesystem ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		$upload_dir_url = wp_upload_dir();
		$dir = trailingslashit($upload_dir_url['basedir']).'product-blocks/';
		$css_dir = glob($dir.'*.css');
		$exclude_typo = implode('|', ['Arial','Tahoma','Verdana','Helvetica','Times New Roman','Trebuchet MS','Georgia']);

		if (count($css_dir) > 0) {
			foreach( $css_dir as $key => $value ) {
				$css = $wp_filesystem->get_contents($value);
				$filter_css = preg_replace('/(@import)[\w\s:\/?=,;.\'()+]*;/m', '', $css); // Remove Import Font
				$final_css = preg_replace('/(font-family:)((?!'.$exclude_typo.')[\w\s:,\\\'-])*;/mi', '', $filter_css); // Font Replace Except Default Font
				$wp_filesystem->put_contents( $value, $final_css ); // Update CSS File
			}
		}

		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE `meta_key`='_wopb_css'" );
		if (!empty($results)) {
			foreach ($results as $key => $value) {
				$filter_css = preg_replace('/(@import)[\w\s:\/?=,;.\'()+]*;/m', '', $value->meta_value); // Remove Import Font
				$final_css = preg_replace('/(font-family:)((?!'.$exclude_typo.')[\w\s:,\\\'-])*;/mi', '', $filter_css); // Font Replace Except Default Font
				update_option($value->meta_key, $final_css);
			}
		}

		return wp_send_json_success(__('CSS Updated!', 'product-blocks'));
    }

	/**
	 * REST API Action
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
	public function save_block_css_callback(){
		register_rest_route(
			'wopb/v1', 
			'/save_block_css/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'save_block_content_css'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
		);
		
		register_rest_route(
			'wopb/v1',
			'/get_posts/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'get_posts_call'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);

		register_rest_route(
			'wopb/v1',
			'/appened_css/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'appened_css_call'),
					'permission_callback' => function () {
						return current_user_can('edit_posts');
					},
					'args' => array()
				)
			)
		);

		register_rest_route(
			'wopb/v1',
			'/action_option/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'global_settings_action'),
					'permission_callback' => function () {
						return current_user_can('manage_options');
					},
					'args' => array()
				)
			)
		);

	}

	/**
	 * Get and Set ProductX Global Settings
     * 
     * @since v.2.4.24
	 * @param OBJECT | Request Param of the REST API
	 * @return ARRAY | Array of the Custom Message
	 */
	public function global_settings_action($server) {
		$post = $server->get_params();
		if (isset($post['type'])) {
			if ($post['type'] == 'set') {
				update_option('productx_global', $post['data']);
				return ['success' => true];
			} else {
				return ['success' => true, 'data' => get_option('productx_global', [])];
			}
		} else {
			return ['success' => false];
		}
	}


	/**
	 * Save Import CSS in the top of the File
     * 
     * @since v.1.0.0
	 * @param OBJECT | Request Param of the REST API
	 * @return ARRAY/Exception | Array of the Custom Message
	 */
	public function appened_css_call($server) {
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$post = $server->get_params();
		$css = $post['inner_css'];
		$post_id = (int) sanitize_text_field($post['post_id']);
		if( $post_id ){
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			$filename = "wopb-css-{$post_id}.css";
			$dir = trailingslashit(wp_upload_dir()['basedir']).'product-blocks/';
			
			WP_Filesystem( false, $upload_dir_url['basedir'], true );
			if( ! $wp_filesystem->is_dir( $dir ) ) {
				$wp_filesystem->mkdir( $dir );
			}
			
			update_post_meta($post_id, '_wopb_css', $css);
			update_post_meta($post_id, '_wopb_active', 'yes');
			
			if ( ! $wp_filesystem->put_contents( $dir . $filename, $css ) ) {
				throw new Exception(__('CSS can not be saved due to permission!!!', 'product-blocks' )); 
			}

			wp_send_json_success(array('success' => true, 'message' => __('Data retrive done', 'product-blocks' )));
		} else {
			return array('success' => false, 'message' => __('Data not found!!', 'product-blocks' ));
		}
	}


	/**
	 * Save Import CSS in the top of the File
     * 
     * @since v.1.0.0
	 * @param OBJECT | Request Param of the REST API
	 * @return ARRAY/Exception | Array of the Custom Message
	 */
	public function get_posts_call($server) {
		$post = $server->get_params();
		if (isset($post['postId'])) {
			return array('success' => true, 'data'=> get_post($post['postId'])->post_content, 'message' => __('Data retrive done', 'product-blocks' ));
		} else {
			return array('success' => false, 'message' => __('Data not found!!', 'product-blocks' ));
		}
	}


	/**
	 * Save Import CSS in the top of the File
     * 
     * @since v.1.0.0
	 * @param OBJECT | Request Param of the REST API
	 * @return ARRAY/Exception | Array of the Custom Message
	 */
	public function save_block_content_css($request){
		try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$params 		= $request->get_params();
			$post_id 		= sanitize_text_field($params['post_id']);
			
			if ($post_id == 'wopb-widget' && $params['has_block']) {
				update_option($post_id, $params['block_css']);
				return ['success' => true, 'message' => __('Widget CSS Saved', 'product-blocks')];
			}

			$filename 		= "wopb-css-{$post_id}.css";
			$upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'product-blocks/';

			if ($params['has_block']) {
				// Set Saving ID for Clean Cache
				wopb_function()->set_setting('save_version', rand(1, 1000));

				update_post_meta($post_id, '_wopb_active', 'yes');
				$wopb_block_css = $this->set_top_css($params['block_css']);

				// Preview Check
				if ($params['preview']) {
					set_transient('_wopb_preview_'.$post_id, $wopb_block_css , 60*60);
					return ['success' => true];
				}

				WP_Filesystem( false, $upload_dir_url['basedir'], true );
				if( ! $wp_filesystem->is_dir( $dir ) ) {
					$wp_filesystem->mkdir( $dir );
				}
				update_post_meta($post_id, '_wopb_css', $wopb_block_css);
				if ( ! $wp_filesystem->put_contents( $dir . $filename, $wopb_block_css ) ) {
					throw new Exception(__('CSS can not be saved due to permission!!!', 'product-blocks' )); 
				}
				return ['success'=>true, 'message'=>__('Product Blocks css file has been updated.', 'product-blocks' )];
			} else {
				delete_post_meta($post_id, '_wopb_active');
				if (file_exists($dir.$filename)) {
					unlink($dir.$filename);
				}
				delete_post_meta($post_id, '_wopb_css');
				return ['success'=>true, 'message'=>__('Product Blocks CSS Deleted.', 'product-blocks' )];
			}
		}catch(Exception $e){
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
	}


	/**
	 * Save Import CSS in the top of the File
     * 
     * @since v.1.0.0
	 * @param STRING | CSS (STRING)
	 * @return STRING | Generated CSS
	 */
	public function set_top_css($get_css = ''){
		$disable_google_font = wopb_function()->get_setting('disable_google_font');
		if ($disable_google_font != 'yes') {
            $css_url = "@import url('https://fonts.googleapis.com/css?family=";
            $font_exists = substr_count($get_css, $css_url);
            if ($font_exists) {
                $pattern = sprintf('/%s(.+?)%s/ims', preg_quote($css_url, '/'), preg_quote("');", '/'));
                if (preg_match_all($pattern, $get_css, $matches)) {
                    $fonts = $matches[0];
                    $get_css = str_replace($fonts, '', $get_css);
                    if (preg_match_all('/font-weight[ ]?:[ ]?[\d]{3}[ ]?;/', $get_css, $matche_weight)) {
                        $weight = array_map(function ($val) {
                            $process = trim(str_replace(array('font-weight', ':', ';'), '', $val));
                            if (is_numeric($process)) {
                                return $process;
                            }
                        }, $matche_weight[0]);
                        foreach ($fonts as $key => $val) {
                            $fonts[$key] = str_replace("');", '', $val) . ':' . implode(',', $weight) . "');";
                        }
                    }
                    $fonts = array_unique($fonts);
                    $get_css = implode('', $fonts) . $get_css;
                }
            }
        }
		return $get_css;
	}


	/**
	 * Enqueue CSS in HEAD or as a File
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
	public function require_block_css(){
	    $this->custom_element_check();
		$save_as = wopb_function()->get_setting('css_save_as');
		$save_as = $save_as ? $save_as : '';
		if (isset($_GET['preview_id']) && isset($_GET['preview_nonce'])) {
			add_action('wp_head', array( $this, 'add_block_inline_css' ), 100);
		} else if ($save_as === 'wp_head') {
			add_action('wp_head', array( $this, 'add_block_inline_css' ), 100);
		} else {
			add_action('wp_enqueue_scripts', array($this, 'add_block_css_file'));
		}
		add_action('wp_enqueue_scripts', array($this, 'productx_global_css'));
		add_action('admin_enqueue_scripts', array($this, 'productx_global_css'));
	}

	/**
	 * Set Inline Css for Custom Element
     *
     * @since v.2.5.6
	 * @return NULL
	 */
	public function custom_element_check() {
	    $post_types = [
	        'kadence_wootemplate'
        ];
	    $args = array(
			'post_type' => $post_types,
		);
	    $posts = get_posts( $args );
	    foreach( $posts as $post) {
            $post_id = $post->ID;
            add_action('wp_head', function () use ($post_id) {
                $this->block_inline_css($post_id);
            });
        }
    }

	/**
	 * Set Global Color Codes
     * 
     * @since v.2.3.7
	 * @return NULL
	 */
	public function productx_global_css() {
		// Preset CSS
		$global = get_option('productx_global', []);
		$custom_css = ':root {
			--productx-color1: '.(isset($global['presetColor1'])?$global['presetColor1']:'#037fff').';
			--productx-color2: '.(isset($global['presetColor2'])?$global['presetColor2']:'#026fe0').';
			--productx-color3: '.(isset($global['presetColor3'])?$global['presetColor3']:'#071323').';
			--productx-color4: '.(isset($global['presetColor4'])?$global['presetColor4']:'#132133').';
			--productx-color5: '.(isset($global['presetColor5'])?$global['presetColor5']:'#34495e').';
			--productx-color6: '.(isset($global['presetColor6'])?$global['presetColor6']:'#787676').';
			--productx-color7: '.(isset($global['presetColor7'])?$global['presetColor7']:'#f0f2f3').';
			--productx-color8: '.(isset($global['presetColor8'])?$global['presetColor8']:'#f8f9fa').';
			--productx-color9: '.(isset($global['presetColor9'])?$global['presetColor9']:'#ffffff').';
			}';
		wp_register_style( 'productx-global-style', false );
    	wp_enqueue_style( 'productx-global-style' );
		wp_add_inline_style( 'productx-global-style', $custom_css );
	}

	/**
	 * Set CSS as File
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
	public function add_block_css_file(){
        $post_id = wopb_function()->get_ID();
		wopb_function()->set_css_style($post_id);	
	}


    /**
     * Set Inline CSS in Head
     *
     * @return NULL
     * @since v.1.0.0
     */
	public function add_block_inline_css() {
        $post_id = wopb_function()->get_ID();
        $this->block_inline_css($post_id);
	}

    /**
     * Return Inline CSS
     *
     * @param $post_id
     * @return NULL
     * @since v.2.5.6
     */
	public function block_inline_css($post_id) {
		if( $post_id ){
            $upload_dir_url = wp_get_upload_dir();
            $upload_css_dir_url = trailingslashit( $upload_dir_url['basedir'] );
			$css_dir_path = $upload_css_dir_url."product-blocks/wopb-css-{$post_id}.css";

			// Reusable CSS
			$reusable_css = '';
			$reusable_id = wopb_function()->reusable_id($post_id);
			foreach ( $reusable_id as $id ) {
				$reusable_dir_path = $upload_css_dir_url."product-blocks/wopb-css-{$id}.css";
				if (file_exists( $reusable_dir_path )) {
					$reusable_css .= file_get_contents($reusable_dir_path);
				}else{
					$reusable_css .= get_post_meta($id, '_wopb_css', true);
				}
			}

			$css = '';
			if (isset($_GET['preview_id']) && isset($_GET['preview_nonce'])) {
				$css = get_transient('_wopb_preview_'.$post_id, true);
			} else if (file_exists( $css_dir_path )) {
				$css = file_get_contents($css_dir_path);
			}
			if (!$css) {
				$css = get_post_meta($post_id, '_wopb_css', true);
			}
			if ($reusable_css) {
				$css = $this->set_top_css($css.$reusable_css);
			}
			if ($css) {
				echo  wopb_function()->esc_inline($css);
			}
		}
	}
}
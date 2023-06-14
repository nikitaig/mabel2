<?php
/**
 * Plugin Cache.
 * 
 * @package WOPB\Caches
 * @since v.1.0.0
 */
namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Caches class.
 */
class Caches{

    /**
	 * API Endpoint
	 *
	 * @since v.1.0.0
	 */
    private $api_endpoint = 'https://wopb.wpxpo.com/wp-json/restapi/v2/';
    
    /**
	 * Setup class.
	 *
	 * @since v.1.0.0
	 */
    public function __construct(){
        add_action('admin_init', array($this, 'get_source_data_callback'), 10, 1);
        add_action('rest_api_init', array($this, 'get_template_data'));
    }

    /**
	 * Where is Cache is Located if not found the download and create file
     * 
     * @since v.1.0.0
	 * @return ARRAY / Exception
	 */
    public function get_source_data_callback(){
        $this->get_source_data('all');
    }

    /**
	 * Get Template or Desing from the API Action
     * 
     * @since v.1.0.0
	 * @return NULL
	 */
	public function get_template_data(){
		register_rest_route(
			'wopb/v2', 
			'/get_all_templates/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'get_all_templates_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
                    'args' => array(),
                    'permission_callback' => '__return_true'
				)
			)
        );
        register_rest_route(
			'wopb/v2', 
			'/get_design/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'get_design_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
                    'args' => array(),
                    'permission_callback' => '__return_true'
				)
			)
        );
        register_rest_route(
			'wopb/v2', 
			'/fetch_all_design/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'fetch_all_design_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
        );
        register_rest_route(
			'wopb/v2',
			'/get_premade/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array( $this, 'get_premade_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
        );
    }


    /**
	 * ResetData from API
     * 
     * @since v.2.0.7
     * @param ARRAY
	 * @return ARRAY | Data of the Design
	 */
    public function fetch_all_design_callback($request) {
        $test = '';
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/wopb';
        if ( file_exists($upload_dir . '/template_nd_design.json') ) {
            $test .= $upload_dir . '/template_nd_design.json';
            wp_delete_file($upload_dir . '/template_nd_design.json');
        }
        if ( file_exists($upload_dir . '/design.json') ) {
            $test .= $upload_dir . '/design.json';
            wp_delete_file($upload_dir . '/design.json');
        }
        if (file_exists($upload_dir . '/premade.json') ) {
            wp_delete_file($upload_dir . '/premade.json');
        }
        $this->get_source_data('all');
        return array('success' => true, 'data' => __('Data Fetched!!!', 'product-blocks'));
    }

    /**
	 * Premade Template Data
     *
     * @since v.2.3.9
     * @param ARRAY
	 * @return ARRAY | Data of the Premade
	 */
    public function get_premade_callback($request) {
		try{
            global $wp_filesystem;
            if (! $wp_filesystem ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'wopb/';
            $file_path      = $dir . "premade.json";

            if (file_exists( $file_path )) {
                if (!file_exists( $dir . "template_nd_design.json" )) {
                    return array( 'success' => true, 'data' => file_get_contents($file_path), 'home' => $this->get_source_data('templates') );
                } else {
                    return array( 'success' => true, 'data' => file_get_contents($file_path), 'home' => file_get_contents($dir . "template_nd_design.json") );
                }
            } else {
                $this->get_source_data('premade');
            }

		}catch(Exception $e) {
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }


    /**
	 * Get Design Data from API
     * 
     * @since v.1.0.0
     * @param ARRAY
	 * @return ARRAY | Data of the Design
	 */
	public function  get_design_callback($request){
		try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
            
			$upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'wopb/';
            $file_path      = $dir . "design.json";
            
            if (file_exists( $file_path )) {
                return array( 'success' => true, 'data' => file_get_contents($file_path) );
            } else {
                $this->get_source_data('design');
            }
			
		}catch(Exception $e){
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }
    

    /**
	 * Get Template Data from API
     * 
     * @since v.1.0.0
     * @param ARRAY
	 * @return ARRAY | Data of the Design
	 */
	public function  get_all_templates_callback($request){
		try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'wopb/';
            $file_path      = $dir . "template_nd_design.json";
            
            if (file_exists( $file_path )) {
                return array( 'success' => true, 'data' => file_get_contents($file_path) );
            } else {
                return array( 'success' => true, 'data' => $this->get_source_data('templates') );
            }
			
		}catch(Exception $e){
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }
    

    /**
	 * Create a Directory in Upload Folder
     * 
     * @since v.1.0.0
     * @param NULL
	 * @return STRING | Directory Path
	 */
    public function create_directory($type = 'all') {
        try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
            $upload_dir_url = wp_upload_dir();
			$dir = trailingslashit($upload_dir_url['basedir']) . 'wopb/';
            WP_Filesystem( false, $upload_dir_url['basedir'], true );
            if( ! $wp_filesystem->is_dir( $dir ) ) {
                $wp_filesystem->mkdir( $dir );
            }
            if (!file_exists($dir . 'template_nd_design.json')) {
                fopen( $dir . 'template_nd_design.json', "w" );
            }
            if (!file_exists($dir . 'design.json')) {
                fopen( $dir . 'design.json', "w" );
            }
            if($type == 'all' || $type == 'premade'){
                if (!file_exists($dir . 'premade.json')) {
                    fopen( $dir . 'premade.json', "w" );
                }
            }
            return $dir;
        } catch(Exception $e) {
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }


    /**
	 * Get Source Data from the file or API
     * 
     * @since v.1.0.0
     * @param STRING | Type (STRING)
	 * @return ARRAY | Exception Message
	 */
    public function get_source_data($type = 'all') {
        if($type == 'templates' || $type == 'design' || $type == 'premade'){
            return $this->download_source($type);
        } else if($type == 'all'){
            try{
                global $wp_filesystem;
                if ( ! $wp_filesystem ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                $upload_dir_url = wp_upload_dir();
                $dir 			= trailingslashit($upload_dir_url['basedir']) . 'wopb/';
                if (!file_exists( $dir . "template_nd_design.json" )) {
                    $this->download_source($type);
                }
                if (!file_exists( $dir . "design.json" )) {
                    $this->download_source($type);
                }
                if (!file_exists( $dir . "premade.json" )) {
                    $this->download_source($type);
                }
            }catch(Exception $e){
                return false;
            }
        }
    }


    /**
	 * Download Source from the Server API
     * 
     * @since v.1.0.0
     * @param STRING | Type (STRING)
	 * @return ARRAY | Message from the API
	 */
    public function download_source($type) {
        $data = '';
        if($type == 'all' || $type == 'templates'){
            $response = wp_remote_post(
                $this->api_endpoint.'templates',
                array(
                    'method' => 'POST',
                    'timeout' => 120,
                    'body' => array( 'type' => 'layouts', 'design' => 'all' )
                )
            );

            if ( !is_wp_error( $response ) ) {
                $path_url = $this->create_directory($type);
                $data = $response['body'];
                file_put_contents($path_url.'template_nd_design.json', $data);
            }
        }
        if($type == 'all' || $type == 'design'){
            $response = wp_remote_post(
                $this->api_endpoint.'design',
                array(
                    'method' => 'POST',
                    'timeout' => 120
                )
            );
            if ( !is_wp_error( $response ) ) {
                $path_url = $this->create_directory($type);
                $data = $response['body'];
                file_put_contents($path_url.'design.json', $data);
            }
        }
        if ($type == 'all' || $type == 'premade') {
            $response = wp_remote_post(
                $this->api_endpoint.'premade',
                array(
                    'method' => 'POST',
                    'timeout' => 120
                )
            );
            if ( !is_wp_error( $response ) ) {
                $path_url = $this->create_directory($type);
                $data = $response['body'];
                file_put_contents($path_url.'premade.json', $data);
            }
        }
        return $data;
    }


    /**
	 * Download Images from the Source
     * 
     * @since v.1.0.0
     * @param MIXED | JSON Encode (STRING), URL (STRING)
	 * @return NULL
	 */
    public function download_images($data, $path_url) {
        if($data){
            $data = json_decode($data);
            foreach ($data as $val) {
                $response = wp_remote_get($val->image);
                if( !is_wp_error( $response ) ) {
                    $file_name = $path_url.'/'.wp_basename($val->image);
                    if(!file_exists($file_name)){
                        $responseBody = wp_remote_retrieve_body( $response );
                        $fp = fopen($path_url.'/'.wp_basename($val->image), "w");
                        fwrite($fp, $responseBody);
                        fclose($fp);
                    }
                }
            }
        }
    }
    

}   
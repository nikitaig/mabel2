<?php
/**
 * Shortcode Core.
 * 
 * @package WOPB\Shortcode
 * @since v.1.1.0
 */

namespace WOPB;

defined('ABSPATH') || exit;

class Shortcode {
    public function __construct(){
        add_shortcode('product_blocks', array($this, 'shortcode_callback'));
    }

    /**
	 * Shortcode Callback
     * 
     * @since v.1.1.0
	 * @return STRING | HTML of the shortcode
	 */
    function shortcode_callback( $atts = array(), $content = null ) {
        extract(shortcode_atts(array(
         'id' => ''
        ), $atts));

        $content = '';
        $id = is_numeric( $id ) ? (float) $id : false;
        if ($id) {
            $init = new \WOPB\Initialization();
            $init->register_scripts_common();
            $extra = wopb_function()->set_css_style($id ,true);
            $content_post = get_post($id);
            if ($content_post && $content_post->post_status == 'publish' && $content_post->post_password == '') {
                $content = $content_post->post_content;
                $content = do_blocks($content);
				$content = do_shortcode($content);
                $content = str_replace(']]>', ']]&gt;', $content);
				$content = preg_replace('%<p>&nbsp;\s*</p>%', '', $content);
				$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);
                return $extra.'<div class="wopb-shortcode" data-postid="'.esc_attr($id).'">' . $content . '</div>';
            }
        }
        return '';
    }
}
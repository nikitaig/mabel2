<?php
/**
 * FlipImage Addons Core.
 * 
 * @package WOPB\FlipImage
 * @since v.1.1.0
 */

namespace WOPB;

defined('ABSPATH') || exit;

class FlipImage {
    public function __construct(){
        if ( wopb_function()->get_setting('flipimage_source') == 'feature' ) {
            add_action( 'save_post', array($this, 'feature_image_save'), 10, 1 );
            add_action( 'add_meta_boxes', array($this, 'feature_image_add_metabox') );
        }
    }

    /**
	 * FlipImage Flip Image Metabox Register
     * 
     * @since v.1.1.0
	 * @return NULL
	 */
    function feature_image_add_metabox () {
        add_meta_box( 'flipimage-feature-image', __( 'Flip Image', 'text-domain' ), array($this, 'feature_image_metabox'), 'product', 'side', 'low');
    }

    /**
	 * FlipImage Flip Image Metabox
     * 
     * @since v.1.1.0
	 * @return NULL
	 */
    function feature_image_metabox ( $post ) {
        global $content_width, $_wp_additional_image_sizes;
        $image_id = get_post_meta( $post->ID, '_flip_image_id', true );
        $old_content_width = $content_width;
        $content_width = 254;
        if ( $image_id && get_post( $image_id ) ) {
            if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
                $thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
            } else {
                $thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
            }
            if ( ! empty( $thumbnail_html ) ) {
                echo wp_kses_post($thumbnail_html);
                echo '<p class="hide-if-no-js"><a href="javascript:;" id="remove_feature_image_button" >' . esc_html__( 'Remove Flip Image', 'product-blocks' ) . '</a></p>';
                echo '<input type="hidden" id="upload_feature_image" name="_flip_image" value="' . esc_attr( $image_id ) . '" />';
            }
            $content_width = $old_content_width;
        } else {
            echo '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
            echo '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set listing image', 'product-blocks' ) . '" href="javascript:;" id="upload_feature_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'product-blocks' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'product-blocks' ) . '">' . esc_html__( 'Set listing image', 'product-blocks' ) . '</a></p>';
            echo '<input type="hidden" id="upload_feature_image" name="_flip_image" value="" />';
        }
    }
    
    /**
	 * FlipImage Save Feature Image
     * 
     * @since v.1.1.0
	 * @return NULL
	 */
    function feature_image_save ( $post_id ) {
        if( isset( $_POST['_flip_image'] ) ) {
            $image_id = (int) sanitize_text_field($_POST['_flip_image']);
            update_post_meta( $post_id, '_flip_image_id', $image_id );
        }
    }

    /**
	 * FlipImage Addons Intitial Setup Action
     * 
     * @since v.1.1.0
	 * @return NULL
	 */
    public function initial_setup(){
        // Set Default Value
        $initial_data = array(
            'flipimage_heading' => 'yes',
            'flipimage_source' => 'gallery'
        );
        foreach ($initial_data as $key => $val) {
            wopb_function()->set_setting($key, $val);
        }
    }
    
}
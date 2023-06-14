<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Rating{

    public function __construct() {
        add_action('init', array($this, 'register'));
    }

    public function get_attributes($default = false){
        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],
            //--------------------------
            //  Rating
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'ratingAlignment' => [
                'type'=> 'string',
                'default'=> "left",
                'style'=> [
                    (object)[
                        'depends' => [
                            (object)['key'=>'ratingAlignment','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:center;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'ratingAlignment','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-start;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'ratingAlignment','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-end;}',
                    ],
                ],
            ],
            'enableLabel' => [
                'type' => 'boolean',
                'default'=> false,
            ],
            'enableOrder' => [
                'type' => 'boolean',
                'default'=> false,
            ],
            'starBgColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}}  .star-rating span:before{color:{{starBgColor}};}'
                    ],
                ],
            ],
            'starSize' => [
                'type' => 'string',
                'default' => 20,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}}  .woocommerce-product-rating .star-rating{font-size:{{starSize}}px;}'
                    ],
                ],
            ],
            'emptyStarColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-rating  .star-rating::before{color:{{emptyStarColor}};}'
                    ],
                ],
            ],

            //--------------------------
            //  Review
            //--------------------------
            'reivewText' => [
                'type' => 'string',
                'default' => 'Reviews'
            ],
            'ratingColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-rating-label {color:{{ratingColor}};}'
                    ],
                ],
            ],
            'ratingTypo' =>  [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}}  .wopb-rating-label'
                    ],
                ],
                
            ],
  
            //--------------------------
            //  Label
            //--------------------------
            'TextLabelTypo'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-rating  .woocommerce-review-link'
                    ],
                ],
            ],
            'ratingLabeColor' => [
                'type'  => 'string',
                'default' => '', 
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-rating   .woocommerce-review-link{color:{{ratingLabeColor}};}'
                    ],
                ],
            ],
            'TextHoverColor' => [
                'type'  => 'string',
                'default' => '', 
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-rating  .woocommerce-review-link:hover {color:{{TextHoverColor}};}'
                    ],
                ],
            ],

            //--------------------------
            //  Order
            //--------------------------
            'orderLabelText' => [
                'type' => 'string',
                'default'=> 'Orders',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                    ],
                ],
            ],
            'orderTypo'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .rating-builder-order'
                    ],
                ],
            ],
            'orderColor' => [
                'type'  => 'string',
                'default' => '', 
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .rating-builder-order{color:{{orderColor}};}'
                    ],
                ],
            ],
            'ratingSpace' => [
                'type' => 'object',
                'default' => '8',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .rating-builder-separator{margin:0 {{ratingSpace}}px;}'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}}   .wopb-block-space{margin:0 {{ratingSpace}}px;}'
                    ],
                ],
            ],
            'ratingSeparator' => [
                'type' => 'string',
                'default' => '/',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'enableOrder','condition'=>'==','value'=>true],
                        ],
                    ],
                ],
            ],

            //--------------------------
            //  Wrap Style
            //--------------------------
            'wrapBg' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#f5f5f5'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper'
                    ],
                ],
            ],
            'wrapBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper'
                    ],
                ],
            ],
            'wrapShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper'
                    ],
                ],
            ],
            'wrapRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ border-radius:{{wrapRadius}}; }'
                    ],
                ],
            ],
            'wrapHoverBackground' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#ff5845'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper:hover'
                    ],
                ],
            ],
            'wrapHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper:hover'
                    ],
                ],
            ],
            'wrapHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper:hover { border-radius:{{wrapHoverRadius}}; }'
                    ],
                ],
            ],
            'wrapHoverShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper:hover'
                    ],
                ],
            ],
            'wrapMargin' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ margin:{{wrapMargin}}; }'
                    ],
                ],
            ],
            'wrapOuterPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ padding:{{wrapOuterPadding}}; }'
                    ],
                ],
            ],
            'wrapInnerPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ padding:{{wrapInnerPadding}}; }'
                    ],
                ],
            ],
            'advanceId' => [
                'type' => 'string',
                'default' => '',
            ],
            'advanceZindex' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {z-index:{{advanceZindex}};}'
                    ],
                ],
            ],
            'hideExtraLarge' => [
                'type' => 'boolean',
                'default' => false,
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {display:none;} .block-editor-block-list__block {{WOPB}} {display:block;}'
                    ],
                ],
            ],
            'hideDesktop' => [
                'type' => 'boolean',
                'default' => false,
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {display:none;} .block-editor-block-list__block {{WOPB}} {display:block;}'
                    ],
                ],
            ],
            'hideTablet' => [
                'type' => 'boolean',
                'default' => false,
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {display:none;} .block-editor-block-list__block {{WOPB}} {display:block;}'
                    ],
                ],
            ],
            'hideMobile' => [
                'type' => 'boolean',
                'default' => false,
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {display:none;} .block-editor-block-list__block {{WOPB}} {display:block;}'
                    ],
                ],
            ],
            'advanceCss' => [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector' => '']],
            ]
        );
        
        if( $default ){
            $temp = array();
            foreach ($attributes as $key => $value) {
                if( isset($value['default']) ){
                    $temp[$key] = $value['default'];
                }
            }
            return $temp;
        }else{
            return $attributes;
        }
    }

    public function register() {
        register_block_type( 'product-blocks/product-rating',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Rating', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        $block_name = 'product-rating';
        $wraper_before = $wraper_after = $content = '';

        $product = wc_get_product();

        if (!empty($product)) {
            if ($product->get_average_rating() > 0) {
                $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
                    $content .= '<div class="wopb-product-wrapper">';
                        if ($attr['enableLabel']) {
                            $content .= '<span class="wopb-rating-label">'.esc_html($attr['reivewText']).'</span>';
                        }
                        ob_start();
                        woocommerce_template_single_rating();
                        if ($attr['enableOrder'] && $attr['ratingSeparator']) {
                            echo '<span class="rating-builder-separator wopb-block-space">'.esc_html($attr['ratingSeparator']).'</span>';
                        }
                        if ($attr['enableOrder']) {
                            echo '<span class="rating-builder-order">'.wp_kses_post($product->get_total_sales()).' '.esc_html($attr['orderLabelText']).'</span>';
                        }
                        $content .= ob_get_clean();
                    $content .= '</div>';
                $wraper_after .= '</div>';   
            }
        }

        return $wraper_before . $content . $wraper_after;
    }

}
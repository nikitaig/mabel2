<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Description{

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
            //  Description Settings
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'showDescHeading'=>[
                'type' => 'boolean',
                'default'=> true,
            ],
            'descAlign' => [
                'type' => 'object',
                'default' => (object)['lg' =>'left'],
                'style' =>[
                    (object)[                   
                        'selector'=>'{{WOPB}}, {{WOPB}} p, {{WOPB}} div, {{WOPB}} span{ text-align:{{descAlign}};}'
                    ],
                ],
            ],
            'headingDescText' => [
                'type' => 'string',
                'default' => 'Description',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showDescHeading','condition'=>'==','value'=>true],
                        ],
                    ]
                ]
            ],
            'desheadingColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showDescHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper > h2{color:{{desheadingColor}};}'
                    ],
                ],
            ],
            'desHeadTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showDescHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper > h2'
                    ],
                ],
            ],
            'desHeadSpace' => [
                'type' => 'string',
                'default' => '20',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showDescHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper > h2{margin-bottom:{{desHeadSpace}}px; margin-top:0px; }'
                    ],
                ],
            ],
            'descDevider' => [
                'type'=> 'string',
                'default' => '',
            ],
            'descColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}}, {{WOPB}} p, {{WOPB}} div, {{WOPB}} span{color:{{descColor}};}'
                    ],
                ],
            ],
            'descTypo'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}}, {{WOPB}} p, {{WOPB}} div, {{WOPB}} span'
                    ],
                ],
            ],

            //--------------------------
            //  Wrapper Settings
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
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{padding:{{wrapOuterPadding}}; }'
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
        
        if ($default) {
            $temp = array();
            foreach ($attributes as $key => $value) {
                if (isset($value['default'])) {
                    $temp[$key] = $value['default'];
                }
            }
            return $temp;
        } else {
            return $attributes;
        }
    }

    public function register() {
        register_block_type( 'product-blocks/product-description',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Description', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        global $productx_desc;
        $productx_desc = $attr['showDescHeading'] ? $attr['headingDescText'] : '';
        $block_name = 'product-description';
        $wraper_before = $wraper_after = $content = '';
        
        $product = wc_get_product();
        
        if (!empty($product)) {

            $description_heading = function() {
                global $productx_desc;
                return $productx_desc;
            };

            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-product-wrapper">';
            
            add_filter( 'woocommerce_product_description_heading', $description_heading );
            ob_start();
            woocommerce_product_description_tab();
            $content = ob_get_clean();
            remove_filter( 'woocommerce_product_description_heading', $description_heading );

            $wraper_after .= '</div>';
            $wraper_after .= '</div>';
        }

        return $wraper_before.$content.$wraper_after;
    }

}
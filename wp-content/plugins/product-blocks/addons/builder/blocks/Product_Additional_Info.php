<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Additional_Info{

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
            //  Additional Info Style
            //--------------------------
            'showHeading'=>[
                'type' => 'boolean',
                'default'=> true,
            ],
            'headingTypo'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'.editor-styles-wrapper {{WOPB}} .wopb-product-wrapper > h2, {{WOPB}} .wopb-product-wrapper > h2'
                    ],
                ],
            ],
            'headingColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'.editor-styles-wrapper {{WOPB}} .wopb-product-wrapper > h2, {{WOPB}} .wopb-product-wrapper > h2 {color:{{headingColor}};}'
                    ]
                ],
            ],
            'headingSpace' => [
                'type' => 'string',
                'default' => 10,
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showHeading','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'.editor-styles-wrapper {{WOPB}} .wopb-product-wrapper > h2, {{WOPB}} .wopb-product-wrapper > h2 {margin-bottom:{{headingSpace}}px;}'
                    ]
                ]
            ],
            'headingText' => [
                'type' => 'string',
                'default' => 'Additional information',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'showHeading','condition'=>'==','value'=>true],
                        ],
                    ]
                ]
            ],
            'infoPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'.woocommerce div.product {{WOPB}} .woocommerce-product-attributes tr td, .woocommerce div.product {{WOPB}} .woocommerce-product-attributes tr th, {{WOPB}} .woocommerce-product-attributes tr td, {{WOPB}} .woocommerce-product-attributes tr th { padding:{{infoPadding}};}'
                    ],
                ],
            ],
            //--------------------------
            //  Label
            //--------------------------
            'labelTypo'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-attributes-item__label'
                    ],
                ],
            ],
            'labelColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-product-attributes-item__label {color:{{labelColor}};}'
                    ]
                ],
            ],
            'labelBg' => [
                'type' => 'object',
                'default' => (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'.woocommerce {{WOPB}} table.shop_attributes .woocommerce-product-attributes-item__label, .editor-styles-wrapper {{WOPB}} table:not( .has-background ) th'
                    ]
                ],
            ],
            //--------------------------
            //  Value
            //--------------------------
            'valueTypo'=>[
                'type' => 'object',
                'default' => (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value p, {{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value'
                    ],
                ],
            ],
            'valueColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'
                            {{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value p a, 
                            {{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value p, 
                            {{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value {color:{{valueColor}};}
                        '
                    ]
                ],
            ],
            'valueBg' => [
                'type' => 'object',
                'default' => (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'.woocommerce {{WOPB}} .wopb-product-wrapper .woocommerce-product-attributes-item__value, .editor-styles-wrapper {{WOPB}} table:not( .has-background ) tbody tr:nth-child(n) td'
                    ]
                ],
            ],
            //--------------------------
            //  Wrapper
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
                        'selector'=>'{{WOPB}} .wopb-product-wrapper { border-radius:{{wrapRadius}}; }'
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
        register_block_type( 'product-blocks/product-additional-info',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Additional Info', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        global $productx_info;
        $productx_info = $attr['showHeading'] ? $attr['headingText'] : '';
        $block_name = 'product-additional-info';
        $wraper_before = $wraper_after = $content = '';

		$product = wc_get_product();

        if (!empty($product)) {

            if ($product->has_attributes() || $product->has_dimensions() || $product->has_weight()) {
                $additional_heading = function() {
                    global $productx_info;
                    return $productx_info;
                };
    
                $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
                $wraper_before .= '<div class="wopb-product-wrapper">';
    
                add_filter( 'woocommerce_product_additional_information_heading', $additional_heading );
    
                ob_start();
                woocommerce_product_additional_information_tab();
                $content .= ob_get_clean();
    
                remove_filter( 'woocommerce_product_additional_information_heading', $additional_heading );
    
                $wraper_after .= '</div>';
                $wraper_after .= '</div>';
            }
        }

        return $wraper_before.$content.$wraper_after;
    }

}
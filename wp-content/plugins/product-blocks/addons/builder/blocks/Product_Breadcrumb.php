<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Breadcrumb{

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
            //  Breadcrumb Style
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'breadcrumbAlignment' => [
                'type' => 'object',
                'default' => (object)['lg' =>'left'],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} {text-align:{{breadcrumbAlignment}}; }'
                    ]
                ],
            ],
            'breadcrumbColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-breadcrumb,.woocommerce {{WOPB}} .woocommerce-breadcrumb{color:{{breadcrumbColor}}; }'
                    ]
                ],
            ],
            'breadcrumbLinkColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-breadcrumb > a, .editor-styles-wrapper {{WOPB}} .woocommerce-breadcrumb > a{color:{{breadcrumbLinkColor}}}'
                    ]
                ],
            ],
            'bcrumbLinkHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-breadcrumb a:hover, .woocommerce {{WOPB}} .woocommerce-breadcrumb a:hover{color:{{bcrumbLinkHoverColor}}}'
                    ]
                ],
            ],
            'breadcrumbTypo'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '20', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-breadcrumb a, {{WOPB}} .woocommerce-breadcrumb, .woocommerce {{WOPB}} .woocommerce-breadcrumb'
                    ],
                ],
            ],     
            'bdcrumbSeparatorlabel' => [
                'type' => 'string',
                'default' => '',
            ],      
            'breadcrumbSpace' => [
                'type' => 'string',
                'default' => 10,
                'style' => [
                    (object)[
                        'selector'=> "{{WOPB}} .breadcrumb-separator{margin: 0 {{breadcrumbSpace}}px;}",
                    ]
                ] 
            ],
            'breadcrumbSeparator'=> [
                'type' => 'string',
                'default' => '/',
            ], 
            'bcrumbSeparatorColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=> "{{WOPB}} .breadcrumb-separator{color:{{bcrumbSeparatorColor}};}",
                    ],
                ], 
            ],
            'bcrumbSeparatorSize' => [
                'type' => 'string',
                'default' => '16',
                'style' => [
                    (object)[
                        'selector'=> "{{WOPB}} .breadcrumb-separator{font-size:{{bcrumbSeparatorSize}}px;}",
                    ]
                ] 
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
        register_block_type( 'product-blocks/product-breadcrumb',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Breadcrumb', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        $block_name = 'product-breadcrumb';
        $wraper_before = $wraper_after = $content = '';
    
        $product = wc_get_product();

        if (!empty($product)) {
            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-product-wrapper">';

            ob_start();
            $settings = $attr['breadcrumbSeparator'] ? array('delimiter' => '<span class="breadcrumb-separator" >'.esc_html($attr['breadcrumbSeparator']).'</span>') : array();
            woocommerce_breadcrumb($settings);
            $content .= ob_get_clean();

            $wraper_after .= '</div>';
            $wraper_after .= '</div>';
        }

        return $wraper_before.$content.$wraper_after;
    }

}
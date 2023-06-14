<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Price{

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
            //  Pricing Style
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'priceAlign' => [
                'type'=> 'string',
                'default'=> "left",
                'style'=> [
                    (object)[
                        'depends' => [
                            (object)['key'=>'priceAlign','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:center;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'priceAlign','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-start;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'priceAlign','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-end;}',
                    ],
                ],
            ],

            'salesLabel'=>[
                'type' => 'boolean',
                'default'=> false
            ],
            'salesBadge'=>[
                'type' => 'boolean',
                'default'=> false
            ],
            'priceTypo'=>[
                'type' => 'object',
                'default' => (object)['openTypography'=>1,'size'=>(object)['lg'=>'14', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'', 'unit'=>'px'],'decoration'=>'none','transform' => '','family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-Price-amount'
                    ],
                ],
            ],
            'priceColor' => [
                'type' => 'string',
                'default' => '#000000',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .amount , .prev-amount-value {color:{{priceColor}};}'
                    ]
                ],
            ],
            'priceSpace' => [
                'type' => 'string',
                'default' => 3,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .price-block-space  .woocommerce-Price-amount , ins  .woocommerce-Price-amount  {margin-left:{{priceSpace}}px;}'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesLabel','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-Price-amount  {margin:0px {{priceSpace}}px;}'
                    ]
                ]
            ],
            'salesColor' => [
                'type' => 'string',
                'default' => '#000000',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} del .woocommerce-Price-amount{color:{{salesColor}};}'
                    ]
                ],
            ],

            //--------------------------
            //  Label Style
            //--------------------------
            'salesTextLabel' => [
                'type' => 'string',
                'default' => __('Price: ', 'product-blocks'),
            ],
            'salesLabelColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesLabel','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-builder-price-label {color:{{salesLabelColor}}}',
                    ],
                ],
            ],
            'salesLabelTypo'=>[
                'type' => 'object',
                'default' => (object)['openTypography'=>0,'size'=>(object)['lg'=>'', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'', 'unit'=>'px'],'decoration'=>'none','transform' => 'capitalize','family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesLabel','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-builder-price-label',
                    ],
                ],
            ],

            //--------------------------
            //  Discount Badge Style
            //--------------------------
            'badgeLabel' => [
                'type' => 'string',
                'default' => __('OFF', 'product-blocks'),
            ],
            'badgeColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge {color:{{badgeColor}}; width: fit-content; }',
                    ],
                ],
            ],
            'salesBadgeTypo'=>[
                'type' => 'object',
                'default' => (object)['openTypography'=>1,'size'=>(object)['lg'=>'12', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'20', 'unit'=>'px'],'decoration'=>'none','transform' => '','family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge',
                    ],
                ],
            ],
            'badgeBg' => [
                'type' => 'string',
                'default' => '#000',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge {background:{{badgeBg}}}',
                    ],
                ],
            ],
            'badgeSpace' => [
                'type' => 'string',
                'default' => 10,
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge {margin-left:{{badgeSpace}}px;}',
                    ],
                ],
            ],
            'badgeBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}}  .woocommerce-discount-badge ',
                    ],
                ],
            ],
            'badgeRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '2','bottom' => '2','left' => '2', 'right' => '2', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge { border-radius:{{badgeRadius}}; }'
                    ],
                ],
            ],
            'badgePading' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '1','bottom' => '1','left' => '6', 'right' => '6', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'salesBadge','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-discount-badge { padding:{{badgePading}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Wrapper Style
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
        register_block_type( 'product-blocks/product-price',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Price', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        $block_name = 'product-price';
        $wraper_before = $wraper_after = $content = '';

        $product = wc_get_product();
        if (!empty($product)) {
            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-product-wrapper">';

            if ($attr['salesLabel']) {
                $content .= '<span class="wopb-builder-price-label"><bdi>'.esc_html($attr['salesTextLabel']).'</bdi></span>';
            }
            
            ob_start();
            woocommerce_template_single_price();
            $content .= ob_get_clean();

            if($product->get_sale_price() && $attr['salesBadge']){
                $percentage = 100-($product->get_sale_price() /$product->get_regular_price()*100);
                $content .= '<div class="woocommerce-discount-badge">'.round($percentage, 2).'% '.esc_html($attr['badgeLabel']).'</div>';
            }

            $wraper_after .= '</div>'; 
            $wraper_after .= '</div>';
        }

        return $wraper_before.$content.$wraper_after;
    }
}
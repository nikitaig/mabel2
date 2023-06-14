<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Order_Payment {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            // General 
            'pageLayout'=> [
                'type' => 'string',
                'default' => 'vertical',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'vertical'],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { flex-direction:row; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'horizontal'],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { flex-direction:column; }'
                    ],
                ]
            ],
            'orderText'=>[
                'type' => 'string',
                'default' => 'Order',
            ],
            'dateText'=>[
                'type' => 'string',
                'default' => 'Date',
            ],
            'emailText'=>[
                'type' => 'string',
                'default' => 'Email',
            ],
            'totalText'=>[
                'type' => 'string',
                'default' => 'Total',
            ],
            'payMethodText'=>[
                'type' => 'string',
                'default' => 'Payment Method',
            ],
            'ulAlign'=> [
                'type' => 'string',
                'default' => 'center',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'ulAlign','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { justify-content:flex-start; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'ulAlign','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { justify-content:center; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'ulAlign','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { justify-content:flex-end; }'
                    ],
                ],
            ],

            // List Item
            'itemPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>0,'bottom'=>10,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li { padding:{{itemPadding}}; }'
                    ],
                ],
            ],
            'itemBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li ']],
            ],
            'itemRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li { border-radius:{{itemRadius}}; }'
                    ],
                ],
            ],
            'itemSpace'=> [
                'type' => 'object',
                'default' => '10',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul { gap:{{itemSpace}}px; }'
                    ],
                ],
            ],

            // label 
            'labelColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li { color:{{labelColor}}; }']],
            ],
            'labelTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li ']]
            ],

            // value 
            'valueColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li strong { color:{{valueColor}}; }']],
            ],
            'valueTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '18', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li strong ']]
            ],
            'valueSpace'=> [
                'type' => 'string',
                'default' => '5',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul li strong { padding-top:{{valueSpace}}px;}']]
            ],

            // separator 
            'separatorColor'=> [
                'type' => 'string',
                'default' => '#d9d9d9',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul .wopb-separator { border-color:{{separatorColor}}; }']],
            ],
            'separatorBorder'=> [
                'type' => 'string',
                'default' => '2',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'vertical'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul .wopb-separator { border-width: 0px {{separatorBorder}}px 0px 0px ; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'horizontal'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul .wopb-separator { border-width: 0px 0px {{separatorBorder}}px 0px ; }'
                    ],
                ],
            ],
            'separatorBorderStyle'=> [
                'type' => 'string',
                'default' => 'solid',
                'style' => [ (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ul .wopb-separator { border-style:{{separatorBorderStyle}}; }']]
            ],

            // Container
            'containerBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1,'type' => 'color', 'color' => '#f4f4f4'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container ']],
            ],
            'containerShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-payment-container'
                    ],
                ],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>25,'right'=>20,'bottom'=>25,'left'=>20, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-payment-container { padding:{{containerPadding}}; }'
                    ],
                ],
            ],
            
            //--------------------------
            // Advanced
            //--------------------------
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
            'wrapInnerPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ padding:{{wrapInnerPadding}}; }'
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
        register_block_type( 'product-blocks/thankyou-order-payment',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Order Payment', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }
    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout() && is_wc_endpoint_url( 'order-received' )) {
            $block_name = 'thankyou-order-payment';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                        if (!is_admin()) {
                            if (isset(WC()->customer)) {
                                ob_start();
                                require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_payment/Template.php';
                                $content .= ob_get_clean();
                            }
                        }
                        
                    $wraper_after.='</div> ';
                $wraper_after.='</div> ';
            }

            return $wraper_before.$content.$wraper_after;
        }
    }

}
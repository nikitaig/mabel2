<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Order_Conformation {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            'showHead' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showMessage' => [
                'type' => 'boolean',
                'default' => true,
            ],

            // Head
            'orderHeadText'=>[
                'type' => 'string',
                'default' => 'Order#',
            ],
            'orderHeadColor'=> [
                'type' => 'string',
                'default' => '#296e9a',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-heading-section .wopb-order-heading { color:{{orderHeadColor}}; }']],
            ],
            'orderHeadTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '42', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-heading-section .wopb-order-heading ']]
            ],
            'orderHeadAlign'=> [
                'type' => 'string',
                'default' => 'center',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-heading-section { text-align:{{orderHeadAlign}}} ']]
            ],
            'orderHeadSpace'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-heading-section { margin-bottom:{{orderHeadSpace}}px;}']]
            ],

            // Message
            'messageText'=>[
                'type' => 'string',
                'default' => 'Thank you. Your order has been received.',
            ],
            'messageColor'=> [
                'type' => 'string',
                'default' => '#8b8b8b',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-message-section .wopb-order-message , {{WOPB}} .wopb-thankyou-order-conformation-container .woocommerce-thankyou-order-failed { color:{{messageColor}}; }']],
            ],
            'messageTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-message-section .wopb-order-message ']]
            ],
            'orderMessageAlign'=> [
                'type' => 'string',
                'default' => 'center',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-conformation-container .wopb-order-message-section { text-align:{{orderMessageAlign}}} ']]
            ],

            // Container
            'containerBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-conformation-container ']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-conformation-container ']],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-conformation-container { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-conformation-container { padding:{{containerPadding}} !important; }'
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
        register_block_type( 'product-blocks/thankyou-order-conformation',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style' => 'wopb-blocks-editor-css',
                'title' => __('Order Confirmation', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);

        if (is_checkout() && is_wc_endpoint_url( 'order-received' )) {
            $block_name = 'thankyou-order-conformation';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                        if (!is_admin()) {
                            if (isset(WC()->customer)) {
                                ob_start();
                                require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_conformation/Template.php';
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
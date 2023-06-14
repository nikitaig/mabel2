<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Free_Shipping_Progress_Bar{
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
            'showProgress'=> [
                'type' => 'boolean',
                'default' => true,
            ],
            'progressTop'=> [
                'type' => 'boolean',
                'default' => false,
            ],
            'beforePriceText'=>[
                'type' => 'string',
                'default' => 'Add',
            ],
            'afterPriceText'=>[
                'type' => 'string',
                'default' => 'to cart and get Free shipping!',
            ],
            'headerTextColor'=> [
                'type' => 'string',
                'default' => 'black',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-progress-msg { color:{{headerTextColor}}; margin-top:15px; margin-bottom:15px; }']],
            ],
            'priceColor'=> [
                'type' => 'string',
                'default' => '#4bbe18',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-shippingRemainingAmount { color:{{priceColor}}; }']],
            ],
            'bodyBgColor'=> [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-free-progress-bar-section { background-color:{{bodyBgColor}}; }']],
            ],
            'headerTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'inherit', 'family'=>'Roboto','weight'=>'normal'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-progress-msg']]
            ],
            'bodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2],'color' => '#d0d0d0','type' => 'dashed' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-free-progress-bar-section']],
            ],
            'bodyRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'0', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-progress-area , {{WOPB}} .wopb-free-progress-bar-section{ border-radius:{{bodyRadius}}; }'
                    ],
                ],
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>30, 'right'=>46, 'bottom'=>30,'left'=>30, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-free-progress-bar-section { padding:{{bodyPadding}}; }'
                    ],
                ],
            ],

            // Progress Bar 
            'progressBarHeight'=> [
                'type' => 'string',
                'default' => '7',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-progress-area { height:{{progressBarHeight}}px; box-sizing: content-box; }']],
            ],
            'emptyBgColor'=> [
                'type' => 'string',
                'default' => '#e0e0e0',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-progress-area { background-color:{{emptyBgColor}}; }']],
            ],
            'filledBgColor'=> [
                'type' => 'string',
                'default' => '#85d11c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-progress-bar-filled { background-color:{{filledBgColor}}; height:100%; }']],
            ],
            'progressBarBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' =>0 , 'bottom' => 0, 'left' => 0],'color' => 'black','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-progress-area']],
            ],
            'progressBarRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'0', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-progress-area , {{WOPB}} .wopb-progress-bar-filled{ border-radius:{{progressBarRadius}}; }'
                    ],
                ],
            ],
            
            //--------------------------
            //  Advanced
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
        register_block_type( 'product-blocks/free-shipping-progress-bar',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Free Shipping Progress Bar', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }
    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        $free_shipping_instance_id = $this->check_free_shipping();
        if(!empty($free_shipping_instance_id)) {
            $block_name = 'free_shipping_progress_bar';
            $wraper_before = $wraper_after = $content = '';

            if (function_exists('WC')) {
                $wraper_before .= '<div id="' . ($attr['advanceId'] ? $attr['advanceId'] : '') . '"' . ' class="wp-block-product-blocks-' . $block_name . ' wopb-block-' . $attr["blockId"] . ' ' . (isset($attr["className"]) ? $attr["className"] : '') . '">';
                $wraper_before .= '<div class="wopb-product-wrapper">';
                if (!is_admin()) {
                    if (isset(WC()->customer)) {
                        ob_start();
                        if (!WC()->cart->is_empty()) {
                            require_once WOPB_PATH . 'addons/builder/blocks/free_shipping_progress_bar/Template.php';
                        }
                        $content .= ob_get_clean();
                    }
                }
                $wraper_after .= '</div> ';
                $wraper_after .= '</div> ';
            }
            return $wraper_before . $content . $wraper_after;
        }
    }

    public function check_free_shipping() {
        if(WC()->cart) {
            $shipping_packages =  WC()->cart->get_shipping_packages();
            $shipping_zone = wc_get_shipping_zone(reset($shipping_packages));
            $zone_id = $shipping_zone->get_id();

            $available_methods = \WC_Data_Store::load('shipping-zone')->get_methods($zone_id, '');
            $free_shipping_instance_id = '';

            foreach ($available_methods as $method) {
                if ($method->method_id == 'free_shipping' && $method->is_enabled) {
                    $free_shipping_instance_id = $method->instance_id;
                    break;
                }
            }
            return $free_shipping_instance_id;
        }
    }
}
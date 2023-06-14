<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Cart_Total{
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
            'cartTotalTxt'=> [
                'type' => 'string',
                'default' => 'Cart Total',
            ],
            'subTotalTxt'=> [
                'type' => 'string',
                'default' => 'Subtotal',
            ],
            'totalTxt'=> [
                'type' => 'string',
                'default' => 'Total',
            ],
            'checkoutTxt'=> [
                'type' => 'string',
                'default' => 'Proceed to checkout',
            ],
            'tableBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total']],
            ],
            'tableRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4], 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-total { border-radius:{{tableRadius}}; overflow:hidden;}'
                    ],
                ],
            ],
            'tablePadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>10,'bottom'=>10, 'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total { padding:{{tablePadding}};}'
                    ],
                ],
            ],

            // Table Head
            'headerTextColor' => [
                'type' => 'string',
                'default' => '#595959',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading { color:{{headerTextColor}}; }']],
            ],
            'headerTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '22', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'400'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading ']]
            ],
            'headerBgColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading { background-color:{{headerBgColor}}!important; }']],
            ],
            'headerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#8c8f94','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading ']],
            ],
            'headerRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0], 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading { border-radius:{{headerRadius}};}'
                    ],
                ],
            ],
            'headerPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>1,'right'=>0,'bottom'=>1, 'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-total .wopb-table-heading { padding:{{headerPadding}}; }'
                    ],
                ],
            ],

            // Table Body
            'titleTextColor' => [
                'type' => 'string',
                'default' => '#3e3e3e',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody tr th { color:{{titleTextColor}}; text-align:left; }']],
            ],
            'priceTextColor' => [
                'type' => 'string',
                'default' => '#444444',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody tr td, {{WOPB}} .wopb-cart-total form p .select2-selection__rendered , {{WOPB}} .wopb-cart-total form p .input-text{ color:{{priceTextColor}}!important; }']],
            ],
            'titleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'decoration' => 'none', 'transform' => 'capitalize', 'weight'=>'normal'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody tr th']]
            ],
            'priceTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'decoration' => 'none', 'transform' => 'capitalize', 'weight'=>'bold'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody tr td, wopb-total-price , .woocommerce-shipping-methods label, {{WOPB}} .wopb-product-wrapper .wopb-cart-total bdi']]
            ],
            'bodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#ebebeb','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody th, {{WOPB}} .wopb-cart-total table tbody td']],
            ],
            'tableBodyBgColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total table tbody tr td , {{WOPB}} .wopb-cart-total table tbody tr th, {{WOPB}} .wopb-cart-total-section { background-color:{{tableBodyBgColor}} !important ; }']],
            ],
            'tableBodyRadius'  => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4], 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-total .wopb-cart-total-section { border-radius:{{tableBodyRadius}}; }'
                    ],
                ],
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>1,'right'=>0,'bottom'=>1, 'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total .wopb-cart-total-section { padding:{{bodyPadding}}; }'
                    ],
                ],
            ],
            'bodySpacing'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>0,'bottom'=>5, 'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-total-section table tbody tr th, {{WOPB}} .wopb-cart-total-section table tbody tr td{ padding:{{bodySpacing}}; }'
                    ],
                ],
            ],

            // Proceed to Checkout
            'checkoutText'=>[
                'type' => 'string',
                'default' => 'Proceed to Checkout',
            ],
            'checkoutTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button { color:{{checkoutTextColor}}; }']],
            ],
            'checkoutHoverTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button:hover { color:{{checkoutHoverTextColor}}; }']],
            ],
            'checkoutBgColor' => [
                'type' => 'string',
                'default' => '#333333',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button, {{WOPB}} .wc-proceed-to-checkout .checkout-button:focus { background-color:{{checkoutBgColor}}; }']],
            ],
            'checkoutHoverBgColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button:hover { background-color:{{checkoutHoverBgColor}}; }']],
            ],
            'checkoutTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize', 'weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wc-proceed-to-checkout .checkout-button']]
            ],
            'checkoutBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#333333','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wc-proceed-to-checkout .checkout-button']],
            ],
            'checkoutMargin' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout { margin:{{checkoutMargin}}; }'
                    ],
                ],
            ],
            'checkoutPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>6,'right'=>21,'bottom'=>6,'left'=>21, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button { padding:{{checkoutPadding}}; }'
                    ],
                ],
            ],
            'checkoutRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4], 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-total .wc-proceed-to-checkout .checkout-button { border-radius:{{checkoutRadius}}; margin-bottom: 0px; }'
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
        
        if ($default) {
            $temp = array();
            foreach ($attributes as $key => $value) {
                if( isset($value['default']) ){
                    $temp[$key] = $value['default'];
                }
            }
            return $temp;
        } else {
            return $attributes;
        }
    }

    public function register() {
        register_block_type( 'product-blocks/cart-total',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Cart Total', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        $block_name = 'cart-total';
        $wraper_before = $wraper_after = $content = '';

        if (function_exists('WC')) {
            $wraper_before.='<div '.($attr['advanceId']?'id="'.$attr['advanceId'].'" ':'').' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                $wraper_before .= '<div class="wopb-product-wrapper">';
                if (!is_admin()) {
                    if (isset(WC()->customer)) {
                        ob_start();
                        if (!WC()->cart->is_empty()) {
                            require_once WOPB_PATH.'addons/builder/blocks/cart_total/Template.php';
                        }
                        $content .= ob_get_clean();
                    }
                }
                $wraper_after .= '</div>';
            $wraper_after .= '</div> ';
        }
        return $wraper_before.$content.$wraper_after;
    }
}
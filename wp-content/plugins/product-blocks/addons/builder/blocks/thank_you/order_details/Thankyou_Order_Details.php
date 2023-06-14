<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Thankyou_Order_Details {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            // Title
            'downloadText'=>[
                'type' => 'string',
                'default' => 'Downloads',
            ],
            'orderDetailsText'=>[
                'type' => 'string',
                'default' => 'Order Details',
            ],
            'titleColor'=> [
                'type' => 'string',
                'default' => '#484c7b',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-order-downloads__title , {{WOPB}} .wopb-thankyou-order-details-container .woocommerce-order-details__title { color:{{titleColor}}; }']],
            ],
            'titleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '37', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'600'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-order-downloads__title , {{WOPB}} .wopb-thankyou-order-details-container .woocommerce-order-details__title']]
            ],
            'tableBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table , .editor-styles-wrapper {{WOPB}} .wopb-thankyou-order-details-container table ']],
            ],
            'tableRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table { overflow: hidden; border-radius:{{tableRadius}}; }'
                    ],
                ],
            ],

            // Table Header
            'productText'=>[
                'type' => 'string',
                'default' => 'Product',
            ],
            'totalText'=>[
                'type' => 'string',
                'default' => 'Total',
            ],
            'headColor'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead tr th { color:{{headColor}}; }']],
            ],
            'headBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1,'type' => 'color', 'color' => '#000000'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead ']],
            ],
            'headTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead tr th ']]
            ],
            'headBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#cccccc','type' => 'solid' ],
                'style' => [ (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead tr th ']],
            ],
            'headPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>0,'bottom'=>5,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead tr th { padding:{{headPadding}}; }'
                    ],
                ],
            ],
            'headAlign'=> [
                'type' => 'string',
                'default' => 'left',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table thead tr th { text-align:{{headAlign}}} ']]
            ],
            
            // Table Body 
            'bodyTextColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td { color:{{bodyTextColor}}; }']],
            ],
            'bodyBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody ']],
            ],
            'bodyLinkColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td a { color:{{bodyLinkColor}}; }']],
            ],
            'bodyLinkHoverColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td a:hover { color:{{bodyLinkHoverColor}}; }']],
            ],
            'bodyTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td']]
            ],
            'bodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td ']],
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>0,'bottom'=>5,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td { padding:{{bodyPadding}}; }' ],
                ],
            ],
            'bodyAlign'=> [
                'type' => 'string',
                'default' => 'left',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tbody tr td { text-align:{{bodyAlign}}} ']]
            ],

            // Button 
            'tableBtnDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            'tableBtnColor'=> [
                'type' => 'string',
                'default' => '#3b3b3b',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button { color:{{tableBtnColor}}; }']],
            ],
            'tableBtnBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1,'type' => 'color', 'color' => '#f4f4f4'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button ']],
            ],
            'tableBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button ']],
            ],
            'tableBtnShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button '
                    ],
                ],
            ],
            'tableBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button { border-radius:{{tableBtnRadius}}; }'
                    ],
                ],
            ],
            'tableBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button { padding:{{tableBtnPadding}} ; }'
                    ],
                ],
            ],

            'tableBtnHoverColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover { color:{{tableBtnHoverColor}}; }']],
            ],
            'tableBtnBgHoverColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover ']],
            ],
            'tableBtnHoverBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover ']],
            ],
            'tableBtnHoverShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover '
                    ],
                ],
            ],
            'tableBtnHoverRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover { border-radius:{{tableBtnHoverRadius}}; }'
                    ],
                ],
            ],
            'tableBtnHoverPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container .woocommerce-MyAccount-downloads-file.button:hover { padding:{{tableBtnHoverPadding}} ; }'
                    ],
                ],
            ],

            // Table Footer
            'subTotalText'=>[
                'type' => 'string',
                'default' => 'Subtotal',
            ],
            'shippingText'=>[
                'type' => 'string',
                'default' => 'Shipping Cost:',
            ],
            'payMethodText'=>[
                'type' => 'string',
                'default' => 'Payment Method',
            ],
            'footTotalText'=>[
                'type' => 'string',
                'default' => 'Total',
            ],
            'footColor'=> [
                'type' => 'string',
                'default' => '#272727',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot { color:{{footColor}}; }']],
            ],
            'footBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot ']],
            ],
            'footTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot']]
            ],
            'footBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot tr th , {{WOPB}} .wopb-thankyou-order-details-container table tfoot tr td ']],
            ],
            'footPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>0,'bottom'=>5,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot tr th , {{WOPB}} .wopb-thankyou-order-details-container table tfoot tr td { padding:{{footPadding}} ; }'
                    ],
                ],
            ],
            'footAlign'=> [
                'type' => 'string',
                'default' => 'left',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-thankyou-order-details-container table tfoot tr th , {{WOPB}} .wopb-thankyou-order-details-container table tfoot tr td { text-align:{{footAlign}}} ']]
            ],

            // Container
            'containerBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-details-container ']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-details-container ']],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-details-container { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-thankyou-order-details-container { padding:{{containerPadding}} ; }'
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
        register_block_type( 'product-blocks/thankyou-order-details',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Thank You Order Details', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout() && is_wc_endpoint_url( 'order-received' )) {
            $block_name = 'thankyou-order-details';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                        if (!is_admin()) {
                            if (isset(WC()->customer)) {
                                ob_start();
                                require_once WOPB_PATH.'addons/builder/blocks/thank_you/order_details/Template.php';
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
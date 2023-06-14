<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Checkout_Coupon{
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],
            // Apply Coupon

            //General 
            'showToggle' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'couponSectionBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#efefef','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-section']],
            ],

            // Coupon Heading
            'toggleText'=>[
                'type' => 'string',
                'default' => 'Enter your promotional code',
            ],
            'toggleTextColor'=> [
                'type' => 'string',
                'default' => '#191919',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-toggle-header { color:{{toggleTextColor}}; font-weight:normal;}']],
            ],
            'toggleTextHoverColor'=> [
                'type' => 'string',
                'default' => '#191919',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-toggle-header:hover { color:{{toggleTextHoverColor}};}']],
            ],
            'toggleTypo'=> [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-toggle-header']]
            ],
            'toggleHeadBgColor'=> [
                'type' => 'string',
                'default' => '#f0f0f0',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-toggle-header { background:{{toggleHeadBgColor}}; }']],
            ],
            'toggleHeadBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#c4c4c4','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-toggle-header']],
            ],
            'toggleHeadRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'0', 'unit' =>'px'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-toggle-header { border-radius:{{toggleHeadRadius}}; }' ],],
            ],
            'toggleHeadPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>13,'bottom'=>4,'left'=>13, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-toggle-header { padding:{{toggleHeadPadding}} }' ],],
            ],

            // Coupon Body 
            'couponTitleText'=>[
                'type' => 'string',
                'default' => 'Discount Coupon',
            ],
            'titleTextColor'=> [
                'type' => 'string',
                'default' => '#191919',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-title { color:{{titleTextColor}}; font-weight:500; margin-right:15px; }']],
            ],
            'titleTypo'=> [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-coupon-title']]
            ],
            
            'titlePosition' => [
                'type' => 'string',
                'default' => 'withCoupon',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'titlePosition','condition'=>'==','value'=>'aboveCoupon'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-coupon-title {margin-bottom:10px; display:inline-block;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'titlePosition','condition'=>'==','value'=>'withCoupon'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-coupon-title {display:inline-block;}',
                    ]
               ],
            ],
            'couponBodyBgColor'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-body { background:{{couponBodyBgColor}}; }']],
            ],
            'couponBodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>14,'right'=>13,'bottom'=>14,'left'=>13, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-coupon-body { padding:{{couponBodyPadding}} }' ],],
            ],
            
            // Input Field
            'couponInputTypo'=> [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-coupon-code']]
            ],
            'couponInputWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'220', 'unit' =>'px'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-code { width: {{couponInputWidth}}; }' ]],
            ],
            'couponInputTextColor'=> [
                'type' => 'string',
                'default' => '#7e7e7e',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-code { color:{{couponInputTextColor}}; font-weight:normal; }']],
            ],
            'couponInputBgColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-code { background:{{couponInputBgColor}}; }']],
            ],
            'couponInputPlaceholder'=>[
                'type' => 'string',
                'default' => 'Enter Coupon Code Here.....',
            ],
            'couponInputBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#c4c4c4','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-code']],
            ],
            'couponInputRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'4', 'unit' =>'px'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-coupon-code { border-radius:{{couponInputRadius}}; }' ],],
            ],
            'couponInputPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>40,'bottom'=>9,'left'=>12, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-coupon-code { padding:{{couponInputPadding}} !important;  line-height: normal; box-shadow:none}' ],],
            ],

            // Coupon Button
            'applyBtnPosition' => [
                'type' => 'string',
                'default' => 'withCoupon',
            ],
            'couponBtnText'=>[
                'type' => 'string',
                'default' => 'Apply Coupon',
            ],
            'couponBtnTextColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn { color:{{couponBtnTextColor}}; }']],
            ],
            'couponBtnTextHoverColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn:hover { color:{{couponBtnTextHoverColor}}; }']],
            ],
            'couponBtnBgColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn { background:{{couponBtnBgColor}}; }']],
            ],
            'couponBtnBgHoverColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn:hover { background-color:{{couponBtnBgHoverColor}}; }']],
            ],
            'couponBtnTypo'=> [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-checkout-coupon-submit-btn , .editor-styles-wrapper .block-editor-block-list__layout {{WOPB}} .wopb-checkout-coupon-submit-btn']]
            ],
            'couponBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn']],
            ],
            'couponBtnSpace'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'applyBtnPosition','condition'=>'==','value'=>'belowCoupon'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn { margin-top: {{couponBtnSpace}}px; }',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'applyBtnPosition','condition'=>'==','value'=>'withCoupon'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn { margin-left: {{couponBtnSpace}}px; }',
                    ]
               ],
            ],
            'couponBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn { border-radius:{{couponBtnRadius}}; }'
                    ],
                ],
            ],
            'couponBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>19,'bottom'=>9,'left'=>19, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-checkout-coupon-submit-btn , .editor-styles-wrapper {{WOPB}} .wopb-checkout-coupon-submit-btn { padding:{{couponBtnPadding}} !important; line-height: normal !important;}'
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
        register_block_type( 'product-blocks/checkout-coupon',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Coupon', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout() || (is_cart() && !WC()->cart->is_empty())) {
            $block_name = 'checkout-coupon';
            $wraper_before = $wraper_after = $content = '';

            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    if (!is_admin()) {
                        if (isset(WC()->customer)) {
                            ob_start();
                            require_once WOPB_PATH.'addons/builder/blocks/checkout/coupon/Template.php';
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
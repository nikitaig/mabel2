<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Checkout_Payment_Method {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            //General
            'showTitle'=> [
                'type' => 'boolean',
                'default' => true,
            ],

            // Section Title
            'sectionTitle'=>[
                'type' => 'string',
                'default' => 'Payment Method',
            ],
            'sectionTitleColor'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { color:{{sectionTitleColor}}; }']],
            ],
            'sectionTitleBg'=> [
                'type' => 'string',
                'default' => '#000000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { background-color:{{sectionTitleBg}}; }']],
            ],
            'headingAlign' => [
                'type' => 'string',
                'default' =>  'center',
                'style' => [(object)['selector' => '{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { text-align:{{headingAlign}}; }']]
            ],
            'sectionTitleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title']]
            ],
            'sectionRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { border-radius:{{sectionRadius}}; }'
                    ],
                ],
            ],
            'sectionTitlePadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>0,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { padding:{{sectionTitlePadding}}; }'
                    ],
                ],
            ],
            'sectionTitleSpace'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-payment-section-title { margin-bottom:{{sectionTitleSpace}}px;}']]
            ],

            // Checkbox & Label
            'labelColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper ul li label { color:{{labelColor}}; }']],
            ],
            'labelBg'=> [
                'type' => 'string',
                'default' => '#ebebeb',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment ul li label { background-color:{{labelBg}}; }']],
            ],
            'labelTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper ul li label ']]
            ],
            'checkboxSpace'=> [
                'type' => 'string',
                'default' => '0',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper ul li { padding-bottom:{{checkboxSpace}}px;}']]
            ],
            'labelPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>0,'bottom'=>5,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper ul li label { padding:{{labelPadding}} !important; }'
                    ],
                ],
            ],

            // Body content
            'bodyColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper ul li div p { color:{{bodyColor}}; }']],
            ],
            'bodyBg'=> [
                'type' => 'string',
                'default' => '#f5f5f5',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment ul li div { background-color:{{bodyBg}}; }']],
            ],
            'bodyTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper ul li div ']]
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>3,'right'=>7,'bottom'=>3,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[  
                        'selector'=>'{{WOPB}} .wopb-product-wrapper ul li .payment_box { padding:{{bodyPadding}} !important; }'
                    ],
                ],
            ],

            // Button
            'btnFullWIdth'=> [
                'type' => 'boolean',
                'default' => true,
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order { width: 100% !important; }']],
            ],
            'btnAlign' => [
                'type' => 'string',
                'default' =>  'left',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'btnAlign','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order {display: block; margin-right: auto; } ',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'btnAlign','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order {display: block; margin-left: auto;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'btnAlign','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order { display: block; margin: 0px auto ;}',
                    ]
               ],
            ],
            'btnTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '18', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order']]
            ],
            'btnColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order { color:{{btnColor}}; }']],
            ],
            'btnBg'=> [
                'type' => 'string',
                'default' => '#141414',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment button { background-color:{{btnBg}}; }']],
            ],
            'btnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order']],
            ],
            'btnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order { border-radius:{{btnRadius}}; }'
                    ],
                ],
            ],
            'btnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>8,'right'=>0,'bottom'=>8,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper #payment button#place_order { padding:{{btnPadding}}; }'
                    ],
                ],
            ],

            // Description
            'descpColor'=> [
                'type' => 'string',
                'default' => '#4a4a4a',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-privacy-policy-text { color:{{descpColor}}; }']],
            ],
            'linkColor'=> [
                'type' => 'string',
                'default' => '#51bf28',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-privacy-policy-link { color:{{linkColor}}; }']],
            ],
            'hoverColor'=> [
                'type' => 'string',
                'default' => '#4a4a4a',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-privacy-policy-text:hover { color:{{hoverColor}}; }']],
            ],
            'descpTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-privacy-policy-text']]
            ],
            'descpMargin'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>3,'right'=>8,'bottom'=>10,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-privacy-policy-text { margin:{{descpMargin}}; }'
                    ],
                ],
            ],
            
            // Field Container
            'containerBg'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-payment-container { background-color:{{containerBg}}; }']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#cccccc','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-payment-container ']],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>6,'right'=>6,'bottom'=>6,'left'=>6, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-payment-container { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-payment-container { padding:{{containerPadding}} !important; }'
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
        register_block_type( 'product-blocks/checkout-payment-method',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Payment Method', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout()) {
            $block_name = 'checkout-payment-method';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                    if (!is_admin()) {
                        if (isset(WC()->customer)) {
                            ob_start();
                            require_once WOPB_PATH.'addons/builder/blocks/checkout/payment_method/Template.php';
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
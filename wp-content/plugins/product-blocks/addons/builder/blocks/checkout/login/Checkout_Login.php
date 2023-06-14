<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Checkout_Login {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            //Toggle Text
            'toggleColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login-toggle .woocommerce-info { color:{{toggleColor}}; }']],
            ],
            'linkColor'=> [
                'type' => 'string',
                'default' => '#007cba',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle a { color:{{linkColor}}; }']],
            ],
            'toggleHoverColor'=> [
                'type' => 'string',
                'default' => '#2c2c2c',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info:hover { color:{{toggleHoverColor}}; }']],
            ],
            'toggleBg'=> [
                'type' => 'string',
                'default' => '#f4f4f4',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info { background-color:{{toggleBg}}; }']],
            ],
            'toggleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info ']]
            ],
            'toggleBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#d8d8d8','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info ']],
            ],
            'toggleRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                        (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info { border-radius:{{toggleRadius}}; }'
                    ],
                ],
            ],
            'togglePadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info { padding:{{togglePadding}}; }'
                    ],
                ],
            ],      
            'toggleSpace'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login-toggle .woocommerce-info { margin-bottom:{{toggleSpace}}px;}']]
            ],

            // Label 
            'labelColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label { color:{{labelColor}}; }']],
            ],
            'requiredIconColor'=> [
                'type' => 'string',
                'default' => '#f41e1e',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label .required { color:{{requiredIconColor}}; }']],
            ],
            'labelTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label']]
            ],
            'labelMargin'=> [
                'type' => 'string',
                'default' => '5',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper  .woocommerce-form-login label { margin-bottom:{{labelMargin}}px;}']]
            ],

            // Input Field
            'inputHeight'=> [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input:not([type="checkbox"]) { min-height:{{inputHeight}}px;}']]
            ],
            'inputColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input { color:{{inputColor}}; }']],
            ],
            'placeholderColor'=> [
                'type' => 'string',
                'default' => '#898989',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input::placeholder { color:{{placeholderColor}}; }']],
            ],
            'inputBgColor' => [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input { background-color:{{inputBgColor}}; }']],
            ],
            'inputTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input']]
            ],
            'inputBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input']],
            ],
            'inputRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                        (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input { border-radius:{{inputRadius}}; }'
                    ],
                ],
            ],
            'inputMargin'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>10,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login .form-row-first , {{WOPB}} .wopb-product-wrapper .woocommerce-form-login .form-row-last { margin:{{inputMargin}}; }'
                    ],
                ],
            ],

            'inputFocusColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input:focus { color:{{inputFocusColor}}; }']],
            ],
            'placeholderFocusColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login input:focus::placeholder { color:{{placeholderFocusColor}}; }']],
            ],

            // Button
            'btnTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit , .editor-styles-wrapper {{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit']]
            ],
            'btnColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit { color:{{btnColor}}; }']],
            ],
            'btnBg'=> [
                'type' => 'string',
                'default' => '#007cba',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit { background-color:{{btnBg}}; }']],
            ],
            'btnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit']],
            ],
            'btnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit { border-radius:{{btnRadius}} ; }'
                    ],
                ],
            ],
            'btnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>6,'right'=>24,'bottom'=>6,'left'=>24, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login__submit { padding:{{btnPadding}} !important; }'
                    ],
                ],
            ],

            // Description
            'descpColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login p:first-child { color:{{descpColor}}; }']],
            ],
            'descpTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login p:first-child']]
            ],
            'descpMargin'=> [
                'type' => 'string',
                'default' => '5',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login p:first-child { margin-bottom:{{descpMargin}}px;}']]
            ],

            // Remember checkbox
            'checkboxColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label.woocommerce-form-login__rememberme { color:{{checkboxColor}}; }']],
            ],
            'checkboxTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label.woocommerce-form-login__rememberme ']]
            ],
            'checkboxMargin'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login label.woocommerce-form-login__rememberme { margin-bottom:{{checkboxMargin}}px;}']]
            ],

            // Field Container
            'containerBg'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login { background-color:{{containerBg}}; }']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#d8d8d8','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login ']],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                        (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>15,'right'=>15,'bottom'=>15,'left'=>15, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .woocommerce-form-login { padding:{{containerPadding}}; }'
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
        register_block_type( 'product-blocks/checkout-login',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Checkout Login', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout()) {
            $block_name = 'checkout-login';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                        if (!is_admin()) {
                            if (isset(WC()->customer)) {
                                ob_start();
                                require_once WOPB_PATH.'addons/builder/blocks/checkout/login/Template.php';
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
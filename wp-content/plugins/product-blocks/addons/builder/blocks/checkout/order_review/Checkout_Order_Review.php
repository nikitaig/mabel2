<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Checkout_Order_Review{
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
                'default' => 'Your Order',
            ],
            'sectionTitleColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { color:{{sectionTitleColor}}; }']],
            ],
            'sectionTitleBg'=> [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { background-color:{{sectionTitleBg}}; }']],
            ],
            'headingAlign' => [
                'type' => 'string',
                'default' =>  'center',
                'style' => [(object)['selector' => '{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { text-align:{{headingAlign}}; }']]
            ],
            'sectionTitleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '' , 'weight' => '700'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title']]
            ],
            'sectionRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { border-radius:{{sectionRadius}}; }'
                    ],
                ],
            ],
            'sectionTitlePadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>3,'right'=>8,'bottom'=>3,'left'=>8, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { padding:{{sectionTitlePadding}}; }'
                    ],
                ],
            ],
            'sectionTitleSpace'=> [
                'type' => 'string',
                'default' => '5',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-order-section-title { margin-bottom:{{sectionTitleSpace}}px;}']]
            ],

            // Table Heading
            'headingColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th{ color:{{headingColor}}; }']],
            ],
            'headingBg'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th{ background-color:{{headingBg}}; }']],
            ],
            'headingTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize' , 'weight' => '700' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th']]
            ],
            'headingBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th']],
            ],
            'headingPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th { padding:{{headingPadding}} !important; }'
                    ],
                ],
            ],
            'headingMargin'=> [
                'type' => 'string',
                'default' => '10',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table thead tr th { padding-bottom:{{headingMargin}}px;}']]
            ],

            // Table Body
            'bodyTextColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td {color:{{bodyTextColor}}; }']],
            ],
            'bodyPriceColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td.product-total { color:{{bodyPriceColor}}; }']],
            ],
            'bodyBg'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td , {{WOPB}} .wopb-product-wrapper table tfoot tr th , {{WOPB}} .wopb-product-wrapper table tfoot tr td { background-color:{{bodyBg}} !important; }']],
            ],
            'bodyTextTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td ']]
            ],
            'bodyPriceTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td.product-total ']]
            ],
            'bodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0 ],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td , {{WOPB}} .wopb-product-wrapper table tfoot tr th , {{WOPB}} .wopb-product-wrapper table tfoot tr td ']],
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td , {{WOPB}} .wopb-product-wrapper table tfoot tr th , {{WOPB}} .wopb-product-wrapper table tfoot tr td { padding:{{bodyPadding}} !important; }'
                    ],
                ],
            ],
            'bodyMargin'=> [
                'type' => 'string',
                'default' => '12',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr td , {{WOPB}} .wopb-product-wrapper table tfoot tr th , {{WOPB}} .wopb-product-wrapper table tfoot tr td { padding-bottom: {{bodyMargin}}px;}']]
            ],

            // Total
            'totalColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tfoot tr th {color:{{totalColor}}; }']],
            ],
            'totalPriceColor'=> [
                'type' => 'string',
                'default' => '#343434',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tfoot tr td {color:{{totalPriceColor}}; }']],
            ],
            'totalTextTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize' , 'weight' => '700' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tfoot tr th']]
            ],
            'totalPriceTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize' , 'weight' => '500' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tfoot tr td']]
            ],

            // Field Container
            'containerBg'=> [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-review-order { background-color:{{containerBg}}; }']],
            ],
            'containerBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e8e8e8','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-review-order ']],
            ],
            'containerRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                        (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-review-order { border-radius:{{containerRadius}}; overflow:hidden; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>15,'right'=>15,'bottom'=>15,'left'=>15, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-checkout-review-order { padding:{{containerPadding}}; }'
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
        register_block_type( 'product-blocks/checkout-order-review',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Order Review', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (is_checkout()) {
            $block_name = 'checkout-order-review';
            $wraper_before = $wraper_after = $content = '';
            if (function_exists('WC')) {
                $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                    $wraper_before .= '<div class="wopb-product-wrapper">';
                    
                    if (!is_admin()) {
                        if (isset(WC()->customer)) {
                            ob_start();
                            require_once WOPB_PATH.'addons/builder/blocks/checkout/order_review/Template.php';
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
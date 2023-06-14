<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Cart_Table {
    public function __construct() {
        add_action('init', array($this, 'register'));
        add_action( 'init', array($this, 'woocommerce_clear_cart_url') );
    }
    public function get_attributes($default = false) {

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],
            'showCoupon' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showUpdate' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showEmpty' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showContinue' => [
                'type' => 'boolean',
                'default' => false,
            ],
            'showCrossSell' => [
                'type' => 'boolean',
                'default' => false,
            ],

            // Table Head
            'productHead'=>[
                'type' => 'string',
                'default' => 'Product',
            ],
            'priceHead'=>[
                'type' => 'string',
                'default' => 'Price',
            ],
            'qtyHead'=>[
                'type' => 'string',
                'default' => 'Quantity',
            ],
            'subTotalHead'=>[
                'type' => 'string',
                'default' => 'Subtotal',
            ],
            'tableBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper .wopb-cart-form table']],
            ],
            'tableHeadBgColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table thead th { background-color:{{tableHeadBgColor}} !important; }']],
            ],
            'tableHeadTextColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table thead th, {{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper tbody tr td:before { color:{{tableHeadTextColor}}; }']],
            ],
            'headingTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table thead th, {{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper tbody tr td:before']]
            ],
            'headingBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#000','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table thead th']],
            ],
            'tableRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0], 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table { border-radius:{{tableRadius}}; overflow:hidden; border-collapse: separate !important; }'
                    ],
                ],
            ],

            'headingPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'bottom'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper table thead th { padding:{{headingPadding}}; }'
                    ],
                ],
            ],

            // table body
            'tableBodyTextColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td , {{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td .qty { color:{{tableBodyTextColor}}; }']],
            ],
            'tableBodyLinkColor'=> [
                'type' => 'string',
                'default' => '#0000FF',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr a { color:{{tableBodyLinkColor}}; }']],
            ],
            'tableBodyLinkHoverColor'=> [
                'type' => 'string',
                'default' => '#0000FF',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr a:hover { color:{{tableBodyLinkHoverColor}}; }']],
            ],

            'bodyTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'normal'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td, {{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td .qty ']]
            ],
            
            'bodyTitleTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'normal'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table .wopb-cart-table-product-section']]
            ],
            'bodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#000','type' => 'solid' ],
                'style' => [ (object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td']],
            ],
            'tableBodyBgColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper table tbody tr > td { background-color:{{tableBodyBgColor}} !important; }']],
            ],
            'bodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['left'=> 0, 'top'=>10, 'right'=>10, 'bottom'=>10, 'unit'=>'px']],
                'style' => [ (object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td { padding:{{bodyPadding}}!important; }' ],],
            ],

            // Quantity
            'quantityBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td .qty']],
            ],
            'quantityWidth'=> [
                'type' => 'string',
                'default' => '84',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr td .qty {width:{{quantityWidth}}px; max-width:{{quantityWidth}}px; line-height: normal; text-align: left;}']],
            ],
            'quantityBgColor'=> [
                'type' => 'string',
                'default' => '#f8f8f8',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr .qty { background-color:{{quantityBgColor}}; }']],
            ],
            'quantityRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [ (object)[ 'selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr .qty { border-radius:{{quantityRadius}} !important; }' ],
                ],
            ],
            'quantityPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>5,'right'=>2,'bottom'=>5,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr .qty { padding:{{quantityPadding}} !important; }'
                    ],
                ],
            ],

            // Product Image
           'imageBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-image-section a img']],
            ],
            'imageSize'=> [
                'type' => 'string',
                'default' => '81',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-image-section { margin-right: 14px; width:{{imageSize}}px; }']],
            ],
            'imageRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>2,'right'=>2,'bottom'=>2,'left'=>2, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-image-section a img { border-radius:{{imageRadius}}; }'
                    ],
                ],
            ],            

            //Remove Button
            'removeBtnTextColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr a.wopb-cart-product-remove { color:{{removeBtnTextColor}}; }']],
            ],
            'removeBtnPosition' => [
                'type' => 'string',
                'default' => 'left',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'removeBtnPosition','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'
                                    {{WOPB}} .wopb-cart-table-medium .product-remove , .block-editor-block-list__block {{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper .product-remove {margin-right:14px} 
                                    {{WOPB}} .product-remove.wopb-cart-table-small{text-align:{{removeBtnPosition}}} 
                                    {{WOPB}} table.cart td.product-remove.wopb-cart-table-small a.remove {position: initial} 
                                    '
                        ,
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'removeBtnPosition','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .product-remove {margin-left:12px}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'removeBtnPosition','condition'=>'==','value'=>'withImage'],
                        ],
                        'selector'=>'{{WOPB}} .product-remove .remove{position: absolute ;}',
                    ]
               ],
            ],
            'removeBtnHoverColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper.wopb-cart-table-wrapper table.wopb-cart-table tbody tr a.wopb-cart-product-remove:hover { color:{{removeBtnHoverColor}}; }']],
            ],
            'removeBtnBgColor'=> [
                'type' => 'string',
                'default' => '#222',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-product-remove { background:{{removeBtnBgColor}}; }']],
            ],
            'removeBtnBgHoverColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-product-remove:hover { background:{{removeBtnBgHoverColor}}; }']],
            ],
            'removeBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-product-remove']],
            ],
            'removeBtnFontSize'=> [
                'type' => 'string',
                'default' => '19',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-product-remove { font-size:{{removeBtnFontSize}}px; }']],
            ],
            'removeBtnSize'=> [
                'type' => 'string',
                'default' => '19',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-product-remove { height:{{removeBtnSize}}px; width:{{removeBtnSize}}px; }']],
            ],
            'removeBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>10,'bottom'=>10,'left'=>10, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-product-remove { border-radius:{{removeBtnRadius}}; }'
                    ],
                ],
            ],
            
            'removeBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-cart-product-remove { padding:{{removeBtnPadding}}; }'
                    ],
                ],
            ],

            // Table Footer
                'tableFooterBgColor'=> [
                    'type' => 'string',
                    'default' => '',
                    'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-cart-table-options { background:{{tableFooterBgColor}}; }']],
                ],
                'tableFooterBgHoverColor'=> [
                    'type' => 'string',
                    'default' => '',
                    'style' => [(object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-cart-table-options:hover { background:{{tableFooterBgHoverColor}}; }']],
                ],
                'tableFooterBorder'=> [
                    'type' => 'object',
                    'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                    'style' => [
                        (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-cart-table-options']],
                ],
                'tableFooterRadius'=> [
                    'type' => 'object',
                    'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                    'style' => [
                         (object)[
                            'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-cart-table-options { border-radius:{{tableFooterRadius}}; }'
                        ],
                    ],
                ],
                'tableFooterPadding'=> [
                    'type' => 'object',
                    'default' => (object)['lg'=>(object)['top'=>10,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                    'style' => [
                        (object)[
                            
                            'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-cart-table-options { padding:{{tableFooterPadding}}; }'
                        ],
                    ],
                ],

            // Apply Coupon
            'couponInputPlaceholder'=>[
                'type' => 'string',
                'default' => 'Enter Coupon Code Here.....',
            ],
            'couponBtnText'=>[
                'type' => 'string',
                'default' => 'Apply Coupon',
            ],
            'couponInputFontSize'=> [
                'type' => 'string',
                'default' => '14',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code { font-size:{{couponInputFontSize}}px; }']],
            ],
            'couponInputTextColor'=> [
                'type' => 'string',
                'default' => '#7e7e7e',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code { color:{{couponInputTextColor}}; font-family:Roboto; font-weight:normal; }']],
            ],
            'couponInputBgColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code { background:{{couponInputBgColor}}; }']],
            ],
            'couponInputBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#8c8f94','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code']],
            ],
            'couponInputRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code { border-radius:{{couponInputRadius}}; }'
                    ],
                ],
            ],
            'couponInputPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>10,'bottom'=>9,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-cart-table-options .wopb-coupon-code { padding:{{couponInputPadding}} !important; line-height: normal; box-shadow:none}'
                    ],
                ],
            ],
            'couponBtnTextColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn { color:{{couponBtnTextColor}}; font-family:FontAwesome; font-weight:normal; }']],
            ],
            'couponBtnTextHoverColor'=> [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn:hover { color:{{couponBtnTextHoverColor}}; }']],
            ],
            'couponBtnBgColor'=> [
                'type' => 'string',
                'default' => '#333333',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn { background:{{couponBtnBgColor}}; }']],
            ],
            'couponBtnBgHoverColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn:hover { background-color:{{couponBtnBgHoverColor}}; }']],
            ],
            'couponBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn']],
            ],
            'couponBtnFontSize'=> [
                'type' => 'string',
                'default' => '14',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-coupon-submit-btn { font-size:{{couponBtnFontSize}}px !important; }']],
            ],
            'couponBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn { border-radius:{{couponBtnRadius}}; }'
                    ],
                ],
            ],
            'couponBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>10,'bottom'=>9,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[
                        
                        'selector'=>'{{WOPB}} .wopb-cart-coupon-submit-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-coupon-submit-btn { padding:{{couponBtnPadding}} !important; line-height: normal;}'
                    ],
                ],
            ],
            
            // cart continue shopping
            'continueShoppingText'=>[
                'type' => 'string',
                'default' => 'Continue Shopping',
            ],
            'continueShoppingTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn { color:{{continueShoppingTextColor}}; font-family:Roboto; font-weight:500;}']],
            ],
            'continueShoppingBgColor' => [
                'type' => 'string',
                'default' => '#333333',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn { background-color:{{continueShoppingBgColor}}; }']],
            ],
            'continueShoppingTextHoverColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn:hover { color:{{continueShoppingTextHoverColor}}; }']],
            ],
            'continueShoppingBgHoverColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn:hover { background-color:{{continueShoppingBgHoverColor}}; }']],
            ],
            'continueShoppingBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn']],
            ],
            'continueShoppingFontSize' => [
                'type' => 'string',
                'default' => '14',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-shopping-btn { font-size:{{continueShoppingFontSize}}px !important; }']],
            ],
            'continueShoppingPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>10,'bottom'=>9,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-shopping-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-shopping-btn  { padding:{{continueShoppingPadding}} !important; line-height: normal;}'],
                ],
            ],
            'continueShoppingRadius' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-shopping-btn { border-radius:{{continueShoppingRadius}}; }'
                    ],
                ],
            ],

            // cart empty
            'emptyText'=>[
                'type' => 'string',
                'default' => 'Empty Cart',
            ],
            'emptyTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn { color:{{emptyTextColor}}; font-family:Roboto; font-weight:500;}']],
            ],
            'emptyBgColor' => [
                'type' => 'string',
                'default' => '#333333',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn { background-color:{{emptyBgColor}}; }']],
            ],
            'emptyHoverTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn:hover { color:{{emptyHoverTextColor}}; }']],
            ],
            'emptyHoverBgColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn:hover { background-color:{{emptyHoverBgColor}}; }']],
            ],
            'emptyBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn']],
            ],
            'emptyFontSize' => [
                'type' => 'string',
                'default' => '14',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-empty-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-empty-btn { font-size:{{emptyFontSize}}px !important; }']],
            ],
            'emptyPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>10,'bottom'=>9,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)[ 'selector'=>'{{WOPB}} .wopb-cart-empty-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-empty-btn { padding:{{emptyPadding}} !important; line-height: normal;}'],
                ],
            ],
            'emptyRadius' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-empty-btn { border-radius:{{emptyRadius}}; }'
                    ],
                ],
            ],

             // cart update
             'updateText'=>[
                'type' => 'string',
                'default' => 'Update Cart',
            ],
            'updateTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-update-btn { color:{{updateTextColor}}; font-family:Roboto; font-weight:500; }']],
            ],
            'updateBgColor' => [
                'type' => 'string',
                'default' => '#333333',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-update-btn { background-color:{{updateBgColor}}; }']],
            ],
            'updateHoverTextColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-update-btn:hover { color:{{updateHoverTextColor}}; }']],
            ],
            'updateHoverBgColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-cart-update-btn:hover { background-color:{{updateHoverBgColor}}; }']],
            ],
            'updateBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#000','type' => 'solid' ],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-update-btn']],
            ],
            'updateFontSize' => [
                'type' => 'string',
                'default' => '14',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-update-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-update-btn { font-size:{{updateFontSize}}px !important; }']],
            ],

            'updatePadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>9,'right'=>10,'bottom'=>9,'left'=>10, 'unit'=>'px']],
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-cart-update-btn , .editor-styles-wrapper {{WOPB}} .wopb-cart-update-btn { padding:{{updatePadding}} !important; line-height: normal;}'],
                ],
            ],
            'updateRadius' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-cart-update-btn { border-radius:{{updateRadius}}; }'
                    ],
                ],
            ],            

            //Cross Sell Product Position
            'crossSellPosition'=> [
                'type' => 'string',
                'default' => 'bottom',
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
        if( $default ) {
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
        register_block_type( 'product-blocks/cart-table',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Cart Table', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    function woocommerce_clear_cart_url() {
        global $woocommerce;
        if ( isset( $_GET['empty-cart'] ) ) {
            $woocommerce->cart->empty_cart();
            header("Location: ".wc_get_cart_url());
            exit();
        }
      }

    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        $block_name = 'cart-table';
        $wraper_before = $wraper_after = $content = '';

        if (function_exists('WC')) {
            $wraper_before.='<div id="'.($attr['advanceId']? $attr['advanceId']:'').'"'.' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:''). (isset($attr["align"])? ' align' .esc_attr($attr["align"]):'') . '">';
                $wraper_before .= '<div class="wopb-product-wrapper wopb-cart-table-wrapper">';
                    if (!is_admin()) {
                        if (isset(WC()->customer)) {
                            ob_start();
                                if (WC()->cart->is_empty()) {
                                    wc_get_template( 'cart/cart-empty.php' );
                                } else {
                                    require_once WOPB_PATH.'addons/builder/blocks/cart_table/Template.php';
                                }
                                $content .= ob_get_clean();
                        }
                    }
                $wraper_after.='</div> ';
            $wraper_after.='</div> ';
        }
        return $wraper_before.$content.$wraper_after;
    }
}
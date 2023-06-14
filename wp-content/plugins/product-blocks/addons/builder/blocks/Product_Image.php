<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Image{

    public function __construct() {
        add_action('init', array($this, 'register'));
    }

    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            //--------------------------
            //  Image Style
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'showGallery' => [
                'type' => 'boolean',
                'default'=> true,
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'showGallery','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}}   .thumb-image {display:block;}'
                    ],
                ],
            ],
            'showSale'=>[
                'type' => 'boolean',
                'default'=> true
            ],
            'showlightBox'=>[
                'type' => 'boolean',
                'default'=> true
            ],
            'arrowLargeImage'=>[
                'type' => 'boolean',
                'default'=> true,
                'style' => [
                    (object)[
                       'selector'=>'{{WOPB}} .woocommerce-product-gallery__wrapper .slick-arrow > svg {display:block;}'
                   ],
               ],
            ],
            'arrowGalleryImage'=>[
                'type' => 'boolean',
                'default'=> true,
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                       'selector'=>'{{WOPB}}  {display:block;}',
                   ],
                ],
            ],
            'imageView'=>[
                'type' => 'string',
                'default'=> 'onclick'
            ],
            'imageHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .slick-slide img , .woocommerce {{WOPB}} .wopb-product-gallery-wrapper .slick-slide img { max-height :{{imageHeight}}; height :{{imageHeight}};}
                        {{WOPB}} .wopb-product-gallery-left .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav , {{WOPB}} .wopb-product-gallery-right .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav { height :{{imageHeight}};} '
                                       
                    ],
                ]
            ],
            'imageScale' => [
                'type' => 'string',
                'default' => 'cover',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-slide img  {object-fit: {{imageScale}};}'
                    ],
                ],
            ],
            'imageBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper img'
                    ],
                ],
            ],
            'imageRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper img { border-radius:{{imageRadius}};}'
                    ],
                ],
            ],


            //--------------------------
            //  Gallery Style
            //--------------------------
            'galleryPosition' => [
                'type' => 'string',
                'default' => 'bottom',
            ],
            'galleryColumns' => [
                'type' => 'object',
                'default' => (object)['lg' =>'4','sm' =>'2','xs'=>'2'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .flex-control-nav { grid-template-columns: repeat({{galleryColumns}}, 1fr); }'
                    ],
                ],
            ],
            'gallerycolumnGap' => [
                'type' => 'object',
                'default' => (object)['lg' =>'10', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'top'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-bottom .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-slide img{ pading: 0px {{gallerycolumnGap}};}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'bottom'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-bottom .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-slide img { padding: 0px {{gallerycolumnGap}};}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-left .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-slide img { padding: 0 0 {{gallerycolumnGap}} 0;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-left .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-slide img{ padding: 0 0 {{gallerycolumnGap}} 0;}',
                    ],
                ],
           
            ],
            'gallerySpace' => [
                'type' => 'object',
                'default' => (object)['lg' =>'20', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-left .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav {padding-right:{{gallerySpace}};} {{WOPB}} .wopb-product-gallery-left .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow {width: calc(100% - {{gallerySpace}});}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-right .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav {padding-left:{{gallerySpace}};} {{WOPB}} .wopb-product-gallery-right .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow {width: calc(100% - {{gallerySpace}});}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'top'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-top .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav {padding-bottom: {{gallerySpace}};} {{WOPB}} .wopb-product-gallery-top .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow {height: calc(100% - {{gallerySpace}});}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'galleryPosition','condition'=>'==','value'=>'bottom'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-bottom .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav {padding-top: {{gallerySpace}};}',
                    ],
                ],
            ],
            //--------------------------
            //      Sales Setting/Style
            //--------------------------
            'saleText' => [
                'type' => 'string',
                'default' => 'Sale!'
            ],
            'salePosition' => [
                'type' => 'string',
                'default' => 'topLeft',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'salePosition','condition'=>'==','value'=>'topLeft'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag{top:0;left:0;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'salePosition','condition'=>'==','value'=>'topRight'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag{top:0;right:0;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'salePosition','condition'=>'==','value'=>'buttonLeft'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag{bottom:0;left:0;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'salePosition','condition'=>'==','value'=>'buttonRight'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag{bottom:0;right:0;}',
                    ],
               ],
            ],
            'saleDesign' => [
                'type' => 'string',
                'default' => 'text',
            ],
            'saleStyle' => [
                'type' => 'string',
                'default' => 'classic',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'saleStyle','condition'=>'==','value'=>'classic'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag::before{display:none;} .wopb-product-gallery-sale-tag{width:auto;height:auto;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'saleStyle','condition'=>'==','value'=>'ribbon'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag{ width:65px; height:40px; }',
                    ],
                ],
                
            ],
            'salesColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag { color:{{salesColor}}; }'
                    ],
                ],
            ],
            'salesBgColor' => [
                'type' => 'string',
                'default' => '#31b54e',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag { background:{{salesBgColor}}; } {{WOPB}} .wopb-onsale.wopb-onsale-ribbon:before { border-left: 23px solid {{salesBgColor}}; border-right: 23px solid {{salesBgColor}}; }'
                    ],
                ],
            ],
            'salesTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography'=>1,'size'=>(object)['lg'=>'11', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'0', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'20', 'unit'=>'px'],'decoration'=>'none','transform' => 'uppercase','family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag'
                    ],
                ],
            ],
            'salesPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'bottom'=>4,'left'=>8,'right'=>8, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'saleStyle','condition'=>'==','value'=>'classic'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag { padding:{{salesPadding}}; }'
                    ],
                ],
            ],
            'salesRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'2', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'saleStyle','condition'=>'==','value'=>'classic'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag { border-radius:{{salesRadius}}; }'
                    ],
                ],
            ],
            'salesMargin' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'bottom'=>4,'left'=>8,'right'=>8, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-sale-tag { margin:{{salesMargin}}; }'
                    ],
                ],
            ],
            //--------------------------
            //      Lightbox Zoom Setting/Style
            //--------------------------
            'iconPosition' => [
                'type' => 'string',
                'default' => 'topRight',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'iconPosition','condition'=>'==','value'=>'topRight'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom {top:10px;right:10px;}'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'iconPosition','condition'=>'==','value'=>'topLeft'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{top:10px;left:10px;}'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'iconPosition','condition'=>'==','value'=>'buttonLeft'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{top:auto ; bottom:10px; left:10px;}'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'iconPosition','condition'=>'==','value'=>'buttonRight'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{top:auto; bottom:10px; right:10px;}'
                    ],
                ],
            ],
            'zoomIconSize' => [
                'type' => 'object',
                'default' => (object)['lg' =>'20', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom >svg { width:{{zoomIconSize}}; }'
                    ],
                ]
            ],
            'iconColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom >svg{fill:{{iconColor}}}'
                    ]
                ],
            ],
            'iconHoverColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom:hover >svg{fill:{{iconHoverColor}}}'
                    ]
                ],
            ],
            'iconBg' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{background-color:{{iconBg}}}'
                    ]
                ],
            ],
            'iconHoverBg' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom:hover{background-color:{{iconHoverBg}}}'
                    ]
                ],
            ],
            'iconBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom'
                    ],
                ],
            ],
            'iconHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom:hover'
                    ],
                ],
            ],
            'iconRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{ border-radius:{{iconRadius}}; }'
                    ],
                ],
            ],
            'iconHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom:hover{ border-radius:{{iconHoverRadius}}; }'
                    ],
                ],
            ],
            'iconPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom {padding:{{iconPadding}};}'
                    ],
                ],
            ],
            'iconMargin' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .wopb-product-zoom{margin:{{iconMargin}};}'
                    ],
                ],
            ],


            //--------------------------
            //      Arrow Setting
            //--------------------------
            'arrowSize' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow svg { width:{{arrowSize}}; }'
                    ],
                ]
            ],
            'arrowWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { width:{{arrowWidth}}; }'
                    ],
                ]
            ],
            'arrowHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { height:{{arrowHeight}}; } {{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { line-height:{{arrowHeight}}; }'
                    ],
                ]
            ],
            'arrowVartical' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-slick-next-large ,.wopb-product-gallery-wrapper .slick-next { right:{{arrowVartical}}; } {{WOPB}} .wopb-slick-prev-large , .wopb-product-gallery-wrapper .slick-prev{ left:{{arrowVartical}}; }'
                    ],
                ]
            ],
            'arrowHorizontal' => [
                'type' => 'object',
                'default' => (object)['lg'=>''],
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { top:{{arrowHorizontal}}; }'
                    ],
                ]
            ],
            'arrowColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow svg { fill:{{arrowColor}}; }'
                    ],
                ],
            ],
            'arrowHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow:hover svg { fill:{{arrowHoverColor}}; }'
                    ],
                ],
            ],
            'arrowBg' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { background:{{arrowBg}}; }'
                    ],
                ],
            ],
            'arrowHoverBg' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow:hover { background:{{arrowHoverBg}}; }'
                    ],
                ],
            ],
            'arrowBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow'
                    ],
                ],
            ],
            'arrowHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow:hover'
                    ],
                ],
            ],
            'arrowRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'left' => '','right' => '', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow { border-radius:{{arrowRadius}}; }'
                    ],
                ],
            ],
            'arrowHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'left' => '','right' => '', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow:hover{border-radius:{{arrowHoverRadius}};}'
                    ],
                ],
            ],
            'arrowShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow'
                    ],
                ],
            ],
            'arrowHoverShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'arrowLargeImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-gallery-wrapper .slick-arrow:hover'
                    ],
                ],
            ],
            //--------------------------
            //   Gallery Arrow Style
            //--------------------------
            'arrowGallerySize' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .thumb-image .slick-arrow > svg{ width:{{arrowGallerySize}};}'
                    ],
                ]
            ],
            'arrowGalleryColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-builder-slider-nav .slick-arrow svg { fill:{{arrowGalleryColor}}; }'
                    ],
                ],
            ],
            'arrowGalleryHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-builder-slider-nav .slick-arrow:hover svg { fill:{{arrowGalleryHoverColor}}; }'
                    ],
                ],
            ],
            'arrowGalleryBg' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow { background:{{arrowGalleryBg}}; }'
                    ],
                ],
            ],
            'arrowGalleryHoverBg' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow:hover { background:{{arrowGalleryHoverBg}}; }'
                    ],
                ],
            ],
            'arrowGalleryPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'depends' => [ 
                            (object)['key'=>'arrowGalleryImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-product-gallery__wrapper .wopb-builder-slider-nav .slick-arrow { padding:{{arrowGalleryPadding}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Wrapper
            //--------------------------

            'wrapBg' => [
                'type' => 'string',
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
                        'selector'=>'{{WOPB}} .wopb-product-wrapper { border-radius:{{wrapRadius}}; }'
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
            'wrapMargin' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper { margin:{{wrapMargin}}; }'
                    ],
                ],
            ],
            'wrapOuterPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper { padding:{{wrapOuterPadding}}; }'
                    ],
                ],
            ],
            'wrapInnerPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper { padding:{{wrapInnerPadding}}; }'
                    ],
                ],
            ],
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
        register_block_type( 'product-blocks/product-image',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Image', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }



    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        $block_name = 'product-image';
        $wraper_before = $wraper_after = $content = '';

		$product = wc_get_product();
        
        if (!empty($product)) {
            global $productx_settings;
            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-product-wrapper wopb-product-gallery-'.esc_attr($attr['galleryPosition']).'">';


            global $productx_sales;
            global $productx_sales_text;
            $sales = $product->get_sale_price();
            $regular = $product->get_regular_price();
            $percentage = ($regular && $sales) ? round((($regular - $sales) / $regular) * 100) : 0;
            if ($attr['showSale'] && $percentage) {
                $productx_sales_text = $attr["saleDesign"] == "textDigit" ? '-'.esc_attr($percentage).'% '.esc_attr($attr["saleText"]) : ($attr["saleDesign"] == "digit" ? '-'.esc_attr($percentage).'%' : esc_attr($attr["saleText"]));
                $flash_sale = function() {
                    global $productx_sales_text;
                    return '<div class="wopb-product-gallery-sale-tag">'.esc_html($productx_sales_text).'</div>';
                };
                add_filter('woocommerce_sale_flash', $flash_sale);
                ob_start();
                woocommerce_show_product_sale_flash();
                $productx_sales = ob_get_clean();
                remove_filter('woocommerce_sale_flash', $flash_sale);
            }

            $gallery_classes = function($classes) {                
                if (in_array('woocommerce-product-gallery', $classes)) {
                    $classes[] = 'woocommerce-product-gallery';
                    $classes[array_search('green', $classes)] = ' woocommerce-product-gallery-off';
                    $classes[] = 'slider';
                }
                return $classes;
            };

            $image_html = function($gallery, $post_id) {
                return '';
            };

            $productx_settings['onview'] = $attr['imageView'];
            $productx_settings['showlight'] = $attr['showlightBox'];
            $productx_settings['position'] = $attr['galleryPosition'];
            $productx_settings['showArrow'] = $attr['arrowLargeImage'];
            $productx_settings['showGalleryArrow'] = $attr['arrowGalleryImage'];
            $productx_settings['column'] = $attr['galleryColumns'];

            $slick_html = function() {
                global $product;
                global $productx_settings;
                global $productx_sales;
                $attachment = $product->get_image_id();
                $gallery    = $product->get_gallery_image_ids();

                $all_id = [];
                if (!empty($attachment)) {
                    $all_id[] = $attachment;
                }
                if (!empty($gallery)) {
                    $all_id = array_merge($all_id, $gallery);
                }

                $image_full = $image_thumb = '';
                $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
                $thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
                $full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) ); 
                foreach ($all_id as $key => $attachment_id) {
                    $thumbnail_src = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
                    $full_src = wp_get_attachment_image_src( $attachment_id, $full_size );
                    $alt_text = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
                    $image_full .= '<div><img src="'.esc_url($full_src[0]).'" alt="'.esc_attr($alt_text).'" data-width="'.esc_attr($full_src[1]).'" data-height="'.esc_attr($full_src[2]).'"/></div>';
                    $image_thumb .= '<div><img src="'.esc_url($thumbnail_src[0]).'" alt="'.esc_attr($alt_text).'" /></div>';
                }

                echo '<div class="wopb-product-gallery-wrapper' . ($productx_settings['showlight'] ? ' wopb-product-zoom-wrapper' : '' ). '">';
                    if ($productx_settings['showlight']) {
                        echo '<a href="#" class="wopb-product-zoom"><svg enable-background="new 0 0 612 612" version="1.1" viewBox="0 0 612 612" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m243.96 340.18-206.3 206.32 0.593-125.75c0-10.557-8.568-19.125-19.125-19.125s-19.125 8.568-19.125 19.125v172.12c0 5.661 2.333 10.232 6.043 13.368 3.462 3.538 8.282 5.757 13.637 5.757h171.57c10.557 0 19.125-8.567 19.125-19.125 0-10.557-8.568-19.125-19.125-19.125h-126.78l206.53-206.51-27.043-27.061zm362-334.42c-3.461-3.538-8.28-5.757-13.616-5.757h-171.59c-10.557 0-19.125 8.568-19.125 19.125s8.568 19.125 19.125 19.125h126.76l-206.51 206.53 27.042 27.042 206.32-206.32-0.612 125.75c0 10.557 8.568 19.125 19.125 19.125s19.125-8.568 19.125-19.125v-172.12c0-5.661-2.333-10.231-6.044-13.368z"/></svg></a>';
                    }
                    echo wp_kses_post($productx_sales);
                    echo '<div class="slider wopb-builder-slider-for" data-arrow="'.esc_attr($productx_settings['showArrow']).'">';
                        echo wp_kses_post($image_full);
                    echo '</div>';
                echo '</div>';
                if (count($all_id) > 1) {
                    $lg = isset($productx_settings['column']->lg) ? $productx_settings['column']->lg : 4;
                    $md = isset($productx_settings['column']->md) ? $productx_settings['column']->md : 4;
                    $sm = isset($productx_settings['column']->sm) ? $productx_settings['column']->sm : 2;
                    $xs = isset($productx_settings['column']->xs) ? $productx_settings['column']->xs : 2;

                    echo '<div class="slider wopb-builder-slider-nav thumb-image" data-arrow="'.esc_attr($productx_settings['showGalleryArrow']).'" data-view="'.esc_attr($productx_settings['onview']).'" data-position="'.esc_attr($productx_settings['position']).'" data-collg="'.esc_attr($lg).'" data-colmd="'.esc_attr($md).'" data-colsm="'.esc_attr($sm).'" data-colxs="'.esc_attr($xs).'">';
                        echo wp_kses_post($image_thumb);
                    echo '</div>';
                }
            };

            add_action('woocommerce_product_thumbnails', $slick_html);
            
            add_filter('woocommerce_single_product_image_gallery_classes', $gallery_classes);
            add_filter('woocommerce_single_product_image_thumbnail_html', $image_html, 10, 2);

            ob_start();
            woocommerce_show_product_images();
            $content .= ob_get_clean();

            remove_filter('woocommerce_single_product_image_gallery_classes', $gallery_classes);
            remove_filter('woocommerce_single_product_image_thumbnail_html', $image_html, 10, 2);

            $wraper_after .= '</div>';
            $wraper_after .= '</div>';
            
            remove_action('woocommerce_product_thumbnails', $slick_html);
        }

        return $wraper_before.$content.$wraper_after;
    }

}
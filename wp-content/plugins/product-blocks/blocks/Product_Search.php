<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Search{
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
            //      Layout Setting
            //--------------------------
            'searchLayout' => [
                'type' => 'string',
                'default' => '1',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>1],
                        ],
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>1],
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>false],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-section .wopb-search-category  .wopb-right-separator {display:none;}
                        '
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>2],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-section .wopb-input-section {order:2}
                            {{WOPB}} .wopb-search-section .wopb-search-category {order:3;}
                            {{WOPB}} .wopb-search-section .wopb-search-category .wopb-right-separator {display:none;}
                        '
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>3],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-section .wopb-input-section {order:2}
                            {{WOPB}} .wopb-search-section .wopb-search-icon {order:3}
                            {{WOPB}} .wopb-search-section .wopb-search-category .wopb-left-separator {display:none;}
                        '
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>4],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-section .wopb-search-category {order:3;}
                            {{WOPB}} .wopb-search-section .wopb-search-category .wopb-right-separator {display:none;}
                        '
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>5],
                        ],
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'searchLayout','condition'=>'==','value'=>5],
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>false],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-section .wopb-search-category .wopb-right-separator {border-right:none;}
                        '
                    ],
                ]
            ],
            'productListLayout' => [
                'type' => 'string',
                'default' => '1',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'productListLayout','condition'=>'==','value'=>1],
                        ],
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'productListLayout','condition'=>'==','value'=>2],
                        ],
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'productListLayout','condition'=>'==','value'=>3],
                        ],
                        'selector' => '
                            {{WOPB}} .wopb-search-result .wopb-item-details {flex-direction: column;}
                            {{WOPB}} .wopb-search-result .wopb-item-title-section .wopb-item-price {justify-content: center;}
                            {{WOPB}} .wopb-search-result .wopb-item-title-section {text-align: center;}
                        '
                    ],
                ]
            ],


            //--------------------------
            //      General Setting
            //--------------------------
            'columns' => [
                'type' => 'object',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-search-items { grid-template-columns: repeat({{columns}}, 1fr); }'
                    ],
                ],
            ],
            'searchBlockWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'100', 'unit' =>'%'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-search-block { width: {{searchBlockWidth}}; }'
                    ],
                ],
            ],
            'searchBlockAlign' => [
                'type' => 'string',
                'default' => "left",
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} { display: flex;justify-content:{{searchBlockAlign}}; }'
                    ],
                ],
            ],
            'columnGap' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-search-items{ column-gap: {{columnGap}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Search Input Form Style
            //--------------------------
            'searchInputTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '14', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-search-input'
                    ]]
            ],
            'searchFormHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'50', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-section form { height: {{searchFormHeight}}; }'
                    ],
                ],
            ],
            'searchFormBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-section form'
                    ],
                ],
            ],
            'searchFormRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-section form { border-radius:{{searchFormRadius}}; }'
                    ],
                ],
            ],
            'searchInputPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['left' => '10','right' => '10', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-input { padding:{{searchInputPadding}} !important; }'
                    ],
                ],
            ],
            'searchInputColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'selector'=>'
                            {{WOPB}} .wopb-search-input { color:{{searchInputColor}} !important; }
                            {{WOPB}} .wopb-search-input:focus { color:{{searchInputColor}} !important;caret-color: {{searchInputColor}}; }
                            {{WOPB}} .wopb-search-input::placeholder { color:{{searchInputColor}} !important; }
                            {{WOPB}} .wopb-input-section .wopb-clear { color:{{searchInputColor}} !important; }
                            {{WOPB}} .wopb-input-section .wopb-loader-container::before, {{WOPB}} .wopb-input-section .wopb-loader-container::after  { border-color:{{searchInputColor}} {{searchInputColor}} transparent transparent !important; }'
                    ],
                ],
            ],
            'searchFormBgColor' => [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#fff'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-section form'
                    ],
                ],
            ],

            //--------------------------
            //  Category Style
            //--------------------------
            'showSearchCategory' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'selectedCategoryTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '15', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-search-category .wopb-selected-item .wopb-selected-text, {{WOPB}} .wopb-search-category .wopb-selected-item .dashicons'
                    ]]
            ],
            'categoryTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '15', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-search-category .wopb-select-items li'
                    ]]
            ],
            'categoryColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-category .wopb-selected-item { color:{{categoryColor}}; }'
                    ],
                ],
            ],
            'categoryWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'250', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-category { width: {{categoryWidth}}; }'
                    ],
                ],
            ],
            'categoryLeftSeparatorWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'1', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-section .wopb-search-category .wopb-separator.wopb-left-separator {width: {{categoryLeftSeparatorWidth}};}'
                    ],
                ],
            ],
            'categoryRightSeparatorWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'1', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-section .wopb-search-category .wopb-separator.wopb-right-separator {width: {{categoryRightSeparatorWidth}};}'
                    ],
                ],
            ],
            'categorySeparatorHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'100', 'unit' =>'%'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-section .wopb-search-category .wopb-separator{ height: {{categorySeparatorHeight}}; }'
                    ],
                ],
            ],
            'categorySeparatorColor' => [
                'type' => 'string',
                'default' => '#e5e5e5',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchCategory','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-section .wopb-search-category .wopb-separator { background-color:{{categorySeparatorColor}};}'
                    ],
                ],
            ],


            //--------------------------
            //      Search Icon Setting
            //--------------------------
            'showSearchIcon' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'searchIconSize' => [
                'type' => 'object',
                'default' => (object)['lg' =>'25', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon svg { height: {{searchIconSize}}; }'
                    ],
                ],
            ],
            'searchIconColor' => [
                'type' => 'string',
                'default' => '#828282',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon svg { fill:{{searchIconColor}}; } {{WOPB}} .wopb-search-icon svg path { stroke:{{searchIconColor}}; }'
                    ],
                ],
            ],
            'searchButtonBgColor' => [
                'type' => 'object',
                'default' => (object)['openColor' => 1,'type' => 'color', 'color' => ''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon'
                    ],
                ],
            ],
            'searchButtonRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon { border-radius:{{searchButtonRadius}}; }'
                    ],
                ],
            ],
            'searchIconHoverColor' => [
                'type' => 'string',
                'default' => '#ff5845',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon:hover svg { fill:{{searchIconHoverColor}}; } {{WOPB}} .wopb-search-icon:hover svg path { stroke:{{searchIconHoverColor}}; }'
                    ],
                ],
            ],
            'searchButtonBgHoverColor' => [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => ''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon:hover'
                    ],
                ],
            ],
            'searchButtonHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon:hover { border-radius:{{SearchButtonHoverRadius}}; }'
                    ],
                ],
            ],
            'searchButtonWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'30', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-icon { width: {{searchButtonWidth}}; }'
                    ],
                ],
            ],
            'searchTextGap' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showSearchIcon','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-input {letter-spacing: {{searchTextGap}}; }'
                    ],
                ],
            ],
            'searchButtonMargin' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '5','right' => '5', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-search-icon { margin:{{searchButtonMargin}}; }'
                    ],
                ],
            ],
            'searchButtonPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '10','right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-search-icon { padding:{{searchButtonPadding}}; }'
                    ],
                ],
            ],


            //--------------------------
            //  More Result Style
            //--------------------------
            'showMoreResult' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'moreResultTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '15', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showMoreResult','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-load-more, {{WOPB}} .wopb-less-result'
                    ]]
            ],
            'moreResultColor' => [
                'type' => 'string',
                'default' => '#2189fa',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showMoreResult','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-load-more, {{WOPB}} .wopb-less-result { color:{{moreResultColor}}; }'
                    ],
                ],
            ],
            'moreResultHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showMoreResult','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-load-more:hover, {{WOPB}} .wopb-less-result:hover { color:{{moreResultHoverColor}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Result Dropdown Style
            //--------------------------
            'dropdownBg' => [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#fff'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result'
                    ],
                ],
            ],
            'dropdownWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result { max-width: {{dropdownWidth}}; }'
                    ],
                ],
            ],
            'dropdownBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)['top' => 1, 'right' => 0, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result'
                    ],
                ],
            ],
            'dropdownRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result { border-radius:{{dropdownRadius}}; }'
                    ],
                ],
            ],
            'dropdownShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result'
                    ],
                ],
            ],
            'itemSpacingX' => [
                'type' => 'object',
                'default' => (object)['lg'=>15, 'unit'=>'px'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-search-result .wopb-search-item {padding-left:{{itemSpacingX}};padding-right:{{itemSpacingX}}; }']]
            ],
            'itemSpacingY' => [
                'type' => 'object',
                'default' => (object)['lg'=>15, 'unit'=>'px'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-search-result .wopb-search-item {padding-top:{{itemSpacingY}};padding-bottom:{{itemSpacingY}}; }']]
            ],
            'itemSeparatorColor' => [
                'type' => 'string',
                'default' => '#e5e5e5',
                'style' => [
                    (object)[
                        'selector'=>'
                            {{WOPB}} .wopb-search-result .wopb-search-item { border-color:{{itemSeparatorColor}}; }
                            {{WOPB}} .wopb-search-result .wopb-search-item-label.wopb-search-item { border-color:{{itemSeparatorColor}}; }
                            {{WOPB}} .wopb-search-result .wopb-load-more-section {border-color: {{itemSeparatorColor}};}
                        '
                    ],
                ],
            ],

            'itemSeparatorWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '1','left' => '', 'right' => '1', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-search-item { border-style:solid;border-width:{{itemSeparatorWidth}}; }'
                    ],
                ],
            ],

            'itemCatLabel' => [
                'type' => 'string',
                'default' => 'Categories',
            ],

            'itemProductLabel' => [
                'type' => 'string',
                'default' => 'Products',
            ],

            'itemLabelTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '15', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'uppercase', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-search-result .wopb-search-item-label'
                    ]]
            ],

            'itemLabelColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-search-item-label { color:{{itemLabelColor}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Search Result Image Style
            //--------------------------
            'showProductImage' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'imageWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'40', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-image { width: {{imageWidth}}; }'
                    ],
                ],
            ],
            'imageHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-image { height: {{imageHeight}}; }'
                    ],
                ],
            ],
            'imageRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'showProductImage','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-image img { border-radius:{{imageRadius}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Search Result Title Style
            //--------------------------
            'showProductTitle' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'titleTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductTitle','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-search-result .wopb-item-title'
                    ]]
            ],
            'titleColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductTitle','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-title, {{WOPB}} .wopb-search-result .wopb-item-title p { color:{{titleColor}}; }'
                    ],
                ],
            ],
            'titleHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductTitle','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-title:hover, {{WOPB}} .wopb-search-result .wopb-item-title p:hover { color:{{titleHoverColor}}; }'
                    ],
                ],
            ],
            'searchHighlightColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductTitle','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-highlight { color:{{searchHighlightColor}}; }'
                    ],
                ],
            ],


            //--------------------------
            //  Search Result Price Style
            //--------------------------
            'showProductPrice' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'priceTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductPrice','condition'=>'==','value'=>true],
                        ],
                        'selector' => '{{WOPB}} .wopb-search-result .wopb-item-price'
                    ]]
            ],
            'priceColor' => [
                'type' => 'string',
                'default' => '#333',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductPrice','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-item-price { color:{{priceColor}}; }'
                    ],
                ],
            ],


            //--------------------------
            //  Search Result Rating Style
            //--------------------------
            'showProductRating' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'ratingSize' => [
                'type' => 'object',
                'default' => (object)['lg' => '12', 'unit' => 'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductRating','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-star-rating { font-size:{{ratingSize}}; }'
                    ],
                ],

            ],
            'ratingFillColor' => [
                'type' => 'string',
                'default' => '#ffd810',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductRating','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}}  .wopb-search-result .wopb-star-rating .wopb-star-fill { color:{{ratingFillColor}}; }'
                    ],
                ],
            ],
            'ratingEmptyColor' => [
                'type' => 'string',
                'default' => '#d3ced2',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'showProductRating','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-search-result .wopb-star-rating:before { color:{{ratingEmptyColor}}; }'
                    ],
                ],
            ],


            //--------------------------
            //  Wrapper Style
            //--------------------------
            'wrapBg' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#f5f5f5'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper'
                    ],
                ],
            ],
            'wrapBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper'
                    ],
                ],
            ],
            'wrapShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper'
                    ],
                ],
            ],
            'wrapRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper { border-radius:{{wrapRadius}}; }'
                    ],
                ],
            ],
            'wrapHoverBackground' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#ff5845'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper:hover'
                    ],
                ],
            ],
            'wrapHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper:hover'
                    ],
                ],
            ],
            'wrapHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper:hover { border-radius:{{wrapHoverRadius}}; }'
                    ],
                ],
            ],
            'wrapHoverShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper:hover'
                    ],
                ],
            ],
            'wrapMargin' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper { margin:{{wrapMargin}}; }'
                    ],
                ],
            ],
            'wrapOuterPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper { padding:{{wrapOuterPadding}}; }'
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
        if ($default) {
            $temp = array();
            foreach ($attributes as $key => $value) {
                if (isset($value['default'])) {
                    $temp[$key] = $value['default'];
                }
            }
            return $temp;
        } else {
            return $attributes;
        }
    }

    public function register() {
        register_block_type( 'product-blocks/product-search',
            array(
                'editor_script' => 'wopb-blocks-editor-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Heading', 'product-blocks'),
              //  'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            )
        );
    }

    /**
     * This
     * @return terminal
     */
    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        if (wopb_function()->is_lc_active()) {
            global $wpdb;
            $wraper_before = '';
            $page_post_id = wopb_function()->get_ID();
            $block_name = 'product-search';
            $post_meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_value LIKE %s", '%.wopb-block-'.$attr['blockId'].'%'));
            if($post_meta && isset($post_meta->post_id) && $post_meta->post_id != $page_post_id) {
                $page_post_id = $post_meta->post_id;
            }

            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):''). (isset($attr["align"])? ' align' .esc_attr($attr["align"]):'') . '">';
                $wraper_before .= '<div class="wopb-block-wrapper wopb-front-block-wrapper wopb-product-search-block " data-blockid="'.esc_attr($attr['blockId']).'" data-postid = "' . $page_post_id . '" data-blockname="product-blocks_' . esc_attr($block_name) . '">';

                    $wraper_before .= '<div class="wopb-search-section">';
                        $wraper_before .= '<form action="javascript:">';
                            ob_start();
                                $this->search_input_content($attr);
                                $this->search_category_content($attr);
                                $this->search_icon_content($attr);
                            $wraper_before .= ob_get_clean();
                        $wraper_before .= '</form>';
                    $wraper_before .= '</div>';

                    $wraper_before .= '<div class="wopb-search-result wopb-d-none wopb-layout-' . $attr['productListLayout'] . '">';
                    $wraper_before .= '</div>';

                $wraper_before .= '</div>';
            $wraper_before .= '</div>';

            return $wraper_before;
        }
    }

    /**
     * Get Search Input Content
     *
     * @param $attr
     * @since v.2.6.8
     */
    public function search_input_content($attr) {
        $html = '';
        $html .= '<div class="wopb-input-section">';
            $html .= '<input type="text" class="wopb-search-input" placeholder="' . __('Search for products...','product-blocks') . '" />';
            $html .= '<span class="dashicons dashicons-no-alt wopb-clear wopb-d-none"></span>';
            $html .= '<span class="wopb-loader-container"></span>';
        $html .= '</div>';
        echo $html;
    }

    /**
     * Get Search Cateogry Content
     *
     * @param $attr
     * @since v.2.6.8
     */
    public function search_category_content($attr) {
        $categories = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => true] );
        if(isset($attr['showSearchCategory']) && $attr['showSearchCategory']) {
?>
            <div class="wopb-search-category">
                    <span class="wopb-separator wopb-left-separator"></span>
                    <div class="wopb-dropdown-select">
                    <span class="wopb-selected-item">
                        <span value="" class="wopb-selected-text">
                            <?php _e('All Categories','product-blocks') ?>
                        </span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </span>
                        <ul class="wopb-select-items">
                                <li value=""><?php _e('All Categories', 'product-blocks'); ?></li>
                            <?php
                                foreach($categories as $category) {
                            ?>
                                <li value="<?php echo $category->term_id; ?>"><?php _e($category->name, 'product-blocks'); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <span class="wopb-separator wopb-right-separator"></span>
                </div>
<?php
        }
    }

    /**
     * Get Search Icon Content
     *
     * @param $attr
     * @since v.2.6.8
     */
    public function search_icon_content($attr) {
        if(isset($attr['showSearchIcon']) && $attr['showSearchIcon']) {
?>
        <a class="wopb-search-icon">
            <?php echo wopb_function()->svg_icon('search'); ?>
        </a>
<?php
        }
    }

    /**
     * Get Search Result Item Content
     *
     * @param array $params
     * @since v.2.6.8
     */
    public function search_item_content($params = []) {
        $attr = $params['attr'];
        $products = $params['products'];
        $tax_terms = $params['tax_terms'];
        $params['view_limit'] = 6;
        if($tax_terms && count($tax_terms) > 0) {
            echo '<div class="wopb-search-item-label wopb-search-item">' . __($attr['itemCatLabel'], 'product-blocks') . '</div>';
            echo '<div class="wopb-tax-term-items wopb-search-items">';
                $i = 0;
                foreach($tax_terms as $term) {
                    $i++;
                    $extend_item_class = '';
                    if($i > $params['view_limit']) {
                      $extend_item_class = ' wopb-extended-item wopb-d-none';
                    }
?>
                     <div class="wopb-search-item<?php esc_attr_e($extend_item_class); ?>">
                         <a href="<?php echo esc_url(get_term_link($term->term_id)); ?>" class="wopb-item-term">
                             <?php echo $this->highlightSearchKey(__($term->name,'product-blocks'), $params['search']) ?>
                         </a>
                     </div>
<?php
                }
            echo '</div>';
        }
        if($products->have_posts()) {
            $i = 0;
            echo '<div class="wopb-search-item-label wopb-search-item">' . __($attr['itemProductLabel'], 'product-blocks') . '</div>';
            echo '<div class="wopb-search-items">';
            while ( $products->have_posts() ) {
                $i++;
                $products->the_post();
                $extend_item_class = '';
                if($i > $params['view_limit'] && $attr['showMoreResult']) {
                  $extend_item_class = ' wopb-extended-item wopb-d-none';
                }
                $product = wc_get_product(get_the_ID());
                if (has_post_thumbnail()) {
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'large');
                }else {
                    $image[0] = esc_url(WOPB_URL . 'assets/img/wopb-placeholder.jpg');
                }
    ?>
                <div class="wopb-search-item wopb-block-item<?php esc_attr_e($extend_item_class); ?>">
                    <div class="wopb-item-details">
                        <?php if($attr['showProductImage']) { ?>
                            <a href="<?php echo esc_url($product->get_permalink()) ?>"  class="wopb-item-image">
                                <img src="<?php echo $image[0] ?>" />
                            </a>
                        <?php } ?>
                        <div class="wopb-item-title-section">
                            <?php if($attr['showProductTitle']) { ?>
                                <a href="<?php echo esc_url($product->get_permalink()) ?>" class="wopb-item-title">
                                    <?php echo $this->highlightSearchKey(__($product->get_name() ,'product-blocks'), $params['search']) ?>
                                </a>
                            <?php
                                }

                                echo $this->rating_content($attr, $product);

                                if($attr['productListLayout'] == 2 || $attr['productListLayout'] == 3) {
                                  echo $this->price_content($attr, $product);
                                }
                            ?>
                        </div>
                    </div>
        <?php
                    if($attr['productListLayout'] == 1) {
                        echo $this->price_content($attr, $product);
                    }
        ?>
                </div>
<?php
            }

        echo '</div>';
        if($params['total_product'] > $params['view_limit']) {
            echo $this->more_result_content($attr, $params);
        }
    }

    if(!$tax_terms && !$products->have_posts()) {
        echo '<div class="wopb-empty-result"><h2> ' . __('No Result Found', 'product-blocks') . ' </h2></div>';
    }

}

    /**
     * Get Price Content
     *
     * @param $attr
     * @param $product
     * @since v.2.6.8
     */
    public function price_content($attr, $product) {
        if($attr['showProductPrice']) {
?>
            <div class="wopb-item-price">
                <?php echo $product->get_price_html() ?>
            </div>
<?php
        }
    }

    /**
     * Get Rating Content
     *
     * @param $attr
     * @param $product
     * @since v.2.6.8
     */
    public function rating_content($attr, $product) {
        if($attr['showProductRating']) {
            $rating_average = $product ? $product->get_average_rating() : 0;
?>
            <div class="wopb-rating-section">
                <div class="wopb-star-rating">
                    <span class="wopb-star-fill" style="width: <?php esc_attr_e($rating_average ? (($rating_average / 5 ) * 100) : 0) ?>%">
                        <strong itemprop="ratingValue" class="wopb-rating"><?php esc_html_e($rating_average) ?></strong>
                    </span>
                </div>
            </div>
<?php
        }
    }

    /**
     * Get More Result Content
     *
     * @param $attr
     * @since v.2.6.8
     */
    public function more_result_content($attr, $params) {
        if($attr['showMoreResult']) {
            $rest_product_count =  $params['total_product'] - $params['view_limit']
?>
            <div class="wopb-load-more-section">
                <a class="wopb-load-more">
                    <?php _e('More results..(' . $rest_product_count .')', 'product-blocks') ?>
                </a>
                <a class="wopb-less-result wopb-d-none">
                    <?php _e('Less results', 'product-blocks') ?>
                </a>
            </div>
<?php
        }
    }

    /**
     * Return content after highlight search key
     *
     * @param $content
     * @param $search
     * @return HTML
     * @since v.2.6.8
     */
    public function highlightSearchKey($content, $search) {
        // Create a new DOMDocument object and load the HTML
        $doc = new \DOMDocument();
        $doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Use DOMXPath to select all text nodes
        $xpath = new \DOMXPath($doc);
        $textNodes = $xpath->query('//text()');

        foreach ($textNodes as $node) {
          $text = $node->nodeValue;
          $highlightedText = preg_replace('/(' . $search . ')/i', '<strong class="wopb-highlight">$1</strong>', $text);
          if ($highlightedText !== $text) {
            $newNode = $doc->createDocumentFragment();
            $newNode->appendXML($highlightedText);
            $node->parentNode->replaceChild($newNode, $node);
          }
        }

        // Output the modified HTML
        return $doc->saveHTML();
    }
}
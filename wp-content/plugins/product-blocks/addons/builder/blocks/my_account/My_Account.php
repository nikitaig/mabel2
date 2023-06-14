<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class My_Account{
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
            'pageLayout'=> [
                'type' => 'string',
                'default' => 'vertical',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'vertical'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container { flex-direction:row; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'horizontal'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container { flex-direction:column; }
                                        {{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation { width:100% }
                                        {{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul {display:flex; flex-direction:row; width:100%; flex-wrap: wrap; row-gap: 10px;}'
                    ],
                ]
            ],
            
            // Profile
            'showProfile'=> [
                'type' => 'boolean',
                'default' => true,
                'style' => [
                    (object)[
                    ],
                ],
            ],
            'profileTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '25', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-profile-section .wopb-my-account-user-data .wopb-user-name ']]
            ],

            'profileColor' => [
                'type' => 'string',
                'default' => '#221f1c',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-my-account-profile-section { color:{{profileColor}}; }'
                    ],
                ],
            ],
            'profileImageWidth'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'120', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-product-wrapper .wopb-my-account-profile-section .wopb-my-account-user-img { width:{{profileImageWidth}}; }'
                    ],
                ]
            ],
            'profileImageHeight'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'120', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-product-wrapper .wopb-my-account-profile-section .wopb-my-account-user-img img { height:{{profileImageHeight}}; }'
                    ],
                ]
            ],
            'profileImageRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '80','bottom' => '80','left' => '80', 'right' => '80', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .wopb-my-account-profile-section .wopb-my-account-user-img img { border-radius:{{profileImageRadius}}; }'
                    ],
                ],
            ],
            'profileSpacing'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'30', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-product-wrapper .wopb-my-account-profile-section { margin-bottom:{{profileSpacing}}; }'
                    ],
                ]
            ],

            // Navigation Tab 
            'navTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation li a']]
            ],
            'navAlign'=> [
                'type' => 'string',
                'default' => 'left',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'navAlign','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul { justify-content:flex-start; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'navAlign','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul { justify-content:center; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'navAlign','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul { justify-content:flex-end; }'
                    ],
                ],
            ],
            'navWidth'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'160', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul { width:{{navWidth}}; }'
                    ],
                ]
            ],
            'navSpacing'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'14', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'vertical'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation { margin-right:{{navSpacing}}; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'horizontal'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation { margin-bottom:{{navSpacing}}; }'
                    ],
                ]
            ],
            'navListSpacing'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'0', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'vertical'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul li { margin-bottom:{{navListSpacing}}; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'pageLayout','condition'=>'==','value'=>'horizontal'],
                        ],
                        'selector' => '{{WOPB}} .wopb-my-account-container nav.woocommerce-MyAccount-navigation ul li { margin-right:{{navListSpacing}}; }'
                    ],
                ]
            ],
            'navColor' => [
                'type' => 'string',
                'default' => '#4d4d4d',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li a , .woocommerce-account {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li a { color:{{navColor}} !important; }']],
            ],
            'navBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#ffffff'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li'
                    ],
                ],
            ],
            'navBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#000000','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li'
                    ],
                ],
            ],
            'navRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '0','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li { border-radius:{{navRadius}}; }'
                    ],
                ],
            ],
            'navPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li { padding:{{navPadding}}; }'
                    ],
                ],
            ],
            'navFocusColor' => [
                'type' => 'string',
                'default' => '#4d4d4d',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active a , .woocommerce-account {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active a { color:{{navFocusColor}} !important; }']],
            ],
            'navBgFocusColor'=> [
                'type' => 'object',
                'default' => (object)[],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active'
                    ],
                ],
            ],
            'navFocusBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 0, 'bottom' => 1, 'left' => 5],'color' => '#4d4d4d','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active'
                    ],
                ],
            ],
            'navFocusRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '0','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active { border-radius:{{navFocusRadius}}; }'
                    ],
                ],
            ],
            'navFocusPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-navigation ul li.is-active { padding:{{navFocusPadding}}; }'
                    ],
                ],
            ],

            // Tab Content
            'tabContentTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content']]
            ],

            'tabContentColor' => [
                'type' => 'string',
                'default' => '#656565',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content { color:{{tabContentColor}}; }']],
            ],
            'tabContentLinkColor'=> [
                'type' => 'string',
                'default' => '#0400fa',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content a { color:{{tabContentLinkColor}}; }']],
            ],
            'tabContentHeadingTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container h2 , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content h2 , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content h3 , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content legend']]
            ],

            'tabContentHeadingColor' => [
                'type' => 'string',
                'default' => '#1c0101',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container h2 , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content h2 , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content h3 ,  {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content legend { color:{{tabContentHeadingColor}}; }']],
            ],
            'tabContentBgColor'=> [
                'type' => 'object',
                'default' => (object)[],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content'
                    ],
                ],
            ],
            'tabContentBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content'
                    ],
                ],
            ],
            'tabContentShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content'
                    ],
                ],
            ],
            'tabContentRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '0','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content {border-radius:{{tabContentRadius}};}'
                    ],
                ],
            ],
            'tabContentPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '5','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content { padding:{{tabContentPadding}};}'
                    ],
                ],
            ],
    
            // Form
            'formRowGap'=> [
                'type' => 'object',
                'default' => (object)['lg' =>'4', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} .wopb-my-account-container form p.form-row { margin-bottom:{{formRowGap}}; } 
                                        {{WOPB}} .wopb-my-account-container form fieldset p.form-row:last-child { margin-bottom: 15px; }
                                        {{WOPB}} .wopb-my-account-container form p.form-row:last-child { margin-bottom: 15px; }'
                    ],
                ]
            ],

            'formLabelTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container form p label']]
            ],

            'formLabelColor' => [
                'type' => 'string',
                'default' => '#656565',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container form p label { color:{{formLabelColor}}; }']],
            ],
            'formInputDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            'formInputColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container form p input { color:{{formInputColor}}; }']],
            ],
            'formInputBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#ededed'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input'
                    ],
                ],
            ],
            'formInputBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input'
                    ],
                ],
            ],
            'formInputRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input { border-radius:{{formInputRadius}}; }'
                    ],
                ],
            ],
            'formInputPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '6', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input { padding:{{formInputPadding}}; }'
                    ],
                ],
            ],

            'formInputFocusColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container form p input:focus { color:{{formInputFocusColor}}; }']],
            ],
            'formInputBgFocusColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#fff'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input:focus'
                    ],
                ],
            ],
            'formInputFocusBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input:focus'
                    ],
                ],
            ],
            'formInputFocusRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input:focus { border-radius:{{formInputFocusRadius}}; }'
                    ],
                ],
            ],
            'formInputFocusPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '6', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form p input:focus { padding:{{formInputFocusPadding}}; }'
                    ],
                ],
            ],
            
            'formBtnDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            
            'formBtnColor' => [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container form button { color:{{formBtnColor}}; }']],
            ],
            'formBtnBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#343434'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button'
                    ],
                ],
            ],
            'formBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button'
                    ],
                ],
            ],
            'tabContformBtnShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button'
                    ],
                ],
            ],
            'formBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button { border-radius:{{formBtnRadius}}; }'
                    ],
                ],
            ],
            'formBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button { padding:{{formBtnPadding}}; }'
                    ],
                ],
            ],
        
            'formBtnHoverColor' => [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container form button:hover { color:{{formBtnHoverColor}}; }']],
            ],
            'formBtnBgHoverColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#343434'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button:hover'
                    ],
                ],
            ],
            'formBtnHoverBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button:hover'
                    ],
                ],
            ],
            'formBtnHoverShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button:hover'
                    ],
                ],
            ],
            'formBtnHoverRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button:hover { border-radius:{{formBtnHoverRadius}}; }'
                    ],
                ],
            ],
            'formBtnHoverPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container form button:hover { padding:{{formBtnHoverPadding}}; }'
                    ],
                ],
            ],
    
        // Table/Order
            'tableAlignment'=> [
                'type' => 'string',
                'default' => 'center',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table td, {{WOPB}} .wopb-my-account-container table th { text-align:{{tableAlignment}}; }'
                    ]
                ],
            ],
            'tableBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' =>(object)['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table'
                    ],
                ],
            ],
            'tableRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '0','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table { border-radius:{{tableRadius}}; overflow: hidden;}'
                    ],
                ],
            ],
            'tableHeadDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            'tableHeadTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '20', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table thead th, {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot th , {{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.woocommerce-button--next']]
            ],

            'tableHeadColor' => [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table thead th , {{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button { color:{{tableHeadColor}}; }']],
            ],
            'tableHeadBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#4D4D4D'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table thead , {{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button'
                    ],
                ],
            ],
            'tableHeadBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table thead th']],
            ],
            'tableHeadPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '8','bottom' => '8','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table thead th { padding:{{tableHeadPadding}}; }'
                    ],
                ],
            ],
            'tableBodyDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            'tableBodyTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tbody td , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot td']]
            ],

            'tableBodyColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tbody td , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot th , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot td { color:{{tableBodyColor}}; }']],
            ],
            'tableBodyBgColor'=> [
                'type' => 'object',
                'default' => (object)[],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tbody , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot'
                    ],
                ],
            ],
            'tableBodyBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 0, 'right' => 0, 'bottom' => 1, 'left' => 0],'color' => '#e0e0e0','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tbody td , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot th , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot td']],
            ],
            'tableLinkColor' => [
                'type' => 'string',
                'default' => '9481ff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a { color:{{tableLinkColor}}; }']],
            ],
            'tableLinkHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a:hover { color:{{tableLinkHoverColor}}; }']],
            ],
            'tableBodyPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '8','bottom' => '8','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tbody td , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot th , {{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table tfoot td { padding:{{tableBodyPadding}}; }'
                    ],
                ],
            ],
            'tableBtnDivider'=> [
                'type' => 'string',
                'default' => '',
            ],

            'tableBtnColor' => [
                'type' => 'string',
                'default' => '#ffffff',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button { color:{{tableBtnColor}} !important; }']],
            ],
            'tableBtnBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#4D4D4D'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button'
                    ],
                ],
            ],
            'tableBtnTypo'=> [
                'type' => 'object',
                'default' => (object)['openTypography' => 0,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button']]
            ],
            'tableBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button'
                    ],
                ],
            ],
            'tableBtnShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button'
                    ],
                ],
            ],
            'tableBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button { border-radius:{{tableBtnRadius}}; }'
                    ],
                ],
            ],
            'tableBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button { padding:{{tableBtnPadding}} !important; }'
                    ],
                ],
            ],

            'tableBtnHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover { color:{{tableBtnHoverColor}} !important; }']],
            ],
            'tableBtnBgHoverColor'=> [
                'type' => 'object',
                'default' => (object)[],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover'
                    ],
                ],
            ],
            'tableBtnHoverBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover'
                    ],
                ],
            ],
            'tableBtnHoverShadow'=> [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => ''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover'
                    ],
                ],
            ],
            'tableBtnHoverRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover { border-radius:{{tableBtnHoverRadius}}; }'
                    ],
                ],
            ],
            'tableBtnHoverPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container .woocommerce-MyAccount-content table a.button:hover { padding:{{tableBtnHoverPadding}} !important; }'
                    ],
                ],
            ],
            // Table Footer
            'tableFooterDivider'=> [
                'type' => 'string',
                'default' => '',
            ],
            'tableFooterBgColor'=> [
                'type' => 'object',
                'default' => (object)[],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination:has(a.button)'
                    ],
                ],
            ],
            'tableFooterBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination:has(a.button)'
                    ],
                ],
            ],
            'tableFooterRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '0','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination:has(a.button) { border-radius:{{tableFooterRadius}}; }'
                    ],
                ],
            ],
            'tableFooterPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '10','bottom' => '','left' => '0', 'right' => '0', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination:has(a.button) { padding:{{tableFooterPadding}} !important; }'
                    ],
                ],
            ],
            'tableFooterBtnColor'=> [
                'type' => 'string',
                'default' => '',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button { color:{{tableFooterBtnColor}} !important; }']],
            ],
            'tableFooterBtnBgColor'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1, 'type' => 'color', 'color' => '#4D4D4D'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button'
                    ],
                ],
            ],
            'tableFooterBtnBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' =>(object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e0e0e0','type' => 'solid'],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button'
                    ],
                ],
            ],
            'tableFooterBtnRadius'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '4','bottom' => '4','left' => '4', 'right' => '4', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button { border-radius:{{tableFooterBtnRadius}}; }'
                    ],
                ],
            ],
            'tableFooterBtnPadding'=> [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '8','left' => '10', 'right' => '10', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-my-account-container table+.woocommerce-pagination a.button { padding:{{tableFooterBtnPadding}} !important; }'
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
        register_block_type( 'product-blocks/my-account',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('My Account', 'product-blocks'),
//                'attributes' => $this->get_attributes(),
                'render_callback' => array($this, 'content')
            )
        );
    }

    public function content($attr, $noAjax = false) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        $block_name = 'my-account';
        $wraper_before = $wraper_after = $content = '';

        $user_id = get_current_user_id();
        $profileUrl = get_avatar_url($user_id);
        $userData = get_userdata($user_id);

        if (function_exists('WC')) {
            $wraper_before.='<div '.($attr['advanceId']?'id="'.$attr['advanceId'].'" ':'').' class="wp-block-product-blocks-'.$block_name.' wopb-block-'.$attr["blockId"].' '.(isset($attr["className"])?$attr["className"]:'').'">';
                $wraper_before .= '<div class="wopb-product-wrapper ">';
                if (!is_admin()) {
                    ob_start();
                    require_once WOPB_PATH . 'addons/builder/blocks/my_account/Template.php';
                    $content .= ob_get_clean();
                }
                $wraper_after .= '</div>';
            $wraper_after .= '</div> ';
        }
        return $wraper_before.$content.$wraper_after;
    }
}
<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Archive_Title{
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
            //      Layout
            //--------------------------
            'layout' => [
                'type' => 'string',
                'default' => '1',
            ],
            'contentAlign' => [
                'type' => 'string',
                'default' => "left",
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'contentAlign','condition'=>'==','value'=>'left'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title { text-align:{{contentAlign}}; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'contentAlign','condition'=>'==','value'=>'center'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title { text-align:{{contentAlign}}; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'contentAlign','condition'=>'==','value'=>'right'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title { text-align:{{contentAlign}}; }'
                    ],
                ],
            ],
            //--------------------------
            //      General Settings
            //--------------------------
            'excerptShow' => [
                'type' => 'boolean',
                'default' => true
            ],
            'prefixShow' => [
                'type' => 'boolean',
                'default' => false,
            ],
            'showImage' => [
                'type' => 'boolean',
                'default' => false,
            ],
            
            //--------------------------
            //      Title Setting/Style
            //--------------------------
            'titleTag' => [
                'type' => 'string',
                'default' => 'h1',
            ],
            'customTaxTitleColor' => [
                'type' => 'boolean',
                'default' => false,
            ],
            'seperatorTaxTitleLink' => [
                'type' => 'string',
                'default' => admin_url( 'edit-tags.php?taxonomy=category' ),
                'style' => [
                    (object)[
                        'depends' => [ 
                            (object)['key'=>'customTaxTitleColor','condition'=>'==','value'=>true],
                        ]
                    ]
                ]
            ],
            'titleColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-name { color:{{titleColor}}; }'
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'customTaxTitleColor','condition'=>'==','value'=>false],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-name { color:{{titleColor}}; }'
                    ],
                    
                ],
            ],
            'titleTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography'=>0,'size'=>(object)['lg'=>'28', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'0', 'unit'=>'px'], 'height'=>(object)['lg'=>'32', 'unit'=>'px'],'transform' => '', 'decoration'=>'none','family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-name'
                    ],
                ],
            ],
            'titlePadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-name { padding:{{titlePadding}}; }'
                    ],
                ],
            ],
            'titleAlign' => [
                'type' => 'string',
                'default' => (object)['lg' => 'center','sm' => 'center','xs' => 'center'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-archive-name { text-align:{{titleAlign}}; }'
                    ]
                ],
            ],

            //--------------------------
            // Content Setting/Style
            //--------------------------
            'excerptColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'excerptShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-desc { color:{{excerptColor}}; }'
                    ],
                ],
            ],
            'excerptTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' =>14, 'unit' =>'px'],'height' => (object)['lg' =>'22', 'unit' =>'px'], 'decoration' => 'none','family'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'excerptShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-desc'
                    ],
                ],
            ],
            'excerptPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '0','bottom' => '', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'excerptShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-desc { padding: {{excerptPadding}}; }'
                    ],
                ],
            ],
            'excerptAlign' => [
                'type' => 'string',
                'default' => (object)['lg' => 'center','sm' => 'center','xs' => 'center'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-archive-desc { text-align:{{excerptAlign}}; }'
                    ]
                ],
            ],

            //--------------------------
            //      Prefix Setting/Style
            //--------------------------
            'prefixText' => [
                'type' => 'string',
                'default' => 'Sample Prefix Text',
            ],
            'prefixColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'prefixShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-prefix { color:{{prefixColor}}; }'
                    ],
                ],
            ],
            'prefixTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography'=>1,'size'=>(object)['lg'=>'', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'0', 'unit'=>'px'], 'height'=>(object)['lg'=>'', 'unit'=>'px'],'transform' => 'capitalize', 'decoration'=>'none','family'=>'','weight'=>''],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'prefixShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-prefix'
                    ],
                ],
            ],
            'prefixPadding' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'bottom'=>5, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'prefixShow','condition'=>'==','value'=>true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-prefix { padding:{{prefixPadding}}; }'
                    ],
                ],
            ],


            //--------------------------
            // Image Setting/Style
            //--------------------------
            'imgWidth' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'%'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['1']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-image { max-width: {{imgWidth}}; }'
                    ],
                ],
            ],
            'imgHeight' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['1']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-image {object-fit: cover; height: {{imgHeight}}; }'
                    ],
                ],
            ],
            'imgSpacing' => [
                'type' => 'object',
                'default' => (object)['lg'=>'10'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['1']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-image { margin-bottom: {{imgSpacing}}px; }'
                    ],
                ],
            ],

            //--------------------------
            //  Custom Wrapper Style
            //--------------------------
            'TaxAnimation' => [
                'type' => 'string',
                'default' => 'none'
            ],
            
            'TaxWrapBg' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content .wopb-archive-overlay { background:{{TaxWrapBg}}; }'
                    ],
                ],
            ],
            'TaxWrapHoverBg' => [
                'type' => 'string',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content:hover .wopb-archive-overlay { background:{{TaxWrapHoverBg}}; }'
                    ],
                ],
            ],
            'TaxWrapBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content'
                    ],
                ],
            ],
            'TaxWrapHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content:hover'
                    ],
                ],
            ],
            'TaxWrapShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content'
                    ],
                ],
            ],
            'TaxWrapHoverShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content:hover'
                    ],
                ],
            ],
            'TaxWrapRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content { border-radius: {{TaxWrapRadius}}; }'
                    ],
                ],
            ],
            'TaxWrapHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content:hover { border-radius: {{TaxWrapHoverRadius}}; }'
                    ],
                ],
            ],
            'customOpacityTax' => [
                'type' => 'string',
                'default' => .6,
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content .wopb-archive-overlay { opacity: {{customOpacityTax}}; }'
                    ],
                ],
            ],
            'customTaxOpacityHover' => [
                'type' => 'string',
                'default' => .9,
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-taxonomy-items li a:hover .wopb-archive-overlay { opacity: {{customTaxOpacityHover}}; }'
                    ],
                ],
            ],
            'TaxWrapPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '20','bottom' => '20','left' => '20','right' => '20', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'layout','condition'=>'==','value'=>['2']],
                        ],
                        'selector'=>'{{WOPB}} .wopb-block-archive-title .wopb-archive-content { padding: {{TaxWrapPadding}}; }'
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
                'default' => (object)['openColor' => 0, 'type' => 'color', 'color' => '#037fff'],
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

            //---------------------
            // Advanced > Responsive 
            //---------------------
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
                'style' => [
                    (object)[
                        'selector' => '{{WOPB}} {display:none;}'
                    ],
                ],
            ],
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
        register_block_type( 'product-blocks/archive-title',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Archive Title', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            )
        );
    }

    public function get_data() {
        if (is_admin()) {
            // For Demonstration Purpose
            return [
                'title' => 'Archive Title',
                'image' => WOPB_URL.'assets/img/blocks/builder/builder-fallback.jpg',
                'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam molestie aliquet molestie.',
            ];
        } else {
            if (is_archive()) {
                if (is_product_category()) {
                    $obj = get_queried_object();
                    $attachment_id = get_term_meta( $obj->term_id, 'thumbnail_id', true );
                    return [
                        'title' => $obj->name,
                        'image' => $attachment_id ? wp_get_attachment_url($attachment_id) : '',
                        'desc' => $obj->description,
                    ];
                } else if (is_product_tag()) {
                    $obj = get_queried_object();
                    $attachment_id = get_term_meta( $obj->term_id, 'thumbnail_id', true );
                    return [
                        'title' => $obj->name,
                        'image' => $attachment_id ? wp_get_attachment_url($attachment_id) : '',
                        'desc' => $obj->description,
                    ];
                } else if (is_product_category() || is_tag()) {
                    $obj = get_queried_object();
                    $attachment_id = get_term_meta( $obj->term_id, 'thumbnail_id', true );
                    return [
                        'title' => $obj->name,
                        'image' => '',
                        'desc' => $obj->description,
                    ];
                } else if (is_date()) {
                    $date = '';
                    if ( is_year() ) {
                        $date = get_the_date('Y');
                    } else if ( is_month() ) {
                        $date = get_the_date('F Y');
                    } else if ( is_day() ) {
                        $date = get_the_date('F j, Y');
                    }
                    return [
                        'title' => $date,
                        'image' => '',
                        'desc' => '',
                    ];
                } else if (is_author()) {
                    return [
                        'title' => get_the_author_meta( 'display_name' ),
                        'image' => get_avatar_url( get_the_author_meta( 'ID' ) ),
                        'desc' => get_the_author_meta( 'user_email' ),
                    ];
                } else if (is_tax()) {
                    $obj = get_queried_object();
                    $attachment_id = get_term_meta( $obj->term_id, 'thumbnail_id', true );
                    return [
                        'title' => $obj->name,
                        'image' => $attachment_id ? wp_get_attachment_url($attachment_id) : '',
                        'desc' => $obj->description,
                    ];
                } else if (is_search()) {
                    return [
                        'title' => get_search_query(),
                        'image' => '',
                        'desc' => '',
                    ];
                }
            } else if (is_search()) {
                return [
                    'title' => get_search_query(),
                    'image' => '',
                    'desc' => '',
                ];
            }
            return ['title' => '', 'image' => '', 'desc' => ''];   
        }
    }


    public function content($attr, $noAjax) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);

        // Dummy
        $data = $this->get_data();
        $wraper_before = $wraper_after = $post_loop = '';
        $block_name = 'archive-title';

        $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).''.(isset($attr["align"])? ' align' .esc_attr($attr["align"]):'').''.(isset($attr["className"])?' '.esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-block-wrapper">';
            $wraper_before .= '<div class="wopb-block-archive-title wopb-archive-layout-'.esc_attr($attr['layout']).'">';

            $style = ($attr['layout'] == '2' && $data['image']) ? 'style="background-image: url('.esc_url($data['image']).')"' : '';
            $prefix = ($attr['prefixShow'] && $attr['prefixText']) ? '<span class="wopb-archive-prefix">'.esc_html($attr['prefixText']).'</span> ' : '';
    
            $name = ($data['title']) ? '<'.esc_attr($attr['titleTag']).' class="wopb-archive-name">'.$prefix.esc_html($data['title']).'</'.esc_attr($attr['titleTag']).'>' : '';
            
            $excerpt = ($attr['excerptShow'] && $data['desc']) ? '<div class="wopb-archive-desc">'.esc_html($data['desc']).'</div>' : '';

                // Prefix
                switch ($attr['layout']) {
                    case 1:
                        $img = ($attr['showImage'] && $data['image']) ? '<img class="wopb-archive-image" src="'.esc_url($data['image']).'" alt="'.esc_attr($data['title']).'"/>' : '';
                        $post_loop .= $img.$name.$excerpt;
                        break;
                    case 2:
                        $post_loop .= '<div class="wopb-archive-content" '.wp_kses_post($style).'><div class="wopb-archive-overlay"></div>'.wp_kses_post($name.$excerpt).'</div>';
                        break;
                }
            
            $wraper_after .= '</div>';
            $wraper_after .= '</div>';
        $wraper_after .= '</div>';

        return $wraper_before.$post_loop.$wraper_after;
    }

}
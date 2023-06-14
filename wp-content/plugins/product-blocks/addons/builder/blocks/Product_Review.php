<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Review{

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
            //  Review
            //--------------------------
            'reviewHeading'=>[
                'type' => 'boolean',
                'default'=> false,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-Reviews-title {display:none;}',
                    ]
                ],
            ],
            'reviewAuthor'=>[
                'type' => 'boolean',
                'default'=> false,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews #comments ol.commentlist li .comment-text {margin:0px 0px 0px 0px;} {{WOPB}} .comment_container > img {display:none;}',
                    ]
                ],
            ],


            'reviewHeadinglColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'reviewHeading','condition'=>'==','value'=>false],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-Reviews-title{color:{{reviewHeadinglColor}};}'
                    ],
                ],
            ],
            'headingTypography'=>[
                'type' => 'object',
                'default' =>  (object)['openTypography' => 0,'size' => (object)['lg' => '24', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'reviewHeading','condition'=>'==','value'=>false],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-Reviews-title'
                    ],
                ],
            ],
            'headingSpace' => [
                'type' => 'string',
                'default' => 10,
                'style' => [
                     (object)[
                        'depends' => [
                            (object)['key'=>'reviewHeading','condition'=>'==','value'=>false],
                        ],
                        'selector'=>'{{WOPB}} .woocommerce-Reviews-title { margin-bottom:{{headingSpace}}px; }'
                    ],
                ],
            ],
            //--------------------------
            //  Review Style
            //--------------------------
            'reviewColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .description > p {color:{{reviewColor}};}'
                    ]
                ],
            ],
            'reviewStarColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .star-rating span::before, {{WOPB}} #reviews p.stars a::before {color:{{reviewStarColor}} !important;}'
                    ]
                ],
            ],
            'reviewEmptyColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .star-rating::before{color:{{reviewEmptyColor}};}'
                    ]
                ],
            ],
            'authorTypography'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .woocommerce-review__author'
                    ],
                ],
            ],
            'dateTypography'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews .commentlist li time'
                    ],
                ],
            ],
            'descTypography'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .description > p',
                    ],
                ],
            ],
            'authorSize' => [
                'type' => 'string',
                'default' => '48',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews #comments .commentlist li img.avatar, .woocommerce {{WOPB}} #reviews #comments .commentlist li img.avatar {width:{{authorSize}}px;}'
                    ]
                ]
            ],
            'authorSpace' => [
                'type' => 'string',
                'default' => 15,
                'style' =>[
                    (object)[
                        'selector'=>'.woocommerce {{WOPB}} #reviews #comments .commentlist li img.avatar, {{WOPB}} #reviews #comments .commentlist li img.avatar {margin-right:{{authorSpace}}px;}'
                    ]
                ]
            ],

            //--------------------------
            //  Review Form Label
            //--------------------------
            'reviewFormLabelColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews .comment-form-rating label, {{WOPB}} #respond .comment-form-comment label {color:{{reviewFormLabelColor}};}'
                    ]
                ],
            ],
            'reviewFormRequiredColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews .comment-form-rating label .required, {{WOPB}} #respond .comment-form-comment label .required {color:{{reviewFormRequiredColor}};}'
                    ]
                ],
            ],
            'reviewFormLabelTypo'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #reviews .comment-form-rating label, {{WOPB}} #respond .comment-form-comment label'
                    ],
                ],
            ],

            //--------------------------
            // Review Form Input
            //--------------------------
            'reviewFormInputColor' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea {color:{{reviewFormInputColor}};}'
                    ]
                ],
            ],
            'reviewFormInputTypo'=>[
                'type' => 'object',
                'default' =>  (object)[],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea'
                    ],
                ],
            ],
            'reviewFormSpace' => [
                'type' => 'string',
                'default' => 10,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea {margin-top:{{reviewFormSpace}}px;}'
                    ]
                ]
            ],
            'reviewFormInputFocus' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea:focus'
                    ]
                ],
            ],
            'reviewFormInputBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea'
                    ]
                ],
            ],
            'reviewFormRadius' => [
                'type' => 'string',
                'default' => 4,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea {border-radius:{{reviewFormRadius}}px;}'
                    ]
                ]
            ],
            'reviewFormInputPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} #respond .comment-form-comment textarea {padding:{{reviewFormInputPadding}};}'
                    ],
                ],
            ],

            
            //--------------------------
            // Review Form Input
            //--------------------------
            'formBg' => [
                'type' => 'string',
                'value' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond.comment-respond, .woocommerce {{WOPB}} #review_form #respond.comment-respond {background:{{formBg}};}'
                    ]
                ],
            ],
            'formSpace' => [
                'type' => 'string',
                'default' => 40,
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond.comment-respond, .woocommerce {{WOPB}} #review_form_wrapper {margin-top:{{formSpace}}px;}'
                    ]
                ]
            ],
            'formBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid'],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond.comment-respond, .woocommerce {{WOPB}} #review_form #respond.comment-respond'
                    ]
                ],
            ],
            'formRadius' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} #respond.comment-respond, .woocommerce {{WOPB}} #review_form #respond.comment-respond {border-radius:{{formRadius}}px;}'
                    ]
                ]
            ],
            'reviewFormPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '','right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} #respond.comment-respond,  .woocommerce {{WOPB}} #review_form #respond.comment-respond { padding:{{reviewFormPadding}}; }'
                    ],
                ],
            ],

            //--------------------------
            //  Button
            //--------------------------
            'btnTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1, 'size' => (object)['lg' =>14, 'unit' =>'px'], 'height' => (object)['lg' =>20, 'unit' =>'px'], 'spacing' => (object)['lg' =>0, 'unit' =>'px'], 'transform' => 'capitalize', 'weight' => '', 'decoration' => 'none','family'=>'' ],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit'
                    ],
                ],
            ],
            'btnColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit { color:{{btnColor}} !important;}'
                    ],
                ],
            ],
            'btnBgColor' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => '#ff5845'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit'
                    ],
                ],
            ],
            'btnBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit'
                    ],
                ],
            ],
            'btnRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit { border-radius:{{btnRadius}};}'
                    ],
                ],
            ],
            'btnShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit'
                    ],
                ],
            ],
            'btnHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"]:hover , .woocommerce {{WOPB}} #review_form #respond #submit:hover { color:{{btnHoverColor}} !important; }'
                    ],
                ],
            ],
            'btnBgHoverColor' => [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => '#1239e2'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"]:hover , .woocommerce {{WOPB}} #review_form #respond #submit:hover'
                    ],
                ],
            ],
            'btnHoverBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4','type' => 'solid' ],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"]:hover , .woocommerce {{WOPB}} #review_form #respond #submit:hover'
                    ],
                ],
            ],
            'btnHoverRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"]:hover , .woocommerce {{WOPB}} #review_form #respond #submit:hover { border-radius:{{btnHoverRadius}}; }'
                    ],
                ],
            ],
            'btnHoverShadow' => [
                'type' => 'object',
                'default' => (object)['openShadow' => 0, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#009fd4'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"]:hover , .woocommerce {{WOPB}} #review_form #respond #submit:hover'
                    ],
                ],
            ],
            'btnSacing' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => 0,'bottom' => 0,'left' => 0,'right' => 0, 'unit' =>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit { margin:{{btnSacing}}; }'
                    ],
                ],
            ],
            'btnPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => "3",'bottom' => "3",'left' => "8",'right' => "8", 'unit' =>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .form-submit input[type="submit"] , .woocommerce {{WOPB}} #review_form #respond #submit { padding:{{btnPadding}}; }'
                    ],
                ],
            ],


            //--------------------------
            //  Wrap
            //--------------------------
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
        register_block_type( 'product-blocks/product-review',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Review', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            ));
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        global $comment;
        $block_name = 'product-review';
        $wraper_before = $wraper_after = $content = '';

        $product = wc_get_product();

        if (!empty($product)) {
            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-product-wrapper">';

            ob_start();
            comments_template();
            $content .= ob_get_clean();

            $wraper_after .= '</div>';
            $wraper_after .= '</div>';
        }

        return $wraper_before.$content.$wraper_after;
    }

}
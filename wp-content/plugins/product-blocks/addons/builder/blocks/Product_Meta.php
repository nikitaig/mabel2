<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Product_Meta{

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
            //  Product Meta Style
            //--------------------------
            'previews' => [
                'type' => 'string',
                'default' => '',
            ],
            'metaAlign' => [
                'type' => 'string',
                'default' => 'left',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaAlign','condition'=>'==','value'=>'left'],
                            (object)['key'=>'metaView','condition'=>'==','value'=>'inline'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-start}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaAlign','condition'=>'==','value'=>'center'],
                            (object)['key'=>'metaView','condition'=>'==','value'=>'inline'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:center}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaAlign','condition'=>'==','value'=>'right'],
                            (object)['key'=>'metaView','condition'=>'==','value'=>'inline'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {justify-content:flex-end}',
                    ],
                ],
            ],
            'metaSku'=>[
                'type' => 'boolean',
                'default'=> true
            ],
            'metaCategory'=>[
                'type' => 'boolean',
                'default'=> true
            ],
            'metaTag'=>[
                'type' => 'boolean',
                'default'=> true
            ],
            'metaLabelShow'=>[
                'type'=> 'boolean',
                'default'=> true,
            ],
            'metaView'=>[
                'type' => 'string',
                'default'=> 'stacked',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaView','condition'=>'==','value'=>'stacked'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper {display:inline-block;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaView','condition'=>'==','value'=>'inline'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{display:flex;flex-wrap: wrap;}',
                    ],
                ],
            ],
            'metaLabelSpace' => [
                'type' => 'string',
                'default'=> 10,
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaView','condition'=>'==','value'=>'stacked'],
                        ],
                        'selector'=>'{{WOPB}} .wopb-product-wrapper > div:not(:last-child){margin-bottom:{{metaLabelSpace}}px;}',
                    ],
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaView','condition'=>'==','value'=>'inline'],
                        ],
                        'selector'=>'{{WOPB}}  .wopb-product-wrapper > div:not(:last-child){margin-right:{{metaLabelSpace}}px;}',
                    ],
                ],
            ],

            //--------------------------
            //  Label Style
            //--------------------------
            'labelSku' => [
                'type' => 'string',
                'default'=> 'sku : ',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaSku','condition'=>'==','value'=>true],
                        ],
                    ],
                ],
            ],
            'labelCat' => [
                'type' => 'string',
                'default'=> 'category :  ',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaCategory','condition'=>'==','value'=>true],
                        ],
                    ],
                ],
            ],
            'labelTag' => [
                'type' => 'string',
                'default'=> 'tag : ',
                'style' =>[
                    (object)[
                        'depends' => [
                            (object)['key'=>'metaTag','condition'=>'==','value'=>true],
                        ],
                    ],
                ],
            ],
            'labelTypography'=>[
                'type' => 'object',
                'default' => (object)['openTypography'=>0,'size'=>(object)['lg'=>'', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'', 'unit'=>'px'],'decoration'=>'none','transform' => '','family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .meta-block__label'
                    ],
                ],
            ],
            'labelColor'=> [
                'type' => 'string',
                'default'=> '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .meta-block__label{color:{{labelColor}};}'
                    ],
                ],
            ],
            //--------------------------
            //  Value Style
            //--------------------------
            'valueTypography'=>[
                'type' => 'object',
                'default' => (object)['openTypography'=>0,'size'=>(object)['lg'=>'', 'unit'=>'px'], 'spacing'=>(object)[ 'lg'=>'', 'unit'=>'px'], 'height'=>(object)[ 'lg'=>'', 'unit'=>'px'],'decoration'=>'none','transform' => '','family'=>'','weight'=>''],
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-meta-list-cat, {{WOPB}} .wopb-meta-list-tag, {{WOPB}} .wopb-meta-list-sku'
                    ],
                ],
            ],
            'valueColor' => [
                'type' => 'string',
                'default' => '#ddd',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper a, .editor-styles-wrapper {{WOPB}} .wopb-product-wrapper a, {{WOPB}} .wopb-meta-list-sku {color:{{valueColor}};}'
                    ]
                ],
            ],
            'valueHoverColor' => [
                'type' => 'string',
                'default' => '',
                'style' =>[
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper .meta-block__value:hover{color:{{valueHoverColor}};}'
                    ]
                ],
            ],

            //--------------------------
            //  Wrapper
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
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{border-radius:{{wrapRadius}}; }'
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
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ margin:{{wrapMargin}}; }'
                    ],
                ],
            ],
            'wrapOuterPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '','bottom' => '','left' => '', 'right' => '', 'unit' =>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-product-wrapper{ padding:{{wrapOuterPadding}}; }'
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
        register_block_type( 'product-blocks/product-meta',
            array(
                'editor_script' => 'wopb-blocks-builder-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Product Meta', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
                'render_callback' =>  array($this, 'content')
            )
        );
    }

    public function list_items($terms, $type) {
        $inc = 1;
        $content = '';
        foreach ($terms as $term_id) {
            $term = get_term_by('id', $term_id, $type);
            if ($inc > 1) {
                $content .= ', ';
            }
            $content .= '<a class="wopb-meta-list-sku meta-block__value" href="'.get_term_link($term->slug, $type).'">'.esc_html($term->name).'</a>';
            $inc++;
        }
        return $content;
    }

    public function content($attr) {
        $default = $this->get_attributes(true);
        $attr = wp_parse_args($attr,$default);
        global $product;
        $block_name = 'product-meta';
        $wraper_before = $wraper_after = $content = '';
        
        $product = wc_get_product();

        if (!empty($product)) {
            do_action( 'woocommerce_product_meta_start' );
            $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
                $content .= '<div class="wopb-product-wrapper">';
                if ($attr['metaSku']) {
                    $content .= '<div class="wopb-meta-sku">';
                        if ($attr['metaLabelShow']) {
                            $content .= '<span class="wopb-meta-label-sku meta-block__label">'.esc_attr($attr['labelSku']).'</span>';
                        }
                        $content .= '<span class="wopb-meta-list-sku meta-block__value">'.esc_html($product->get_sku()).'</span>';
                    $content .= '</div>';
                }
                if ($attr['metaCategory']) {
                    $terms = $product->get_category_ids();
                    if (count($terms)) {
                        $content .= '<div class="wopb-meta-cat">';
                            if ($attr['metaLabelShow']) {
                                $content .= '<div class="wopb-meta-label-cat meta-block__label">'.esc_html($attr['labelCat']).'</div>';
                            }
                            $content .= '<div class="wopb-meta-list-cat meta-block__value">';
                                $content .= $this->list_items($terms, 'product_cat');
                            $content .= '</div>';
                        $content .= '</div>';
                    }
                }
                if ($attr['metaTag']) {
                    $tag_terms = $product->get_tag_ids();
                    if (count($tag_terms)) {
                        $content .= '<div class="wopb-meta-tag">';
                            if ($attr['metaLabelShow']) {
                                $content .= '<span class="wopb-meta-label-tag meta-block__label">'.esc_html($attr['labelTag']).'</span>';
                            }
                            $content .= '<div class="wopb-meta-list-tag">';
                                $content .= $this->list_items($tag_terms, 'product_tag');
                            $content .= '</div>';
                        $content .= '</div>';
                    }
                }
                $content .= '</div>';
            $wraper_after.= '</div>';
            do_action( 'woocommerce_product_meta_end' );
        }

        return $wraper_before.$content.$wraper_after;
    }

}
<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Filter {
    public function __construct() {
        add_action('init', array($this, 'register'));
        add_action('wp_ajax_wopb_show_more_filter_item', array($this, 'wopb_show_more_filter_item_callback'));
		add_action('wp_ajax_nopriv_wopb_show_more_filter_item', array($this, 'wopb_show_more_filter_item_callback'));
    }
    public function get_attributes($default = false){
        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            //--------------------------
            //      Price Filter Setting/Style
            //--------------------------
            'initialBlockTargetSelect' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'repeatableFilter' => [
                'type'=> 'array',
                'fields' => [
                    'type' => [
                        'type' => 'string',
                        'default' => 'price',
                    ],
                    'label' => [
                        'type' => 'string',
                        'default' => 'Filter By Price',
                    ]
                ],
                'default' => [
                    [
                        'type' => 'search',
                        'label' => 'Filter By Search',
                    ],
                    [
                        'type' => 'price',
                        'label' => 'Filter By Price',
                    ],
                    [
                        'type' => 'product_cat',
                        'label' => 'Filter By Category',
                    ],
                    [
                        'type' => 'status',
                        'label' => 'Filter By Status',
                    ],
                    [
                        'type' => 'rating',
                        'label' => 'Filter By Rating',
                    ],
                ],
            ],
            
            'sortingItems' => [
                'default' =>[
                    (object)[
                        "label" => __("Select Sort By", 'product-blocks'),
                        "value" => "",
                    ],
                    (object)[
                        "label" => __("Default Sorting", 'product-blocks'),
                        "value" => "default",
                    ],
                    (object)[
                        "label" => __("Sort by popularity", 'product-blocks'),
                        "value" => "popular",
                    ],
                    (object)[
                        "label" => __("Sort by latest", 'product-blocks'),
                        "value" => "latest",
                    ],
                    (object)[
                        "label" => __("Sort by average rating", 'product-blocks'),
                        "value" => "rating",
                    ],
                    (object)[
                        "label" => __("Sort by price: low to high", 'product-blocks'),
                        "value" => "price_low",
                    ],
                    (object)[
                        "label" => __("Sort by price: high to low", 'product-blocks'),
                        "value" => "price_high",
                    ],
                ]
            ],

            'blockTarget' => [
                'type' => 'string',
                'default' => '',
            ],
            'clearFilter' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'filterHeading' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'productCount' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'expandTaxonomy' => [
                'type' => 'boolean',
                'default' => false,
            ],
            'viewTaxonomyLimit' => [
                'type' => 'string',
                'default' => 10,
            ],
            'labelColor' => [
                'type' => 'string',
                'default' => '#474747',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-filter-block .wopb-filter-header .wopb-filter-label { color:{{labelColor}}; }']],
            ],
            'labelTypo' => [
                'type' => 'object',
                'default' =>  (object)['openTypography' => 1,'size' => (object)['lg' => '', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => '', 'family'=>'','weight'=>''],
                'style' => [(object)['selector' => '{{WOPB}} .wopb-filter-block .wopb-filter-header .wopb-filter-label']]
            ],
            'togglePlusMinus' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'togglePlusMinusInitialOpen' => [
                'type' => 'boolean',
                'default' => true,
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'togglePlusMinus','condition'=>'==','value'=> true],
                        ],
                    ],
                ],
            ],
            'togglePlusMinusSize' => [
                'type' => 'object',
                'default' => (object)['lg' =>'17', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'togglePlusMinus','condition'=>'==','value'=> true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-filter-header .wopb-filter-toggle .dashicons { font-size: {{togglePlusMinusSize}}; }'
                    ],
                ],
            ],
            'togglePlusMinusColor' => [
                'type' => 'string',
                'default' => '#373737',
                'style' => [
                    (object)[
                        'depends' => [
                            (object)['key'=>'togglePlusMinus','condition'=>'==','value'=> true],
                        ],
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-filter-header .wopb-filter-toggle .dashicons { color:{{togglePlusMinusColor}}; }']
                    ],
            ],
            'searchBoxColor' => [
                'type' => 'string',
                'default' => '#3b3b3b',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-filter-block .wopb-search-filter-body input { color:{{searchBoxColor}}; }']],
            ],
            'searchBoxBackgroundColor' => [
                'type' => 'string',
                'default' => '#fff',
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-search-filter-body input { background:{{searchBoxBackgroundColor}}; }'
                    ],
                ],
            ],
            'searchBoxBorder' => [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#9c9c9c','type' => 'solid' ],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-search-filter-body input'
                    ],
                ],
            ],
            'searchBoxRadius' => [
                'type' => 'object',
                'default' => (object)['lg' =>'4', 'unit' =>'px'],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-search-filter-body input { border-radius:{{searchBoxRadius}}; }'
                    ],
                ],
            ],
            'searchBoxPadding' => [
                'type' => 'object',
                'default' => (object)['lg' =>(object)['top' => '6','bottom' => '6','left' => '12','right' => '12', 'unit' =>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-filter-block .wopb-search-filter-body input { padding:{{searchBoxPadding}}; }'
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
        register_block_type( 'product-blocks/filter',
            array(
                'editor_script' => 'wopb-blocks-editor-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Filter by Price', 'product-blocks'),
                // 'attributes' => $this->get_attributes(),
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
            $wraper_before = '';
            $block_name = 'filter';
            $page_post_id = wopb_function()->get_ID();
            $post = get_post($page_post_id);
            $blocks = parse_blocks($post->post_content);
            $target_block_attr = [];
            $target_block_attr = $this->getTargetBlockAttributes($attr, $blocks, $target_block_attr);
            $attr['headingShow'] = true;
            $active_filters = $attr['repeatableFilter'];
            $wrapper_class = '';
            $wrapper_class .= 'wopb-filter-block-front-end ';
            $wrapper_class .= ($attr['togglePlusMinus'] && $attr['togglePlusMinusInitialOpen']) ? 'wopb-filter-toggle-initial-open ' : '';
            $wrapper_class .= ($attr['togglePlusMinus'] && !$attr['togglePlusMinusInitialOpen']) ? 'wopb-filter-toggle-initial-close ' : '';
            $html = '';

            $wraper_before .= '<div ' . ($attr['advanceId'] ? 'id="' . esc_attr($attr['advanceId']) . '" ' : '') . ' class="wp-block-product-blocks-' . esc_attr($block_name) . ' wopb-block-' . esc_attr($attr["blockId"]) . ' ' . (isset($attr["class"]) ? esc_attr($attr["class"]) : '') . '">';
            $wraper_before .= '<div class="wopb-product-wrapper wopb-filter-block ' . $wrapper_class . '" data-postid = "' . $page_post_id . '" data-block-target = "' . $attr['blockTarget'] . '" data-current-url="' . get_pagenum_link() . '">';

            if ($attr['clearFilter']) {
                ob_start();
                $this->removeFilterItem();
                $html .= ob_get_clean();
            }

            if ($attr['filterHeading']) {
                $html .= '<div class="wopb-filter-title-section">';
                $html .= '<span class="wopb-filter-title">Filter</span>';
                $html .= '<span class="dashicons dashicons-filter wopb-filter-icon"></span>';
                $html .= '</div>';
            }

            foreach ($active_filters as $active_filter) {
                $params = [
                    'headerLabel' => $active_filter['label'],
                    'target_block_attr' => $target_block_attr
                ];
                switch ($active_filter['type']) {
                    case 'search':
                        ob_start();
                        $this->search_filter($attr, $params);
                        $html .= ob_get_clean();
                        break;
                    case 'price':
                        ob_start();
                        $this->price_filter($attr, $params);
                        $html .= ob_get_clean();
                        break;
                    case 'status':
                        ob_start();
                        $this->status_filter($attr, $params);
                        $html .= ob_get_clean();
                        break;
                    case 'rating':
                        ob_start();
                        $this->rating_filter($attr, $params);
                        $html .= ob_get_clean();
                        break;
                    case 'sort_by':
                        ob_start();
                        $this->sorting_filter($attr, $params);
                        $html .= ob_get_clean();
                        break;

                    default:
                        $attr['viewTaxonomyLimit'] = isset($attr['viewTaxonomyLimit']) ? $attr['viewTaxonomyLimit'] : 10;
                        $object_taxonomies =  array_diff(get_object_taxonomies('product'), ['product_type', 'product_visibility', 'product_shipping_class']);
                        foreach ($object_taxonomies as $key) {
                            $taxonomy = get_taxonomy($key);
                            if ($taxonomy->name === $active_filter['type']) {
                                $term_query = array (
                                    'taxonomy' => $key,
                                    'hide_empty' => true,
                                );
                                if($key !== 'product_tag') {
                                    $term_query['parent'] = 0;
                                }
                                if(wopb_function()->get_attribute_by_taxonomy($key)) {
                                    $taxonomy->attribute = wopb_function()->get_attribute_by_taxonomy($key);
                                    unset($term_query['parent']);
                                }
                                $taxonomy->total_terms = count(get_terms($key, $term_query));
                                $term_query['number'] = $attr['viewTaxonomyLimit'];
                                $taxonomy->terms = get_terms($term_query);
                                $params['taxonomy'] = $taxonomy;
                                ob_start();
                                    $this->product_taxonomy_filter($attr, $params);
                                $html .= ob_get_clean();
                            }
                        }
                }
            }

            ob_start();
            $this->reset_filter($attr);
            $html .= ob_get_clean();

            $wraper_after = '</div>';
            $wraper_after .= '</div>';

            return $wraper_before . $html . $wraper_after;
        }
    }

    public function removeFilterItem() {
?>
        <div class="wopb-filter-remove-section">
            <span class="wopb-filter-active-item-list">
            </span>
            <span class="wopb-filter-remove-all">
                <?php _e('Clear All', 'product-blocks') ?> <span class="dashicons dashicons-no-alt wopb-filter-remove-icon">
            </span>
        </div>
<?php
    }

    public function filter_header_content ($attr, $params) {
?>
        <div class="wopb-filter-header">
            <span class="wopb-filter-label">
                <?php esc_html_e($params['headerLabel']) ?>
            </span>
            <?php if($attr['togglePlusMinus']) { ?>
                <div class="wopb-filter-toggle">
                    <span class="dashicons dashicons-plus-alt2 wopb-filter-plus"></span>
                    <span class="dashicons dashicons-minus wopb-filter-minus"></span>
                </div>
            <?php } ?>
        </div>
<?php
    }

    public function search_filter($attr, $params) {
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>
            <div class="wopb-filter-body">
                <div class="wopb-search-filter-body">
                    <input type="hidden" class="wopb-filter-slug" value="search">
                    <input type="text" class="wopb-filter-search-input" placeholder="<?php _e('Search Products', 'product-blocks') ?>..."/>
                    <span class="wopb-search-icon">
                        <img src="<?php echo WOPB_URL ?>/assets/img/blocks/search.svg" alt="<?php __('Image', 'product-blocks')?>" />
                    </span>
                </div>
            </div>
        </div>
<?php
    }

    public function price_filter($attr, $params) {
        $highest_price = $this->get_highest_price();
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>

            <div class="wopb-filter-body wopb-price-range-slider">
                <input type="hidden" class="wopb-filter-slug" value="price">
                <div class="wopb-price-range">
                    <span class="wopb-price-range-bar"></span>
                    <input type="range" class="wopb-price-range-input wopb-price-range-input-min" min="0" max="<?php esc_attr_e($highest_price) ?>" value="0" step="1">
                    <input type="range" class="wopb-price-range-input wopb-price-range-input-max" min="0" max="<?php esc_attr_e($highest_price) ?>" value="<?php esc_attr_e($highest_price) ?>" step="1">
                </div>
                <span class="wopb-filter-price-input-group">
<!--                    --><?php //_e('Price', 'product-blocks') ?><!--: <span class="wopb-price-range-value">--><?php //echo wc_price(0) ?><!-- - --><?php //echo wc_price($highest_price) ?><!--</span>-->
                    <input type="number" class="wopb-filter-price-input wopb-filter-price-min" value="0" min="0">
                    <input type="number" class="wopb-filter-price-input wopb-filter-price-max" value="<?php esc_attr_e($highest_price) ?>" min="0" max="<?php esc_attr_e($highest_price) ?>">
                </span>
            </div>
        </div>
<?php
    }

    public function status_filter($attr, $params) {
        $stock_params = [];
        $queried_object = get_queried_object();
        if(is_product_taxonomy()) {
            $stock_params['taxonomy'] = $queried_object->taxonomy;
            $stock_params['taxonomy_term_id'] = $queried_object->term_id;
        }
        $stock_params['target_block_attr'] = $params['target_block_attr'];
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>

            <div class="wopb-filter-body">
                <input type="hidden" class="wopb-filter-slug" value="status">
                <div class="wopb-filter-check-list">
                    <?php
                        foreach (wc_get_product_stock_status_options() as $key => $status) {
                            $count = wopb_function()->generate_stock_status_count_query($key, $stock_params);
                    ?>
                        <div class="wopb-filter-check-item-section">
                            <div class="wopb-filter-check-item">
                                <label for="status_<?php esc_attr_e($key) ?>">
                                    <input type="checkbox" class="wopb-filter-status-input" id="status_<?php esc_attr_e($key) ?>" value="<?php esc_attr_e($key) ?>"/>
                                    <?php esc_html_e($status) ?> <?php $attr['productCount'] ? esc_html_e('(' . $count .')') : '' ?>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php
    }

    public function rating_filter($attr, $params) {
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>

            <div class="wopb-filter-body">
                <input type="hidden" class="wopb-filter-slug" value="rating">
                <div class="wopb-filter-check-list wopb-filter-ratings">
                    <?php for ($row = 5; $row > 0; $row--) { ?>
                        <div class="wopb-filter-check-item-section">
                            <div class="wopb-filter-check-item">
                                <label for="filter-rating-<?php esc_attr_e($row) ?>">
                                    <input type="checkbox" class="wopb-filter-rating-input" value="<?php esc_attr_e($row) ?>" id="filter-rating-<?php esc_attr_e($row) ?>">
                                    <?php for ($filledStar = $row; $filledStar > 0; $filledStar--) { ?>
                                        <span class="dashicons dashicons-star-filled"></span>
                                    <?php } ?>
                                    <?php for ($emptyStar = 0; $emptyStar < 5- $row; $emptyStar++) { ?>
                                        <span class="dashicons dashicons-star-empty"></span>
                                    <?php } ?>
                                </label>
                            </div>
                        </div>
                   <?php } ?>
                </div>
            </div>
        </div>
<?php
    }

     public function product_taxonomy_filter($attr, $params) {

         if(is_search()) {
             $attr['is_search'] = is_search();
             $attr['search_query'] = get_search_query();
         }
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>

            <div class="wopb-filter-body">
                <input
                    type="hidden"
                    class="wopb-filter-slug"
                    value="product_taxonomy"
                    data-taxonomy="<?php esc_attr_e($params['taxonomy']->name) ?>"
                    data-term-limit="<?php esc_attr_e($attr['viewTaxonomyLimit']) ?>"
                    data-attributes="<?php esc_attr_e(json_encode($attr)) ?>"
                    data-target-block-attributes="<?php esc_attr_e(json_encode($params['target_block_attr'])) ?>"
                />
                <div class="wopb-filter-check-list">
                    <?php
                        !empty($params['taxonomy']) ? $this->product_taxonomy_terms($attr, $params) : '';
                    ?>
                </div>
                <?php
                    if( $params['taxonomy']->total_terms > $attr['viewTaxonomyLimit']) {
                        $item_total_page = $params['taxonomy']->total_terms / $attr['viewTaxonomyLimit'];
                        $item_total_page = ceil((float)$item_total_page);

                ?>
                        <a href="javascript:" class="wopb-filter-extend-control wopb-filter-show-more" data-item-page="1" data-item-total-page="<?php esc_attr_e($item_total_page); ?>">
                            <?php _e('Show More', 'product-blocks') ?>
                        </a>
                        <a href="javascript:" class="wopb-filter-extend-control wopb-filter-show-less" data-item-page="1">
                            <?php _e('Show Less', 'product-blocks') ?>
                        </a>
                <?php } ?>
            </div>
        </div>
<?php
    }



    public function product_taxonomy_terms($attr, $params) {
        $queried_object = get_queried_object();
        $taxonomy = $params['taxonomy'];
?>
        <?php
            $key = 0;
            foreach ($taxonomy->terms as $term) {
                $key++;
                 $child_term_query = array (
                    'hide_empty' => true,
                    'parent' => $term->term_id,
                    'number' => $attr['viewTaxonomyLimit']
                );
                $query_args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'post_status' => 'publish',
                );
                if(isset($attr['is_search']) && $attr['is_search']) {
                    $query_args['s'] = $attr['search_query'];
                }
                $term->child_terms = get_terms($taxonomy->name, $child_term_query);
                $query_args['tax_query'][] = array(
                    'taxonomy' => $taxonomy->name,
                    'field' => 'term_id',
                    'terms'    => $term->term_id,
                );
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => 'exclude-from-catalog',
                    'operator' => 'NOT IN',
                );
                if(is_product_taxonomy()) {
                    $query_args['tax_query'][] = [
                        'taxonomy' => $queried_object->taxonomy,
                        'field' => 'id',
                        'terms' => $queried_object->term_id,
                        'operator' => 'IN'
                    ];
                }
                if(isset($params['target_block_attr']['queryCat'])) {
                    $query_args['tax_query'][] = array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => json_decode(stripslashes($params['target_block_attr']['queryCat'])),
                        'operator' => 'IN',
                    );
                }
                $recent_posts = new \WP_Query( $query_args);
                $term->product_count = count($recent_posts->posts);
                $extended_item_class = isset($params['show_more']) ? 'wopb-filter-extended-item' : '';
        ?>
            <div class="wopb-filter-check-item-section <?php esc_attr_e($extended_item_class) ?>">
                <div class="wopb-filter-check-item">
                    <label for="tax_term_<?php esc_attr_e($term->name . '_' . $term->term_id) ?>">
                        <input
                            type="checkbox"
                            class="wopb-filter-tax-term-input"
                            id="tax_term_<?php esc_attr_e($term->name . '_' . $term->term_id) ?>"
                            value="<?php esc_attr_e($term->term_id) ?>"
                            data-label="<?php esc_attr_e($term->name) ?>"
                        />
                        <?php
                            if(isset($taxonomy->attribute) && $taxonomy->attribute->attribute_type === 'color') {
                                $color_code = get_term_meta($term->term_id, $taxonomy->attribute->attribute_type, true);
                                $color_html = $color_code ? "<span class='wopb-filter-tax-color' style='background-color: " . esc_attr($color_code) . "'></span>" : '';
                                echo $color_html;
                            }
                        ?>
                       <span><?php esc_html_e($term->name) ?> <?php $attr['productCount'] ? esc_html_e('(' . $term->product_count .')') : '' ?></span>
                    </label>
                    <?php
                        if($term->child_terms) {
                            $params['taxonomy']->terms = $term->child_terms;
                            $params['term_type'] = 'child';
                    ?>
                         <div class="wopb-filter-check-list wopb-filter-child-check-list<?php
                                isset($attr['expandTaxonomy']) && $attr['expandTaxonomy'] == 'true'
                                    ? ''
                                    : esc_attr_e(' wopb-d-none')
                            ?>"
                        >
                            <?php $this->product_taxonomy_terms($attr, $params);?>
                        </div>
                    <?php } ?>
                </div>
                <?php if($term->child_terms) { ?>
                    <div class="wopb-filter-child-toggle">
                        <span class="dashicons dashicons-arrow-right-alt2 wopb-filter-right-toggle<?php echo $attr['expandTaxonomy'] == 'true' ? ' wopb-d-none' : '' ?>"></span>
                        <span class="dashicons dashicons-arrow-down-alt2 wopb-filter-down-toggle<?php echo $attr['expandTaxonomy'] == 'true' ? '' : ' wopb-d-none' ?>"></span>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
<?php

    }

    public function sorting_filter($attr, $params) {
?>
        <div class="wopb-filter-section">
            <?php $this->filter_header_content($attr, $params); ?>

            <div class="wopb-filter-body">
                <input type="hidden" class="wopb-filter-slug" value="sorting">
                <select name="sortBy" class="select wopb-filter-sorting-input">
                    <?php foreach ($attr['sortingItems'] as $item) { ?>
                        <option value="<?php esc_attr_e($item->value)?>" ><?php esc_html_e($item->label)?></option>
                   <?php } ?>
                </select>
            </div>
        </div>
<?php
    }

    public function reset_filter($attr) {
        $queried_object = get_queried_object();
        $slug = '';
        $current_page_value = '';
        $taxonomy = '';
        if(is_product_taxonomy()) {
            $slug = 'product_taxonomy';
            $current_page_value = $queried_object->term_id;
            $taxonomy = $queried_object->taxonomy;
        }elseif (is_search()) {
            $slug = 'product_search';
            $current_page_value = get_search_query();
        }
?>
        <div class="wopb-filter-section wopb-filter-reset-section">
            <div class="wopb-filter-body">
                <input type="hidden" class="wopb-filter-slug wopb-filter-slug-reset wopb-d-none" value="reset">
                <?php if(isset($slug)) { ?>
                    <input type="hidden" class="wopb-filter-current-page wopb-d-none" value="<?php esc_attr_e($current_page_value); ?>" data-slug="<?php esc_attr_e($slug); ?>" data-taxonomy="<?php esc_attr_e($taxonomy); ?>">
                <?php } ?>
            </div>
        </div>
<?php
    }

    public function get_highest_price() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );

        $query = new \WP_Query($args);
        $max_price = '';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                if($product->is_type('variable')) {
                    if($product->get_stock_status() != 'outofstock') {
                        $variation_ids = $product->get_children();
                        foreach ($variation_ids as $variation_id) {
                            $variation = wc_get_product($variation_id);
                            if($variation->get_sale_price()) {
                                $max_price = $max_price < $variation->get_sale_price() ? $variation->get_sale_price() : $max_price;
                            }else {
                                $max_price = $max_price < $variation->get_regular_price() ? $variation->get_regular_price() : $max_price;
                            }
                        }
                    }
                }else {
                    if($product->get_sale_price()) {
                        $max_price = $max_price < $product->get_sale_price() ? $product->get_sale_price() : $max_price;
                    }else {
                        $max_price = $max_price < $product->get_regular_price() ? $product->get_regular_price() : $max_price;
                    }
                }
            }
        }
        wp_reset_postdata();
        return ceil((float)$max_price);
    }


    /**
	 * Show more filter item by ajax
     *
     * @since v.2.5.3
	 * @return HTML
	 */
    public function wopb_show_more_filter_item_callback() {
        $attr = $_POST['attributes'];
        $taxonomy = new \stdClass();
        $taxonomy->name = $_POST['taxonomy'];
        $term_query = array (
            'taxonomy' => $taxonomy->name,
            'hide_empty' => true,
        );
        $previous_term_ids = wp_list_pluck(get_terms(
            array_merge(
                [
                    'number' => $_POST['term_limit']
                ], $term_query
        )), 'term_id');

        if(wopb_function()->get_attribute_by_taxonomy($taxonomy->name)) {
            $taxonomy->attribute = wopb_function()->get_attribute_by_taxonomy($taxonomy->name);
        }elseif($taxonomy->name !== 'product_tag') {
            $term_query['parent'] = 0;
        }
        $taxonomy->terms = get_terms(array_merge(
            [
                'offset' => ($_POST['item_page'] - 1) * $_POST['term_limit'],
                'number' => $_POST['term_limit']
            ], $term_query));
        $params = [
                'taxonomy' => $taxonomy,
                'show_more' => true,
                'target_block_attr' => $_POST['target_block_attr'],
        ];
        if(count($taxonomy->terms) == 0) {
            return false;
        }
        echo $this->product_taxonomy_terms($attr, $params);
        wp_die();
    }

    /**
     * Get targeted filter block attribute
     *
     * @param $attr
     * @param $blocks
     * @param $target_block_attr
     * @return array
     * @since v.2.5.4
     */
    public function getTargetBlockAttributes ($attr, $blocks, &$target_block_attr) {
        foreach ($blocks as $block) {
            if($block['blockName'] == 'product-blocks/'.$attr['blockTarget'] ) {
                $target_block_attr = $block['attrs'];
            } elseif (count($block['innerBlocks']) > 0) {
                $this->getTargetBlockAttributes($attr, $block['innerBlocks'], $target_block_attr);
            }
        }
        return $target_block_attr;
    }
}
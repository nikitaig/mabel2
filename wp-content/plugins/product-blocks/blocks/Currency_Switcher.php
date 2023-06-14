<?php
namespace WOPB\blocks;

defined('ABSPATH') || exit;

class Currency_Switcher {
    public function __construct() {
        add_action('init', array($this, 'register'));
    }
    public function get_attributes($default = false){

        $attributes = array(
            'blockId' => [
                'type' => 'string',
                'default' => '',
            ],

            'currencySymbolPosition' => [
                'type' => 'string',
                'default' => 'leftDollar',
            ],
            'containerRadius' => [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container , {{WOPB}} .wopb-block-wrapper .wopb-set-default-currency { border-radius:{{containerRadius}}; }'
                    ],
                ],
            ],
            'containerMargin'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>0,'right'=>0,'bottom'=>0,'left'=>0, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container { margin:{{containerMargin}}; }'
                    ],
                ],
            ],
            'containerPadding'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>10,'right'=>15,'bottom'=>10,'left'=>15, 'unit'=>'px']],
                'style' => [
                    (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container { padding:{{containerPadding}}; }'
                    ],
                ],
            ],

            // Flag 
            'showFlag' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'flagSpace'=> [
                'type' => 'string',
                'default' => '6',
                'style' => [ (object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container img { margin-right: {{flagSpace}}px; }']],
            ],
            'flagHeight'=> [
                'type' => 'string',
                'default' => '26',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container img { height: {{flagHeight}}px; }']],
            ],
            'flagWidth'=> [
                'type' => 'string',
                'default' => '26',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container img { width:{{flagWidth}}px; }']],
            ],
            'flagBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>0, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container img ']],
            ],
            'flagRadius'=> [
                'type' => 'object',
                'default' => (object)['lg'=>(object)['top'=>4,'right'=>4,'bottom'=>4,'left'=>4, 'unit'=>'px']],
                'style' => [
                     (object)[
                        'selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container img  { border-radius:{{flagRadius}}; }'
                    ],
                ],
            ],

            // Arrow 
            'arrowSize'=> [
                'type' => 'string',
                'default' => '13',
                'style' => [
                    (object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-arrow , {{WOPB}} .wopb-block-wrapper .wopb-currency-arrow::before { height: {{arrowSize}}px; width:{{arrowSize}}px; }']],
            ],
            // Field 
            'fieldTextColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-set-default-currency , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-currency-arrow { color:{{fieldTextColor}}; }']],
            ],
            'fieldTextHoverColor'=> [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container:hover , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-set-default-currency:hover , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container:hover .wopb-currency-arrow { color:{{fieldTextHoverColor}}; }']],
            ],
            'fieldBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container ']],
            ],
            'fieldHoverBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container:hover ']],
            ],
            'fieldBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-set-default-currency .wopb-select-container']],
            ],
            'fieldHoverBorder'=> [
                'type' => 'object',
                'default' => (object)['openBorder'=>1, 'width' => (object)[ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],'color' => '#e5e5e5','type' => 'solid' ],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-selected-currency-container:hover']],
            ],
            
            'optionBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 1,'type' => 'color', 'color' => '#f0eeee'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-set-default-currency , {{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-select-container ']],
            ],
            'optionHoverBg'=> [
                'type' => 'object',
                'default' => (object)['openColor' => 0,'type' => 'color', 'color' => ''],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-container .wopb-select-container li:hover ']],
            ],
        
            // prefix 
            'prefixText' =>[
                'type' => 'string',
                'default' => 'Currency Switcher',
            ],
            'prefixColor' => [
                'type' => 'string',
                'default' => '#000',
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-prefixText { color:{{prefixColor}}; }']],
            ],
            'prefixTypo' => [
                'type' => 'object',
                'default' => (object)['openTypography' => 1,'size' => (object)['lg' => '16', 'unit' => 'px'], 'height' => (object)['lg' => '', 'unit' => 'px'],'decoration' => 'none', 'transform' => 'capitalize','weight'=>'500'],
                'style' => [(object)['selector'=>'{{WOPB}} .wopb-block-wrapper .wopb-currency-switcher-prefixText']]
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
        register_block_type( 'product-blocks/currency-switcher',
            array(
                'editor_script' => 'wopb-blocks-editor-script',
                'editor_style'  => 'wopb-blocks-editor-css',
                'title' => __('Heading', 'product-blocks'),
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
        $wopb_currency_switcher = wopb_function()->get_setting('wopb_currency_switcher');
        $block_name = 'currency-switcher';

        $wraper_before = '';
        $wraper_before .= '<div '.($attr['advanceId']?'id="'.esc_attr($attr['advanceId']).'" ':'').' class="wp-block-product-blocks-'.esc_attr($block_name).' wopb-block-'.esc_attr($attr["blockId"]).' '.(isset($attr["className"])?esc_attr($attr["className"]):'').'">';
            $wraper_before .= '<div class="wopb-block-wrapper">';

                if($wopb_currency_switcher === 'true' && wopb_function()->is_lc_active()) {
                    $currency_code_options = get_woocommerce_currencies();
                    foreach ( wopb_function()->get_setting('wopb_currencies') as $key => $currency ) {
                        if($attr['currencySymbolPosition']=='leftDollar') {
                            $added_currency[$currency['wopb_currency']] = '( ' . get_woocommerce_currency_symbol( $currency['wopb_currency'] ) . ' ) '.$currency_code_options[$currency['wopb_currency']].' '.$currency['wopb_currency'];
                        }
                        else if($attr['currencySymbolPosition'] =='rightDollar') {
                            $added_currency[$currency['wopb_currency']] = $currency_code_options[$currency['wopb_currency']].'( ' . get_woocommerce_currency_symbol( $currency['wopb_currency'] ) . ' ) '.' '.$currency['wopb_currency'];
                        }
                    }

                    $wopb_current_currency = array_key_exists( wopb_function()->get_setting('wopb_current_currency') , $added_currency) ?  wopb_function()->get_setting('wopb_current_currency') : wopb_function()->get_setting('wopb_default_currency');
                    $wraper_before .= '<div class="wopb-currency-switcher-prefixText">'.$attr['prefixText'].'</div>';

                    $wraper_before .= '<div class="wopb-currency-switcher-container">';
                        $wraper_before .= '<div class="wopb-selected-currency-container">';
                            $wraper_before .= isset($added_currency) && count($added_currency) > 1 ? '<span class="wopb-currency-arrow"></span>' : '';
                            $wraper_before .= '<div class="wopb-selected-currency" value="">'.( $attr['showFlag'] ? '<img src="https://raw.githubusercontent.com/wpxpo/wpxpo_profile/main/country_flags/'.strtolower($wopb_current_currency).'.png" alt="flag"> ':'').$added_currency[$wopb_current_currency].'</div>';
                        $wraper_before .= '</div>';

                        if(isset($added_currency) && count($added_currency) > 1) {
                            $wraper_before .= '<div name="wopb_current_currency" class="wopb-set-default-currency" style="display:none">';
                                $wraper_before .= '<ul class="wopb-select-container" >';
                                    foreach ( $added_currency as $key => $label ) {
                                        $wraper_before .= '<li class="'.( $wopb_current_currency == $key ? "hide-currency" : '' ).'" value="'.esc_attr($key).'">'.( $attr['showFlag'] ? '<img src="https://raw.githubusercontent.com/wpxpo/wpxpo_profile/main/country_flags/'.strtolower($key).'.png" alt="flag"> ':'').strip_tags( esc_html($label) ).'</li>';
                                    }
                                $wraper_before .= '</ul>';
                            $wraper_before .= '</div>';
                        }
                    $wraper_before .= '</div>';
                }else {
                    $wraper_before .= '<div class="wopb-currency-switcher-container-pro-message">Enable Currency Switcher Addon to use this block.</div>';
                }

            $wraper_before .= '</div>';
        $wraper_before .= '</div>';
        return $wraper_before;
    }

}
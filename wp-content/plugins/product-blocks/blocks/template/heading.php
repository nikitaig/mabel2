<?php
defined('ABSPATH') || exit;

if ($attr['headingShow']) {
    $wraper_before .= '<div class="wopb-heading-wrap wopb-heading-'.esc_attr($attr["headingStyle"]).' wopb-heading-'.esc_attr($attr["headingAlign"]).'">';
        if ($attr['headingURL']) {
            $wraper_before .= '<'.esc_attr($attr['headingTag']).' class="wopb-heading-inner"><a href="'.esc_url($attr["headingURL"]).'"><span>'.wp_kses_post($attr["headingText"]).'</span></a></'.esc_attr($attr['headingTag']).'>';
        } else {
            $wraper_before .= '<'.esc_attr($attr['headingTag']).' class="wopb-heading-inner"><span>'.wp_kses_post($attr["headingText"]).'</span></'.esc_attr($attr['headingTag']).'>';
        }
        if ($attr['headingStyle'] == 'style11' && $attr['headingURL'] && $attr['headingBtnText']) {
            $wraper_before .= '<a class="wopb-heading-btn" href="'.esc_url($attr['headingURL']).'">'.esc_html($attr["headingBtnText"]).wopb_function()->svg_icon('rightArrowLg').'</a>';
        }
        if ($attr['subHeadingShow']) {
            $wraper_before .= '<div class="wopb-sub-heading"><div class="wopb-sub-heading-inner">'.wp_kses_post($attr['subHeadingText']).'</div></div>';
        }
    $wraper_before .= '</div>';
}
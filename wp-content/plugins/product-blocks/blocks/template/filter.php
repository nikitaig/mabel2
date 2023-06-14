<?php
defined('ABSPATH') || exit;
global $wpdb;
$post_meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_value LIKE %s", '%.wopb-block-'.$attr['blockId'].'%'));
if($post_meta && isset($post_meta->post_id) && $post_meta->post_id != $page_post_id) {
    $page_post_id = $post_meta->post_id;
}
$wraper_before .= '<div class="wopb-filter-wrap" data-taxtype='.esc_attr($attr['filterType']).' data-blockid="'.esc_attr($attr['blockId']).'" data-blockname="product-blocks_'.esc_attr($block_name).'" data-postid="'.esc_attr($page_post_id).'" data-current-url="' . get_pagenum_link() . '">';
    $wraper_before .= wopb_function()->filter($attr['filterText'], $attr['filterType'], $attr['filterCat'], $attr['filterTag'], $attr['filterAction'], $attr['filterActionText'], $noAjax, $attr['filterMobileText'], $attr['filterMobile']);
$wraper_before .= '</div>';
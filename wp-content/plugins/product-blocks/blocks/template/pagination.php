<?php
defined('ABSPATH') || exit;
global $wpdb;
$page_post_id = $page_post_id ? $page_post_id : $attr['page_post_id'];
$post_meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_value LIKE %s", '%.wopb-block-'.$attr['blockId'].'%'));
if($post_meta && isset($post_meta->post_id) && $post_meta->post_id != $page_post_id) {
    $page_post_id = $post_meta->post_id;
}
$filter_attributes = [];
if(isset($attr['product_filters'])) {
    $filter_attributes['product_filters'] = $attr['product_filters'];
}elseif(isset($attr['queryTax'])) {
    $filter_attributes['queryTax'] = $attr['queryTax'];
    if(isset($attr['queryCatAction'])) {
        $filter_attributes['queryCatAction'] = $attr['queryCatAction'];
    }elseif(isset($attr['queryTagAction'])) {
        $filter_attributes['queryTagAction'] = $attr['queryTagAction'];
    }
}
$data_filter_attributes = " data-filter-attributes=" . json_encode($filter_attributes);
$wrapper_main_content .= '<div class="wopb-pagination-wrap'.($attr["paginationAjax"] ? " wopb-pagination-ajax-action" : "").'" data-paged="1" data-blockid="'.esc_attr($attr['blockId']).'" data-postid="'.esc_attr($page_post_id).'" data-pages="'.esc_attr($pageNum).'" data-blockname="product-blocks_'.esc_attr($block_name).'" '.wopb_function()->get_builder_attr() . $data_filter_attributes . '>';
    $wrapper_main_content .= wopb_function()->pagination($pageNum, $attr['paginationNav'], $attr['paginationText'], $attr);
$wrapper_main_content .= '</div>';
<?php
defined('ABSPATH') || exit;

$post_id        = get_the_ID();
$title          = get_the_title( $post_id );
$titlelink      = get_permalink( $post_id );
$post_thumb_id  = get_post_thumbnail_id( $post_id );

$product        = wc_get_product($post_id);
$_sales         = $product ? $product->get_sale_price() : 0;
$_regular       = $product ? $product->get_regular_price() : 0;
$_discount      = ($_sales && $_regular) ? round( ( $_regular - $_sales ) / $_regular * 100 ).'%' : '';
$rating_count   = $product ? $product->get_rating_count() : 0;
$rating_average = $product ? $product->get_average_rating() : 0;

$wishlist_active= in_array($post_id, $wishlist_data);
$compare_active = in_array($post_id, $compare_data);
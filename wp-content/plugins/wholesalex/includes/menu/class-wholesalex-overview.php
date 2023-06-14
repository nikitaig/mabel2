<?php
/**
 * WholesaleX Overview
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

use DateTime;
use WC_Admin_Report;
use WP_Query;
use WP_User_Query;


/**
 * WholesaleX Overview Class
 */
class WHOLESALEX_Overview {

	/**
	 * Overview Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'overview_submenu_page_callback' ), 1 );
		add_action( 'rest_api_init', array( $this, 'overview_callback' ) );
		add_action( 'admin_menu', array( $this, 'go_pro_menu_page' ),99999 );
		global $wpdb;
	}

	/**
	 * Overview Menu callback
	 *
	 * @return void
	 */
	public function overview_submenu_page_callback() {
		$wholesalex_menu_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNTcuOTI0IiBoZWlnaHQ9IjIzNi4yNTciPjxnIGRhdGEtbmFtZT0iR3JvdXAgMzI5MyI+PGcgZGF0YS1uYW1lPSJHcm91cCAzMjcyIj48ZyBkYXRhLW5hbWU9Ikdyb3VwIDMyNjUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0yNTk4LjQ5MiAxNDguMDk3KSI+PHJlY3Qgd2lkdGg9Ijc0LjU0NiIgaGVpZ2h0PSI3NC41NDYiIGZpbGw9IiNmZmYiIGRhdGEtbmFtZT0iUmVjdGFuZ2xlIDI2MjMiIHJ4PSI2LjU5NCIgdHJhbnNmb3JtPSJyb3RhdGUoLTExLjc3NCA3NjQuNzI1IC0xMjkxNi41MTgpIi8+PC9nPjxnIGRhdGEtbmFtZT0iR3JvdXAgMzI2NiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTI1OTguNDkyIDE0OC4wOTcpIj48cmVjdCB3aWR0aD0iNzQuNTQ2IiBoZWlnaHQ9Ijc0LjU0NiIgZmlsbD0iI2ZmZiIgZGF0YS1uYW1lPSJSZWN0YW5nbGUgMjYyNCIgcng9IjYuNTk0IiB0cmFuc2Zvcm09InJvdGF0ZSgtMTEuNzc0IDExNzIuNjMgLTEyOTU4LjU4KSIvPjwvZz48ZyBkYXRhLW5hbWU9Ikdyb3VwIDMyNjciIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0yNTk4LjQ5MiAxNDguMDk3KSI+PHJlY3Qgd2lkdGg9Ijc0LjU0NiIgaGVpZ2h0PSI3NC41NDYiIGZpbGw9IiNmZmYiIGRhdGEtbmFtZT0iUmVjdGFuZ2xlIDI2MjUiIHJ4PSI2LjU5NCIgdHJhbnNmb3JtPSJyb3RhdGUoLTExLjc3NCA3MjIuNjYgLTEzMzI0LjQ4MykiLz48L2c+PGcgZGF0YS1uYW1lPSJHcm91cCAzMjY4IiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjU5OC40OTIgMTQ4LjA5NykiPjxyZWN0IHdpZHRoPSI3NC41NDYiIGhlaWdodD0iNzQuNTQ2IiBmaWxsPSIjZmZmIiBkYXRhLW5hbWU9IlJlY3RhbmdsZSAyNjI2IiByeD0iNi41OTQiIHRyYW5zZm9ybT0icm90YXRlKC0xMS43NzQgMTEzMC42MiAtMTMzNjYuNTQ0KSIvPjwvZz48ZyBkYXRhLW5hbWU9Ikdyb3VwIDMyNjkiPjxwYXRoIGZpbGw9IiNmZmYiIGQ9Ik02MS41MjggMTc5Ljk4NWE1LjIzNSA1LjIzNSAwIDAgMS01LjExNy00LjE2NmwtMzIuNzYtMTU3LjNhNi42NjYgNi42NjYgMCAwIDAtNy44NzYtNS4xNjcgNi42MjUgNi42MjUgMCAwIDAtNC4yMTUgMi44NzQgNi42IDYuNiAwIDAgMC0uOTUgNS4wMTFMMTMuMTg3IDMzLjZhNS4yMzMgNS4yMzMgMCAwIDEtNC4wNTUgNi4xOSA1LjIzMSA1LjIzMSAwIDAgMS02LjE4OS00LjA1NUwuMzY2IDIzLjM3MmExNi45ODggMTYuOTg4IDAgMCAxIDIuNDQ2LTEyLjg4OEExNy4wMTIgMTcuMDEyIDAgMCAxIDEzLjY0IDMuMTA4YTE3LjE0NSAxNy4xNDUgMCAwIDEgMjAuMjU1IDEzLjI3NmwzMi43NiAxNTcuM2E1LjIzNiA1LjIzNiAwIDAgMS01LjEyNyA2LjNaIiBkYXRhLW5hbWU9IlBhdGggMjE0OSIvPjwvZz48ZyBkYXRhLW5hbWU9Ikdyb3VwIDMyNzAiPjxwYXRoIGZpbGw9IiNmZmYiIGQ9Ik0xMDcuMTUxIDIxMC4wMzNhNS4yMzMgNS4yMzMgMCAwIDEtMS4wNjMtMTAuMzU1bDE0NS41MzUtMzAuMzM0YTUuMjMyIDUuMjMyIDAgMCAxIDIuMTM1IDEwLjI0NGwtMTQ1LjUzNSAzMC4zMzRhNS4zMTkgNS4zMTkgMCAwIDEtMS4wNzIuMTExWiIgZGF0YS1uYW1lPSJQYXRoIDIxNTAiLz48L2c+PGcgZGF0YS1uYW1lPSJHcm91cCAzMjcxIj48cGF0aCBmaWxsPSIjZmZmIiBkPSJNNjkuMjc1IDIzNi4yNTdhMjMuNjY3IDIzLjY2NyAwIDEgMSAyMy4yMTMtMjguNSAyMy42NjMgMjMuNjYzIDAgMCAxLTE4LjMzNyAyNy45OTMgMjMuOTExIDIzLjkxMSAwIDAgMS00Ljg3Ni41MDdabS4wNzUtMzYuODczYTEzLjMgMTMuMyAwIDAgMC0yLjcyLjI4MiAxMy4yIDEzLjIgMCAxIDAgMTUuNjE0IDEwLjIzMSAxMy4yMSAxMy4yMSAwIDAgMC0xMi44OTQtMTAuNTA5WiIgZGF0YS1uYW1lPSJQYXRoIDIxNTEiLz48L2c+PC9nPjwvZz48L3N2Zz4=';
		add_menu_page(
			'WholesaleX',
			'WholesaleX',
			'manage_options',
			'wholesalex-overview',
			array( $this, 'output' ),
			$wholesalex_menu_icon,
			59
		);
		add_submenu_page(
			'wholesalex-overview',
			__( 'Dashboard', 'wholesalex' ),
			__( 'Dashboard', 'wholesalex' ),
			'manage_options',
			'wholesalex-overview',
			array( $this, 'output' ),
		);
	}

	/**
	 * Overview Actions
	 *
	 * @since 1.0.0
	 */
	public function overview_callback() {
		register_rest_route(
			'wholesalex/v1',
			'/overview_action/',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'overview_action_callback' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get B2B Coupon Data
	 *
	 * @param string $__start_date Start Date.
	 * @param string $__end_date End Date.
	 */
	public function get_b2b_coupon_data( $__start_date, $__end_date ) {
		global $wpdb;

		$_final_b2b_coupon_data = $wpdb->get_results(
			$wpdb->prepare(
				"select * from (SELECT DATE(max({$wpdb->posts}.post_date)) as post_date,SUM(table_three.coupon_amount) as amount FROM
				{$wpdb->posts} INNER JOIN
				(SELECT * from (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='__wholesalex_order_type' and meta_value='b2b') as table_one INNER JOIN
				(SELECT post_id as pid,meta_value as coupon_amount from {$wpdb->postmeta} WHERE meta_key='_cart_discount') as table_two
				on table_one.post_id=table_two.pid) as table_three
				on table_three.post_id={$wpdb->posts}.ID
				group by CAST({$wpdb->posts}.post_date as DATE)) as final
				where final.post_date between %s and %s;",
				array( $__start_date, $__end_date )
			)
		);
		return $_final_b2b_coupon_data;
	}

	/**
	 * Get B2B Gross Data
	 *
	 * @param string $__start_date Start Date.
	 * @param string $__end_date End Date.
	 */
	public function get_b2b_gross_data( $__start_date, $__end_date ) {
		global $wpdb;
		$__final_b2b_gross_data = $wpdb->get_results(
			$wpdb->prepare(
				"select * from (select DATE(max(total_gross_data.post_date)) as post_date, SUM(total_gross_data.gross_sale_price) as amount from (select table_one.post_id,table_one.post_date,(total-other_costs) as gross_sale_price from (select __order.post_id, __order.post_date, SUM(__order.meta_value) as total from (select * from {$wpdb->postmeta}
				inner join {$wpdb->posts} on {$wpdb->posts}.ID= {$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order') as __order
				where __order.meta_key = '_order_total' or __order.meta_key = '_cart_discount' GROUP BY __order.post_id) as table_one inner join
				(select _order.post_id, _order.post_date, SUM(_order.meta_value) as other_costs from (select * from {$wpdb->postmeta}
				inner join {$wpdb->posts} on {$wpdb->posts}.ID= {$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.ID NOT IN (select post_id from {$wpdb->postmeta} inner join {$wpdb->posts} on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' and {$wpdb->postmeta}.meta_key='_wholesalex_topup_success') ) as _order where
				_order.meta_key = '_order_shipping' or
				_order.meta_key = '_order_shipping_tax' OR
				_order.meta_key = '_order_tax'
				GROUP BY _order.post_id) as table_two
				on table_one.post_id=table_two.post_id) as total_gross_data
				inner join (select * from {$wpdb->postmeta} where meta_key='__wholesalex_order_type' and meta_value='b2b') as b2b_order
				on b2b_order.post_id=total_gross_data.post_id 
				group by CAST(total_gross_data.post_date as DATE)) as final
				where final.post_date between %s and %s;",
				array( $__start_date, $__end_date )
			)
		);

		return $__final_b2b_gross_data;
	}


	/**
	 * Get B2B Refund Data
	 *
	 * @param string $__start_date Start Date.
	 * @param string $__end_date End Date.
	 */
	public function get_b2b_refund_data( $__start_date, $__end_date ) {
		global $wpdb;
		$__final_b2b_refund_data = $wpdb->get_results(
			$wpdb->prepare(
				"select * from ( select DATE(max(all_refund.post_date)) as post_date, SUM(all_refund.meta_value) as amount from (select {$wpdb->posts}.post_parent,{$wpdb->posts}.post_date,{$wpdb->postmeta}.meta_value from {$wpdb->postmeta} inner join {$wpdb->posts}
				on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id
				WHERE {$wpdb->posts}.post_type='shop_order_refund' and {$wpdb->postmeta}.meta_key='_refund_amount' and {$wpdb->postmeta}.post_id not in (select post_id from {$wpdb->postmeta} inner join {$wpdb->posts} on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' and {$wpdb->postmeta}.meta_key='_wholesalex_topup_success')) as all_refund
				inner join (select * from {$wpdb->postmeta} where meta_key='__wholesalex_order_type' and meta_value='b2b') as b2b_data
				on all_refund.post_parent=b2b_data.post_id
				group by CAST(all_refund.post_date as DATE)) as final
				where final.post_date between %s and %s;",
				array( $__start_date, $__end_date )
			)
		);

		return $__final_b2b_refund_data;
	}

	/**
	 * Get B2B Tax Data
	 *
	 * @param string $__start_date Start Date.
	 * @param string $__end_date End Date.
	 */
	public function get_b2b_tax_data( $__start_date, $__end_date ) {
		global $wpdb;
		$__final_b2b_total_tax_data = $wpdb->get_results(
			$wpdb->prepare(
				"select * from (select DATE(max(total_tax_data.post_date)) as post_date,sum(total_tax_data.tax_data) as amount from (select _order.post_id, _order.post_date, SUM(_order.meta_value) as tax_data from (select * from {$wpdb->postmeta}
				inner join {$wpdb->posts} on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->postmeta}.post_id not in (select post_id from {$wpdb->postmeta} inner join {$wpdb->posts} on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' and {$wpdb->postmeta}.meta_key='_wholesalex_topup_success') ) as _order where
				_order.meta_key = '_order_shipping_tax' OR
				_order.meta_key = '_order_tax'
				GROUP BY _order.post_id) as total_tax_data 
	
				inner join (select * from {$wpdb->postmeta} where meta_key='__wholesalex_order_type' and meta_value='b2b') as b2b_order
				on b2b_order.post_id=total_tax_data.post_id
	
				group by CAST(total_tax_data.post_date as DATE)) as final
				where final.post_date between %s and %s;
				",
				array( $__start_date, $__end_date )
			)
		);

		return $__final_b2b_total_tax_data;
	}

	/**
	 * Get Topup Data
	 */
	public function get_topup_orders() {
		global $wpdb;
		$topup_orders = $wpdb->get_row(
			"select sum(meta_value) as total_topup,count(meta_value) as topup_count from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id IN (select {$wpdb->postmeta}.post_id from {$wpdb->postmeta} 
			where {$wpdb->postmeta}.post_id IN ( select ID from {$wpdb->posts} where {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed')
			and {$wpdb->postmeta}.meta_key='_wholesalex_topup_success') and {$wpdb->postmeta}.meta_key='_order_total';"
		);
		return $topup_orders;
	}

	/**
	 * Get Tax Data
	 */
	public function get_tax_data() {
		global $wpdb;
		$b2b_tax = $wpdb->get_row(
			"select sum(meta_value) as total_b2b_tax from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id IN (select {$wpdb->postmeta}.post_id from {$wpdb->postmeta} 
			where {$wpdb->postmeta}.post_id IN ( select ID from {$wpdb->posts} where {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' )) and {$wpdb->postmeta}.meta_key='_order_tax' and {$wpdb->postmeta}.post_id IN (select {$wpdb->postmeta}.post_id from {$wpdb->postmeta} where {$wpdb->postmeta}.meta_key = '__wholesalex_order_type' and {$wpdb->postmeta}.meta_value='b2b'); "
		);

		return $b2b_tax;
	}
	/**
	 * Get Shipping Tax Data
	 */
	public function get_shipping_tax() {
		global $wpdb;
		$b2b_shipping_tax = $wpdb->get_row(
			"select sum(meta_key) as total_b2b_shipping_tax from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id IN (select {$wpdb->postmeta}.post_id from {$wpdb->postmeta} 
			where {$wpdb->postmeta}.post_id IN ( select ID from {$wpdb->posts} where {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' )) and {$wpdb->postmeta}.meta_key='_order_shipping_tax' and {$wpdb->postmeta}.post_id IN (select {$wpdb->postmeta}.post_id from {$wpdb->postmeta} where {$wpdb->postmeta}.meta_key = '__wholesalex_order_type' and {$wpdb->postmeta}.meta_value='b2b');
		"
		);

		return $b2b_shipping_tax;
	}


	/**
	 * Get B2B Shipping Data
	 *
	 * @param string $__start_date Start Date.
	 * @param string $__end_date End Date.
	 */
	public function get_b2b_shipping_data( $__start_date, $__end_date ) {
		global $wpdb;

		$__final_b2b_shipping_data = $wpdb->get_results(
			$wpdb->prepare(
				"select * from (select DATE(max(total_shipping_data.post_date)) as post_date,sum(total_shipping_data.shipping_data) as amount from (select _order.post_id, _order.post_date, SUM(_order.meta_value) as shipping_data from (select * from {$wpdb->postmeta}
				inner join {$wpdb->posts}
				on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->postmeta}.post_id not in (select post_id from {$wpdb->postmeta} inner join {$wpdb->posts} on {$wpdb->posts}.ID={$wpdb->postmeta}.post_id WHERE {$wpdb->posts}.post_type='shop_order' and {$wpdb->posts}.post_status ='wc-completed' and {$wpdb->postmeta}.meta_key='_wholesalex_topup_success')  ) as _order
				where
				_order.meta_key = '_order_shipping'
				GROUP BY _order.post_id) as total_shipping_data 
	
				inner join (select * from {$wpdb->postmeta} where meta_key='__wholesalex_order_type' and meta_value='b2b') as b2b_order
				on b2b_order.post_id=total_shipping_data.post_id
	
				group by CAST(total_shipping_data.post_date as DATE)) as final
				where final.post_date between %s and %s;
				",
				array( $__start_date, $__end_date )
			)
		);

		return $__final_b2b_shipping_data;
	}



	/**
	 * Overview Action
	 *
	 * @param object $server Server.
	 * @since 1.0.0
	 */
	public function overview_action_callback( $server ) {
		$post = $server->get_params();
		if ( ! ( isset( $post['nonce'] ) && wp_verify_nonce( $post['nonce'], 'wholesalex-registration' ) ) ) {
			return;
		}

		include_once WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php';

		$wc_admin_report = new WC_Admin_Report();

		$type = isset( $post['type'] ) ? sanitize_text_field( $post['type'] ) : '';

		$__start_date = gmdate( 'Y-m-d', strtotime( '-7 Day', time() ) );
		$__end_date   = gmdate( 'Y-m-d', strtotime( '+0 Day', time() ) );
		$datetime1    = date_create( $__start_date );
		$datetime2    = date_create( $__end_date );
		$interval     = date_diff( $datetime1, $datetime2 );
		$interval     = $interval->days;
		global $wpdb;
		$prefix = $wpdb->prefix;

		if ( 'post' === $type ) {

			if ( isset( $post['start_date'] ) && ! empty( $post['start_date'] ) ) {
				$__start_date = gmdate( 'Y-m-d', strtotime( sanitize_text_field( $post['start_date'] ) . ' +1 day' ) );
			}
			if ( isset( $post['end_date'] ) && ! empty( $post['end_date'] ) ) {
				$__end_date = gmdate( 'Y-m-d', strtotime( sanitize_text_field( $post['end_date'] ) . ' +1 day' ) );
			}

			$datetime1                   = date_create( $__start_date );
			$datetime2                   = date_create( $__end_date );
			$interval                    = date_diff( $datetime1, $datetime2 );
			$interval                    = $interval->days;
			$wc_admin_report->start_date = strtotime( $__start_date );
			$wc_admin_report->end_date   = strtotime( $__end_date );

			$_final_b2b_coupon_data = $this->get_b2b_coupon_data( $__start_date, $__end_date );

			$__final_b2b_gross_data = $this->get_b2b_gross_data( $__start_date, $__end_date );

			$__final_b2b_refund_data = $this->get_b2b_refund_data( $__start_date, $__end_date );

			$data['final_b2b_refund_data'] = $__final_b2b_refund_data;

			$__final_b2b_total_tax_data = $this->get_b2b_tax_data( $__start_date, $__end_date );

			$__final_b2b_shipping_data = $this->get_b2b_shipping_data( $__start_date, $__end_date );

			$data['final_b2b_shipping_data'] = $__final_b2b_shipping_data;

			$data['final_b2b_tax_data'] = $__final_b2b_total_tax_data;

			$data['final_b2b_gross_data']     = $__final_b2b_gross_data;
			$data['final_b2b_b2c_gross_data'] = $__final_b2b_b2c_gross_data;
			$data['final_b2b_coupon_data']    = $_final_b2b_coupon_data;

			$data['gross_sale_chart_data']    = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_gross_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['coupon_sale_chart_data']   = array_values( $wc_admin_report->prepare_chart_data( $_final_b2b_coupon_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['refund_sale_chart_data']   = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_refund_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['shipping_sale_chart_data'] = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_shipping_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['tax_sale_chart_data']      = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_total_tax_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );

			$data['price_html'] = wc_price( 0.00 );

			wp_send_json_success( $data );

		} elseif ( 'get' === $type ) {

			$wc_admin_report->start_date = strtotime( $__start_date );
			$wc_admin_report->end_date   = strtotime( $__end_date );

			// For Get B2B User Count.
			$user_query         = new WP_User_Query(
				array(
					'meta_key'     => '__wholesalex_role',
					'meta_value'   => array( '', 'wholesalex_guest', 'wholesalex_b2c_users' ),
					'meta_compare' => '!=',
					'count_total'  => true,
				)
			);
			$data['user_count'] = $user_query->get_total();

			/**
			 * Remove Topup Orders Data From Total B2B orders and sales.
			 *
			 * @since 1.0.1
			 */
			// For Get Topups Data.
			$topup_orders = $this->get_topup_orders();

			$b2b_tax = $this->get_tax_data();

			$b2b_shipping_tax = $this->get_shipping_tax();

			// For Get B2B total Completed orders and total sales amount.
			$b2b_orders = $wpdb->get_row(
				"SELECT meta.meta_key, SUM(meta.meta_value) AS total_sales, COUNT(posts.ID) AS total_orders FROM (SELECT * FROM {$wpdb->posts} AS posts LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id WHERE meta.meta_key='__wholesalex_order_type' AND meta.meta_value='b2b') AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
				WHERE meta.meta_key = '_order_total'
				AND posts.post_type = 'shop_order'
				AND posts.post_status ='wc-completed'
			 "
			);

			$data['b2b_total_order'] = (int) $b2b_orders->total_orders - (int) $topup_orders->topup_count;
			$data['b2b_total_sales'] = (float) $b2b_orders->total_sales - (float) $topup_orders->total_topup;

			// Total Messages.
			$data['total_message'] = ( wholesalex()->is_pro_active() && post_type_exists( 'wsx_conversation' ) ) ? wp_count_posts( 'wsx_conversation' )->publish : 0;

			// Pending Messages Count.
			$messages_query = new WP_Query(
				array(
					'post_type'    => 'wsx_conversation',
					'post_status'  => 'publish',
					'meta_key'     => '__conversation_status',
					'meta_value'   => 'open',
					'meta_compare' => '=',
				)
			);

			$data['pending_messages_count'] = $messages_query->found_posts;

			// For Getting Top B2B Users Data. (Based on Sales).
			$active_user_query = new WP_User_Query(
				array(
					'fields'     => array( 'display_name', 'user_email', 'ID' ),
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => '__wholesalex_status',
							'value'   => 'active',
							'compare' => '=',
						),
						array(
							'key'     => '__wholesalex_role',
							'value'   => array( '', 'wholesalex_guest', 'wholesalex_b2c_users' ),
							'compare' => '!=',
						),

					),
				)
			);

			$active_users_data = (array) $active_user_query->get_results();

			foreach ( $active_users_data as $key => $value ) {
				$__temp                = (array) $value;
				$__id                  = $__temp['id'];
				$__temp['avatar_url']  = get_avatar_url( $__id );
				$__temp['total_spent'] = wc_get_customer_total_spent( $__id );
				if ( wholesalex()->is_pro_active() && function_exists( 'wholesalex_wallet' ) ) {
					$__temp['credit_balance'] = wholesalex_wallet()->get_wholesalex_balance( $__id );
				}
				$__temp['wholesalex_role'] = wholesalex()->get_role_name_by_role_id( get_user_meta( $__id, '__wholesalex_role', true ) );
				$active_users_data[ $key ] = $__temp;
			}

			$__sort_colum = array_column( $active_users_data, 'total_spent' );
			array_multisort( $__sort_colum, SORT_DESC, $active_users_data );

			$data['top_customers'] = $active_users_data;

			// For Pending B2B Users Data.
			$pending_user_query         = new WP_User_Query(
				array(
					'meta_key'     => '__wholesalex_status',
					'meta_value'   => 'pending',
					'meta_compare' => '=',
					'fields'       => array( 'display_name', 'user_email', 'ID', 'user_registered' ),
					'count_total'  => true,
				)
			);
			$data['pending_user_count'] = $pending_user_query->get_total();

			$pending_user_data = (array) $pending_user_query->get_results();

			foreach ( $pending_user_data as $key => $value ) {
				$__temp                    = (array) $value;
				$__id                      = $__temp['id'];
				$__temp['avatar_url']      = get_avatar_url( $__id );
				$__temp['wholesalex_role'] = wholesalex()->get_role_name_by_role_id( get_user_meta( $__id, '__wholesalex_registration_role', true ) );
				$__temp['edit_user_link']  = get_edit_user_link( $__id );
				$pending_user_data[ $key ] = $__temp;
			}
			$data['pending_users'] = $pending_user_data;

			// WholesaleX Users Page Link.
			$data['admin_link'] = admin_url( 'admin.php?page=wholesalex-users' );

			// WholesaleX Conversation Page Link.
			$data['conversations_link'] = admin_url( 'edit.php?post_type=wsx_conversation' );

			// For B2B Recents Orders.
			$b2b_recent_orders = $wpdb->get_results(
				"SELECT posts.ID,meta.meta_value as amount,posts.post_author as customer_id, posts.post_date as order_date, posts.post_status as order_status, posts.guid as order_link FROM (SELECT * FROM {$wpdb->posts} AS posts LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id WHERE meta.meta_key='__wholesalex_order_type' AND meta.meta_value='b2b') AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
				WHERE meta.meta_key = '_order_total'
				AND posts.post_type = 'shop_order'
				AND posts.post_status ='wc-completed'
				ORDER BY posts.post_date DESC
				LIMIT 10
			 "
			);

			foreach ( $b2b_recent_orders as $key => $value ) {
				$__temp                    = (array) $value;
				$__id                      = $__temp['ID'];
				$__user_data               = get_userdata( $__temp['customer_id'] );
				$__temp['customer_name']   = $__user_data->display_name;
				$__temp['order_status']    = wc_get_order_status_name( $__temp['order_status'] );
				$__temp['order_link']      = admin_url( 'post.php?post=' . $__id . '&action=edit' );
				$b2b_recent_orders[ $key ] = $__temp;
			}
			$data['b2b_recent_orders'] = $b2b_recent_orders;

			// WholesaleX B2B Orders Link.
			$data['all_b2b_order_link'] = admin_url( 'edit.php?post_type=shop_order&wholesalex_order_type=b2b' );

			$data['net_earning']    = (float) $data['b2b_total_sales'] - (float) ( $b2b_tax->total_b2b_tax + $b2b_shipping_tax->total_b2b_shipping_tax );
			$_final_b2b_coupon_data = $this->get_b2b_coupon_data( $__start_date, $__end_date );

			$__final_b2b_gross_data = $this->get_b2b_gross_data( $__start_date, $__end_date );

			$__final_b2b_refund_data = $this->get_b2b_refund_data( $__start_date, $__end_date );

			$data['final_b2b_refund_data'] = $__final_b2b_refund_data;

			$__final_b2b_total_tax_data = $this->get_b2b_tax_data( $__start_date, $__end_date );

			$__final_b2b_shipping_data = $this->get_b2b_shipping_data( $__start_date, $__end_date );

			$data['final_b2b_shipping_data'] = $__final_b2b_shipping_data;

			$data['final_b2b_tax_data'] = $__final_b2b_total_tax_data;

			$data['final_b2b_gross_data']  = $__final_b2b_gross_data;
			$data['final_b2b_coupon_data'] = $_final_b2b_coupon_data;

			$data['gross_sale_chart_data']    = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_gross_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['coupon_sale_chart_data']   = array_values( $wc_admin_report->prepare_chart_data( $_final_b2b_coupon_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['refund_sale_chart_data']   = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_refund_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['shipping_sale_chart_data'] = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_shipping_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );
			$data['tax_sale_chart_data']      = array_values( $wc_admin_report->prepare_chart_data( $__final_b2b_total_tax_data, 'post_date', 'amount', $interval, strtotime( '+1 DAY', $wc_admin_report->start_date ), 'day' ) );

			$data['default_start_date'] = $__start_date;
			$data['default_end_date']   = $__end_date;
			$data['price_html']         = wc_price( 0.00 );
			wp_send_json_success( $data );
		}
	}


	/**
	 * Output Function
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function output() {
		wp_register_script( 'wc-reports', WC()->plugin_url() . '/assets/js/admin/reports.js', array( 'jquery', 'jquery-ui-datepicker' ), WHOLESALEX_VER, true );
		wp_enqueue_script( 'wc-reports' );
		wp_enqueue_script( 'flot' );
		wp_enqueue_script( 'flot-resize' );
		wp_enqueue_script( 'flot-time' );
		wp_enqueue_script( 'flot-pie' );
		wp_enqueue_script( 'flot-stack' );

		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_overview' );
		wp_set_script_translations( 'wholesalex_overview', 'wholesalex', WHOLESALEX_PATH . 'languages/' );

		?>
		<div id="wholesalex-overview"></div>
		<?php
	}

	/**
	 * Add Go Pro Menu Page
	 *
	 * @return void
	 * @since 1.1.2
	 */
	public function go_pro_menu_page() {
		if ( ! wholesalex()->is_pro_active() ) {
			add_submenu_page(
				'wholesalex-overview',
				'',
				'<span class="wholesalex-dashboard-upgrade"><span class="dashicons dashicons-update"></span> Go Pro</span>',
				'manage_options',
				'go_wholesalex_pro',
				array( $this, 'go_pro_redirect' )
			);
		}
	}

	/**
	 * Go Pro Redirect From Dashboard
	 *
	 * @since 1.1.2
	 */
	public function go_pro_redirect() {
		if ( isset( $_GET['page'] ) && 'go_wholesalex_pro' === $_GET['page'] ) {
			wp_redirect( 'https://getwholesalex.com/pricing/?utm_source=wholesalex-plugins&utm_medium=go_pro&utm_campaign=wholesalex-DB' );
			die();
		} else {
			return;
		}
	}

}

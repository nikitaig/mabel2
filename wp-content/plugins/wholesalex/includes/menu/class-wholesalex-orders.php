<?php
/**
 * Orders Action.
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * WholesaleX Category Class.
 */
class WHOLESALEX_Orders {

	/**
	 * Order Constructor
	 */
	public function __construct() {
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_type_column_on_order_page' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'populate_data_on_order_type_column' ), 10, 2 );
		add_filter( 'pre_get_posts', array( $this, 'filter_wholesalex_order' ) );
		add_action( 'restrict_manage_posts', array( $this, 'wholesalex_order_type_filter' ) );

	}

	/**
	 * Add Order Type Column On Order Page.
	 *
	 * @param array $columns Order Columns.
	 * @return array
	 */
	public function add_order_type_column_on_order_page( $columns ) {
		$columns = array_slice( $columns, 0, 4, true )
		+ array( 'wholesalex_order_type' => __( 'Order Type', 'wholesalex' ) )
		+ array_slice( $columns, 4, null, true );
		return $columns;
	}

	/**
	 * Populate Data on Order Type Column on Orders page
	 *
	 * @param string $column Order Page Column.
	 * @param int    $order_id Order ID.
	 */
	public function populate_data_on_order_type_column( $column, $order_id ) {

		if ( 'wholesalex_order_type' === $column ) {
			$order      = wc_get_order( $order_id );
			$order_type = $order->get_meta( '__wholesalex_order_type' );
			if ( 'b2b' === $order_type ) {
				$__custom_meta_value = apply_filters( 'wholesalex_order_meta_b2b_value', __( 'WholesaleX B2B', 'wholesalex' ) );
				echo esc_html( $__custom_meta_value );
			} elseif ( 'b2c' === $order_type ) {
				$__custom_meta_value = apply_filters( 'wholesalex_order_meta_b2c_value', __( 'WholesaleX B2C', 'wholesalex' ) );
				echo esc_html( $__custom_meta_value );
			}
		}

	}

	/**
	 * Filter WholesaleX Order
	 *
	 * @param WP_Query $query WP Query.
	 * @return void
	 */
	public function filter_wholesalex_order( $query ) {
		global $pagenow;
		if ( $query->is_admin && $pagenow === 'edit.php' && isset( $_GET['wholesalex_order_type'] )
		&& $_GET['wholesalex_order_type'] != '' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' ) {
			$meta_query = array(
				array(
					'key'     => '__wholesalex_order_type',
					'value'   => esc_attr( $_GET['wholesalex_order_type'] ),
					'compare' => '=',
				),
			);
			$query->set( 'meta_query', $meta_query );
			$query->set( 'posts_per_page', 10 );
			$query->set( 'paged', ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ) );
		}
	}

	/**
	 * WholesaleX Order Type Filter
	 *
	 * @return void
	 */
	public function wholesalex_order_type_filter() {
		global $pagenow, $post_type;

		$__order_types = array(
			'b2b' => __( 'WholesaleX B2B', 'wholesalex' ),
			'b2c' => __( 'WholesaleX B2C', 'wholesalex' ),
		);
		$__current     = isset( $_GET['wholesalex_order_type'] ) ? sanitize_text_field( $_GET['wholesalex_order_type'] ) : '';
		if ( 'shop_order' === $post_type && 'edit.php' === $pagenow ) {
			?>
			<select name="wholesalex_order_type">
				<option value=""><?php esc_html_e( 'Filter By WholesaleX Order Type', 'wholesalex' ); ?></option>
				<option value="b2b" <?php selected( $__current, 'b2b' ); ?>><?php esc_html_e( 'WholesaleX B2B', 'wholesalex' ); ?></option>
				<option value="b2c" <?php selected( $__current, 'b2c' ); ?>><?php esc_html_e( 'WholesaleX B2C', 'wholesalex' ); ?></option>
			</select>

			<?php
		}
	}
}

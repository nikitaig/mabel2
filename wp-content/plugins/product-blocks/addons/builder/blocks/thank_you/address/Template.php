<?php
/**
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
$order_id = absint( get_query_var('order-received') );
$order = wc_get_order( $order_id );

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();

if ( ! $order ) {
	return;
}
?>
<div class="wopb-thankyou-address-container">
	<section class="woocommerce-customer-details">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">
			<div class="wopb-billing-shipping-address">
				<h2 class="woocommerce-column__title wopb-address-title "><?php esc_html_e( $attr['billingText'], 'product-blocks' ); ?></h2>
				<address>
					<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'product-blocks' ) ) ); ?>

					<?php if ( $order->get_billing_phone() ) : ?>
						<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
					<?php endif; ?>

					<?php if ( $order->get_billing_email() ) : ?>
						<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
					<?php endif; ?>
				</address>
			</div>
			
		</div>

		<?php if ( $show_shipping ) { ?>
		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<div class="wopb-billing-shipping-address">
				<h2 class="woocommerce-column__title wopb-address-title "><?php esc_html_e( $attr['shippingText'], 'product-blocks' ); ?></h2>
				<address>
					<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'product-blocks' ) ) ); ?>

					<?php if ( $order->get_shipping_phone() ) : ?>
						<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
					<?php endif; ?>
				</address>
			</div>
			
		</div>
		<?php } ?>
	</section>
	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
</div>	

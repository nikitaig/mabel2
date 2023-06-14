<?php

defined( 'ABSPATH' ) || exit;
$order_id = absint( get_query_var('order-received') );
$order = wc_get_order( $order_id );

if ( $order && !($order->has_status( 'failed' ))) {
?>
	<div class="wopb-thankyou-order-payment-container">
		<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

			<li class="woocommerce-order-overview__order order">
				<?php esc_html_e( $attr['orderText'], 'product-blocks' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<div class="wopb-separator"></div>

			<li class="woocommerce-order-overview__date date">
				<?php esc_html_e( $attr['dateText'], 'product-blocks' ); ?>
				<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
			</li>
			<div class="wopb-separator"></div>

			<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
				<li class="woocommerce-order-overview__email email">
					<?php esc_html_e( $attr['emailText'], 'product-blocks' ); ?>
					<strong><?php echo $order->get_billing_email(); ?></strong>
				</li>
				<div class="wopb-separator"></div>
			<?php endif; ?>

			<li class="woocommerce-order-overview__total total">
				<?php esc_html_e( $attr['totalText'], 'product-blocks' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<div class="wopb-separator"></div>
			<?php if ( $order->get_payment_method_title() ) : ?>
				<li class="woocommerce-order-overview__payment-method method">
					<?php esc_html_e( $attr['payMethodText'], 'product-blocks' ); ?>
					<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
				</li>
			<?php endif; ?>
		</ul>
	</div>
<?php
}
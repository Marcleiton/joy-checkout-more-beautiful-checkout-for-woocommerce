<?php
/**
 * Order Review Table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package JoyCommerce
 * @version 1.3.4
 */

$checkout_options = get_option( 'custom_checkout_settings' );?>

<table class="onestepcheckout-summary">
	<thead>
		<tr>
			<th class="thumb"><?php esc_html_e( 'Product', 'cmb2' ); ?></th>
			<th class="qty"><?php esc_html_e( 'Amount', 'cmb2' ); ?></th>

			<th class="total"><?php esc_html_e( 'Total', 'cmb2' ); ?></th>
			<th class="removepro"><?php esc_html_e( 'Remove', 'cmb2' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		}
		?>
	<tr>
	<td class="thumb">
		<?php
		$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 50, 80 ) ), $cart_item, $cart_item_key );

		if ( ! $product_permalink ) {
			echo $thumbnail; // PHPCS: XSS ok.
		} else {
			printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
		}
		?>
	</td>
	<td class="joyc_qty" nowrap="">
		<div class="wrapper_qty">
		<?php
		if ( isset( $checkout_options['joyc_skip_qty'] ) && 'yes' === $checkout_options['joyc_skip_qty'] ) {
			echo esc_html( $cart_item['quantity'] );
		} else {
			?>
			<button type="button" class="joycminus" >-</button>
			<input type="number" id="qty1" class="input-text qty text" step="1" min="1" max="<?php echo esc_attr( $_product->get_stock_quantity() ); ?>" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" title="Qty" size="4" inputmode="numeric">
			<button type="button" class="joycplus" >+</button>
			<?php
		}
		?>
		</div>
		</td>
		<td class="total">
			<span class="price"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
		</td>
		<td class="removepro">
			<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="joyc_remove" title="Excluir esse produto">x</a>
		</td>	
	</tr>
	<tr>
	<td colspan="4" class="name more_details" style="text-align:left !important;">
		<?php
		echo esc_html( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;';
		?>
		<div class="more_details_slide"><?php echo esc_html( wc_get_formatted_cart_item_data( $cart_item ) ); ?></div>
	</td>	
	</tr>
		<?php
	}
		do_action( 'woocommerce_review_order_after_cart_contents' );
	?>

	<tbody>
</table>	

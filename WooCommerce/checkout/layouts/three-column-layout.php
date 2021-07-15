<?php
/**
 * Three column layout
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

$checkout_options = get_option( 'custom_checkout_settings' );
$step_3_number    = '3';
$step_4_number    = '4';
?>
	<div class="grid-col-1 grid-col-checkout">

		<div id="customer_address_details">
		<?php if ( count( $checkout->checkout_fields ) > 0 ) : ?>
			<?php do_action( 'woocommerce_checkout_billing' ); ?>
		<?php endif; ?>
		</div>
	</div>

	<div class="grid-col-2 grid-col-checkout">
		<h3 data-step-index="3" class="step-heading"><div class="step-number-3"><?php echo esc_html( $step_3_number ); ?></div><?php echo esc_html( $checkout_options['joyc_step_3_title'] ); ?><div class="step-3-desc"><?php echo esc_html( $checkout_options['joyc_step_3_description'] ); ?></div></h3>
		<!--<div class="msg-payment"><i class="fa fa-comments"></i><p>Preencha os dados pessoais e de entrega para visualizar o método de pagamento.</p></div>-->
		<?php woocommerce_checkout_payment(); ?>
	</div>
	<div class="grid-col-3 grid-col-checkout">
		<h3 data-step-index="4" class="step-heading"><div class="step-number-4"><?php echo esc_html( $step_4_number ); ?></div><?php echo esc_html( $checkout_options['joyc_step_4_title'] ); ?><div class="step-4-desc"><?php echo esc_html( $checkout_options['joyc_step_4_description'] ); ?></div></h3>
		<div id="order_review_table">
		<?php require_once JOYC_PLUGIN_DIR . 'WooCommerce/joyc_checkout/orderreview-table.php'; ?> 
		</div>
		<?php require_once JOYC_PLUGIN_DIR . 'WooCommerce/joyc_checkout/additional-fields.php'; ?> 
		<?php remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 ); ?>	
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

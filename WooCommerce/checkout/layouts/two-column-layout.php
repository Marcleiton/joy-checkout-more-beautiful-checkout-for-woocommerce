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

?>
<div class="two-column-layout-left">
	<div id="customer_address_details">
	<?php do_action( 'woocommerce_checkout_billing' ); ?>
	<?php do_action( 'woocommerce_checkout_shipping' ); ?>		
	</div>
	<?php require_once JOYC_PLUGIN_DIR . 'WooCommerce/joyc_checkout/additional-fields.php'; ?>  
</div>
<div class="two-column-layout-right">
   
	<h3 class="border_html"><?php esc_html_e( 'Confirm & Pay', 'cmb2' ); ?></h3>
	<div id="order_review_table">
	<?php require_once JOYC_PLUGIN_DIR . 'WooCommerce/joyc_checkout/orderreview-table.php'; ?> 
	</div>
	<?php do_action( 'woocommerce_checkout_order_review' ); ?>	

</div>
<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

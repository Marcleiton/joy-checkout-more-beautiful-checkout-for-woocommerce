<?php
/**
 * Header
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
<div class="header_checkout">
	<div class="header_checkout__container">
		<a href="<?php echo esc_url( home_url() ); ?>">
			<img class="header-logo" src="<?php echo ! empty( cmb2_get_option( 'custom_checkout_settings', 'joyc_logo_header' ) ) ? esc_url( cmb2_get_option( 'custom_checkout_settings', 'joyc_logo_header' ) ) : esc_url( JOYC_PLUGIN_URL . 'asserts/images/joy-checkout-logo.png' ); ?>" />
		</a>
		<img class="header-seguranca" src="<?php echo ! empty( cmb2_get_option( 'custom_checkout_settings', 'joyc_logo_compra_protegida' ) ) ? esc_url( cmb2_get_option( 'custom_checkout_settings', 'joyc_logo_compra_protegida' ) ) : esc_url( JOYC_PLUGIN_URL . 'asserts/images/joy-checkout-secure.png' ); ?>" />
	</div>
</div>
<div class="clearfix"></div>

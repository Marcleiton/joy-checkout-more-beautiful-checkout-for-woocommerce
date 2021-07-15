<?php
/**
 * Checkout Form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
do_action( 'woocommerce_before_checkout_form', $checkout );
?>
<?php

if ( defined( 'WC_CHECKOUT_FLOW_AUTH' ) ) {
	return;
}

$layout = cmb2_get_option( 'custom_checkout_settings', 'joyc_checkout_layouts' );
if ( '' === $layout || ! isset( $layout ) || empty( $layout ) ) {
	$layout = 'three-column-layout';
}

?>

<?php $get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>

<?php wc_get_template( 'checkout/components/header.php' ); ?>

<div id="joy-checkout">
	<p class="txt-checkout"><?php echo esc_html( cmb2_get_option( 'custom_checkout_settings', 'joyc_texto_header' ) ); ?></p>
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">
	<div class="express-one-page-checkout-main checkout-<?php echo esc_attr( $layout ); ?>">

	<?php
		require_once dirname( __FILE__ ) . '/layouts/' . $layout . '.php';
	?>

	</div>
	</form>
	<div class="clearfix"></div>
	<footer style="display: block !important;background: #f7f7f7;">
		<div id="footer-checkout">
		<div class="footer-info"><?php echo esc_html( cmb2_get_option( 'custom_checkout_settings', 'joyc_texto_info_rodape' ) ); ?></div>
		<div class="footer-copy"><?php echo esc_html( cmb2_get_option( 'custom_checkout_settings', 'joyc_texto_rodape' ) ); ?></div>
		<div class="footer-joy"><?php esc_html_e( 'We use', 'cmb2' ); ?> <a href="https://joycheckout.com/" target="_blank"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'xnw16aZ.png' ); ?>" /></a></div>
		</div>
	</footer>
</div>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

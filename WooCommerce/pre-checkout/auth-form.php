<?php
/**
 * Authentication form
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Joy_Checkout\Helpers as h;

$input_class         = get_option( 'wc_checkout_flow_input_class' );
$submit_button_label = esc_html__( 'Continue', 'cmb2' );
$lost_password_link  = add_query_arg( 'lost-password', '', get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
$checkout_setting    = get_option( 'custom_checkout_settings' );

?>
<?php wc_get_template( 'checkout/components/header.php' ); ?>

<div class="joy-checkout" style="max-width: 640px; margin: auto;">
	<noscript>
		<ul class="woocommerce-error" role="alert">
			<li>
				<?php echo wc_kses_notice( __( 'The form bellow will not work because it depends on Javascript.', 'cmb2' ) ); ?>
			</li>
		</ul>
	</noscript>

	<div class="joy-checkout-container">
		<h2><?php esc_html( $checkout_setting['joyc_auth_form'][0]['joyc_auth_title'] ); ?></h2>
		<p class="joy-checkout__description"><?php esc_html( $checkout_setting['joyc_auth_form'][0]['joyc_auth_description'] ) ; ?></p>
		<section class="joy-checkout-messages"></section>

		<?php do_action( 'before_wc_checkout_flow_auth_form' ); ?>

		<form action="post" class="woocommerce-form joy-checkout-auth-form  joy-checkout__form">
			<p class="form-row form-row-wide joy-checkout-form-email">
				<label for="email"><?php echo esc_html__( 'E-mail', 'cmb2' ); ?></label>
				<input type="email"
					class="woocommerce-Input woocommerce-Input--text input-text<?php echo $input_class ? ' ' . sanitize_html_class( $input_class ) : ''; ?>"
					name="email"
					id="email"
					autocomplete="off" value="" required>
			</p>

			<p class="form-row joy-checkout-form-submit">
				<input type="hidden" id="wc_checkout_flow_action" value="check_email">
				<button type="submit" class="woocommerce-Button button"><?php echo esc_html( $submit_button_label ); ?></button>
			</p>
			<div class="error-message"></div>
		</form>

		<?php do_action( 'after_wc_checkout_flow_auth_form' ); ?>

		<template id="template_wc_checkout_flow_password_input">
			<p class="form-row form-row-wide joy-checkout-form-email">
				<label for="password"><?php echo esc_html__( 'Senha', 'cmb2' ); ?></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password"
					id="password" autocomplete="off" value="" required>
			</p>
			<p class="form-row form-row-wide woocommerce-LostPassword lost_password">
				<a class="lost-password-link" href="<?php echo esc_html( $lost_password_link ); ?>"><?php echo esc_html__( 'Redefine password', 'cmb2' ); ?></a>
			</p>
		</template>

		<template id="template_wc_checkout_flow_message_lost_password">
			<?php
			$message = get_option( 'wc_checkout_flow_lost_password' );
			$message = apply_filters( h\prefix( 'message_lost_password' ), $message );
			?>
			<span class="lost-email-message"><?php echo esc_html( wc_kses_notice( $message ) ); ?></span>
		</template>
	</div>
</div>

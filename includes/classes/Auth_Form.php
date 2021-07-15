<?php

namespace Joy_Checkout;

use Joy_Checkout\Common\Hooker_Trait;
use Joy_Checkout\Helpers as h;

class Auth_Form {

	use Hooker_Trait;

	public function add_hooks() {
		$this->add_action( 'wp', 'validate_page', 0 );
		$this->add_action( 'woocommerce_before_checkout_form', 'show_auth_form', 0 );
		$this->add_filter( 'body_class', 'add_body_class' );
	}

	public function validate_page() {
		if ( ! \is_checkout() ) {
			return;
		}
		if ( \is_user_logged_in() ) {
			return;
		}
		if ( isset( $_GET[ h\prefix( 'hash' ) ] ) ) {
			return;
		}
		if ( isset( $_COOKIE['email_to_register'] ) ) {
			return;
		}

		define( 'WC_CHECKOUT_FLOW_AUTH', true );
	}

	public function show_auth_form() {
		if ( ! defined( 'WC_CHECKOUT_FLOW_AUTH' ) || isset( $_COOKIE['email_to_register'] ) ) {
			return;
		}

		\remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		\remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		\remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		\remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		\remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
		\remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );

		$this->print_css();
		$this->print_form();
	}

	public function add_body_class( $classes ) {
		if ( defined( 'WC_CHECKOUT_FLOW_AUTH' ) ) {
			$classes[] = h\config_get( 'SLUG' );
		}

		return $classes;
	}

	public function print_css() {       ?>
		<style data-type="joy-checkout">
			.woocommerce-checkout form.checkout {
				display: none !important;
			}
		</style>
		<?php
	}

	public function print_form() {
		wc_get_template(
			'pre-checkout/auth-form.php',
			array(
				// data
			),
			'',
			h\config_get( 'WC_TEMPLATE_PATH' )
		);
	}
}

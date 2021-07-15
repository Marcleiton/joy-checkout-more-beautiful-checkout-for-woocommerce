<?php

namespace Joy_Checkout;

use Joy_Checkout\Common\Hooker_Trait;
use Joy_Checkout\Helpers as h;

class Password_Validator
{
	use Hooker_Trait;

	public function add_hooks()
	{
		$this->add_action('before_wc_checkout_flow_reset_password_form', 'password_instructions');
		$this->add_filter(h\prefix('validate_password'), 'validate_password');
	}

	public function password_instructions()
	{
		$min_length   = apply_filters(h\prefix('password_min_length'), 8);
		$instructions = sprintf(esc_html__('Sua nova senha deve ter no mínimo %1$s caracteres.', 'cmb2'), $min_length);
		$instructions = esc_html(apply_filters(h\prefix('password_min_length'), $instructions));
		echo "<p>$instructions</b>";
	}

	public function validate_password($password)
	{
		$min_length = apply_filters(h\prefix('password_min_length'), 8);
		if (\strlen($password) < $min_length) {
			return false;
		}

		return $password;
	}
}

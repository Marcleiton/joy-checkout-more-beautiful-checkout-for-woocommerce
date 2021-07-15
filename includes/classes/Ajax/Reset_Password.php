<?php

namespace Joy_Checkout\Ajax;

use Joy_Checkout\Common\Abstract_Ajax_Action;
use Joy_Checkout\Helpers as h;
use Joy_Checkout\Utils\User;

class Reset_Password extends Abstract_Ajax_Action {
	public function get_action_name() {
		return h\prefix( 'ajax_reset_password' );
	}

	public function callback() {
		$hash      = h\request_value( 'hash', 'post' );
		$email     = User::validate_email_hash( $hash );
		$password  = h\request_value( 'password', 'post' );
		$password2 = h\request_value( 'password2', 'post' );
		$res       = array(
			'message' => '',
		);
		$code      = 400;

		if ( ! User::email_exists( $email ) ) {
			$res['message'] = esc_html__( 'E-mail inválido.', 'cmb2' );
		} elseif ( $password !== $password2 ) {
			$res['message'] = esc_html__( 'Os campos de senha devem ter valores iguais.', 'cmb2' );
		} elseif ( ! $this->is_valid_password( $password ) ) {
			$res['message'] = esc_html__( 'Senha inválida.', 'cmb2' );
		} else {
			$code = 200;
			User::change_password( $email, $password );
			User::delete_email_hash( $hash );
			User::login( $email, $password );
			$res['message'] = get_option( 'wc_checkout_flow_reset_password_success' );
		}

		$this->send_json_response( $res, $code );
	}

	protected function is_valid_password( $password ) {
		$password = \apply_filters( h\prefix( 'validate_password' ), $password );

		return ! empty( $password );
	}

	public function is_public() {
		return true;
	}
}

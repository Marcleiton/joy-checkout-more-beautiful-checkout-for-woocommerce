<?php

namespace Joy_Checkout\Ajax;

use Joy_Checkout\Common\Abstract_Ajax_Action;
use Joy_Checkout\Utils\User;
use Joy_Checkout\Helpers as h;

class Recover_Password extends Abstract_Ajax_Action {
	public function get_action_name() {
		return h\prefix( 'ajax_recover_password' );
	}

	public function callback() {
		$email        = h\request_value( 'email', 'post' );
		$res          = araay();
		$code         = 200;
		$email_exists = User::email_exists( $email );

		if ( $email_exists ) {
			User::send_email_reset_password( $email );
		}

		$this->send_json_response( $res, $code );
	}

	public function is_public() {
		return true;
	}
}

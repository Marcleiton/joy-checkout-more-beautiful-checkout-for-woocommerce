<?php

namespace Joy_Checkout\Ajax;

use Joy_Checkout\Common\Abstract_Ajax_Action;
use Joy_Checkout\Helpers as h;
use Joy_Checkout\Utils\User;

class Check_Email extends Abstract_Ajax_Action {

	public function get_action_name() {
		return h\prefix( 'ajax_check_email' );
	}

	public function callback() {
		$email        = h\request_value( 'email', 'post' );
		$res          = array(
			'user_created' => false,
			'message'      => '',
		);
		$code         = 200;
		$email_exists = \email_exists( $email );
		$messages     = $this->get_response_messages();

		if ( $email_exists ) {
			$res['message'] = $messages['found'];
		} else {
			$res['email_to_register']    = $email;
			$res['user_will_be_created'] = true;
		}

		$this->send_json_response( $res, $code );
	}

	protected function get_response_messages() {
		$auth_form_settings = get_option( 'custom_checkout_settings' );
		$auth_form_settings = $auth_form_settings['joyc_auth_form'][0];

		$messages = array(
			'not_found' => $auth_form_settings['joyc_email_not_found'],
			'found'     => $auth_form_settings['joyc_email_found'],
		);

		foreach ( $messages as $key => $value ) {
			$messages[ $key ] = \apply_filters( h\prefix( 'message_' ) . $key . '_email', $value );
		}

		return $messages;
	}

	public function is_public() {
		return true;
	}
}

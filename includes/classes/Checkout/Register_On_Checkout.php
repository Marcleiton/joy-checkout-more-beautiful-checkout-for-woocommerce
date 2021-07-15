<?php

namespace Joy_Checkout\Checkout;

use Joy_Checkout\Ajax\Check_Email;
use Joy_Checkout\Ajax\Login;
use Joy_Checkout\Ajax\Recover_Password;
use Joy_Checkout\Ajax\Reset_Password;
use Joy_Checkout\Auth_Form;
use Joy_Checkout\Common\Hooker_Trait;
use Joy_Checkout\Password_Validator;
use Joy_Checkout\Reset_Password_Form;

use function Joy_Checkout\Helpers\assets;
use function Joy_Checkout\Helpers\config_get;
use function Joy_Checkout\Helpers\config_set;
use function Joy_Checkout\Helpers\get_asset_url;
use function Joy_Checkout\Helpers\prefix;

class Register_On_Checkout {
	use Hooker_Trait;

	public function __construct() {
		$this->boot();
	}

	public function add_hooks() {
		$this->add_filter( 'woocommerce_billing_fields', 'add_register_fields_on_checkout', \PHP_INT_MAX );
		$this->add_action( 'user_register', 'remove_email_to_register_cookie' );
	}

	public function add_register_fields_on_checkout( $fields ) {

		if ( ! $this->register_on_checkout() ) {
			return $fields;
		}

		$fields['billing_email']['default'] = isset( $_COOKIE['email_to_register'] );

		$fields['account_password'] = array(
			'label'    => \__( 'Password', 'cmb2' ),
			'required' => true,
			'type'     => 'password',
			'class'    => array( 'form-row-first' ),
			'validate' => array( 'password' ),
			'priority' => 10,
		);

		$fields['confirm_account_password'] = array(
			'label'    => \__( 'Confirm', 'cmb2' ),
			'required' => true,
			'type'     => 'password',
			'class'    => array( 'form-row-last' ),
			'validate' => array( 'password' ),
			'priority' => 10,
		);

		return $fields;
	}

	public function remove_email_to_register_cookie() {
		setcookie( 'email_to_register', null, -1 );
	}

	private function register_on_checkout() {
		return isset( $_COOKIE['email_to_register'] );
	}

	private function boot() {
		$_FILE = config_get( 'MAIN_FILE' );
		config_set( 'WC_TEMPLATE_PATH', config_get( 'ROOT_DIR' ) . '/WooCommerce/' );

		$this->load_js();

		$this->boot_auth_form();
		$this->boot_password_form();
		$this->boot_password_validator();
		$this->boot_ajax_check_email();
		$this->boot_ajax_login();
		$this->boot_ajax_recover_password();
		$this->boot_ajax_reset_password();
	}

	private function boot_auth_form() {
		$auth_form = config_set( '$auth_form', new Auth_Form() );
		$auth_form->add_hooks();
	}

	private function boot_password_form() {
		$reset_pass_form = config_set( '$reset_pass_form', new Reset_Password_Form() );
		$reset_pass_form->add_hooks();
	}

	private function boot_password_validator() {
		$password_validator = config_set( '$password_validator', new Password_Validator() );
		$password_validator->add_hooks();
	}

	private function boot_ajax_check_email() {
		$ajax_check_email = config_set( '$ajax_check_email', new Check_Email() );
		$ajax_check_email->add_hooks();
	}

	private function boot_ajax_login() {
		$ajax_login = config_set( '$ajax_login', new Login() );
		$ajax_login->add_hooks();
	}

	private function boot_ajax_recover_password() {
		$ajax_recover_password = config_set( '$ajax_recover_password', new Recover_Password() );
		$ajax_recover_password->add_hooks();
	}

	private function boot_ajax_reset_password() {
		$ajax_reset_password = config_set( '$ajax_reset_password', new Reset_Password() );
		$ajax_reset_password->add_hooks();
	}

	private function load_js() {
		$assets = assets();
		$assets->add(
			get_asset_url( 'disabled_guest_checkout.js' ),
			array(
				'in_admin'    => false,
				'script_data' => array(
					'ajax_url'              => \admin_url( 'admin-ajax.php' ),
					'prefix'                => prefix( '' ),
					'debug'                 => WP_DEBUG,
					'password_doesnt_match' => \__( 'Password doesn\'t match.', 'cmb2' ),
				),
				'condition'   => function() {
					return is_checkout();
				},
				'in_footer'   => true,
			)
		);
	}
}

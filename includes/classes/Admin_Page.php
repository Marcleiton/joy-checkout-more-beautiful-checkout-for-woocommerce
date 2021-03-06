<?php

namespace Joy_Checkout;

use function Joy_Checkout\Helpers\config_get;

class Admin_Page {
	public function __construct() {
		// Create the section beneath the advanced tab.
		add_filter( 'woocommerce_get_sections_advanced', array( __CLASS__, 'add_section' ) );

		// Add settings to the specific section we created before.
		add_filter( 'woocommerce_get_settings_advanced', array(__CLASS__, 'get_settings'), 10, 2);

		// Add action links.
		add_filter(
			'plugin_action_links_' . plugin_basename( config_get( 'MAIN_FILE' ) ),
			array(
				__CLASS__,
				'plugin_action_links',
			)
		);
	}

	public function add_section( $sections ) {
		$sections['joy-checkout'] = __( 'WC Checkout Flow', 'cmb2' );

		return $sections;
	}

	public function get_settings( $settings, $current_section )
	{
		// Check the current section is what we want.
		if ( $current_section != 'joy-checkout' ) {
			return $settings;
		}

		$settings = array();

		// Add title.
		$settings[] = array(
			'name' => __( 'Configurações do WC Checkout Flow', 'cmb2' ),
			'type' => 'title',
			'desc' => __( 'As opções a seguir são usadas para configurar o WC Checkout Flow', 'cmb2' ),
			'id'   => 'wc_checkout_flow',
		);

		// Add plugin options
		$settings[] = array(
			'name' => __( 'Mensagem da confirmação de recuperação de senha', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_lost_password',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem da confirmação de novo cadastro', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_email_not_found',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem de confirmação de e-mail já cadastrado', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_email_found',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem recebida por e-mail da confirmação de novo cadastro', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_new_account',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem de erro ao tentar fazer login', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_login_error',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem de login efetuado com sucesso', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_login_success',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem recebida por e-mail da solicitação de redefinição de senha', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_reset_password',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Mensagem de confirmação da redefinição de senha', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_reset_password_success',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Descrição do formulário de autenticação por e-mail', 'cmb2' ),
			'type' => 'textarea',
			'id'   => 'wc_checkout_flow_auth_form_description',
			'css'  => 'min-width: 50%; height: 75px;',
		);

		$settings[] = array(
			'name' => __( 'Classe CSS personalizada para o campo e-mail', 'cmb2' ),
			'type' => 'text',
			'id'   => 'wc_checkout_flow_input_class',
			'css'  => 'min-width: 50%;',
		);

		$settings[] = array(
			'title'   => __( 'Remover mensagens de confirmação', 'cmb2' ),
			'type'    => 'checkbox',
			'id'      => 'wc_checkout_flow_skip_messages',
			'default' => 'no',
		);

		// End section.
		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'wc_checkout_flow',
		);

		return $settings;
	}

	public static function plugin_action_links( $links ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=joy-checkout' ) ) . '">' . __( 'Configurações', 'cmb2' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}
}

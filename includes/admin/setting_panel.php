<?php
$prefix = 'joyc_';

			$joyc_panel = new_cmb2_box(
				array(
					'id'            => $prefix . 'custom_checkout',
					'title'         => 'Configurações do Checkout',
					'object_types'  => array( 'options-page' ),
					'option_key'    => 'custom_checkout_settings',
					'parent_slug'   => 'woocommerce',

					'vertical_tabs' => false, // Set vertical tabs, default false
					'tabs'          => array(
						array(
							'id'     => $prefix . 'general_settings',
							'title'  => 'Configurações Gerais',
							'fields' => array(
								$prefix . 'logo_header',
								$prefix . 'logo_compra_protegida',
								$prefix . 'texto_header',
								$prefix . 'degrade_header_step',
								$prefix . 'cor_circulo_passos',
								$prefix . 'bg_btn_pagamento',
								$prefix . 'borda_pagamento',
								$prefix . 'texto_rodape',
								$prefix . 'texto_info_rodape',
								$prefix . 'skip_cart',
								$prefix . 'auth_form',
								$prefix . 'step_1_title',
								$prefix . 'step_1_description',
								$prefix . 'step_2_title',
								$prefix . 'step_2_description',
								$prefix . 'step_3_title',
								$prefix . 'step_3_description',
								$prefix . 'step_4_title',
								$prefix . 'step_4_description',
							),
						),
					),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => 'Imagem do Cabeçalho',
					'desc'    => 'Envie uma imagem ou digite a URL',
					'id'      => $prefix . 'logo_header',
					'type'    => 'file',
					'default' => JOYC_PLUGIN_URL . 'asserts/images/joy-checkout-logo.png',
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => 'Imagem de segurança',
					'desc'    => 'Envie uma imagem ou digite a URL',
					'id'      => $prefix . 'logo_compra_protegida',
					'type'    => 'file',
					'default' => JOYC_PLUGIN_URL . 'asserts/images/joy-checkout-secure.png',
				)
			);

			$joyc_panel->add_field(
				array(
					'name' => 'Texto do cabeçalho',
					'desc' => 'Digite o texto que será exibido após a imagem do cabeçalho',
					'id'   => $prefix . 'texto_header',
					'type' => 'text',
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => 'Imagem do topo dos passos',
					'desc'    => 'Envie uma imagem ou digite a URL',
					'id'      => $prefix . 'degrade_header_step',
					'type'    => 'file',
					'default' => JOYC_PLUGIN_URL . 'asserts/images/fields-group-header.png',
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 1 title', 'cmb2' ),
					'id'      => $prefix . 'step_1_title',
					'type'    => 'text',
					'default' => __( 'Step 1', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 1 description', 'cmb2' ),
					'id'      => $prefix . 'step_1_description',
					'type'    => 'textarea',
					'default' => __( 'This data will not be disclosed, we value your security', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 2 title', 'cmb2' ),
					'id'      => $prefix . 'step_2_title',
					'type'    => 'text',
					'default' => __( 'Step 2', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 2 description', 'cmb2' ),
					'id'      => $prefix . 'step_2_description',
					'type'    => 'textarea',
					'default' => __( 'This data will not be disclosed, we value your security', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 3 title', 'cmb2' ),
					'id'      => $prefix . 'step_3_title',
					'type'    => 'text',
					'default' => __( 'Step 3', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 3 description', 'cmb2' ),
					'id'      => $prefix . 'step_3_description',
					'type'    => 'textarea',
					'default' => __( 'This data will not be disclosed, we value your security', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 4 title', 'cmb2' ),
					'id'      => $prefix . 'step_4_title',
					'type'    => 'text',
					'default' => __( 'Step 4', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => __( 'Step 4 description', 'cmb2' ),
					'id'      => $prefix . 'step_4_description',
					'type'    => 'textarea',
					'default' => __( 'This data will not be disclosed, we value your security', 'cmb2' ),
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => 'Cor dos círculos dos passos',
					'desc'    => 'Selecione uma cor',
					'id'      => $prefix . 'cor_circulo_passos',
					'type'    => 'colorpicker',
					'default' => '#7b00b0',
				)
			);

	$joyc_panel->add_field(
				array(
					'name'    => 'Borda dos botões',
					'desc'    => 'Adicione a radius border "APENAS NUMERO, É CALCULADO EM PIXEL"',
					'id'      => $prefix . 'borda_pagamento',
					'type'    => 'text',
					'default' => '30',
				)
			);

			$joyc_panel->add_field(
				array(
					'name'    => 'Cor do background do botão de Checkout',
					'desc'    => 'Selecione uma cor para o botão de Finalizar Pagamento',
					'id'      => $prefix . 'bg_btn_pagamento',
					'type'    => 'colorpicker',
					'default' => '#7b00b0',
				)
			);

			$joyc_panel->add_field(
				array(
					'name' => 'Texto do rodapé',
					'desc' => 'Digite o texto do rodapé. Normal o local para o copyright.',
					'id'   => $prefix . 'texto_rodape',
					'type' => 'text',
				)
			);

			$joyc_panel->add_field(
				array(
					'name' => 'Informação adicional',
					'desc' => 'Informação adicional para ser inserido no rodapé',
					'id'   => $prefix . 'texto_info_rodape',
					'type' => 'textarea',
				)
			);

			$joyc_panel->add_field(
				array(
					'name'             => 'Redirecionar para o checkout',
					'desc'             => 'Redirecionar para o checkout após adicionar um produto ao carrinho',
					'id'               => $prefix . 'skip_cart',
					'type'             => 'select',
					'show_option_none' => true,
					'options'          => array(
						'yes' => 'Sim',
						'no'  => 'Não',
					),
				)
			);

			$auth_group_id = $joyc_panel->add_field(
				array(
					'id'          => $prefix . 'auth_form',
					'type'        => 'group',
					'description' => __( 'Fields for authentication form', 'cmb2' ),
					'options'     => array(
						'group_title'   => __( 'Authentication Form', 'cmb2' ),
						'add_button'    => __( 'Add Another Entry', 'cmb2' ),
						'remove_button' => __( 'Remove Entry', 'cmb2' ),
						'sortable'      => true,
					),
				)
			);


			$joyc_panel->add_group_field(
				$auth_group_id,
				array(
					'id'      => $prefix . 'auth_title',
					'name'    => __( 'Authentication title', 'cmb2' ),
					'type'    => 'text',
					'default' => __( 'Enter your e-mail in the input below', 'cmb2' ),
				)
			);

			$joyc_panel->add_group_field(
				$auth_group_id,
				array(
					'id'      => $prefix . 'auth_description',
					'name'    => __( 'Authentication description', 'cmb2' ),
					'type'    => 'textarea',
					'default' => __( 'We\'ll use your e-mail to find an existing user ' . PHP_EOL . 'or to create a new one', 'cmb2' ),
				)
			);

			$joyc_panel->add_group_field(
				$auth_group_id,
				array(
					'id'          => $prefix . 'email_not_found',
					'name'        => __( 'E-mail not found', 'cmb2' ),
					'description' => __( 'Message to show when an e-mail is not found', 'cmb2' ),
					'type'        => 'textarea',
					'default'     => __( 'There is no user registered with this e-mail. You\'ll be redirected to the checkout page and create and account there.', 'cmb2' ),
				)
			);

			$joyc_panel->add_group_field(
				$auth_group_id,
				array(
					'id'          => $prefix . 'email_found',
					'name'        => __( 'E-mail found', 'cmb2' ),
					'description' => __( 'Message to show when an e-mail is found', 'joy_checkout' ),
					'type'        => 'textarea',
					'default'     => __( 'E-mail already exists. Please, sign in or redfine your password.', 'cmb2' ),
				)
			);

			$joyc_panel->add_group_field(
				$auth_group_id,
				array(
					'id'      => $prefix . 'btn_text_color',
					'name'    => __( 'Submit button text color', 'cmb2' ),
					'type'    => 'colorpicker',
					'default' => '#ffffff',
				)
			);










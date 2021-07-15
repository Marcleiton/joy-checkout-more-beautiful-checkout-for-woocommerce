<?php
/**
Plugin Name:  Joy Checkout - More beautiful checkout
Plugin URI: https://joycheckout.com
Description: Plug-in designed to combine the cart and checkout process, which provides users with a faster shopping experience, with fewer interruptions.
Author: Joy Checkout
Author URI: https://joycheckout.com
Version: 1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

@package JoyCheckout
 */

use Joy_Checkout\Checkout\Register_On_Checkout;
use Joy_Checkout\CMB2_Custom_Fields\Register_Custom_Fields;
use Joy_Checkout\Core\Config;

use function Joy_Checkout\Helpers\is_thank_you;

if ( ! defined( 'WPINC' ) ) {
	die;
}

require __DIR__ . '/vendor/autoload.php';

define( 'JOYC_VERSION', '1.0' );
define( 'JOYC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JOYC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! class_exists( 'JoyCheckout' ) ) {
	class JoyCheckout {
		function __construct() {
			Config::setup( __FILE__ );
			$this->includes();
			$this->set_checkout_flow();

			add_action( 'plugins_loaded', array( $this, 'joyc_verify_woocommerce_installed' ) );

			if ( file_exists( JOYC_PLUGIN_DIR . '/cmb2/init.php' ) ) {
				require_once JOYC_PLUGIN_DIR . '/cmb2/init.php';
				require_once JOYC_PLUGIN_DIR . '/cmb2-fontawesome-picker.php';
			}

			add_action( 'init', array( $this, 'joyc_load_plugin_textdomain' ) );
			add_action( 'cmb2_admin_init', array( $this, 'joyc_custom_checkout_panel' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'joyc_register_plugin_styles' ) );
			add_filter( 'woocommerce_locate_template', array( $this, 'joyc_adon_plugin_template' ), 20, 3 );
			add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'joyc_add_quantity' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'joyc_add_js' ) );
			add_action( 'init', array( $this, 'joyc_load_ajax' ) );
			add_filter( 'gettext', array( $this, 'joyc_text_strings' ), 20, 3 );

			add_filter( 'woocommerce_checkout_fields', array( $this, 'custom_override_checkout_fields' ), 10, 2 );
			add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'my_custom_checkout_field_display_admin_order_meta' ), 10, 1 );
			add_filter( 'woocommerce_billing_fields', array( $this, 'move_fields' ), 1 );
			add_action( 'admin_head', array( $this, 'scripts_head_admin' ), 99, 2 );
			add_filter( 'site_transient_update_plugins', array( $this, 'filter_plugin_updates' ), 10, 2 );
			add_filter( 'woocommerce_registration_redirect', array( $this, 'iconic_register_redirect' ), 10, 2 );
			add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
			add_filter( 'woocommerce_default_address_fields', array( $this, 'custom_override_default_address_fields' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'rv_custom_wp_admin_style_enqueue' ), 10, 2 );

			/*redirect to checkout page*/
			add_action( 'template_redirect', array( $this, 'joyc_redirect_to_checkout_if_cart' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'joyc_setup_admin_scripts' ) );
			// add_action( 'cmb2_before_form', array( $this, 'joyc_before_field_row_rating' ), 10, 4 );
			add_action( 'cmb2_before_form', array( $this, 'joyc_before_form' ), 20, 4 );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'joyc_action_links' ) );

			add_action(
				'wp_enqueue_scripts',
				function () {
					if ( ! is_checkout() ) {
						return;
					}

					if ( is_thank_you() ) {
						wp_enqueue_style( 'thankyou', JOYC_PLUGIN_URL . 'dist/thankyou.css', array(), JOYC_VERSION, 'all' );
					}

					wp_enqueue_script( 'custom-checkout-script', JOYC_PLUGIN_URL . 'dist/main.js', array( 'jquery' ), JOYC_VERSION, true );
				}
			);

			add_filter(
				'print_styles_array',
				function ( $array ) {
					if ( ! is_checkout() ) {
						return $array;
					}

					return array_filter(
						$array,
						function ( $handle ) {
							return 'custom-checkout-css' === $handle || 'thankyou' === $handle;
						}
					);
				}
			);

			add_action( 'woocommerce_before_checkout_form', array( $this, 'add_checkout_loading' ) );
		}

		function set_checkout_flow() {
			$enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' );

			if ( 'yes' === $enable_guest_checkout ) {
				return;
			}

			$checkout = new Register_On_Checkout();
			$checkout->add_hooks();
		}

		function includes() {
			include JOYC_PLUGIN_DIR . 'includes/classes/integrations/jc-integrate-wcbrf.php';
		}

		function add_checkout_loading() {
			include_once JOYC_PLUGIN_DIR . 'WooCommerce/checkout/loading.php';
		}

		function rv_custom_wp_admin_style_enqueue() {
			wp_enqueue_style( 'custom_wp_admin_css', get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
		}

		function custom_override_default_address_fields( $address_fields ) {
			$address_fields['address_2']['required'] = false;
			$address_fields['country']['required']   = false;
			return $address_fields;
		}

		function iconic_register_redirect( $redirect ) {
			return wc_get_page_permalink( 'checkout' );
		}

		function filter_plugin_updates( $value ) {
			unset( $value->response['custom-checkout-layouts-for-woocommerce/woocommerce-one-page-checkout-and-layouts.php'] );
			return $value;
		}


		function scripts_head_admin() {
			echo '
		        <style data-type="joy-checkout">
		        #custom-checkout-layouts-for-woocommerce-update{
                    display: none !important;
                }
                tr[data-plugin="custom-checkout-layouts-for-woocommerce/woocommerce-one-page-checkout-and-layouts.php"] a[data-title="Joy Checkout"]{
                    display: none !important;    
                }
                #custom-checkout-layouts-for-woocommerce-update{
                    display: none !important;
                }
		        </style>
		    ';
		}

		function move_fields( $address_fields ) {
			$address_fields['billing_email']['priority']    = 10;
			$address_fields['billing_phone']['priority']    = 20;
			$address_fields['billing_postcode']['priority'] = 45;
			return $address_fields;
		}

		function my_custom_checkout_field_display_admin_order_meta( $order ) {
			echo '<p><strong>' . esc_html( 'CPF' ) . ':</strong> ' . esc_html( get_post_meta( $order->get_id(), '_billing_cpf', true ) ) . '</p>';
			echo '<p><strong>' . esc_html( 'Bairro' ) . ':</strong> ' . esc_html( get_post_meta( $order->get_id(), '_billing_neighborhood', true ) ) . '</p>';
			echo '<p><strong>' . esc_html( 'Número' ) . ':</strong> ' . esc_html( get_post_meta( $order->get_id(), '_billing_number', true ) ) . '</p>';
		}

		function custom_override_checkout_fields( $fields ) {
			unset( $fields['billing']['billing_last_name'] );
			unset( $fields['billing']['billing_company'] );

			$fields['billing']['billing_number'] = array(
				'label'       => __( 'Number', 'cmb2' ),
				'placeholder' => _x( '', 'placeholder', 'cmb2' ),
				'required'    => true,
				'priority'    => 60,
				'class'       => array( 'form-row-wide' ),
				'clear'       => false,
			);

			$fields['billing']['billing_neighborhood'] = array(
				'label'       => __( 'Neighborhood', 'cmb2' ),
				'placeholder' => _x( '', 'placeholder', 'cmb2' ),
				'required'    => true,
				'priority'    => 62,
				'class'       => array( 'form-row-last' ),
				'clear'       => true,
			);

			$fields_to_be_wide = array( 'neighborhood', 'postcode', 'number', 'address_1', 'address_2', 'city' );
			$billing_fields    = &$fields['billing'];

			array_walk(
				$fields_to_be_wide,
				function( $field_to_be_wide ) use ( &$billing_fields ) {
					$prefix = 'billing_';
					$field  = &$billing_fields[ $prefix . $field_to_be_wide ];

					$field['class'] = array_filter(
						$field['class'],
						function ( $class_name ) {
							return ! preg_match( '/form-row-/', $class_name );
						}
					);

					array_unshift( $field['class'], 'form-row-wide' );
				}
			);

			return $fields;
		}

		function joyc_action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=custom_checkout_settings' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			return array_merge( $custom_links, $links );
		}
		/*a rating section*/
		function joyc_before_field_row_rating() {
			$showratings = get_option( 'show_joyc_rating' );
			if ( '' !== $showratings && 'no' === $showratings ) {
				echo esc_html( '' );
			} else {
				echo '<div class="notice joyc_rating_wrap" style="border-left: 4px solid #26a51e;padding-bottom: 10px;">
				<div class="joyc_rating_rop">
                <h3>Let Us Know How We Did?</h3>
				<div class="joyc_star" style="color:#ffb900;">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				</div>
				<p>Hope you are Enjoying using <strong>"Woocommerce one page checkout and layouts"</strong>. Please submit your valueable feedback. It helps us to improve and provide you the best features.</a> Thanks, We appreciate it! </p>
			    </div>
			
			   <div class="joyc_rating_links"><a class="button1" href="https://wordpress.org/support/plugin/custom-checkout-layouts-for-woocommerce/reviews/#new-post">Rate Now</a>
              <a class="button3" href="#"> Later</a>			  
			  <a class="button2" href="#">Never </a>
			   
			   </div> 

				
            </div>';
			} //end if if condition
		}

		/*load traslations*/
		function joyc_load_plugin_textdomain() {
			$rs = load_plugin_textdomain( 'cmb2', false, dirname( plugin_basename( __FILE__ ) ) . '/cmb2/languages' );
		}
		/*Check if woocommerce is installed*/
		function joyc_verify_woocommerce_installed() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'joyc_show_woocommerce_error_message' ) );
			}
		}

		function joyc_show_woocommerce_error_message() {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url   = 'plugin-install.php?s=woocommerce&tab=search&type=term';
				$title = __( 'WooCommerce', 'woocommerce' );
				echo '<div class="error"><p>' . sprintf( esc_html( __( 'To begin using "%1$s" , please install the latest version of %2$s%3$s%4$s and add some product.', 'cmb2' ) ), 'Custom Checkout Layouts WooCommerce', '<a href="' . esc_url( admin_url( $url ) ) . '" title="' . esc_attr( $title ) . '">', 'WooCommerce', '</a>' ) . '</p></div>';
			}
		}

		/*register admin section*/
		function joyc_setup_admin_scripts( $hook ) {
			wp_register_style( 'joyc-tabs-admin', JOYC_PLUGIN_URL . 'asserts/css/tabs.css', array(), JOYC_VERSION );

			wp_enqueue_style( 'joyc-tabs-admin' );
			wp_enqueue_script( 'joyc_tabs_js', JOYC_PLUGIN_URL . 'asserts/js/tabs.js', array( 'jquery' ), JOYC_VERSION, true );

			if ( 'woocommerce_page_custom_checkout_settings' === $hook ) {
				wp_enqueue_script( 'joyc_admin_js', JOYC_PLUGIN_URL . 'asserts/js/joyc_admin_js.js', array( 'jquery' ), JOYC_VERSION, true );
				wp_localize_script( 'joyc_admin_js', 'joyc_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}

		function joyc_before_form( $cmb_id, $object_id, $object_type, $cmb ) {
			if ( $cmb->prop( 'tabs' ) && is_array( $cmb->prop( 'tabs' ) ) ) :
				?>
				<div class="cmb-tabs-wrap cmb-tabs-<?php echo ( ( $cmb->prop( 'vertical_tabs' ) ) ? 'vertical' : 'horizontal' ); ?>">
					<div class="cmb-tabs">

					<?php
					foreach ( $cmb->prop( 'tabs' ) as $tab ) :
						$fields_selector = array();

						foreach ( $tab['fields'] as $tab_field ) :
							$fields_selector[] = '.cmb2-id-' . str_replace( '_', '-', sanitize_html_class( $tab_field ) ) . ':not(.cmb2-tab-ignore)';
							endforeach;

						$fields_selector = apply_filters( 'cmb2_tabs_tab_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
						$fields_selector = apply_filters( 'cmb2_tabs_tab_' . $tab['id'] . '_fields_selector', $fields_selector, $tab, $cmb_id, $object_id, $object_type, $cmb );
								?>

							<div id="<?php echo $cmb_id . '-tab-' . $tab['id']; ?>" class="cmb-tab" data-fields="<?php echo implode( ', ', $fields_selector ); ?>">

								<?php
								if ( isset( $tab['icon'] ) && ! empty( $tab['icon'] ) ) :
									$tab['icon'] = strpos( $tab['icon'], 'dashicons' ) !== false ? 'dashicons ' . $tab['icon'] : $tab['icon']
									?>
									<span class="cmb-tab-icon"><i class="<?php echo $tab['icon']; ?>"></i></span>
								<?php endif; ?>

									<?php if ( isset( $tab['title'] ) && ! empty( $tab['title'] ) ) : ?>
									<span class="cmb-tab-title"><?php echo $tab['title']; ?></span>
								<?php endif; ?>
							</div>
							<?php endforeach; ?>

					</div>
				  <?php
				endif;
		}

		/**
		 * Register style sheet.
		 */
		function joyc_register_plugin_styles() {
			if ( is_checkout() ) {
				wp_register_style( 'custom-checkout-css', JOYC_PLUGIN_URL . 'dist/main.css', array(), JOYC_VERSION );

				wp_enqueue_style( 'custom-checkout-css' );
			}
		}
		/* custom checkout setting page  */
		function joyc_custom_checkout_panel() {
			require_once JOYC_PLUGIN_DIR . 'includes/admin/setting_panel.php';
		}

		/*Locate new woocommerce setting folder */
		function joyc_adon_plugin_template( $template, $template_name, $template_path ) {
			global $woocommerce;
			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/WooCommerce/';

			$template = $plugin_path . $template_name;

			if ( ! file_exists( $template ) ) {
				$template = $_template;
			}

			return $template;
		}

		/*set product quantity*/


		function joyc_add_quantity( $cart_item, $cart_item_key ) {
			$product_quantity = '';
			return $product_quantity;
		}

		/**add some footer js*/


		function joyc_add_js() {
			?>
				<?php
				$checkout_setting = get_option( 'custom_checkout_settings' );

				if ( $checkout_setting['joyc_skip_cart'] == 'yes' ) {
					?>
					<style data-type="joy-checkout">
						.mini_cart_content .checkout {
							display: none !important;
						}
					</style>
					<?php
				}

				if ( is_checkout() ) {

					?>

					<style data-type="joy-checkout">
						:root {
							--main-bg-color: <?php echo esc_attr( $checkout_setting['joyc_heading_background'] ); ?>;
							--main-bor-color: <?php echo esc_attr( $checkout_setting['joyc_heading_border'] ); ?>;
							--main-bor-text-color: <?php echo esc_attr( $checkout_setting['joyc_heading_text_color'] ); ?>;
							--main-button-color: <?php echo esc_attr( $checkout_setting['joyc_button_color'] ); ?>;
							--main-buttontext-color: <?php echo esc_attr( $checkout_setting['joyc_buttontext_color'] ); ?>;
							--main-btn-pagamento: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?>;
							--main-btn-pagamento: <?php echo esc_attr( $checkout_setting['joyc_borda_pagamento'] ); ?>;
						}

						body {
							overflow-x: hidden !important;
						}

						.woocommerce-error::before {
							content: "" !important;
						}

						h3 {
							margin-top: 20px !important;
						}

						.woocommerce-error li,
						.woocommerce-info li,
						.woocommerce-message li {
							font-size: 14px !important;
						}

						td.joyc_qty button.joycminus,
						button.joycplus {
							background: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
						}

						.onestepcheckout-summary .removepro a.joyc_remove {
							color: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
						}

						.woocommerce ul.woocommerce-error {
							border-top-color: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
						}

						.woocommerce ul.woocommerce-error {
							border-top-color: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
						}

						.woocommerce .woocommerce-error:before {
							color: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
						}

						.site-header,
						.main-menu,
						footer,
						.footer-nav-widgets-wrapper {
							display: none !important;
						}

						h1 {
							display: none !important;
						}

						.optional {
							display: none !important;
						}

						.woocommerce-checkout-review-order-table .shipping-total {
							padding: 10px 0 !important;
						}

						.woocommerce-shipping-fields {
							display: none !important;
						}

						.woocommerce-billing-fields input,
						.woocommerce-billing-fields select {
							border: 1px solid #c4c4c4 !important;
						}

						header,
						#billing_country_field {
							display: none !important;
						}

						/*.coupon_border{display: none !important;}*/

						.footer-info {
							padding-top: 30px;
							text-align: center;
							font-size: 10px;
							max-width: 700px;
							margin: 0 auto;
							text-transform: uppercase;
							color: #797979;
						}

						.grid-col-checkout {
							padding: 20px !important;
							background: #f2f2f2 url(<?php echo esc_attr( $checkout_setting['joyc_degrade_header_step'] ); ?>) no-repeat left top !important;
							background-size: contain !important;
							margin-right: 10px;
							margin-left: 10px;
						}

						.step-number-1,
						.step-number-2,
						.step-number-3,
						.step-number-4 {
							display: inline-block;
							width: 30px;
							height: 30px;
							background-color: <?php echo esc_attr( $checkout_setting['joyc_cor_circulo_passos'] ); ?>;
							border-radius: 50px;
							color: #ffffff;
							text-align: center;
							margin-right: 5px;
							font-size: 22px;
						}

						#coupon_code {
							border: 1px solid #c4c4c4 !important;
						}

						.checkout_coupon {
							display: block !important;
						}

						.coupon_border {
							display: none !important;
						}

						#place_order,
						.woocommerce-checkout .woocommerce .joyc_button {
							font-size: 14px !important;
							background: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
							border-radius: <?php echo esc_attr( $checkout_setting['joyc_borda_pagamento'] ); ?>px !important;
							color: #ffffff !important;
							font-weight: 400 !important;
							width: 100% !important;
							text-transform: uppercase !important;
						}

						.footer-joy img {
							max-width: 90px !important;
							margin-left: 10px;
						}

						.footer-joy {
							font-size: 12px !important;
							display: flex;
							margin-top: 10px;
						}

						#footer-checkout {
							max-width: 930px !important;
							margin: 0 auto !important;
							padding-bottom: 30px;
						}

						#btn_step_01,
						#btn_step_02 {
							display: block !important;
							padding: 5px 10px !important;
							font-size: 14px !important;
							background: <?php echo esc_attr( $checkout_setting['joyc_bg_btn_pagamento'] ); ?> !important;
							border-radius: <?php echo esc_attr( $checkout_setting['joyc_borda_pagamento'] ); ?>px !important;
							color: #ffffff !important;
							font-weight: 400 !important;
							width: 100% !important;
							text-transform: uppercase !important;
							text-align: center;
							text-decoration: none !important;
						}


						#joy-checkout {
							background-color: #f7f7f7;
							width: 99.225vw;
							position: relative;
							margin-left: -49.59vw;
							left: 50%;
							border-top: 1px solid #e2e2e2;
						}

						.joyc_qty input {
							border: 1px solid #ccc !important;
						}

						.step-heading,
						.step-number-1,
						.step-number-2,
						.step-number-3,
						.step-number-4,
						.step-1-desc,
						.step-2-desc,
						.step-3-desc,
						.step-4-desc,
						#joy-checkout,
						#btn_step_01,
						#btn_step_02,
						#place_order,
						.cart_totals,
						.wc_payment_methods,
						.footer-joy,
						.footer-copy,
						.footer-info {
							font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif !important;
							letter-spacing: initial !important;
						}

						.express-one-page-checkout-main {
							max-width: 960px !important;
							margin: 0 auto;
						}

						#joy-checkout .txt-checkout {
							text-align: center;
							font-size: 16px !important;
							font-weight: 600;
							padding-top: 20px;
						}

						.woocommerce-checkout {}

						.header_checkout {
							max-width: 900px;
							margin: 0 auto;
						}

						.button {
							text-decoration: none !important;
						}

						.onestepcheckout-summary .price>.amount {
							font-size: 13px !important;
							color: #23282d !important;
							letter-spacing: -0.5px !important;
						}

						.clearfix::after {
							content: "";
							clear: both;
							display: table;
						}

						.header_checkout .header-logo {
							float: left;
							max-width: 200px;
							margin: 20px 0;
						}

						.header_checkout .header-seguranca {
							float: right;
							max-width: 125px;
							margin-top: 20px;
						}

						.header_checkout p {
							font-size: 18px !important;
							font-weight: 600;
							margin-bottom: 30px;
							margin-top: -10px;
						}

						/*#payment{
				display: none;
			}*/

						.payment_method_woo-mercado-pago-custom {
							min-width: 293px !important;
						}

						.checkout-img-card {
							width: 280px !important;
						}

						.woocommerce-additional-fields {
							display: none !important;
						}

						.footer-copy {
							margin-top: 40px;
							font-size: 12px !important;
						}

						.woocommerce-checkout #payment {
							background-color: initial !important;
						}

						.wc_payment_methods {
							padding: 0 !important;
						}

						.woocommerce label {
							font-size: 14px !important;
							font-weight: 600 !important;
							color: #000000 !important;
						}

						.wc_payment_methods label {
							font-size: 12px !important;
							vertical-align: top;
						}

						.msg-payment p {
							font-size: 13px !important;
							color: #adadad;
							font-weight: 400 !important;
							line-height: 16px;
							margin-top: 35px;
						}

						.msg-payment i {
							font-size: 36px;
							float: left;
							margin-right: 10px;
						}

						.mp-subtitle-custom-checkout,
						.mp-frame-links,
						.woocommerce-terms-and-conditions-wrapper {
							display: none !important;
						}

						#order_review .woocommerce-Price-amount {
							color: #6B02AE !important;
						}

						.img-card {
							width: 100% !important;
							max-height: initial !important;
							margin-left: 0px;
						}

						.grid-col-3 button {
							/*border-radius: 40px !important;*/
						}

						#joy-checkout label {
							margin-bottom: 0 !important;
						}

						.woocommerce-billing-fields h3 {
							text-align: left;
							margin-top: 20px;
							text-transform: initial !important;
							color: #6B02AE;
						}

						.white-space {
							height: 30px !important;
							margin-left: -20px;
							margin-right: -20px;
							background: #f7f7f7 url(<?php echo esc_attr( $checkout_setting['joyc_degrade_header_step'] ); ?>) no-repeat 0px 20px !important;
							background-size: contain !important;
							clear: both;
						}


						#checkout-step-4 {
							padding: 20px;
							background: #f2f2f2 url(<?php echo esc_attr( $checkout_setting['joyc_degrade_header_step'] ); ?>) no-repeat left top;
							background-size: contain;
						}

						#checkout-step-3 {
							padding: 20px;
							background: #f2f2f2 url(<?php echo esc_attr( $checkout_setting['joyc_degrade_header_step'] ); ?>) no-repeat left top;
							background-size: contain;
						}

						#checkout-step-2 {
							padding: 20px;
							background: #f2f2f2 url(<?php echo esc_attr( $checkout_setting['joyc_degrade_header_step'] ); ?>) no-repeat left top;
							background-size: contain;
							margin-left: -20px;
							margin-right: -20px;
							padding-top: 50px;
						}

						.step-1-ccod {
							display: inline-block;
							width: 40px;
							height: 31px;
							background-color: #ccc;
							font-weight: 600 !important;
							color: #000;
							text-align: center;
							float: left;
							padding-top: 4px;
						}

						.step-heading {
							text-transform: initial !important;
							color: #000000 !important;
							margin-top: 20px !important;
							font-size: 21px !important;
							font-weight: 600 !important;
						}


						.step-1-desc,
						.step-2-desc,
						.step-3-desc,
						.step-4-desc {
							font-size: 12px;
							display: block;
							margin-left: 35px;
							color: #000;
							text-transform: initial;
							font-weight: 100;
						}

						@media(max-width: 700px) {

							#footer-checkout {
								margin-left: 33px !important;
							}

							#joy-checkout .txt-checkout {
								font-size: 12px !important;
							}

							.header_checkout {
								text-align: center !important;
							}

							.grid-col-checkout {
								margin-top: 25px !important;
							}
						}
					</style>
					<?php

					$admin_url        = get_admin_url();
					$checkout_setting = get_option( 'custom_checkout_settings' );

					?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							setTimeout(function() {

								jQuery("<a href=\"#\" id=\"btn_step_01\"><?php echo __( 'Continue', 'cmb2' ); ?></a><div class=\"white-space\"></div><h3 data-step-index=\"2\" class=\"step-heading\"><div class=\"step-number-2\">2</div><?php echo $checkout_setting['joyc_step_2_title']; ?><div class=\"step-2-desc\"><?php echo $checkout_setting['joyc_step_2_description']; ?></div></h3>").insertBefore("#billing_address_1_field");
								var postcode = jQuery('#billing_postcode_field').detach();
								jQuery("#billing_address_1_field").before(postcode);
								jQuery("#billing_state_field").after("<div class=\"clearfix\"></div><a href=\"#\" id=\"btn_step_02\"><?php echo __( 'Continue', 'cmb2' ); ?></a>");

							}, 500);

							jQuery("#billing_address_1").attr("placeholder", "");
							jQuery(".expresss-one-page-coupen").insertBefore("#order_review_table")



						});

						jQuery('.woocommerce-checkout').change(function() {
							selected_value = jQuery("input[name='payment_method']:checked").val();
							if (selected_value == 'woo-mercado-pago-custom') {
								jQuery(".checkout-img-card").show();
							} else {
								jQuery(".checkout-img-card").hide();
							}
						});

						//jQuery(".woocommerce-billing-fields .input-text").keyup(function(){

						//if(jQuery("#billing_first_name").val() != "" && jQuery("#billing_email").val() != "" && jQuery("#billing_cpf").val() != "" && jQuery("#billing_phone").val() != "" && jQuery("#billing_postcode").val() != "" && jQuery("#billing_address_1").val() != "" && jQuery("#billing_number").val() != "" && jQuery("#billing_neighborhood").val() != "" && jQuery("#billing_city").val() != "" && jQuery("#billing_neighborhood").val() != ""){

						//jQuery("#payment").show(1000);
						//jQuery(".msg-payment").hide(500);

						//}else{
						//jQuery("#payment").hide(1000);
						//jQuery(".msg-payment").show(500);
						//	}

						//});

						jQuery('form.checkout').on("click", "button.joycminus", function() {
							var inputqty = jQuery(this).next('input.qty');
							var val = parseInt(inputqty.val());
							var max = parseFloat(inputqty.attr('max'));
							var min = parseFloat(inputqty.attr('min'));
							var step = parseFloat(inputqty.attr('step'));

							if (val > min) {
								inputqty.val(val - step);
								jQuery(this).next('input.qty').trigger("change");

							}

						});

						jQuery('form.checkout').on("click", "button.joycplus", function() {

							var inputqty = jQuery(this).prev('input.qty');
							var val = parseInt(inputqty.val());
							var max = inputqty.attr('max');
							var min = parseFloat(inputqty.attr('min'));
							var step = parseFloat(inputqty.attr('step'));

							if (val < max || max == '') {
								inputqty.val(val + step);
								jQuery(this).prev('input.qty').trigger("change");
							}


						});


						jQuery("form.checkout #order_review_table").on("change", "input.qty", function() {

							var data = {
								action: 'joyc_update_order_review',
								security: wc_checkout_params.update_order_review_nonce,
								post_data: jQuery('form.checkout').serialize()
							};

							jQuery.post('<?php echo esc_url( $admin_url ); ?>' + 'admin-ajax.php', data, function(response) {
								jQuery('body').trigger('update_checkout');
							});
						});

						jQuery(document.body).on('added_to_cart', function() {
							jQuery('body').trigger('update_checkout');
						});

						jQuery(document.body).on('updated_checkout', function() {

							var data = {
								action: 'joyc_update_order_table',

							};

							jQuery.post('<?php echo esc_url( $admin_url ); ?>' + 'admin-ajax.php', data, function(response) {
								// alert(response);
								jQuery('#order_review_table').html();

								jQuery('#order_review_table').html(response);

								//('#order_review_table').html(response);
							});
						});
					</script>
					<?php
				}
		}


			/*starts ajax*/
		function joyc_load_ajax() {
			if ( ! is_user_logged_in() ) {
				add_action( 'wp_ajax_nopriv_joyc_update_order_review', array( $this, 'joyc_update_order_review' ) );
				add_action( 'wp_ajax_nopriv_joyc_update_order_table', array( $this, 'joyc_update_order_table' ) );
				add_action( 'wp_ajax_nopriv_joyc_update_rating', array( $this, 'joyc_update_rating' ) );
			} else {
				add_action( 'wp_ajax_joyc_update_order_review', array( $this, 'joyc_update_order_review' ) );
				add_action( 'wp_ajax_joyc_update_order_table', array( $this, 'joyc_update_order_table' ) );
				add_action( 'wp_ajax_joyc_update_rating', array( $this, 'joyc_update_rating' ) );
			}
		}


		function joyc_update_order_review() {
			$values = array();
			if ( ! empty( $_POST['post_data'] ) ) {
				parse_str( recursive_sanitize_text_field( $_POST['post_data'] ), $values );
				$cart = $values['cart'];
				foreach ( $cart as $cart_key => $cart_value ) {
					WC()->cart->set_quantity( $cart_key, $cart_value['qty'], false );
					WC()->cart->calculate_totals();
					woocommerce_cart_totals();
				}
			}
			exit;
		}
		function joyc_update_rating() {
			if ( isset( $_POST['joyc_ratings'] ) ) {
				$joyc_ratings = sanitize_text_field( wp_unslash( $_POST['joyc_ratings'] ) );
				update_option( 'show_joyc_rating', $joyc_ratings );
				exit;
			}
		}
			/*update order table*/
		function joyc_update_order_table() {
			include_once JOYC_PLUGIN_DIR . 'WooCommerce/joyc_checkout/orderreview-table.php';
			exit;
		}
			/*replace add to cart content*/
		function joyc_text_strings( $translated_text, $text, $domain ) {
			$checkout_text = get_option( 'custom_checkout_settings' );

			if ( isset( $checkout_text['joyc_addtocart'] ) && '' === $checkout_text['joyc_addtocart'] ) {
				$addtocart       = $checkout_text['joyc_addtocart'];
				$translated_text = str_ireplace( 'Add to cart', $addtocart, $translated_text );
			}
			if ( isset( $checkout_text['joyc_viewcart'] ) && '' === $checkout_text['joyc_viewcart'] && isset( $checkout_text['joyc_skip_cart'] ) && 'yes' === $checkout_text['joyc_skip_cart'] ) {
				$viewcart        = $checkout_text['joyc_viewcart'];
				$translated_text = str_ireplace( 'View cart', $viewcart, $translated_text );
			}
			if ( isset( $checkout_text['joyc_placeorder'] ) && '' === $checkout_text['joyc_placeorder'] ) {
				$placeorder      = $checkout_text['joyc_placeorder'];
				$translated_text = str_ireplace( 'Place order', $placeorder, $translated_text );
			}
			if ( isset( $checkout_text['joyc_continueshop'] ) && '' === $checkout_text['joyc_continueshop'] ) {
				$cont_shop       = $checkout_text['joyc_continueshop'];
				$translated_text = str_ireplace( 'Continue shopping', $cont_shop, $translated_text );
			}

			return $translated_text;
		}

		function joyc_redirect_to_checkout_if_cart() {
			global $woocommerce;
			$checkout_setting = get_option( 'custom_checkout_settings' );

			if ( is_cart() && WC()->cart->get_cart_contents_count() > 0 && isset( $checkout_text['joyc_skip_cart'] ) && 'yes' === $checkout_setting['joyc_skip_cart'] ) {

				// Redirect to check out url!
				wp_safe_redirect( $woocommerce->cart->get_checkout_url(), '301' );
				exit;
			}
		}

		function isCpfValid( $cpf ) {
			$j          = 0;
			$cpf_length = strlen( $cpf );
			for ( $i = 0; $i < ( $cpf_length ); $i++ ) {
				if ( is_numeric( $cpf[ $i ] ) ) {
					$num[ $j ] = $cpf[ $i ];
					$j++;
				}
			}
			// Etapa 2: Conta os dígitos, um cpf válido possui 11 dígitos numéricos.
			if ( count( $num ) !== 11 ) {
				$is_cpf_valid = false;
			}

			// Etapa 3: Combinações como 00000000000 e 22222222222 embora não sejam cpfs reais resultariam em cpfs válidos após o calculo dos dígitos verificares e por isso precisam ser filtradas nesta parte.
			else {
				for ( $i = 0; $i < 10; $i++ ) {
					if ( $num[0] === $i && $num[1] === $i && $num[2] === $i && $num[3] === $i && $num[4] === $i && $num[5] === $i && $num[6] === $i && $num[7] === $i && $num[8] === $i ) {
						$is_cpf_valid = false;
						break;
					}
				}
			}
			// Etapa 4: Calcula e compara o primeiro dígito verificador.
			if ( ! isset( $is_cpf_valid ) ) {
				$j = 10;
				for ( $i = 0; $i < 9; $i++ ) {
					$multiplica[ $i ] = $num[ $i ] * $j;
					$j--;
				}
				$soma  = array_sum( $multiplica );
				$resto = $soma % 11;
				if ( $resto < 2 ) {
					$dg = 0;
				} else {
					$dg = 11 - $resto;
				}
				if ( $dg !== $num[9] ) {
					$is_cpf_valid = false;
				}
			}
			// Etapa 5: Calcula e compara o segundo dígito verificador.
			if ( ! isset( $is_cpf_valid ) ) {
				$j = 11;
				for ( $i = 0; $i < 10; $i++ ) {
					$multiplica[ $i ] = $num[ $i ] * $j;
					$j--;
				}
				$soma  = array_sum( $multiplica );
				$resto = $soma % 11;
				if ( $resto < 2 ) {
					$dg = 0;
				} else {
					$dg = 11 - $resto;
				}
				if ( $dg != $num[10] ) {
					$is_cpf_valid = false;
				} else {
					$is_cpf_valid = true;
				}
			}
			// Etapa 6: Retorna o Resultado em um valor booleano.
			return $is_cpf_valid;
		}

		public static function joyc_myplugin_deactivate() {
			return false;
		}
	} // end of class
} // end of if class


add_filter( 'woocommerce_default_address_fields', 'reorder_checkout_fields' );

function reorder_checkout_fields( $fields ) {
	$fields['country']['priority'] = 90;
	return $fields;
}

function warp_ajax_product_remove() {
	// Get order review fragment!
	if ( ! empty( $_POST['product_id'] && ! empy( $_POST['cart_item_key'] ) ) ) {
		ob_start();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( sanitize_key( $_POST['product_id'] ) === $cart_item['product_id'] && sanitize_key( $_POST['cart_item_key'] ) === $cart_item_key ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}

		WC()->cart->calculate_totals();
		WC()->cart->maybe_set_cart_cookies();

		woocommerce_order_review();
		$woocommerce_order_review = ob_get_clean();
	}
}

	register_deactivation_hook( __FILE__, array( 'joyCheckout', 'joyc_myplugin_deactivate' ) );

	$JoyCheckout_obj = new JoyCheckout();

/***
 * To ensure arrays are properly sanitized to WordPress Codex standards,
 * they encourage usage of sanitize_text_field(). That only works with a single
 * variable (string). This function allows for a full blown array to get sanitized
 * properly, while sanitizing each individual value in a key -> value pair.
 * Source: https://wordpress.stackexchange.com/questions/24736/wordpress-sanitize-array
 * Author: Broshi, answered Feb 5 '17 at 9:14
 *
 * @param array $array Post content for sanitize.
 */
function recursive_sanitize_text_field( $array ) {
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = wporg_recursive_sanitize_text_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
	}
	return $array;
}

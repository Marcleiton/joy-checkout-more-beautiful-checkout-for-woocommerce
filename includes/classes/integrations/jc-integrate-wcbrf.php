<?php

if ( ! class_exists( 'JC_Integrate_WCBRF' ) ) {
    class JC_Integrate_WCBRF {
        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'init' ) );
        }

        public function init() {
            $active_plugins = (array) get_option( 'active_plugins', array() );
            $wcbrf_slug     = 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php';

            if ( ! in_array( $wcbrf_slug, $active_plugins, true ) ) {
                return;
            }

            add_filter( 'wcbcf_billing_fields', array( $this, 'modify_custom_billing_fields' ) );
        }

        public function modify_custom_billing_fields( $billing_fields ) {
            if ( isset( $billing_fields['billing_cellphone'] ) ) {
                $billing_fields['billing_cellphone']['priority'] = 21;
            }

            if ( isset( $billing_fields['billing_ie'] ) ) {
                $billing_fields['billing_ie']['class'] = array(
                    'form-row',
                    'form-row-wide',
                    'person-type-field',
                    'validate-required',
                );
            }

            if ( isset( $billing_fields['billing_cnpj'] ) ) {
                $billing_fields['billing_cnpj']['class'] = array(
                    'form-row',
                    'form-row-wide',
                    'person-type-field',
                    'validate-required',
                );
            }

            return $billing_fields;
        }
    }
}

new JC_Integrate_WCBRF;

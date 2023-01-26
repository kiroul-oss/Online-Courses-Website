<?php

/**
 * WooCommerce PayPal Payments by WooCommerce V 1.9.3
 * Plugin URI: https://woocommerce.com/products/woocommerce-paypal-payments/
 */

class WFACP_Woocommerce_Paypal_Payments {
	public function __construct() {
		add_action( 'wfacp_after_template_found', [ $this, 'action' ] );
	}

	public function action() {
		add_filter( 'woocommerce_paypal_payments_checkout_dcc_renderer_hook', function () {
			return 'wfacp_woocommerce_review_order_after_submit';
		} );

	}
}

if ( ! class_exists( '\WooCommerce\PayPalCommerce\PluginModule' ) ) {
	return;
}

WFACP_Plugin_Compatibilities::register( new WFACP_Woocommerce_Paypal_Payments(), 'woocommerce-paypal-payments' );
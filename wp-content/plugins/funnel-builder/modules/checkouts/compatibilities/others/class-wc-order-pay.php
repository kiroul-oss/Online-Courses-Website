<?php

/**
 * Compatibility for create order by order-pay in admin
 */
class WFACP_WC_Order_Pay {
	public function __construct() {
		add_action( 'woocommerce_before_pay_action', [ $this, 'update_aero_id' ], 10, 1 );
		add_action( 'before_woocommerce_pay', [ $this, 'update_checkout_views' ] );
		add_filter( 'woocommerce_payment_successful_result', [ $this, 'update_checkout_reporting' ], 10, 2 );
	}

	/**
	 * add aero id in order meta
	 *
	 * @param $order
	 *
	 * @return void
	 */
	public function update_aero_id( $order ) {
		if ( ! isset( $_POST['_wfacp_post_id'] ) || ! isset( $_POST['woocommerce_pay'] ) ) {
			return;
		}

		$wfacp_id = absint( $_POST['_wfacp_post_id'] );
		if ( $wfacp_id > 0 ) {
			update_post_meta( $order->get_id(), '_wfacp_post_id', $wfacp_id );
		}
	}

	/**
	 * update checkout views reporting data
	 * @return void
	 */
	public function update_checkout_views() {
		if ( isset( $_GET['pay_for_order'] ) && "true" === $_GET['pay_for_order'] ) {
			$wfacp_id = WFACP_Common::get_id();
			if ( $wfacp_id > 0 ) {
				$str = '_wfacp_post_id=' . $wfacp_id;
				WFACP_Core()->reporting->update_order_review( $str );
			}
		}
	}

	/**
	 * update checkout reporting meta
	 *
	 * @param $result
	 * @param $order_id
	 *
	 * @return mixed
	 */
	public function update_checkout_reporting( $result, $order_id ) {
		$order    = wc_get_order( $order_id );
		$wfacp_id = $order->get_meta( '_wfacp_post_id' );
		if ( empty( $wfacp_id ) ) {
			return $result;
		}

		$posted_data = array(
			'wfacp_post_id'         => $wfacp_id,
			'wfacp_woocommerce_pay' => true
		);

		WFACP_Core()->reporting->update_reporting_data_in_meta( $order, $posted_data );

		return $result;
	}


}


new WFACP_WC_Order_Pay();

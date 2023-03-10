<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WFACP_Compatibility_With_Active_Avada {

	public $js_folder_url = '';

	public function __construct() {

		add_action( 'wfacp_after_checkout_page_found', [ $this, 'remove_actions' ] );
		add_action( 'wfacp_checkout_page_found', [ $this, 'remove_actions' ] );
		add_filter( 'wfacp_do_not_allow_shortcode_printing', [ $this, 'do_not_execute_shortcode' ] );
	}

	public function remove_actions() {

		global $avada_woocommerce, $fusion_settings;

		if ( class_exists( 'Avada_Woocommerce' ) && $avada_woocommerce instanceof Avada_Woocommerce ) {
			remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'avada_top_user_container' ), 1 );
			remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'checkout_coupon_form' ), 10 );
			remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'before_checkout_form' ) );
			remove_action( 'woocommerce_after_checkout_form', array( $avada_woocommerce, 'after_checkout_form' ) );
			remove_action( 'woocommerce_checkout_before_customer_details', array( $avada_woocommerce, 'checkout_before_customer_details' ) );
			remove_action( 'woocommerce_checkout_after_customer_details', array( $avada_woocommerce, 'checkout_after_customer_details' ) );
			remove_action( 'woocommerce_checkout_billing', array( $avada_woocommerce, 'checkout_billing' ), 20 );
			remove_action( 'woocommerce_checkout_shipping', array( $avada_woocommerce, 'checkout_shipping' ), 20 );

			remove_filter( 'woocommerce_order_button_html', array( $avada_woocommerce, 'order_button_html' ) );

			add_action( 'wfacp_internal_css', [ $this, 'internal_css' ] );

		}
		if ( class_exists( 'Fusion_Dynamic_CSS' ) ) {
			$dynamic_css = Fusion_Dynamic_CSS::get_instance();
			if ( $dynamic_css->inline instanceof Fusion_Dynamic_CSS_Inline ) {
				remove_action( 'wp_head', array( $dynamic_css->inline, 'add_inline_css' ), 999 );
			}
		}

		if ( class_exists( 'Fusion_Scripts' ) && $fusion_settings instanceof Fusion_Settings ) {
			$lazy_load = $fusion_settings->get( 'lazy_load' );
			if ( isset( $lazy_load ) ) {
				$pageID = WFACP_Common::get_id();
				$design = WFACP_Common::get_page_design( $pageID );

				if ( ! is_array( $design ) || count( $design ) === 0 ) {
					return;
				}
				if ( isset( $design['selected_type'] ) && 'pre_built' !== $design['selected_type'] ) {
					$path = ( true === FUSION_LIBRARY_DEV_MODE ) ? '' : '/min';
					if ( defined( 'FUSION_LIBRARY_URL' ) ) {
						$this->js_folder_url = FUSION_LIBRARY_URL . '/assets' . $path . '/js';
						add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_script' ] );
					}
				}
			}
		}
	}

	public function wp_enqueue_script() {
		wp_enqueue_script( 'lazysizes', $this->js_folder_url . '/library/lazysizes.js', [], '4.1.5', true );
	}

	public function internal_css() {
		?>

        <style>
            html:not(.avada-html-layout-boxed):not(.avada-html-layout-framed) body,
            html {
                background-color: transparent !important;

            }

            body.wfacp_checkout-template-wfacp-canvas-php {
                overflow-x: initial;
            }

            body #wfacp-e-form .shop_table tbody tr {
                height: auto;
            }

            body #wfacp-e-form table th {
                font-family: inherit;
            }

            body #wfacp-e-form ul.woocommerce-error li {
                padding: 0;
                margin-bottom: 0;
            }

            body #wfacp-e-form .wfacp_main_form .wfacp_whats_included h3 {
                border-bottom: none;
            }


            body #wfacp-e-form .wfacp_main_form .wfacp_shipping_recurring label {
                display: block;
                margin-bottom: 0;
            }

            body #wfacp-e-form .wfacp_main_form .recurring-total ul li {
                padding: 11px;
                text-indent: 0;
            }

            body #wfacp-e-form form.checkout_coupon.woocommerce-form-coupon {
                margin-bottom: 20px;
            }

            body #wfacp-e-form .select2-container--default .select2-selection--single .select2-selection__arrow {
                border-left: none;
            }

            body #wfacp-e-form .wfacp_main_form .wfacp_shipping_table tr.shipping td p small {
                font-size: 13px;
            }

            body #wfacp-e-form .wfacp_main_form #product_switching_field .wfacp_product_switcher_col.wfacp_product_switcher_col_1 .wfacp-qty-count {
                color: #fff;
            }

            body #wfacp-e-form .wfacp_main_form .wfacp_shipping_table ul li input[type=radio] {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }

            body #wfacp-e-form .wfacp_main_form .checkout .shop_table tfoot {
                border: none;
            }

            body #wfacp-e-form #wrapper .select-arrow,
            body #wfacp-e-form .avada-select-parent .select-arrow,
            body #wfacp-e-form .fusion-modal-content .select-arrow {
                display: none;
            }

        </style>
		<?php
	}

	public function do_not_execute_shortcode( $status ) {
		if ( isset( $_REQUEST['fusion_use_builder'] ) ) {
			$status = true;
		}

		return $status;
	}
}

if ( defined( 'AVADA_VERSION' ) ) {
	WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_Active_Avada(), 'avada' );
}

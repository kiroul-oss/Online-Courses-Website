<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFACP_Compatibility_With_Theme_Brick {
	private $shortcode_content = '';

	public function __construct() {
		/* checkout page */

		add_filter( 'wfacp_shortcode_exist', [ $this, 'is_shortcode_exists' ], 10, 2 );
		add_filter( 'wfacp_detect_shortcode', [ $this, 'send_brick_content' ] );
		add_action( 'wp_ajax_bricks_get_element_html', [ $this, 'remove_our_shortcode' ], 9 );

		//	add_action( 'wfacp_none_checkout_pages', [ $this, 'force_execute_embed_shortcode' ], 0 );
		add_filter( 'wfacp_embed_form_allow_header', [ $this, 'disable_embed_form_header_footer' ] );
		add_action( 'wfacp_update_page_design', array( $this, 'update_page_template' ), 99, 1 );
		add_filter( 'rest_dispatch_request', [ $this, 'remove_our_shortcode' ], 20 );
		add_filter( 'show_admin_bar', [ $this, 'remove_admin_bar' ], 100 );
		add_filter( 'wfacp_do_not_execute_shortcode', [ $this, 'do_not_execute_shortcode' ], 20 );
		add_filter( 'wfacp_do_not_allow_shortcode_printing', [ $this, 'do_not_execute_shortcode' ], 20 );

	}

	public function is_enabled() {
		if ( function_exists( 'bricks_is_builder' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove admin bar if Bricks builder is open
	 *
	 * @param $status
	 *
	 * @return false|mixed
	 */
	public function remove_admin_bar( $status ) {
		if ( isset( $_GET['bricks'] ) && 'run' == $_GET['bricks'] ) {
			$status = false;
		}

		return $status;
	}

	/**
	 * Do Not execute shortcode when bricks builder is open( sometime Session expired message displayed)
	 *
	 * @param $status
	 *
	 * @return false|mixed
	 */
	public function do_not_execute_shortcode( $status ) {

		if ( isset( $_GET['bricks'] ) && 'run' == $_GET['bricks'] ) {
			$status = true;
		}

		return $status;
	}

	public function disable_embed_form_header_footer( $status ) {


		global $post;
		if ( ! $this->is_enabled() || ! $post instanceof WP_Post ) {
			return $status;
		}
		$panels_data = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );;
		if ( ! is_array( $panels_data ) || count( $panels_data ) == 0 ) {
			return $status;
		}
		add_filter( 'wfacp_embed_form_allow_footer', '__return_false' );

		return false;
	}

	public function is_shortcode_exists( $status, $post ) {
		if ( true == $status ) {
			return $status;
		}

		$content = $this->get_shortcode_content( $post );
		if ( false !== $content ) {
			$this->shortcode_content = $content;
			$status                  = true;
		}

		return $status;


	}

	public function send_brick_content( $post_content ) {
		return ! empty( $this->shortcode_content ) ? $this->shortcode_content : $post_content;
	}

	public function get_shortcode_content( $post ) {


		if ( is_null( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$panels_data = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );;


		if ( empty( $panels_data ) ) {
			return false;
		}
		$shortcodes     = json_encode( $panels_data );
		$start_position = strpos( $shortcodes, '[wfacp_forms' );
		if ( false === $start_position ) {
			return false;
		}
		$shortcode_string = substr( $shortcodes, $start_position );
		$closing_position = strpos( $shortcode_string, ']', 1 );
		if ( false === $closing_position ) {
			return false;
		}
		$shortcode_string = substr( $shortcodes, $start_position, $closing_position + 1 );
		if ( strlen( $shortcode_string ) <= 0 ) {
			return false;
		}

		return $shortcode_string;

	}


	public function remove_our_shortcode( $request ) {
		if ( ! function_exists( 'bricks_is_rest_call' ) || ! function_exists( 'bricks_is_ajax_call' ) ) {
			return $request;
		}
		//echo wp_debug_backtrace_summary();
		if ( bricks_is_rest_call() || bricks_is_ajax_call() ) {

			remove_shortcode( 'wfacp_forms' );
			remove_shortcode( 'wfacp_mini_cart' );
		}

		return $request;
	}

	/**
	 * Set default template when bricks theme activated
	 *
	 * @param $page_id
	 *
	 * @return void
	 */
	public function update_page_template( $page_id ) {
		if ( true === $this->is_enabled() && 'bricks' === get_template() ) {
			update_post_meta( $page_id, '_wp_page_template', '' );
		}
	}

}

if ( ! defined( 'BRICKS_VERSION' ) ) {
	return;
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_Theme_Brick(), 'wfacp-brick' );

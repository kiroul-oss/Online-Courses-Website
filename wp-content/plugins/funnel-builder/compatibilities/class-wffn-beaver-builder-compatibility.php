<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WFFN_Compatibility_With_Beaver_Builder
 */
if ( ! class_exists( 'WFFN_Compatibility_With_Beaver_Builder' ) ) {
	class WFFN_Compatibility_With_Beaver_Builder {

		public function __construct() {
			add_filter( 'fl_builder_post_types', array( $this, 'post_types' ) );

		}

		public function is_enable() {
			if ( class_exists( 'FLBuilderLoader' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @param $post_types
		 *
		 * @return mixed
		 */
		public function post_types( $post_types ) {

			$wffn_posts = array(
				'wfacp_checkout',
				'wfocu_offer',
				'wffn_landing',
				'wffn_ty',
				'wffn_optin',
				'wffn_oty',
			);
			$post_types = array_merge( $post_types, $wffn_posts );

			return $post_types;
		}
	}

	WFFN_Plugin_Compatibilities::register( new WFFN_Compatibility_With_Beaver_Builder(), 'beaver_builder' );
}


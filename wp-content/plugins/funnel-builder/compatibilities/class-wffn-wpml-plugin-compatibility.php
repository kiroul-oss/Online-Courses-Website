<?php
//WPML Multilingual CMS
if ( ! class_exists( 'WFFN_Compatibility_With_WPML' ) ) {

	class WFFN_Compatibility_With_WPML {
		public function __construct() {
			add_filter( 'wffn_import_elementor_content', [ $this, 'update_content' ], 10, 1 );
		}

		public function is_enable() {
			if ( class_exists( 'WPML_Elementor_Adjust_Global_Widget_ID' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @param $content
		 *
		 * @return array|mixed|string
		 */
		public function update_content( $content ) {
			if ( $this->is_enable() ) {
				//return content in string format
				$content = wp_slash( wp_json_encode( $content ) );
			}

			return $content;

		}
	}

	WFFN_Plugin_Compatibilities::register( new WFFN_Compatibility_With_WPML(), 'wpml_language' );
}
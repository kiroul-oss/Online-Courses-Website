<?php

/**
 * Class WFFN_Module_Common
 */
if ( ! class_exists( 'WFFN_Module_Common' ) ) {
	class WFFN_Module_Common {
		public $edit_id = 0;

		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'remove_conflicted_themes_styles' ), 9999 );
			add_action( 'wp_print_scripts', array( $this, 'print_custom_css_in_head' ), 1000 );
			add_action( 'wp_footer', array( $this, 'print_custom_js_in_footer' ) );
		}

		public function remove_conflicted_themes_styles() {
			//globally registered styles and scripts
			global $wp_styles;
			global $wp_scripts;
			global $post;

			$get_stylesheet = 'themes/' . get_stylesheet() . '/';
			$get_template   = 'themes/' . get_template() . '/';

			$allowed_post_types = $this->get_post_type_slug();
			$allowed_themes     = apply_filters( 'wffn_allowed_themes', [ 'flatsome', 'Extra', 'Divi', 'astra', 'jupiterx', 'kadence' ] );


			if ( is_null( $post ) || ! $post instanceof WP_Post ) {
				return;
			}
			$post_type = $post->post_type;
			if ( $post_type !== $allowed_post_types ) {
				return;
			}

			$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

			/**
			 * if our templates then prevent CSS and JS
			 */
			if ( false === $this->is_our_template( $page_template ) ) {
				return;
			}

			if ( get_template() === 'Divi' ) {
				wp_enqueue_style( 'wffn-template-style' );
			}


			if ( true === apply_filters( 'wffn_allow_themes_css', false, $post->ID, $this ) ) {
				return;
			}

			// Only Remove CSS & JS For Canvas type template
			if ( in_array( get_template(), $allowed_themes, true ) ) {
				return;
			}


			/**
			 * By default when we prevent theme css we need to dequeue frontend and load only template style which covers minimal
			 */
			if ( ! defined( 'WFFN_IS_DEV' ) || true !== WFFN_IS_DEV ) {
				wp_dequeue_style( 'wffn-frontend-style' );
			}

			wp_enqueue_style( 'wffn-template-style' );


			// Dequeue and deregister all of the registered styles
			foreach ( $wp_styles->registered as $handle => $data ) {

				if ( false !== strpos( $data->src, $get_template ) || false !== strpos( $data->src, $get_stylesheet ) ) {

					wp_deregister_style( $handle );
					wp_dequeue_style( $handle );
				}
			}

			// Dequeue and deregister all of the registered scripts
			foreach ( $wp_scripts->registered as $handle => $data ) {
				if ( false !== strpos( $data->src, $get_stylesheet ) || false !== strpos( $data->src, $get_template ) ) {
					wp_deregister_script( $handle );
					wp_dequeue_script( $handle );
				}
			}
			if ( 'bb-theme' === get_template() && class_exists( 'FLCustomizer' ) ) {
				wp_dequeue_style( 'fl-automator-skin', FLCustomizer::css_url(), array(), FL_THEME_VERSION );
			}
			if ( 'oceanwp' === strtolower( get_template() ) ) {
				$enqu_fa = apply_filters( 'wfocu_enqueue_fa_style', true );
				if ( $enqu_fa ) {
					wp_enqueue_style( 'wfocu-font-awesome', OCEANWP_CSS_DIR_URI . 'third/font-awesome.min.css', false );
				}
			}
			if ( 'porto' === strtolower( get_template() ) ) {
				wp_deregister_script( 'porto-shortcodes' );
				wp_deregister_script( 'porto-bootstrap' );
				wp_deregister_script( 'porto-dynamic-style' );
				wp_dequeue_style( 'porto-shortcodes' );
				wp_dequeue_style( 'porto-bootstrap' );
				wp_dequeue_style( 'porto-dynamic-style' );
				if ( is_rtl() ) { //font-awesome css is written in this css in porto theme
					wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins_rtl.css?ver=' . PORTO_VERSION );
				} else {
					wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins.css?ver=' . PORTO_VERSION );
				}
				wp_enqueue_style( 'porto-plugins' );
			}
		}


		/**
		 * Runs "no conflict mode".
		 *
		 * @param $wp_objects
		 * @param $wp_required_objects
		 * @param $wffn_required_scripts
		 * @param string $type
		 */
		public function no_conflict_mode( &$wp_objects, $wp_required_objects, $wffn_required_scripts, $type = 'scripts', $page = '' ) {

			$current_page = filter_input( INPUT_GET, 'page' );
			if ( is_null( $current_page ) || empty( $current_page ) ) {
				return;
			}
			$current_page = trim( strtolower( $current_page ) );

			if ( $page !== $current_page ) {
				return;
			}
			$section       = filter_input( INPUT_GET, 'section' );
			$page_objects  = isset( $wffn_required_scripts[ $section ] ) ? $wffn_required_scripts[ $section ] : array();
			$more_objects  = isset( $wffn_required_scripts[ $page ] ) ? $wffn_required_scripts[ $page ] : array();
			$total_objects = array_merge( $page_objects, $more_objects );


			if ( ! is_array( $total_objects ) ) {
				$total_objects = array();
			}

			//merging wp scripts with wffn scripts
			$required_objects = array_merge( $wp_required_objects, $wffn_required_scripts['common'], $total_objects );

			//allowing addons or other products to change the list of no conflict scripts
			$required_objects = apply_filters( "wffn_noconflict_{$type}", $required_objects );

			$queue = array();
			foreach ( $wp_objects->queue as $object ) {
				if ( in_array( $object, $required_objects, true ) ) {
					$queue[] = $object;
				}
			}
			$wp_objects->queue = $queue;

			$required_objects = $this->add_script_dependencies( $wp_objects->registered, $required_objects );

			//unregistering scripts
			$registered = array();
			foreach ( $wp_objects->registered as $script_name => $script_registration ) {
				if ( in_array( $script_name, $required_objects, true ) ) {
					$registered[ $script_name ] = $script_registration;
				}
			}

			$wp_objects->registered = $registered;
		}

		/**
		 * Adds script dependencies needed.
		 *
		 * @param $registered
		 * @param $scripts
		 *
		 * @return array
		 */
		public function add_script_dependencies( $registered, $scripts ) {

			//gets all dependent scripts linked to the $scripts array passed
			do {
				$dependents = array();
				foreach ( $scripts as $script ) {
					$deps = isset( $registered[ $script ] ) && is_array( $registered[ $script ]->deps ) ? $registered[ $script ]->deps : array();
					foreach ( $deps as $dep ) {
						if ( ! in_array( $dep, $scripts, true ) && ! in_array( $dep, $dependents, true ) ) {
							$dependents[] = $dep;
						}
					}
				}
				$scripts = array_merge( $scripts, $dependents );
			} while ( ! empty( $dependents ) );

			return $scripts;
		}

		public function get_supported_permalink_strcutures_to_normalize() {
			return array( '/%postname%/' );
		}

		public function setup_custom_options( $id = 0 ) {
			$module_id = empty( $id ) ? $this->edit_id : $id;

			$db_options = get_post_meta( $module_id, 'wffn_step_custom_settings', true );

			$db_options = ( ! empty( $db_options ) && is_array( $db_options ) ) ? array_map( function ( $val ) {
				return is_scalar( $val ) ? html_entity_decode( $val ) : $val;
			}, $db_options ) : array();

			$this->custom_options = wp_parse_args( $db_options, $this->default_custom_settings() );

			return $this->custom_options;
		}

		public function print_custom_css_in_head() {
			global $post;

			if ( ( ! empty( $post ) && $post->post_type === $this->get_post_type_slug() ) ) {
				$this->setup_custom_options( $post->ID );
				printf( '<style>%s</style>', esc_html( $this->get_custom_option( 'custom_css' ) ) );
				printf( '<style>%s</style>', esc_html( $this->get_option( 'css' ) ) );
			}

		}

		public function print_custom_js_in_footer() {
			global $post;

			if ( ( ! empty( $post ) && $post->post_type === $this->get_post_type_slug() ) ) {
				$this->setup_custom_options( $post->ID );
				echo html_entity_decode( $this->get_custom_option( 'custom_js' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo html_entity_decode( $this->get_option( 'script' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * @param $template template from postmeta
		 *
		 * @return bool
		 */
		public function is_our_template( $template ) {
			if ( false !== strpos( $template, '-canvas.php' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @param $post_id
		 *
		 * add ct_inner in oxygen url
		 *
		 *
		 * @return string
		 */
		public function check_oxy_inner_content( $post_ID ) {
			// Get post template
			$post_template = intval( get_post_meta( $post_ID, 'ct_other_template', true ) );
			// Check if we should edit the post or it's template
			$post_editable  = false;
			$template_inner = false;
			if ( $post_template === 0 ) { // default template
				// Get default template
				$default_template = null;
				if ( get_option( 'page_for_posts' ) == $post_ID || get_option( 'page_on_front' ) == $post_ID ) { //phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					$default_template = ct_get_archives_template( $post_ID );
				}
				if ( empty( $default_template ) ) {
					$default_template = ct_get_posts_template( $post_ID );
				}
				if ( $default_template ) {
					$shortcodes = get_post_meta( $default_template->ID, 'ct_builder_shortcodes', true );
					if ( $shortcodes && strpos( $shortcodes, '[ct_inner_content' ) !== false ) {
						$post_editable  = true;
						$template_inner = true;
					}
				} else {
					$post_editable = true;
				}
			} else if ( $post_template == - 1 ) { //phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$post_editable = true;
			} else { // Custom template
				$shortcodes = get_post_meta( $post_template, 'ct_builder_shortcodes', true );
				if ( $shortcodes && strpos( $shortcodes, '[ct_inner_content' ) !== false ) {
					$post_editable  = true;
					$template_inner = true;
				}
			}
			$edit_link_href = '';
			// Generate edit link
			if ( $post_editable ) {
				if ( $template_inner ) {
					$edit_link_href = '&ct_inner=true';
				}
			}

			return $edit_link_href;
		}
	}
}
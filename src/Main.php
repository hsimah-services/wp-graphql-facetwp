<?php
/**
 * Initializes a singleton instance of the plugin.
 *
 * @package WPGraphQL\FacetWP
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP;

use WPGraphQL\FacetWP\Registry\TypeRegistry;

if ( ! class_exists( 'WPGraphQL\FacetWP\Main' ) ) :

	/**
	 * Class - Main
	 */
	final class Main {
		/**
		 * Class instances.
		 *
		 * @var ?self $instance
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		public static function instance() : self {
			if ( ! isset( self::$instance ) || ! self::$instance instanceof self ) {
				// @codeCoverageIgnoreStart
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				// @codeCoverageIgnoreEnd
				self::$instance = new self();
				self::$instance->includes();
				self::$instance->setup();
			}

			/**
			 * Fire off init action.
			 *
			 * @param self $instance the instance of the plugin class.
			 */
			do_action( 'graphql_facetwp_init', self::$instance );

			return self::$instance;
		}

		/**
		 * Includes the required files with Composer's autoload.
		 *
		 * @codeCoverageIgnore
		 */
		private function includes() : void {
			if ( defined( 'WPGRAPHQL_FACETWP_AUTOLOAD' ) && false !== WPGRAPHQL_FACETWP_AUTOLOAD && defined( 'WPGRAPHQL_FACETWP_PLUGIN_DIR' ) ) {
				require_once WPGRAPHQL_FACETWP_PLUGIN_DIR . 'vendor/autoload.php';
			}
		}

		/**
		 * Sets up the schema.
		 *
		 * @codeCoverageIgnore
		 */
		private function setup() : void {
			// Initialize plugin type registry.
			TypeRegistry::init();
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @codeCoverageIgnore
		 *
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The WPGraphQL\FacetWP\Main class should not be cloned.', 'wpgraphql-facetwp' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @codeCoverageIgnore
		 */
		public function __wakeup() : void {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL\FacetWP\Main class is not allowed', 'wpgraphql-facetwp' ), '0.0.1' );
		}
	}
endif;

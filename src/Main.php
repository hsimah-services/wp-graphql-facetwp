<?php
/**
 * Initializes a singleton instance of the plugin.
 *
 * @package WPGraphQL\FacetWP
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP;

use WPGraphQL\FacetWP\Registry\TypeRegistry;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Helper\Helper;

if ( ! class_exists( 'WPGraphQL\FacetWP\Main' ) ) :

	/**
	 * Class - Main
	 */
	final class Main {
		/**
		 * Class instance.
		 *
		 * @var ?self $instance
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		public static function instance(): self {
			if ( ! isset( self::$instance ) || ! self::$instance instanceof self ) {
				self::$instance = new self();
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
		 * Sets up the schema.
		 *
		 * @codeCoverageIgnore
		 */
		private function setup(): void {
			// Setup boilerplate hook prefix.
			Helper::set_hook_prefix( 'graphql_facetwp' );

			// Initialize plugin type registry.
			TypeRegistry::init();
			CoreSchemaFilters::init();
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
		public function __wakeup(): void {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL\FacetWP\Main class is not allowed', 'wpgraphql-facetwp' ), '0.0.1' );
		}
	}
endif;

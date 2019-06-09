<?php
/**
 * Plugin Name: WP GraphQL FacetWP
 * Plugin URI: https://github.com/hsimah/wp-graphql-facetwp
 * Description: WP GraphQL provider for FacetWP
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.0.1
 * Text Domain: wpgraphql-facetwp
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_FacetWP
 * @author   hsimah
 * @version  0.0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WPGraphQL does not exist
if ( ! class_exists( 'WPGraphQL' ) ) {
    exit;
}

// Exit if FWP does not exist
if ( ! class_exists( 'FWP' ) ) {
    exit;
}

if ( ! class_exists( 'WPGraphQL_FacetWP' ) ) :
    final class WPGraphQL_FacetWP {
        
        /**
		 * Stores the instance of the WPGraphQL_FacetWP class
		 *
		 * @var WPGraphQL_FacetWP The one true WPGraphQL_FacetWP
		 * @since  0.0.1
		 * @access private
		 */
        private static $instance;
        
		/**
		 * The instance of the WPGraphQL_FacetWP object
		 *
		 * @return object|WPGraphQL_FacetWP - The one true WPGraphQL_FacetWP
		 * @since  0.0.1
		 * @access public
		 */
        public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPGraphQL_FacetWP ) ) {
                self::$instance = new WPGraphQL_FacetWP();
                self::$instance->init();
			}

			/**
			 * Return the WPGraphQL_FacetWP Instance
			 */
			return self::$instance;
        }
        
		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
        public function __clone() {

			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The WPGraphQL_FacetWP class should not be cloned.', 'wpgraphql-facetwp' ), '0.0.1' );

		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  0.0.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {

			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL_FacetWP class is not allowed', 'wpgraphql-facetwp' ), '0.0.1' );

        }
        
        /**
		 * Initialise plugin.
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function init() {
            // code go here
        }
    }

endif;

if ( ! function_exists( 'wpgraphql_facetwp_init' ) ) {
	/**
	 * Function that instantiates the plugins main class
	 *
	 * @since 0.0.1
	 */
	function wpgraphql_facetwp_init() {

		/**
		 * Return an instance of the action
		 */
		return \WPGraphQL_FacetWP::instance();
	}
}
wpgraphql_facetwp_init();

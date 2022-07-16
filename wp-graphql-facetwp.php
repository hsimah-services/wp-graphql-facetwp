<?php
/**
 * Plugin Name: WP GraphQL FacetWP
 * Plugin URI: https://github.com/hsimah-services/wp-graphql-facetwp
 * Description: WP GraphQL provider for FacetWP
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.3.0
 * Text Domain: wpgraphql-facetwp
 * Requires at least: 5.4.1
 * Requires PHP: 7.1
 * WPGraphQL requires at least: 1.0.4
 * FacetWP requires at least: 3.5.7
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_FacetWP
 * @author   hsimah
 * @license GPL-3
 * @version  0.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If the codeception remote coverage file exists, require it.
// This file should only exist locally or when CI bootstraps the environment for testing.
if ( file_exists( __DIR__ . '/c3.php' ) ) {
	require_once __DIR__ . '/c3.php';
}

if ( ! function_exists( 'graphql_facetwp_constants' ) ) {
	/**
	 * Define plugin constants.
	 */
	function graphql_facetwp_constants() : void {
			// Plugin version.
		if ( ! defined( 'WPGRAPHQL_FACETWP_VERSION' ) ) {
			define( 'WPGRAPHQL_FACETWP_VERSION', '0.3.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WPGRAPHQL_FACETWP_PLUGIN_DIR' ) ) {
			define( 'WPGRAPHQL_FACETWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WPGRAPHQL_FACETWP_PLUGIN_URL' ) ) {
			define( 'WPGRAPHQL_FACETWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'WPGRAPHQL_FACETWP_PLUGIN_FILE' ) ) {
			define( 'WPGRAPHQL_FACETWP_PLUGIN_FILE', __FILE__ );
		}

		// Whether to autoload the files or not.
		if ( ! defined( 'WPGRAPHQL_FACETWP_AUTOLOAD' ) ) {
			define( 'WPGRAPHQL_FACETWP_AUTOLOAD', true );
		}
	}
}

if ( ! function_exists( 'graphql_facetwp_deps_not_ready' ) ) {
	/**
	 * Checks if all the the required plugins are installed and activated.
	 *
	 * @return array<class-string, string> The list of missing dependencies.
	 */
	function graphql_facetwp_deps_not_ready() : array {
		$wpgraphql_version = '1.0.4';
		$facetwp_version   = '3.5.7';

		$deps = [];

		if ( ! class_exists( 'WPGraphQL' ) || ( defined( 'WPGRAPHQL_VERSION' ) && version_compare( WPGRAPHQL_VERSION, $wpgraphql_version, '<' ) ) ) {
			$deps['WPGraphQL'] = $wpgraphql_version;
		}

		if ( ! class_exists( 'FacetWP' ) || defined( 'FACETWP_VERSION' ) && version_compare( FACETWP_VERSION, $facetwp_version, '<' ) ) {
			$deps['FacetWP'] = $facetwp_version;
		}

		return $deps;
	}
}

if ( ! function_exists( 'graphql_facetwp_init' ) ) {
	/**
	 * Initializes the plugin.
	 *
	 * @return \WPGraphQL\FacetWP\Main|false
	 */
	function graphql_facetwp_init() {
		graphql_facetwp_constants();

		$not_ready = graphql_facetwp_deps_not_ready();

		if ( empty( $not_ready ) && defined( 'WPGRAPHQL_FACETWP_PLUGIN_DIR' ) ) {
			require_once WPGRAPHQL_FACETWP_PLUGIN_DIR . 'src/Main.php';
			return \WPGraphQL\FacetWP\Main::instance();
		}

		/**
		 * For users with lower capabilities, don't show the notice.
		 *
		 * @todo Are we sure we don't what to tell all users with backend access that the plugin isnt working?
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		foreach ( $not_ready as $dep => $version ) {
			add_action(
				'admin_notices',
				function() use ( $dep, $version ) {
					?>
					<div class="error notice">
						<p>
							<?php
								printf(
									/* translators: dependency not ready error message */
									esc_html__( '%1$s (v%2$s) must be active for WPGraphQL Plugin Name to work.', 'wpgraphql-facetwp' ),
									esc_attr( $dep ),
									esc_attr( $version )
								);
							?>
						</p>
					</div>

					<?php
				}
			);
		}

		return false;
	}
}

add_action( 'graphql_init', 'graphql_facetwp_init' );


add_filter(
	'facetwp_graphql_facet_connection_config',
	function ( array $default_graphql_config ) {
		return $default_graphql_config;
	},
	10,
	1
);

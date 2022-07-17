<?php
/**
 * Plugin Name: WP GraphQL FacetWP
 * Plugin URI: https://github.com/hsimah-services/wp-graphql-facetwp
 * Description: WP GraphQL provider for FacetWP
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.3.0.1
 * Text Domain: wpgraphql-facetwp
 * Requires at least: 5.4.1
 * Requires PHP: 7.1
 * WPGraphQL requires at least: 1.0.4
 * FacetWP requires at least: 3.5.7
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPGraphQL_FacetWP
 * @author  hsimah
 * @license GPL-3
 * @version 0.3.0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		if ( class_exists( 'FacetWP' ) && class_exists( 'WPGraphQL' ) ) {
			require_once __DIR__ . '/class-facetwp.php';
		}
	},
	5
);

add_action(
	'admin_init',
	function () {
		$versions = [
			'wp-graphql' => '1.0.4',
			'facetwp'    => '3.5.7',
		];

		if (
		! class_exists( 'FacetWP' ) ||
		! class_exists( 'WPGraphQL' ) ||
		( defined( 'WPGRAPHQL_VERSION' ) && version_compare( WPGRAPHQL_VERSION, $versions['wp-graphql'], 'lt' ) ) ||
		( defined( 'FACETWP_VERSION' ) && version_compare( FACETWP_VERSION, $versions['facetwp'], 'lt' ) )
		) {

			/**
			 * For users with lower capabilities, don't show the notice
			 */
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			/**
			 * Show admin notice to admins if this plugin is active but either FacetWP and/or WPGraphQL
			 * are not active
			 *
			 * @return bool
			 */
			add_action(
				'admin_notices',
				function () use ( $versions ) {
					?>
			<div class="error notice">
				<p>
						<?php
						printf(
								// translators: Plugin versions.
							esc_html__( 'Both WPGraphQL (%1$s) and FacetWP (%2$s) must be active for "wp-graphql-facetwp" to work.', 'wpgraphql-facetwp' ),
							esc_attr( $versions['wp-graphql'] ),
							esc_attr( $versions['facetwp'] )
						);
						?>
				</p>
			</div>
					<?php
				}
			);

			return false;
		}
	}
);

add_filter(
	'facetwp_graphql_facet_connection_config',
	function ( array $default_graphql_config ) {
		return $default_graphql_config;
	},
	10,
	1
);

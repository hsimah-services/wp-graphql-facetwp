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
 * @version  0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPGraphQL_FacetWP' ) ) {

    add_action( 'admin_init', 'show_admin_notice' );

	if ( class_exists( 'FacetWP' ) && class_exists( 'WPGraphQL' ) )
		require_once __DIR__ . '/class-facetwp.php';

}


/**
 * Show admin notice to admins if this plugin is active but either FacetWP and/or WPGraphQL
 * are not active
 *
 * @return bool
 */
function show_admin_notice() {

    $wp_graphql_required_min_version = '0.3.2';
    
	if ( ! class_exists( 'FacetWP' ) || ! class_exists( 'WPGraphQL' ) || ( defined( 'WPGRAPHQL_VERSION' ) && version_compare( WPGRAPHQL_VERSION, $wp_graphql_required_min_version, 'lt' ) ) ) {

		/**
		 * For users with lower capabilities, don't show the notice
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		add_action(
			'admin_notices',
			function() use ( $wp_graphql_required_min_version ) {
				?>
			<div class="error notice">
				<p>
					<?php _e( sprintf('Both WPGraphQL (v%s+) and FacetWP (v3.3.9) must be active for "wp-graphql-facetwp" to work', $wp_graphql_required_min_version ), 'wpgraphiql-facetwp' ); ?>
				</p>
			</div>
				<?php
			}
		);

		return false;
	}
}
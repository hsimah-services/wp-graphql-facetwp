<?php

/**
 * Plugin Name: WP GraphQL FacetWP
 * Plugin URI: https://github.com/hsimah/wp-graphql-facetwp
 * Description: WP GraphQL provider for FacetWP
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.1.1
 * Text Domain: wpgraphql-facetwp
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_FacetWP
 * @author   hsimah
 * @version  0.1.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

add_action('init', function () {
	if (class_exists('FacetWP') && class_exists('WPGraphQL')) {
		require_once __DIR__ . '/class-facetwp.php';
	}
}, 5);

add_action('admin_init', function () {
	$versions = [
		'wp-graphql' => '0.8.1',
		'facetwp' => '3.5.2.1',
	];

	if (
		!class_exists('FacetWP') ||
		!class_exists('WPGraphQL') ||
		(defined('WPGRAPHQL_VERSION') && version_compare(WPGRAPHQL_VERSION, $versions['wp-graphql'], 'lt')) ||
		(defined('FACETWP_VERSION') && version_compare(FACETWP_VERSION, $versions['facetwp'], 'lt'))
	) {

		/**
		 * For users with lower capabilities, don't show the notice
		 */
		if (!current_user_can('manage_options')) {
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
			function () use ($versions) {
?>
			<div class="error notice">
				<p>
					<?php _e(vsprintf('Both WPGraphQL (v%s+) and FacetWP (v%s+) must be active for "wp-graphql-facetwp" to work', $versions), 'wpgraphql-facetwp'); ?>
				</p>
			</div>
<?php
			}
		);

		return false;
	}
});

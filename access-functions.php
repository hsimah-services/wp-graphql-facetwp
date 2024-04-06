<?php
/**
 * This file contains access functions for various class methods.
 *
 * @package WPGraphQL/FacetWP
 * @since 0.4.0
 */

use WPGraphQL\FacetWP\Registry\FacetRegistry;

/**
 * Get an option value from the plugin settings.
 *
 * @param string $option_name The key of the option to return.
 * @param mixed  $default The default value the setting should return if no value is set.
 * @param string $section_name The settings group section that the option belongs to.
 *
 * @return mixed
 */

if ( ! function_exists( 'register_graphql_facet_type' ) ) {
	/**
	 * Register a post type as a FacetWP queryable
	 *
	 * @param string $type_name The name of the WP object type to register.
	 */
	function register_graphql_facet_type( string $type_name ): void {
		add_action(
			get_graphql_register_action(),
			static function () use ( $type_name ) {
				FacetRegistry::register( $type_name );
			}
		);
	}
}

if ( ! function_exists( 'get_graphql_allowed_facets' ) ) {
	/**
	 * Get the facets that are allowed to be queried via GraphQL.
	 *
	 * @return array<string,mixed>
	 *
	 * @since 0.4.1
	 */
	function get_graphql_allowed_facets(): array {
		return FacetRegistry::get_allowed_facets();
	}
}

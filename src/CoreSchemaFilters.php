<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\FacetWP
 * @since   @todo
 */

namespace WPGraphQL\FacetWP;

use WPGraphQL\FacetWP\Interfaces\Initializable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Initializable {
	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		// Prefix the GraphQL type names.
		add_filter( 'graphql_login_type_prefix', [ __CLASS__, 'get_type_prefix' ] );
	}

	/**
	 * Don't prefix type names.
	 */
	public static function get_type_prefix() : string {
		return '';
	}
}

<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\FacetWP
 * @since   @todo
 */

namespace WPGraphQL\FacetWP;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		// Prefix the GraphQL type names.
		add_filter( 'graphql_facetwp_type_prefix', [ __CLASS__, 'get_type_prefix' ] );
	}

	/**
	 * Don't prefix type names.
	 */
	public static function get_type_prefix() : string {
		return '';
	}
}

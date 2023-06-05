<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\FacetWP
 * @since   0.4.1
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
	public static function init(): void {
		// Prefix the GraphQL type names.
		add_filter( 'graphql_facetwp_type_prefix', [ self::class, 'get_type_prefix' ] );
	}

	/**
	 * Don't prefix type names.
	 */
	public static function get_type_prefix(): string {
		return '';
	}
}

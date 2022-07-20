<?php
/**
 * Interface for classes that register a GraphQL type to the GraphQL schema.
 *
 * @package WPGraphQL\FacetWP\Interfaces
 * @since @todo
 */

namespace WPGraphQL\FacetWP\Interfaces;

use WPGraphQL\FacetWP\Interfaces\Initializable;

/**
 * Interface - GraphQLType
 */
interface GraphQLType extends Initializable {
	/**
	 * Register connections to the GraphQL Schema.
	 */
	public static function register() : void;

	/**
	 * Gets the WPGraphQL config array for registering the type.
	 */
	public static function get_config() : array;
}

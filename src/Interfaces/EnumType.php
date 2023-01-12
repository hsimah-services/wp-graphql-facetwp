<?php
/**
 * Interface for a GraphQL EnumType.
 *
 * @package WPGraphQL\FacetWP\Interfaces
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP\Interfaces;

/**
 * Interface - EnumType.
 */
interface EnumType extends GraphQLType, TypeWithDescription {
	/**
	 * Gets the enum values for the type.
	 */
	public static function get_values() : array;
}

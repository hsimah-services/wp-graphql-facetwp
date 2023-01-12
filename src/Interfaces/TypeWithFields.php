<?php
/**
 * Interface for a GraphQL TypeWithFields.
 *
 * @package WPGraphQL\FacetWP\Interfaces
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP\Interfaces;

/**
 * Interface - TypeWithFields.
 */
interface TypeWithFields {
	/**
	 * Gets the properties for the type.
	 */
	public static function get_fields() : array;
}

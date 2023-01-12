<?php
/**
 * Interface for a GraphQL TypeWithDescription.
 *
 * @package WPGraphQL\FacetWP\Interfaces
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP\Interfaces;

/**
 * Interface - TypeWithDescription.
 */
interface TypeWithDescription {
	/**
	 * Gets the properties for the type.
	 */
	public static function get_description() : string;
}

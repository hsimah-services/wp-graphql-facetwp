<?php
/**
 * Abstract GraphQL Type.
 *
 * @package WPGraphQL\FacetWP\Type
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type;

use WPGraphQL\FacetWP\Interfaces\GraphQLType;

/**
 * Class - AbstractType
 */
abstract class AbstractType implements GraphQLType {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type;

	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_action( get_graphql_register_action(), [ static::class, 'register' ] );
	}

}

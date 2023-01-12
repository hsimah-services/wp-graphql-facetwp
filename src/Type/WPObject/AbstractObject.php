<?php
/**
 * Abstract GraphQL Object Type.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Interfaces\GraphQLType;
use WPGraphQL\FacetWP\Interfaces\TypeWithDescription;
use WPGraphQL\FacetWP\Interfaces\TypeWithFields;
use WPGraphQL\FacetWP\Type\AbstractType;

/**
 * Class - AbstractObject
 */
abstract class AbstractObject extends AbstractType implements TypeWithFields, TypeWithDescription {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type;

	/**
	 * Whether the type should be loaded eagerly by WPGraphQL. Defaults to false.
	 *
	 * Eager load should only be necessary for types that are not referenced directly (e.g. in Unions, Interfaces ).
	 *
	 * @var boolean
	 */
	public static $should_load_eagerly = false;

	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_action( get_graphql_register_action(), [ static::class, 'register' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register() : void {
		$config = static::get_config();

		register_graphql_object_type( static::$type, $config );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_config() : array {
		return [
			'description'     => static::get_description(),
			'fields'          => static::get_fields(),
			'eagerlyLoadType' => static::$should_load_eagerly,
		];
	}
}

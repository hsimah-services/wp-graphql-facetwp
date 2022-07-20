<?php
/**
 * Abstract GraphQL Input Type.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Interfaces\GraphQLType;
use WPGraphQL\FacetWP\Interfaces\TypeWithDescription;
use WPGraphQL\FacetWP\Interfaces\TypeWithFields;

/**
 * Class - AbstractInput
 */
abstract class AbstractInput implements GraphQLType, TypeWithFields, TypeWithDescription {
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

		register_graphql_input_type( static::$type, $config );
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

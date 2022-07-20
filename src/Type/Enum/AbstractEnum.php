<?php
/**
 * Abstract GraphQL Enum Type.
 *
 * @package WPGraphQL\FacetWP\Type\Enum
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\Enum;

use WPGraphQL\FacetWP\Interfaces\EnumType;

/**
 * Class - AbstractEnum
 */
abstract class AbstractEnum implements EnumType {
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

	/**
	 * {@inheritDoc}
	 */
	public static function register() : void {
		$config = static::get_config();

		register_graphql_enum_type( static::$type, $config );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_config() : array {
		return [
			'description' => static::get_description(),
			'values'      => static::get_values(),
		];
	}
}

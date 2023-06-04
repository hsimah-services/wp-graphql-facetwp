<?php
/**
 * GraphQL Enum Type - SortOptionsEnum.
 *
 * @package WPGraphQL\FacetWP\Type\Enum
 * @since   0.4.3
 */

namespace WPGraphQL\FacetWP\Type\Enum;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\GraphQLType;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - SortOptionsEnum
 */
class SortOptionsEnum implements GraphQLType, Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_action( 'graphql_register_types', [ static::class, 'register' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register() : void {
		$allowed_facets = get_graphql_allowed_facets();

		// Get the facets where the type is 'sort'.
		$sort_facets = array_filter(
			$allowed_facets,
			static function ( $facet ) {
				return 'sort' === $facet['type'];
			}
		);

		foreach ( $sort_facets as $facet ) {
			self::register_enum( $facet );
		}
	}

	/**
	 * Registers the enum type for the provided facet.
	 *
	 * @param array $facet The facet to register the enum type for.
	 */
	public static function register_enum( array $facet ) : string {
		$name = self::get_type_name( $facet['name'] );

		$sort_options = $facet['sort_options'] ?: [];

		$values = [];

		// Go through the sort facets to generate the values.
		foreach ( $sort_options as $option ) {
			if ( empty( $option['name'] ) ) {
				continue;
			}

			$values[ WPEnumType::get_safe_name( $option['name'] ) ] = [
				'value'       => $option['name'],
				'description' => sprintf(
					// translators: %s is the label of the sort option.
					__( 'Sort by %s', 'wpgraphql-facetwp' ),
					$option['label']
				),
			];
		}

		// Register the enum type.
		register_graphql_enum_type(
			$name,
			[
				'description' => sprintf(
					// translators: %s is the label of the facet.
					__( 'Sort options for %s facet.', 'wpgraphql-facetwp' ),
					$facet['label']
				),
				'values'      => $values,
			]
		);

		return $name;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $name The name of the facet.
	 */
	public static function get_type_name( string $name ) : string {
		return 'FacetSortOptions' . graphql_format_type_name( $name ) . 'Enum';
	}
}

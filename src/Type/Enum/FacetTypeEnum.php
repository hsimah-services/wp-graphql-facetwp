<?php
/**
 * GraphQL Enum Type - FacetTypeEnum.
 *
 * @package WPGraphQL\FacetWP\Type\Enum
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\Enum;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\EnumType;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - FacetTypeEnum
 */
class FacetTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'FacetTypeEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$facet_types = FWP()->helper->get_facet_types();

		$values = [];

		foreach ( $facet_types as $key => $obj ) {
			$values[ WPEnumType::get_safe_name( $key ) ] = [
				'description' => sprintf(
					// translators: %s is the facet type.
					__( 'The %s facet type', 'wpgraphql-facetwp' ),
					$obj->label,
				),
				'value'       => $key,
			];
		}

		return $values;
	}
}

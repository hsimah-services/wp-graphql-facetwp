<?php
/**
 * GraphQL Object Type - Facet.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Type\WPInterface\FacetConfig;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - Facet
 */
class Facet extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'Facet';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Active FacetWP payload', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'selected' => [
				'type'        => [
					'list_of' => 'String',
				],
				'description' => __( 'Selected values', 'wpgraphql-facetwp' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			FacetConfig::get_type_name(),
		];
	}
}

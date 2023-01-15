<?php
/**
 * GraphQL Object Type - Facet.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Facet
 */
class Facet extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name() : string {
		return 'Facet';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Active FacetWP payload', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'choices'  => [
				'type'        => [
					'list_of' => FacetChoice::get_type_name(),
				],
				'description' => __( 'Facet choices', 'wpgraphql-facetwp' ),
			],
			'label'    => [
				'type'        => 'String',
				'description' => __( 'Facet label', 'wpgraphql-facetwp' ),
			],
			'name'     => [
				'type'        => 'String',
				'description' => __( 'Facet name', 'wpgraphql-facetwp' ),
			],
			'selected' => [
				'type'        => [
					'list_of' => 'String',
				],
				'description' => __( 'Selected values', 'wpgraphql-facetwp' ),
			],
			'settings' => [
				'type'        => FacetSettings::get_type_name(),
				'description' => __( 'Facet settings', 'wpgraphql-facetwp' ),
			],
			'type'     => [
				'type'        => 'String',
				'description' => __( 'Facet type', 'wpgraphql-facetwp' ),
			],
		];
	}
}

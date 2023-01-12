<?php
/**
 * GraphQL Object Type - Facet.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Type\WPObject\AbstractObject;

/**
 * Class - Facet
 */
class Facet extends AbstractObject {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'Facet';

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
					'list_of' => FacetChoice::$type,
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
				'type'        => FacetSettings::$type,
				'description' => __( 'Facet settings', 'wpgraphql-facetwp' ),
			],
			'type'     => [
				'type'        => 'String',
				'description' => __( 'Facet type', 'wpgraphql-facetwp' ),
			],
		];
	}
}

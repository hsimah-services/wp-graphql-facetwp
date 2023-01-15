<?php
/**
 * GraphQL Object Type - FacetRangeSettings.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FacetRangeSettings
 */
class FacetRangeSettings extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name() : string {
		return 'FacetRangeSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Range settings for Slider facets', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'max' => [
				'type'        => 'Float',
				'description' => __( 'Slider max value', 'wpgraphql-facetwp' ),
			],
			'min' => [
				'type'        => 'Float',
				'description' => __( 'Slider min value', 'wpgraphql-facetwp' ),
			],
		];
	}
}

<?php
/**
 * GraphQL Object Type - FacetRangeSettings.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Type\WPObject\AbstractObject;

/**
 * Class - FacetRangeSettings
 */
class FacetRangeSettings extends AbstractObject {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetRangeSettings';

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

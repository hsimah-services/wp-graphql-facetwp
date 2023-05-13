<?php
/**
 * GraphQL Object Type - FacetSortOptionOrderBySetting.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.3
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FacetSortOptionOrderBySetting
 */
class FacetSortOptionOrderBySetting extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name() : string {
		return 'FacetSortOptionOrderBySetting';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The individual Order By setting', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'key'   => [
				'type'        => 'String',
				'description' => __( 'The orderby key.', 'wpgraphql-facetwp' ),
			],
			'order' => [
				'type'        => 'OrderEnum',
				'description' => __( 'Sort order', 'wpgraphql-facetwp' ),
			],
		];
	}
}

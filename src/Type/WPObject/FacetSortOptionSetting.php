<?php
/**
 * GraphQL Object Type - FacetSortOptionSetting.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.3
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FacetSortOptionSetting
 */
class FacetSortOptionSetting extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetSortOptionSetting';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Sort Options setting for Sort facets', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'label'   => [
				'type'        => 'String',
				'description' => __( 'Sort option label.', 'wpgraphql-facetwp' ),
			],
			'name'    => [
				'type'        => 'String',
				'description' => __( 'Sort option name', 'wpgraphql-facetwp' ),
			],
			'orderby' => [
				'type'        => [ 'list_of' => FacetSortOptionOrderBySetting::get_type_name() ],
				'description' => __( 'The orderby rules for the sort option', 'wpgraphql-facetwp' ),
			],
		];
	}
}

<?php
/**
 * GraphQL Object Type - FacetSettings.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FacetSettings
 */
class FacetSettings extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Union of possible Facet settings', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'overflowText'       => [
				'type'        => 'String',
				'description' => __( 'Overflow text', 'wpgraphql-facetwp' ),
			],
			'placeholder'        => [
				'type'        => 'String',
				'description' => __( 'Placeholder text', 'wpgraphql-facetwp' ),
			],
			'autoRefresh'        => [
				'type'        => 'String',
				'description' => __( 'Auto refresh', 'wpgraphql-facetwp' ),
			],
			'decimalSeparator'   => [
				'type'        => 'String',
				'description' => __( 'Decimal separator', 'wpgraphql-facetwp' ),
			],
			'format'             => [
				'type'        => 'String',
				'description' => __( 'Date format', 'wpgraphql-facetwp' ),
			],
			'noResultsText'      => [
				'type'        => 'String',
				'description' => __( 'No results text', 'wpgraphql-facetwp' ),
			],
			'operator'           => [
				'type'        => 'String',
				'description' => __( 'Operator', 'wpgraphql-facetwp' ),
			],
			'prefix'             => [
				'type'        => 'String',
				'description' => __( 'Field prefix', 'wpgraphql-facetwp' ),
			],
			'range'              => [
				'type'        => FacetRangeSettings::get_type_name(),
				'description' => __( 'Selected slider range values', 'wpgraphql-facetwp' ),
			],
			'searchText'         => [
				'type'        => 'String',
				'description' => __( 'Search text', 'wpgraphql-facetwp' ),
			],
			'showExpanded'       => [
				'type'        => 'String',
				'description' => __( 'Show expanded facet options', 'wpgraphql-facetwp' ),
			],
			'start'              => [
				'type'        => FacetRangeSettings::get_type_name(),
				'description' => __( 'Starting min and max position for the slider', 'wpgraphql-facetwp' ),
			],
			'step'               => [
				'type'        => 'Int',
				'description' => __( 'The amount of increase between intervals', 'wpgraphql-facetwp' ),
			],
			'suffix'             => [
				'type'        => 'String',
				'description' => __( 'Field suffix', 'wpgraphql-facetwp' ),
			],
			'thousandsSeparator' => [
				'type'        => 'String',
				'description' => __( 'Thousands separator', 'wpgraphql-facetwp' ),
			],
			'defaultLabel'       => [
				'type'        => 'String',
				'description' => __( 'Default label', 'wpgraphql-facetwp' ),
			],
			'sortOptions'        => [
				'type'        => [ 'list_of' => FacetSortOptionSetting::get_type_name() ],
				'description' => __( 'Sort options', 'wpgraphql-facetwp' ),
			],
		];
	}
}

<?php
/**
 * GraphQL Input Type - DateRangeArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - DateRangeArgs
 */
class DateRangeArgs extends InputType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetDateRangeArgs';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Input args for Date Range facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'max' => [
				'type'        => 'String',
				'description' => __( 'The end date', 'wpgraphql-facetwp' ),
			],
			'min' => [
				'type'        => 'String',
				'description' => __( 'The start date', 'wpgraphql-facetwp' ),
			],
		];
	}
}

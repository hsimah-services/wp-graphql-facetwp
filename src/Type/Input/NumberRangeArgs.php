<?php
/**
 * GraphQL Input Type - NumberRangeArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - NumberRangeArgs
 */
class NumberRangeArgs extends InputType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetNumberRangeArgs';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Input args for Number Range facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'max' => [
				'type'        => 'Int',
				'description' => __( 'Maximum value', 'wpgraphql-facetwp' ),
			],
			'min' => [
				'type'        => 'Int',
				'description' => __( 'Minimum value', 'wpgraphql-facetwp' ),
			],
		];
	}
}

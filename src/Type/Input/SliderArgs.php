<?php
/**
 * GraphQL Input Type - SliderArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - SliderArgs
 */
class SliderArgs extends InputType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetSliderArgs';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Input args for Slider facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'max' => [
				'type'        => 'Float',
				'description' => __( 'Maximum value', 'wpgraphql-facetwp' ),
			],
			'min' => [
				'type'        => 'Float',
				'description' => __( 'Minimum value', 'wpgraphql-facetwp' ),
			],
		];
	}
}

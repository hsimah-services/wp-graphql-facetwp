<?php
/**
 * GraphQL Input Type - SliderArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Input\AbstractInput;

/**
 * Class - SliderArgs
 */
class SliderArgs extends AbstractInput {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetSliderArgs';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Input args for Slider facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'max' => [
				'type' => 'Float',
			],
			'min' => [
				'type' => 'Float',
			],
		];
	}
}

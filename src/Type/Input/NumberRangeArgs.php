<?php
/**
 * GraphQL Input Type - NumberRangeArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Input\AbstractInput;

/**
 * Class - NumberRangeArgs
 */
class NumberRangeArgs extends AbstractInput {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetNumberRangeArgs';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Input args for Number Range facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'max' => [
				'type' => 'Int',
			],
			'min' => [
				'type' => 'Int',
			],
		];
	}
}

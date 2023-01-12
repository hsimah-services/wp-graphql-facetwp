<?php
/**
 * GraphQL Input Type - DateRangeArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Input\AbstractInput;

/**
 * Class - DateRangeArgs
 */
class DateRangeArgs extends AbstractInput {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetDateRangeArgs';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Input args for Date Range facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'max' => [
				'type' => 'String',
			],
			'min' => [
				'type' => 'String',
			],
		];
	}
}

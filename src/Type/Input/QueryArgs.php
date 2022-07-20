<?php
/**
 * GraphQL Input Type - QueryArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Input\AbstractInput;

/**
 * Class - QueryArgs
 */
class QueryArgs extends AbstractInput {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetQueryArgs';

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

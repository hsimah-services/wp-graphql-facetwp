<?php
/**
 * GraphQL Input Type - ProximityArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Enum\ProximityRadiusOptions;
use WPGraphQL\FacetWP\Type\Input\AbstractInput;

/**
 * Class - ProximityArgs
 */
class ProximityArgs extends AbstractInput {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetProximityArgs';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Input args for Proximity facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'chosenRadius' => [
				'type' => ProximityRadiusOptions::$type,
			],
			'latitude'     => [
				'type' => 'Float',
			],
			'locationName' => [
				'type' => 'String',
			],
			'longitude'    => [
				'type' => 'Float',
			],
		];
	}
}

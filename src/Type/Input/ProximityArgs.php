<?php
/**
 * GraphQL Input Type - ProximityArgs.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL\FacetWP\Type\Enum\ProximityRadiusOptions;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - ProximityArgs
 */
class ProximityArgs extends InputType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetProximityArgs';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Input args for Proximity facet type', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'chosenRadius' => [
				'type'        => ProximityRadiusOptions::get_type_name(),
				'description' => __( 'The chosen radius from the location.', 'wpgraphql-facetwp' ),
			],
			'latitude'     => [
				'type'        => 'Float',
				'description' => __( 'The latitude of the location.', 'wpgraphql-facetwp' ),
			],
			'locationName' => [
				'type'        => 'String',
				'description' => __( 'The name of the location.', 'wpgraphql-facetwp' ),
			],
			'longitude'    => [
				'type'        => 'Float',
				'description' => __( 'The longitude of the location.', 'wpgraphql-facetwp' ),
			],
		];
	}
}

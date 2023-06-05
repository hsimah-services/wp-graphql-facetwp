<?php
/**
 * Generates the Facet objects.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use FacetWP_Facet;
use WPGraphQL\FacetWP\Type\WPInterface\FacetConfig;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;

/**
 * Class - FacetObjects
 */
class FacetObjects implements Registrable {
	use TypeNameTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		add_action( 'graphql_register_types', [ static::class, 'register' ] );
	}

	/**
	 * Registers the Facet GraphQL objects to the schema.
	 */
	public static function register(): void {
		$facet_types = FWP()->helper->get_facet_types();

		// Sort the facet types by key.
		ksort( $facet_types );

		foreach ( $facet_types as $name => $facet_type ) {
			register_graphql_object_type(
				graphql_format_type_name( $name ) . 'Facet',
				[
					// translators: %s is the facet type.
					'description'     => sprintf( __( 'The %s facet object', 'wpgraphql-facetwp' ), $facet_type->label ),
					// Use base interface fields.
					'fields'          => FacetConfig::get_fields(),
					'interfaces'      => self::get_interfaces( $facet_type ),
					'eagerlyLoadType' => true,
				],
			);
		}
	}

	/**
	 * Gets the interfaces for the facet object.
	 *
	 * @param \FacetWP_Facet $facet_type The facet type object.
	 *
	 * @return string[]
	 */
	public static function get_interfaces( FacetWP_Facet $facet_type ): array {
		return [
			FacetConfig::get_type_name(),
		];
	}
}

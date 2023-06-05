<?php
/**
 * GraphQL Input Type - FacetInput.
 *
 * @package WPGraphQL\FacetWP\Type\Input
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\Input;

use WPGraphQL;
use WPGraphQL\AppContext;
use WPGraphQL\FacetWP\Type\WPInterface\FacetConfig;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - FacetsInput
 */
class FacetsInput extends InputType {
	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		// Only register if there are allowed facets.
		if ( empty( get_graphql_allowed_facets() ) ) {
			return;
		}

		// Register the input.
		parent::register();

		// Register the input as where args for each post type.
		$allowed_post_types = WPGraphQL::get_allowed_post_types( 'objects' );

		foreach ( $allowed_post_types as $post_type_obj ) {
			// Register Where arg to connection.
			register_graphql_connection_where_arg(
				'RootQuery',
				$post_type_obj->graphql_single_name,
				'facets',
				[
					'type'        => 'FacetsInput',
					'description' => __( 'Filter by FacetWP facets.', 'wpgraphql-facetwp' ),
				],
			);

			self::register_edge_field( $post_type_obj );
		}
	}

	/**
	 * Register the edge field for the connection.
	 *
	 * @param \WP_Post_Type $post_type_obj The post type object.
	 */
	public static function register_edge_field( \WP_Post_Type $post_type_obj ): void {
		register_graphql_edge_field(
			'RootQuery',
			$post_type_obj->graphql_single_name,
			'activeFacets',
			[
				'type'        => [ 'list_of' => FacetConfig::get_type_name() ],
				'description' => __( 'The active facets on this connection', 'wpgraphql-facetwp' ),
				'resolve'     => static function ( $source, array $args, AppContext $context ) {
					$keys = array_keys( (array) $source );
					foreach ( $keys as $key ) {
						error_log( $key . ': ' . gettype( $source[ $key ] ) );
					}
					error_log( $source['node']::class );
					error_log( $source['connection']::class );
					error_log( print_r( $source['node'], true ) );
					return [];
				},
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetsInput';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Input for filtering by FacetWP facets.', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 */
	public static function get_fields(): array {
		$facet_configs = get_graphql_allowed_facets();
		$fields        = [];

		// Generate the input field for each facet.
		foreach ( $facet_configs as $config ) {
			// Skip if the facet is not configured to be used via GraphQL.
			if ( empty( $config['graphql_field_name'] ) || empty( $config['graphql_type'] ) ) {
				continue;
			}

			$field_config = [
				'description' => sprintf(
					// translators: %1$s is the facet label.
					__( 'The %1$s facet.', 'wpgraphql-facetwp' ),
					$config['label']
				),
				'type'        => $config['graphql_type'],
			];

			$fields[ $config['graphql_field_name'] ] = $field_config;
		}

		return $fields;
	}
}

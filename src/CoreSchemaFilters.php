<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\FacetWP
 * @since   0.4.1
 */

namespace WPGraphQL\FacetWP;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		// Prefix the GraphQL type names.
		add_filter( 'graphql_facetwp_type_prefix', [ self::class, 'get_type_prefix' ] );

		// Filter the ConnectionResolver to use FacetWP.
		add_filter( 'graphql_connection_query_args', [ __CLASS__, 'filter_connection_query_args' ], 10, 3 );
		add_filter( 'graphql_connection_query', [ __CLASS__, 'filter_connection_query' ], 10, 2 );
	}

	/**
	 * Don't prefix type names.
	 */
	public static function get_type_prefix(): string {
		return '';
	}

	/**
	 * Filters connection query args.
	 *
	 * @param array                                                 $query_args Query args.
	 * @param \WPGraphQL\Data\Connection\AbstractConnectionResolver $connection_resolver Connection resolver.
	 * @param array                                                 $args       Connection args.
	 */
	public static function filter_connection_query_args( array $query_args, AbstractConnectionResolver $connection_resolver, array $args ) : array {
		if ( empty( $args['where']['facets'] ) ) {
			return $query_args;
		}

		$facet_query = self::parse_facet_query_args( $args['where']['facets'] );

		$fwp_args = [
			'facets'     => $facet_query,
			'query_args' => $query_args,
		];

		$payload = FWP()->api->process_request( $fwp_args );

		return [
			'post__in' => ! empty( $payload['results'] ) ? $payload['results'] : [ 'no_results' ], // We use 'no_results' as a special value to indicate that the facet has no results.
			'fields'   => 'ids',
		];
	}

	/**
	 * Filters the Connection Query to handle a facet with no results.
	 *
	 * @param mixed                      $query The query to filter.
	 * @param AbstractConnectionResolver $resolver The resolver.
	 *
	 * @return mixed|null
	 */
	public static function filter_connection_query( $query, AbstractConnectionResolver $resolver ) {
		if ( ! $resolver instanceof PostObjectConnectionResolver ) {
			return $query;
		}

		$query_args = $resolver->get_query_args();

		// If FWP finds no results, return null.
		if ( isset( $query_args['post__in'] ) && 'no_results' === $query_args['post__in'][0] ) {
			$resolver->set_query_arg( 'post__in', [] );
			return null;
		}

		return $query;
	}

	/**
	 * Parses the facet query args from the where args provided in the GraphQL query.
	 *
	 * @param array $args The facet query args.
	 */
	protected static function parse_facet_query_args( array $args ) : array {
		$facets = get_graphql_allowed_facets();

		$facet_args = [];
		foreach ( $args as $field_name => $value ) {
			// If no facet has the same graphql_single_name as $field name, return.
			$key = array_search( $field_name, array_column( $facets, 'graphql_field_name' ), true );

			if ( false === $key ) {
				continue;
			}

			switch ( $facets[ $key ]['type'] ) {
				case 'checkboxes':
				case 'fselect':
				case 'rating':
				case 'radio':
				case 'dropdown':
				case 'hierarchy':
				case 'search':
				case 'autocomplete':
					$facet_args[ $facets[ $key ]['name'] ] = $value;
					break;
				case 'slider':
				case 'date_range':
				case 'number_range':
					if ( isset( $value['min'] ) ) {
						$facet_args[ $facets[ $key ]['name'] ]['min'] = $value['min'];
					}
					if ( isset( $value['max'] ) ) {
						$facet_args[ $facets[ $key ]['name'] ]['max'] = $value['max'];
					}
					break;
				case 'proximity':
					if ( isset( $value['latitude'] ) ) {
						$facet_args[ $facets[ $key ]['name'] ]['latitude'] = $value['latitude'];
					}
					if ( isset( $value['longitude'] ) ) {
						$facet_args[ $facets[ $key ]['name'] ]['longitude'] = $value['longitude'];
					}
					if ( isset( $value['chosenRadius'] ) ) {
						$facet_args[ $facets[ $key ]['name'] ]['chosenRadius'] = $value['chosenRadius'];
					}
					if ( isset( $value['locationName'] ) ) {
						$facet_args[ $facets[ $key ]['name']['locationName'] ] = $value['locationName'];
					}
					break;
				default:
					$arg_value = apply_filters( 'facetwp_facet_query_arg_value', null, $value, $facets[ $key ]['type'], $facets[ $key ]['name'], $field_name, $args );

					if ( null !== $arg_value ) {
						$facet_args[ $facets[ $key ]['name'] ] = $arg_value;
					}
			}
		}

		return $facet_args;
	}
}

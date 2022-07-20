<?php
/**
 * Registers individual facetsto the GraphQL schema.
 *
 * @package WPGraphQL\FacetWP\Registry
 * @since @todo
 */

namespace WPGraphQL\FacetWP\Registry;

use FacetWP_API_Fetch;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\FacetWP\Type\Input;

/**
 * Class - FacetRegistry
 */
class FacetRegistry {

	/**
	 * Register WPGraphQL Facet query.
	 *
	 * @todo Move to type registry.
	 *
	 * @param string $type The Post Type name.
	 */
	public static function register( string $type ) : void {
		$post_type = get_post_type_object( $type );

		if ( null === $post_type || ! $post_type->show_in_graphql ) {
			return;
		}

		$config = [
			'type'     => $type,
			'singular' => $post_type->graphql_single_name,
			'plural'   => $post_type->graphql_plural_name,
			'field'    => $post_type->graphql_single_name . 'Facet',
		];

		self::register_root_field( $config );
		self::register_input_arg_types( $config );
		self::register_custom_output_types( $config );
		self::register_facet_connection( $config );
	}

	/**
	 * Register facet-type root field.
	 *
	 * @param array $facet_config The config array.
	 */
	private static function register_root_field( array $facet_config ) :void {
		$type     = $facet_config['type'];
		$singular = $facet_config['singular'];
		$field    = $facet_config['field'];

		$use_graphql_pagination = self::use_graphql_pagination();

		register_graphql_field(
			'RootQuery',
			$field,
			[
				'type'        => $field,
				'description' => sprintf(
					// translators: The GraphQL singular type name.
					__( '%s FacetWP Query', 'wpgraphql-facetwp' ),
					$singular
				),
				'args'        => [
					'where' => [
						'type'        => $field . 'WhereArgs',
						'description' => sprintf(
							// translators: The GraphQL Field name.
							__( 'Arguments for %s query', 'wpgraphql-facetwp' ),
							$field
						),
					],
				],
				'resolve'     => function ( $source, array $args ) use ( $type, $use_graphql_pagination ) {
					$where = $args['where'];

					$pagination = [
						'per_page' => 10,
						'page'     => 1,
					];
					if ( ! empty( $where['pager'] ) ) {
						$pagination = array_merge( $pagination, $where['pager'] );
					}

					$query = self::parse_query( $where['query'] );

					// Clean up null args.
					foreach ( $query as $key => $value ) {
						if ( ! $value ) {
							$query[ $key ] = [];
						}
					}

					$fwp_args = [
						'facets'     => $query,
						'query_args' => [
							'post_type'      => $type,
							'post_status'    => ! empty( $where['status'] ) ? $where['status'] : 'publish',
							'posts_per_page' => (int) $pagination['per_page'],
							'paged'          => (int) $pagination['page'],
						],
					];

					$filtered_ids = [];
					if ( $use_graphql_pagination ) {

						// TODO find a better place to register this handler.
						add_filter(
							'facetwp_filtered_post_ids',
							function ( $post_ids ) use ( &$filtered_ids ) {
								$filtered_ids = $post_ids;
								return $post_ids;
							},
							10,
							2
						);
					}

					$fwp     = new FacetWP_API_Fetch();
					$payload = $fwp->process_request( $fwp_args );

					$results = $payload['results'];
					if ( $use_graphql_pagination ) {
						$results = $filtered_ids;
					}

					// @todo helper function.
					foreach ( $payload['facets'] as $key => $facet ) {
						if ( isset( $facet['settings'] ) ) {
							$facet['settings'] = self::to_camel_case( $facet['settings'] );
						}

						$payload['facets'][ $key ] = $facet;
					}

					/**
					 * The facets array is the resolved payload for this field.
					 * Results & pager are returned so the connection resolver can use the data.
					 */
					$return_vals = [
						'facets'  => array_values( $payload['facets'] ),
						'results' => count( $results ) ? $results : [ -1 ],
					];

					if ( $use_graphql_pagination ) {
						$return_vals['pager'] = $payload['pager'];
					}

					return $return_vals;
				},
			]
		);
	}

	/**
	 * Register input argument types.
	 *
	 * @param array $facet_config The config array.
	 */
	private static function register_input_arg_types( array $facet_config ) : void {
		$field = $facet_config['field'];

		$use_graphql_pagination = self::use_graphql_pagination();

		$facets = FWP()->helper->get_facets();

		$graphql_fields = array_reduce(
			$facets,
			function ( $prev, $cur ) {
				if ( $cur && $cur['name'] ) {
					$type = [
						'list_of' => 'String',
					];

					switch ( $cur['type'] ) {
						case 'fselect':
							// Single string for single fSelect.
							if ( 'yes' === $cur['multiple'] ) {
								break;
							}
							// Continuing...
						case 'radio':
						case 'search':
							// Single string.
							$type = 'String';
							break;
						case 'date_range':
							// Custom payload.
							$type = Input\DateRangeArgs::$type;
							break;
						case 'number_range':
							// Custom payload.
							$type = Input\NumberRangeArgs::$type;
							break;
						case 'slider':
							// Custom payload.
							$type = Input\SliderArgs::$type;
							break;
						case 'proximity':
							// Custom payload.
							$type = Input\ProximityArgs::$type;
							break;
						case 'rating':
							// Single Int.
							$type = 'Int';
							break;
						case 'autocomplete':
						case 'checkboxes':
						case 'dropdown':
						case 'hierarchy':
						default:
							// String array - default.
							break;
					}

					$prev[ $cur['name'] ] = [
						'type'        => $type,
						'description' => sprintf(
							// translators: The current Facet label.
							__( '%s facet query', 'wpgraphql-facetwp' ),
							$cur['label']
						),
					];
				}

				return $prev;
			},
			[]
		);

		register_graphql_input_type(
			'FacetQueryArgs',
			[
				'description' => sprintf(
					// translators: The GraphQL Field name.
					__( 'Seleted facets for %s query', 'wpgraphql-facetwp' ),
					$field
				),
				'fields'      => array_reduce(
					$facets,
					function ( $prev, $cur ) {
						if ( $cur && $cur['name'] ) {
							$type = [
								'list_of' => 'String',
							];

							switch ( $cur['type'] ) {
								case 'fselect':
									// Single string for single fSelect.
									if ( 'yes' === $cur['multiple'] ) {
										break;
									}
									// Continuing...
								case 'radio':
								case 'search':
									// Single string.
									$type = 'String';

									break;
								case 'date_range':
									// Custom payload.
									$type = Input\DateRangeArgs::$type;

									break;
								case 'number_range':
									// Custom payload.
									$type = Input\NumberRangeArgs::$type;

									break;
								case 'slider':
									// Custom payload.
									$type = Input\SliderArgs::$type;

									break;
								case 'proximity':
									// Custom payload.
									$type = Input\ProximityArgs::$type;

									break;
								case 'rating':
									// Single Int.
									$type = 'Int';

									break;
								case 'autocomplete':
								case 'checkboxes':
								case 'dropdown':
								case 'hierarchy':
								default:
									// String array - default.
									break;
							}

							$prev[ $cur['name'] ] = [
								'type'        => $type,
								'description' => sprintf(
									// translators: The current Facet label.
									__( '%s facet query', 'wpgraphql-facetwp' ),
									$cur['label']
								),
							];
						}

						return $prev;
					},
					[]
				),
			]
		);

		if ( ! $use_graphql_pagination ) {
			register_graphql_input_type(
				$field . 'Pager',
				[
					'description' => __(
						'FacetWP Pager input type.',
						'wpgraphql-facetwp'
					),
					'fields'      => [
						'per_page' => [
							'type'        => 'Int',
							'description' => __(
								'Number of post to show per page. Passed to posts_per_page of WP_Query.',
								'wpgraphql-facetwp'
							),
						],
						'page'     => [
							'type'        => 'Int',
							'description' => __(
								'The page to fetch.',
								'wpgraphql-facetwp'
							),
						],
					],
				]
			);
		}

		$where_fields = [
			'status' => [
				'type' => 'PostStatusEnum',
			],
			'query'  => [
				'type' => 'FacetQueryArgs',
			],
		];

		if ( ! $use_graphql_pagination ) {
			$where_fields['pager'] = [
				'type' => $field . 'Pager',
			];
		}

		register_graphql_input_type(
			$field . 'WhereArgs',
			[
				'description' => sprintf(
					// translators: The GraphQL Field name.
					__( 'Arguments for %s query', 'wpgraphql-facetwp' ),
					$field
				),
				'fields'      => $where_fields,
			]
		);
	}

	/**
	 * Register custom output types.
	 *
	 * @param array $facet_config The config array.
	 */
	private static function register_custom_output_types( array $facet_config ) : void {
		$singular = $facet_config['singular'];
		$field    = $facet_config['field'];

		$use_graphql_pagination = self::use_graphql_pagination();

		$fields = [
			'facets' => [
				'type' => [
					'list_of' => 'Facet',
				],
			],
		];

		if ( ! $use_graphql_pagination ) {
			$fields['pager'] = [
				'type' => 'FacetPager',
			];
		}

		register_graphql_object_type(
			$field,
			[
				'description' => sprintf(
					// translators: The GraphQL singular type name.
					__( '%s FacetWP Payload', 'wpgraphql-facetwp' ),
					$singular
				),
				'fields'      => $fields,
			]
		);
	}

	/**
	 * Register facet-type connection types.
	 *
	 * @param array $facet_config The config array.
	 */
	private static function register_facet_connection( array $facet_config ) : void {
		$type     = $facet_config['type'];
		$singular = $facet_config['singular'];
		$field    = $facet_config['field'];
		$plural   = $facet_config['plural'];

		$use_graphql_pagination = self::use_graphql_pagination();

		$default_facet_connection_config = [
			'fromType'       => $field,
			'toType'         => $singular,
			'fromFieldName'  => lcfirst( $plural ),
			'connectionArgs' => PostObjects::get_connection_args(),
			'resolveNode'    => function ( $node, $_args, $context ) {
				return $context->get_loader( 'post' )->load_deferred( $node->ID );
			},
			'resolve'        => function ( $source, $args, $context, $info ) use ( $type, $use_graphql_pagination ) {
				if ( ! $use_graphql_pagination ) {
					// Manually override the first query arg if per_page > 10, the first default value.
					$args['first'] = $source['pager']['per_page'];
				}
				$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info, $type );

				return $resolver
					->set_query_arg( 'post__in', $source['results'] )
					->get_connection();
			},
		];

		/**
		 * @param array $connection_config The connection config array.
		 * @param array $facet_config The facet data array used to generate the config.
		 */
		$graphql_connection_config = apply_filters(
			'facetwp_graphql_facet_connection_config',
			$default_facet_connection_config,
			$facet_config
		);

		register_graphql_connection( $graphql_connection_config );
	}

	/**
	 * Parse WPGraphQL query into FacetWP query
	 *
	 * @param mixed $query @todo.
	 *
	 * @return mixed FacetWP query
	 */
	private static function parse_query( $query ) {
		$facets = FWP()->helper->get_facets();

		return array_reduce(
			$facets,
			function ( $prev, $cur ) use ( $query ) {
				$name  = $cur['name'];
				$facet = isset( $query[ $name ] ) ? $query[ $name ] : null;

				if ( isset( $facet ) ) {
					switch ( $cur['type'] ) {
						case 'checkboxes':
						case 'fselect':
						case 'rating':
						case 'radio':
						case 'dropdown':
						case 'hierarchy':
						case 'search':
						case 'autocomplete':
							$prev[ $name ] = $facet;
							break;
						case 'slider':
						case 'date_range':
						case 'number_range':
							$input         = $facet;
							$prev[ $name ] = [
								$input['min'],
								$input['max'],
							];

							break;
						case 'proximity':
							$input         = $facet;
							$prev[ $name ] = [
								$input['latitude'],
								$input['longitude'],
								$input['chosenRadius'],
								$input['locationName'],
							];
							break;
					}
				}

				return $prev;
			},
			[]
		);
	}

	/**
	 * Converts strings to camelCase.
	 * If an array is supplied, the array keys will be camelCased.
	 *
	 * @todo move to helper class.
	 *
	 * @param string|array<string, string> $input The string or list of strings to convert.
	 * @return string|array
	 */
	private static function to_camel_case( $input ) {
		if ( is_array( $input ) ) {
			$out = [];

			foreach ( $input as $key => $value ) {
				$camel_key = self::to_camel_case( $key );
				// @todo refactor recursion to avoid this.
				if ( is_string( $camel_key ) ) {
					$camel_key = $key;
				}
				$out[ $key ] = $value;
			}

			return $out;
		}

		return lcfirst( str_replace( '_', '', ucwords( $input, ' /_' ) ) );
	}

	/**
	 * @todo Work in progress - pull settings from facetwp instead of hard coding them.
	 */
	private static function register_facet_settings() : void {
		$facets      = FWP()->helper->get_facets();
		$facet_types = FWP()->helper->facet_types;

		// loop over configured facets and loop up facet type.
		// call settings_js() if it exists for type.
		// determine type for setting field.
		// camelCase the field name.
		// register graphql object.

		foreach ( $facet_types as $name => $value ) {
			if ( method_exists( $facet_types[ $name ], 'settings_js' ) ) {
				$settings_name = str_replace( '_', '', ucwords( $name, ' /_' ) );
				// @phpstan-ignore-next-line
				$settings = $facet_types[ $name ]->settings_js( [] );

				foreach ( $settings as $setting_key => $setting ) {
					if ( is_array( $setting ) ) {
						// recurse.
						continue;
					}

					$setting_type = gettype( $setting );

					switch ( $setting_type ) {
						case 'string':
							$type = 'String';
							break;
					}
				}
			}
		}
	}

	/**
	 * Whether to use WPGraphQL Pagination
	 *
	 * GraphQL handles pagination differently than the traditional API or FacetWP.
	 * If you would like to use WP GraphQL's native pagination, filter this value.
	 * By default, this plugin presumes that anyone using it is deeply familiar with
	 * FacetWP and is looking for a similar experience using FacetWP with WP GraphQL as
	 * one would expect its native functionality in WordPress.
	 *
	 * If you choose to use the WP GraphQL pagination, then the pager return values
	 * will not be accurate and you will need to handle the challenge page counts as you
	 * would for the rest of your GraphQL application.
	 *
	 * @see https://graphql.org/learn/pagination/
	 */
	public static function use_graphql_pagination() : bool {
		return apply_filters( 'wpgraphql_facetwp_user_graphql_pagination', false );
	}
}

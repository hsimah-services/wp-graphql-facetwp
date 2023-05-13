<?php
/**
 * Registers individual facets to the GraphQL schema.
 *
 * @package WPGraphQL\FacetWP\Registry
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP\Registry;

use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\FacetWP\Type\Enum\SortOptionsEnum;
use WPGraphQL\FacetWP\Type\Input;

/**
 * Class - FacetRegistry
 */
class FacetRegistry {

	/**
	 * The facet configs to register to WPGraphQL
	 *
	 * @var ?array
	 */
	protected static $facets;

	/**
	 * Gets the facet configs to be registered to WPGraphQL.
	 *
	 * @since 0.4.1
	 */
	public static function get_allowed_facets() : array {
		if ( ! isset( self::$facets ) ) {
			$configs = FWP()->helper->get_facets();

			// Add GraphQL properties to each facet config.
			foreach ( $configs as $key => $config ) {
				// @todo set these on the backend.
				$configs[ $key ]['graphql_field_name'] = $config['graphql_field_name'] ?? graphql_format_field_name( $config['name'] );
				$configs[ $key ]['show_in_graphql']    = $config['show_in_graphql'] ?? true;
				$configs[ $key ]['graphql_type']       = self::get_facet_input_type( $config );
			}

			self::$facets = array_values(
				array_filter(
					$configs,
					function( $config ) {
						return $config['show_in_graphql'];
					}
				)
			);
		}

		return self::$facets;
	}

	/**
	 * Gets the GraphQL input type for a facet.
	 *
	 * @param array $config The facet config.
	 *
	 * @return string|array
	 *
	 * @since 0.4.1
	 */
	public static function get_facet_input_type( array $config ) {
		// The default facet type is a list of strings.
		$type = [ 'list_of' => 'String' ];

		switch ( $config['type'] ) {
			case 'fselect':
				// Single string for single fSelect.
				if ( 'yes' === $config['multiple'] ) {
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
				$type = Input\DateRangeArgs::get_type_name();

				break;
			case 'number_range':
				// Custom payload.
				$type = Input\NumberRangeArgs::get_type_name();

				break;
			case 'slider':
				// Custom payload.
				$type = Input\SliderArgs::get_type_name();

				break;
			case 'proximity':
				// Custom payload.
				$type = Input\ProximityArgs::get_type_name();

				break;
			case 'rating':
				// Single Int.
				$type = 'Int';

				break;
			case 'sort':
				$type = SortOptionsEnum::get_type_name( $config['name'] );

				break;
			case 'autocomplete':
			case 'checkboxes':
			case 'dropdown':
			case 'hierarchy':
			default:
				// String array - default.
				break;
		}

		/**
		 * Filter the GraphQL input type for a facet.
		 *
		 * @param string|array $input_type The GraphQL Input type name to use.
		 * @param string       $facet_type The FacetWP facet type.
		 * @param array        $facet_config The FacetWP facet config.
		 */
		$type = apply_filters( 'graphql_facetwp_facet_input_type', $type, $config['type'], $config );

		return $type;
	}

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
					__( 'The %s FacetWP Query', 'wpgraphql-facetwp' ),
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

					$query = ! empty( $where['query'] ) ? $where['query'] : [];
					$query = self::parse_query( $query );

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

					// Stash the sort settings, since we don't get them from the payload.
					$sort_settings = [];

					// Apply the orderby args.
					foreach ( $fwp_args['facets'] as $key => $facet_args ) {
						if ( ! empty( $facet_args['is_sort'] ) ) {
							$fwp_args['query_args'] = array_merge_recursive( $fwp_args['query_args'], $facet_args['query_args'] );
							$sort_settings[ $key ]  = $facet_args['settings'];

							// Set the selected facet back to a string.
							$fwp_args['facets'][ $key ] = $facet_args['selected'];
						}
					}

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
							1
						);
					}

					$payload = FWP()->api->process_request( $fwp_args );

					$results = $payload['results'];
					if ( $use_graphql_pagination ) {
						$results = $filtered_ids;
					}

					// @todo helper function.
					foreach ( $payload['facets'] as $key => $facet ) {
						// Try to get the settings from the payload, otherwise fallback to the parsed query args.
						if ( isset( $facet['settings'] ) ) {
							$facet['settings'] = self::to_camel_case( $facet['settings'] );
						} elseif ( isset( $sort_settings[ $key ] ) ) {
							$facet['settings'] = self::to_camel_case( $sort_settings[ $key ] );
						}

						$payload['facets'][ $key ] = $facet;
					}

					/**
					 * The facets array is the resolved payload for this field.
					 * Results & pager are returned so the connection resolver can use the data.
					 */
					return [
						'facets'  => array_values( $payload['facets'] ),
						'results' => count( $results ) ? $results : null,
						'pager'   => $payload['pager'] ?? [],
						'is_sort' => ! empty( $fwp_args['query_args']['orderby'] ),
					];
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

		$facets = self::get_allowed_facets();

		$field_configs = array_reduce(
			$facets,
			function ( $prev, $cur ) {
				if ( empty( $cur['graphql_field_name'] ) ) {
					return $prev;
				}

				// Add the field config.
				$prev[ $cur['graphql_field_name'] ] = [
					'type'        => $cur['graphql_type'],
					'description' => sprintf(
						// translators: The current Facet label.
						__( '%s facet query', 'wpgraphql-facetwp' ),
						$cur['label']
					),
				];

				// Maybe add the deprecate type name.
				if ( $cur['name'] !== $cur['graphql_field_name'] ) {
					$prev[ $cur['name'] ] = [
						'type'              => $cur['graphql_type'],
						'description'       => sprintf(
							// translators: The current Facet label.
							__( 'DEPRECATED since 0.4.1', 'wpgraphql-facetwp' ),
							$cur['label']
						),
						'deprecationReason' => sprintf(
							// translators: The the GraphQL field name.
							__( 'Use %s instead.', 'wpgraphql-facetwp' ),
							$cur['graphql_field_name']
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
				'fields'      => $field_configs,
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
				'type'        => 'PostStatusEnum',
				'description' => __( 'The post status.', 'wpgraphql-facetwp' ),
			],
			'query'  => [
				'type'        => 'FacetQueryArgs',
				'description' => __( 'The FacetWP query args.', 'wpgraphql-facetwp' ),
			],
		];

		if ( ! $use_graphql_pagination ) {
			$where_fields['pager'] = [
				'type'        => $field . 'Pager',
				'description' => __( 'The FacetWP pager args.', 'wpgraphql-facetwp' ),
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
				'type'        => [ 'list_of' => 'Facet' ],
				'description' => __( 'The facets for this query.', 'wpgraphql-facetwp' ),
			],
		];

		if ( ! $use_graphql_pagination ) {
			$fields['pager'] = [
				'type'        => 'FacetPager',
				'description' => __( 'The FacetWP pager for this query.', 'wpgraphql-facetwp' ),
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

				if ( ! empty( $source['results'] ) ) {
					$resolver->set_query_arg( 'post__in', $source['results'] );
				}

				// Use post__in when delegating sorting to FWP.
				if ( ! empty( $source['is_sort'] ) ) {
					$resolver->set_query_arg( 'orderby', 'post__in' );
				}

				return $resolver->get_connection();
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
	 * @param array $query @todo.
	 *
	 * @return array FacetWP query
	 */
	private static function parse_query( array $query ) : array {
		// Bail early if no query set.
		if ( empty( $query ) ) {
			return [];
		}

		$facets = FWP()->helper->get_facets();

		$reduced_query = array_reduce(
			$facets,
			function ( $prev, $cur ) use ( $query ) {
				// Get the facet name.
				$name             = $cur['name'] ?? '';
				$camel_cased_name = ! empty( $name ) ? self::to_camel_case( $name ) : '';
				$facet            = is_string( $camel_cased_name ) && isset( $query[ $camel_cased_name ] ) ? $query[ $camel_cased_name ] : null;

				// Fallback to snakeCased name.
				if ( ! isset( $facet ) ) {
					$facet = isset( $query[ $name ] ) ? $query[ $name ] : null;
				}

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
							$input['min'] ?? null,
							$input['max'] ?? null,
						];

						break;
					case 'proximity':
						$input         = $facet;
						$prev[ $name ] = [
							$input['latitude'] ?? null,
							$input['longitude'] ?? null,
							$input['chosenRadius'] ?? null,
							$input['locationName'] ?? null,
						];

						break;

					case 'sort':
						$input        = $facet;
						$sort_options = self::parse_sort_facet_options( $cur );

						// We pass these through to create our sort args.
						$prev[ $name ] = [
							'is_sort'    => true,
							'selected'   => $facet,
							'settings'   => [
								'default_label' => $cur['default_label'],
								'sort_options'  => $cur['sort_options'],
							],
							'query_args' => [],
						];

						/**
						 * Define the query args for the sort.
						 *
						 * This is a shim of FacetWP_Facet_Sort::apply_sort()
						 */
						if ( ! empty( $sort_options[ $facet ] ) ) {
							$qa = $sort_options[ $facet ]['query_args'];

							if ( isset( $qa['meta_query'] ) ) {
								$prev[ $name ]['query_args']['meta_query'] = $qa['meta_query'];
							}

							$prev[ $name ]['query_args']['orderby'] = $qa['orderby'];
						}

						break;
				}

				return $prev;
			},
			[]
		);

		return $reduced_query ?: [];
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
					$key = $camel_key;
				}
				$out[ $key ] = $value;
			}

			return $out;
		}

		return lcfirst( str_replace( '_', '', ucwords( $input, '_' ) ) );
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

	/**
	 * Parses the sort options for a sort facet into a WP_Query compatible array.
	 *
	 * @see \FacetWP_Facet_Sort::parse_sort_facet()
	 *
	 * @param array<string, mixed> $facet The facet configuration.
	 */
	private static function parse_sort_facet_options( array $facet ) : array {
		$sort_options = [];

		foreach ( $facet['sort_options'] as $row ) {
			$parsed = FWP()->builder->parse_query_obj( [ 'orderby' => $row['orderby'] ] );

			$sort_options[ $row['name'] ] = [
				'label'      => $row['label'],
				'query_args' => array_intersect_key(
					$parsed,
					[
						'meta_query' => true,
						'orderby'    => true,
					]
				),
			];
		}

		$sort_options = apply_filters(
			'facetwp_facet_sort_options',
			$sort_options,
			[
				'facet'         => $facet,
				'template_name' => 'graphql',
			]
		);

		return $sort_options;
	}
}

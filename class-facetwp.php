<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Type\WPEnumType;

final class WPGraphQL_FacetWP {
        
    /**
     * Stores the instance of the WPGraphQL_FacetWP class
     *
     * @var WPGraphQL_FacetWP The one true WPGraphQL_FacetWP
     * @since  0.0.1
     * @access private
     */
    private static $instance;
    
    /**
     * The instance of the WPGraphQL_FacetWP object
     *
     * @return object|WPGraphQL_FacetWP - The one true WPGraphQL_FacetWP
     * @since  0.0.1
     * @access public
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPGraphQL_FacetWP ) ) {
            self::$instance = new WPGraphQL_FacetWP();
            self::$instance->init();
        }

        /**
         * Return the WPGraphQL_FacetWP Instance
         */
        return self::$instance;
    }
    
    /**
     * Throw error on object clone.
     * The whole idea of the singleton design pattern is that there is a single object
     * therefore, we don't want the object to be cloned.
     *
     * @since  0.0.1
     * @access public
     * @return void
     */
    public function __clone() {

        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'The WPGraphQL_FacetWP class should not be cloned.', 'wpgraphql-facetwp' ), '0.0.1' );

    }

    /**
     * Disable unserializing of the class.
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public function __wakeup() {

        // De-serializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL_FacetWP class is not allowed', 'wpgraphql-facetwp' ), '0.0.1' );

    }

    /**
     * Register WPGraphQL Facet query.
     *
     * @access public
     * @since  0.0.1
     * @return void
     */
    public static function register( $type ) {
        
        $post_type = get_post_type_object( $type );

        if ( ! $post_type->show_in_graphql ) return;
        
        $config = [
            'type'      => $type,
            'singular'  => $post_type->graphql_single_name,
            'plural'    => $post_type->graphql_plural_name,
            'field'     => $post_type->graphql_single_name . 'Facet',
        ];

        self::$instance->register_root_field( $config );
        self::$instance->register_input_arg_types( $config );
        self::$instance->register_custom_output_types( $config );
        self::$instance->register_facet_connection( $config );

    }
    
    /**
     * Initialise plugin.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function init() {
        $this->register_output_types();
    }

    /**
     * Register facet-type root field.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function register_root_field( $config ) {

        $type = $config['type'];
        $singular = $config['singular'];
        $field = $config['field'];

        register_graphql_field( 'RootQuery', $field, [
            'type'        => $field,
            'description' => __( $singular . ' FacetWP Query', 'wpgraphql-facetwp' ),
            'args'        => [
                'where' => [
                    'type'          => $field . 'WhereArgs',
                    'description'   => __( 'Arguments for ' . $field . ' query', 'wpgraphql-facetwp' ),
                ],
            ],
            'resolve'     => function ( $source, array $args ) use ( $type ) {    

                $where = $args['where'];

                $query = $this->parse_query( $where['query'] );

                // clean up null args
                foreach ( $query as $key => $value ) {
                    if ( ! $value ) $query[$key] = [];
                }

                $fwp_args = [
                    'facets'        => $query,
                    'query_args'    => [
                        'post_type'         => $type,
                        'post_status'       => $where['status'],
                        'posts_per_page'    => 10,
                        'paged'             => 1,
                    ],
                ];

                $filtered_ids = [];
                    
                // TODO find a better place to register this handler
                add_filter( 'facetwp_filtered_post_ids', function( $post_ids, $class ) use ( &$filtered_ids ) {
                    $filtered_ids = $post_ids;
                    return $post_ids;
                }, 10, 2 );

                $fwp = new FacetWP_API_Fetch();
                $payload = $fwp->process_request( $fwp_args );

                // TODO helper function
                foreach ( $payload['facets'] as $key => $facet ) {
                    if ( isset( $facet['settings'] ) ) {
                        
                        $facet['settings'] = self::to_camel_case( $facet['settings'] );
                        
                    }

                    $payload['facets'][$key] = $facet;
                }

                /**
                 * facets array is the resolved payload for this field
                 * results & pager are returned so the connection resoler can use the data
                 */
                return [
                    'facets'    => array_values( $payload['facets'] ),
                    'results'   => $filtered_ids,
                ];
            },
        ] );

    }

    /**
     * Register facet-type connection types.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function register_facet_connection( $config ) {

        $type = $config['type'];
        $singular = $config['singular'];
        $field = $config['field'];
        $plural = $config['plural'];

        register_graphql_connection( [
            'fromType'          => $field,
            'toType'            => $singular,
            'fromFieldName'     => lcfirst( $plural ),
            'connectionArgs'    => PostObjects::get_connection_args(),
            'resolveNode'       => function( $id, $args, $context, $info ) {

                return ! empty( $id ) ? DataSource::resolve_post_object( $id, $context ) : null;
                
            },
            'resolve'           => function ( $source, $args, $context, $info ) use ( $type ) {
                
                $resolver   = new PostObjectConnectionResolver( $source, $args, $context, $info, $type );
                $resolver->setQueryArg( 'post__in', $source['results'] );
                
                $connection = $resolver->get_connection();
                
                return $connection;

            },
        ] );

    }

    /**
     * Register output types.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function register_output_types() {

        register_graphql_object_type( 'FacetRangeSettings', [
            'description'   => __( 'Range settings for Slider facets', 'wpgraphql-facetwp' ),
            'fields'        => [
                'min'   => [
                    'type'          => 'Float',
                    'description'   => __( 'Slider min value', 'wpgraphql-facetwp' ),
                ],
                'max'   => [
                    'type'          => 'Float',
                    'description'   => __( 'Slider max value', 'wpgraphql-facetwp' ),
                ],
            ],
        ] );

        register_graphql_object_type( 'FacetSettings', [
            'description'   => __( 'Union of possible Facet settings', 'wpgraphql-facetwp' ),
            'fields'        => [
                'showExpanded'          => [
                    'type'          => 'String',
                    'description'   => __( 'Show expanded facet options', 'wpgraphql-facetwp' ),
                ],
                'placeholder'           => [
                    'type'          => 'String',
                    'description'   => __( 'Placeholder text', 'wpgraphql-facetwp' ),
                ],
                'overflowText'          => [
                    'type'          => 'String',
                    'description'   => __( 'Overflow text', 'wpgraphql-facetwp' ),
                ],
                'searchText'            => [
                    'type'          => 'String',
                    'description'   => __( 'Search text', 'wpgraphql-facetwp' ),
                ],
                'noResultsText'         => [
                    'type'          => 'String',
                    'description'   => __( 'No results text', 'wpgraphql-facetwp' ),
                ],
                'operator'              => [
                    'type'          => 'String',
                    'description'   => __( 'Operator', 'wpgraphql-facetwp' ),
                ],
                'autoRefresh'           => [
                    'type'          => 'String',
                    'description'   => __( 'Auto refresh', 'wpgraphql-facetwp' ),
                ],
                'range'                 => [
                    'type'          => 'FacetRangeSettings',
                    'description'   => __( 'Selected slider range values' , 'wpgraphql-facetwp' ),
                ],
                'decimalSeparator'      => [
                    'type'          => 'String',
                    'description'   => __( 'Decimal separator', 'wpgraphql-facetwp' ),
                ],
                'thousandsSeparator'    => [
                    'type'          => 'String',
                    'description'   => __( 'Thousands separator', 'wpgraphql-facetwp' ),
                ],
                'start'                 => [
                    'type'          => 'FacetRangeSettings',
                    'description'   => __( 'Starting min and max position for the slider', 'wpgraphql-facetwp' ),
                ],
                'format'                => [
                    'type'          => 'String',
                    'description'   => __( 'Date format', 'wpgraphql-facetwp' ),
                ],
                'prefix'                => [
                    'type'          => 'String',
                    'description'   => __( 'Field prefix', 'wpgraphql-facetwp' ),
                ],
                'suffix'                => [
                    'type'          => 'String',
                    'description'   => __( 'Field suffix', 'wpgraphql-facetwp' ),
                ],
                'step'                  => [
                    'type'          => 'Int',
                    'description'   => __( 'The amount of increase between intervals', 'wpgraphql-facetwp' ),
                ],
            ],
        ] );

        register_graphql_object_type( 'Facet', [
            'description' => __( 'Active FacetWP payload', 'wpgraphql-facetwp' ),
            'fields'    => [
                'name'      => [
                    'type'          => 'String',
                    'description'   => __( 'Facet name', 'wpgraphql-facetwp' ),
                ],
                'label'     => [
                    'type'          => 'String',
                    'description'   => __( 'Facet label', 'wpgraphql-facetwp' ),
                ],
                'type'              => [
                    'type' => 'String',
                    'description'   => __( 'Facet type', 'wpgraphql-facetwp' ),
                ],
                'selected'  => [
                    'type'          => [
                        'list_of' => 'String',
                    ],
                    'description'   => __( 'Selected values', 'wpgraphql-facetwp' ),
                ],
                'choices'   => [
                    'type'          => [
                        'list_of' => 'FacetChoice',
                    ],
                    'description'   => __( 'Facet choices', 'wpgraphql-facetwp' ),
                ],
                'settings'  => [
                    'type'          => 'FacetSettings',
                    'description'   => __( 'Facet settings', 'wpgraphql-facetwp' ),
                ],
            ],
        ]);

        register_graphql_object_type( 'FacetChoice', [
            'description'   => __( 'FacetWP choice', 'wpgraphql-facetwp' ),
            'fields'        => [
                'value' => [
                    'type'          => 'String',
                    'description'   => __( 'Taxonomy value or post ID', 'wpgraphql-facetwp' ),
                ],
                'label' => [
                    'type'          => 'String',
                    'description'   => __( 'Taxonomy label or post title', 'wpgraphql-facetwp' ),
                ],
                'count' => [
                    'type'          => 'Int',
                    'description'   => __( 'Count', 'wpgraphql-facetwp' ),
                ],
                'depth' => [
                    'type'          => 'Int',
                    'description'   => __( 'Depth', 'wpgraphql-facetwp' ),
                ],
                'termId' => [
                    'type'          => 'Int',
                    'description'   => __( 'Term ID (Taxonomy choices only)', 'wpgraphql-facetwp' ),
                ],
                'parentId' => [
                    'type'          => 'Int',
                    'description'   => __( 'Parent Term ID (Taxonomy choices only', 'wpgraphql-facetwp' ),
                ],
            ],
        ] );

    }

    /**
     * Work in progress - pull settings from facetwp instead of hard coding them
     */
    private function register_facet_settings() {
        $facets = FWP()->helper->get_facets();
        $facet_types = FWP()->helper->facet_types;

        // loop over configured facets and loop up facet type
        // call settings_js() if it exists for type
        // determine type for setting field
        // camelCase the field name
        // register graphql object

        foreach ( $facet_types as $name => $value ) {
            if ( method_exists( $facet_types[$name], 'settings_js' ) ) {
                $settings_name = str_replace("_", "", ucwords($name, " /_"));
                $settings = $facet_types[$name]->settings_js( [] );

                foreach ( $settings as $setting_key => $setting ) {
                    if ( is_array( $setting ) ) {
                        // recurse
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
     * Register output types.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function register_custom_output_types( $config ) {

        $type = $config['type'];
        $singular = $config['singular'];
        $field = $config['field'];

        register_graphql_object_type( $field, [
            'description' => __( $singular . ' FacetWP Payload', 'wpgraphql-facetwp' ),
            'fields' => [
                'facets' => [
                    'type'  => [
                        'list_of' => 'Facet',
                    ],
                ],
            ],
        ] );

    }

    /**
     * Register input argument types.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function register_input_arg_types( $config ) {
        
        $field = $config['field'];
        
        $facets = FWP()->helper->get_facets();

        register_graphql_input_type( 'FacetQueryArgs', [
            'description' => __( 'Seleted facets for ' . $field . ' query', 'wpgraphql-facetwp' ),
            'fields'      => array_reduce( $facets, function( $prev, $cur ) {
                
                if ( $cur && $cur['name'] ) {

                    $type = [
                        'list_of' => 'String'
                    ];

                    switch ( $cur['type'] ) {
                        case 'fselect':
                            // Single string for single fSelect
                            if ( $cur['multiple'] === 'yes' ) break;
                        case 'radio':
                        case 'search':
                            // Single string
                            $type = 'String';

                            break;
                        case 'date_range':
                            // Custom payload
                            $type = 'FacetDateRangeArgs';

                            register_graphql_input_type( $type, [
                                'description'   => __( 'Input args for Date Range facet type', 'wpgraphql-facetwp' ),
                                'fields'        => [
                                    'mix' => [
                                        'type'  => 'String',
                                    ],
                                    'max'   => [
                                        'type'  => 'String',
                                    ],
                                ],
                            ]);

                            break;
                        case 'number_range':
                            // Custom payload
                            $type = 'FacetNumberRangeArgs';

                            register_graphql_input_type( $type, [
                                'description'   => __( 'Input args for Number Range facet type', 'wpgraphql-facetwp' ),
                                'fields'        => [
                                    'min' => [
                                        'type'  => 'Int',
                                    ],
                                    'max'   => [
                                        'type'  => 'Int',
                                    ],
                                ],
                            ]);

                            break;
                        case 'slider':
                            // Custom payload
                            $type = 'FacetSliderArgs';

                            register_graphql_input_type( $type, [
                                'description'   => __( 'Input args for Slider facet type', 'wpgraphql-facetwp' ),
                                'fields'        => [
                                    'min' => [
                                        'type'  => 'Float',
                                    ],
                                    'max'   => [
                                        'type'  => 'Float',
                                    ],
                                ],
                            ]);

                            break;
                        case 'proximity':
                            // Custom payload
                            $type = 'FacetProximityArgs';

                            register_graphql_enum_type( 'FacetProximityRadiusOptions', [
                                'description'   => __( 'Proximity radius options', 'wpgraphql-facetwp' ),
                                'values'        => [
                                    WPEnumType::get_safe_name( '10' )    => [
                                        'description'   => __( 'Radius of 10', 'wpgraphql-facetwp' ),
                                        'value'         => 10,
                                    ],
                                    WPEnumType::get_safe_name( '25' )    => [
                                        'description'   => __( 'Radius of 25', 'wpgraphql-facetwp' ),
                                        'value'         => 25,
                                    ],
                                    WPEnumType::get_safe_name( '50' )    => [
                                        'description'   => __( 'Radius of 50', 'wpgraphql-facetwp' ),
                                        'value'         => 50,
                                    ],
                                    WPEnumType::get_safe_name( '100' )   => [
                                        'description'   => __( 'Radius of 100', 'wpgraphql-facetwp' ),
                                        'value'         => 100,
                                    ],
                                    WPEnumType::get_safe_name( '250' )   => [
                                        'description'   => __( 'Radius of 250', 'wpgraphql-facetwp' ),
                                        'value'         => 250,
                                    ],
                                ],
                            ] );

                            register_graphql_input_type( $type, [
                                'description'   => __( 'Input args for Number Range facet type', 'wpgraphql-facetwp' ),
                                'fields'        => [
                                    'latitude' => [
                                        'type'  => 'Float',
                                    ],
                                    'longitude' => [
                                        'type'  => 'Float',
                                    ],
                                    'chosenRadius' => [
                                        'type'  => 'FacetProximityRadiusOptions',
                                    ],
                                    'locationName' => [
                                        'type'  => 'String',
                                    ],
                                ],
                            ]);

                            break;
                        case 'rating':
                            // Single Int
                            $type = 'Int';

                            break;
                        case 'autocomplete':
                        case 'checkboxes':
                        case 'dropdown':
                        case 'hierarchy':
                            // String array - default
                            break;
                    }

                    $prev[$cur['name']] = [
                        'type'          => $type,
                        'description'   => __( $cur['label'] . ' facet query', 'wpgraphql-facetwp' ),
                    ];
                }
    
                return $prev;
            }, [] ),
        ] );

        register_graphql_input_type( $field . 'WhereArgs', [
            'description' => __( 'Arguments for ' . $field . ' query', 'wpgraphql-facetwp' ),
            'fields'      => [
                'status'    => [
                    'type' => 'PostStatusEnum',
                ],
                'query'     => [
                    'type' => 'FacetQueryArgs',
                ],
            ],
        ] );
    }


    /**
     * Parse WPGraphQL query into FacetWP query
     *
     * @access private
     * @since  0.0.1
     * @return mixed FacetWP query
     */
    private function parse_query( $query ) {

        $facets = FWP()->helper->get_facets();

        return array_reduce( $facets, function( $prev, $cur) use ( $query ) {

            $name = $cur['name'];
            $facet = $query[$name];

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
                        $prev[$name] = $facet;
                        break;
                    case 'slider':
                    case 'date_range':
                    case 'number_range':
                        $input = $facet;
                        $prev[$name] = [
                            $input['min'],
                            $input['max'],
                        ];

                        break;
                    case 'proximity':
                        $input = $facet;
                        $prev[$name] = [
                            $input['latitude'],
                            $input['longitude'],
                            $input['chosenRadius'],
                            $input['locationName'],
                        ];
                        break;
                }
            }

            return $prev;

        }, [] );

    }

    // move to helper class
    private static function to_camel_case( $input ) {

        if ( is_array( $input ) ) {

            $out;

            foreach ( $input as $key => $value ) {
                $key = self::to_camel_case( $key );
                $out[$key] = $value;
            }

            return $out;
        }

        return lcfirst( str_replace( '_', '', ucwords( $input, ' /_' ) ) );
        
    }
}

add_action( 'init', 'wpgraphql_facetwp_init' );

if ( ! function_exists( 'wpgraphql_facetwp_init' ) ) {
    /**
     * Function that instantiates the plugins main class
     *
     * @since 0.0.1
     */
    function wpgraphql_facetwp_init() {

        /**
         * Return an instance of the action
         */
        return \WPGraphQL_FacetWP::instance();
    }
}

/**
 * Register a post type as a FacetWP queryable
 *
 * @param string $type_name The name of the WP object type to register
 */
function register_graphql_facet_type( $type_name ) {
    \WPGraphQL_FacetWP::register( $type_name );
}
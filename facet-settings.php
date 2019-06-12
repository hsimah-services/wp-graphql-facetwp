<?php

function get_facet_settings() {
    register_graphql_object_type( 'CheckboxesSettings', [
        'description' => __( 'Settings for Checkboxes facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'showExpanded' => [
                'type' => 'Boolean',
                'description' => __( 'UI should show expanded facet options', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );

    register_graphql_object_type( 'FselectSettings', [
        'description' => __( 'Settings for Fselect facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'placeholder' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'overflowText' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'searchText' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'noResultsText' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'operator' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );

    register_graphql_object_type( 'SearchSettings', [
        'description' => __( 'Settings for Search facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'auto_refresh' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );

    register_graphql_object_type( 'SliderRangeSettings', [
        'description' => __( 'Range settings for Slider facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'min' => [
                'type' => 'Float',
                'description' => __( 'Slider min value', 'wpgraphql-facetwp' ),
            ],
            'max' => [
                'type' => 'Float',
                'description' => __( 'Slider max value', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );

    register_graphql_object_type( 'SliderSettings', [
        'description' => __( 'Settings for Slider facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'range' => [
                'type' => 'SliderRangeSettings',
                'description' => __( 'Selected slider range values' , 'wpgraphql-facetwp' ),
            ],
            'decimal_separator' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'thousands_separator' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'start' => [
                'type' => 'SliderRangeSettings',
                'description' => __( 'Starting min and max position for the slider', 'wpgraphql-facetwp' ),
            ],
            'format' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'prefix' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'suffix' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
            'step' => [
                'type' => 'Int',
                'description' => __( 'The amount of increase between intervals', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );

    register_graphql_object_type( 'DateRangeSettings', [
        'description' => __( 'Settings for DateRange facets', 'wpgraphql-facetwp' ),
        'fields' => [
            'format' => [
                'type' => 'String',
                'description' => __( '...', 'wpgraphql-facetwp' ),
            ],
        ],
    ] );
}
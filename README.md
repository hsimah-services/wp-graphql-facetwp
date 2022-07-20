![Logo](./logo.png)
# WPGraphQL-FacetWP: WPGraphQL provider for FacetWP

## Quick Install
Download and install like any WordPress plugin.

## Documentation
The WPGraphQL documentation can be found [here](https://docs.wpgraphql.com).
The FacetWP documentation can be found [here](https://facetwp.com/documentation/).

- Requires WPGraphQL 1.6.0+
- Requires FacetWP 3.5.7+

## Overview
This plugin exposes configured facets through the graph schema. Once registered for a type, a query is available. The payload includes both facet choices and information and a connection to the post type data. This allows for standard GraphQL pagination of the returned data set.

This plugin has been tested and is functional with SearchWP.

## Usage:
**It is assumed that facets have been configured**

To register a FacetWP query in the WPGraphQL schema for a WordPress post type (eg `post`) simply call the following function:
```
// Register facet for Posts
add_action( 'graphql_register_types', function () {
  register_graphql_facet_type( 'post' );
} );
```

This will create a WPGraphQL `postFacet` field on the `RootQuery`. The payload includes a collection of queried `facets` and a `posts` connection. The connection is a standard WPGraphQL connection supporting pagination and server side ordering. The connection payload only includes filtered posts.

A simple query might look like this:
```
query GetPosts($query: FacetQueryArgs, $after: String, $search: String, $orderBy: [PostObjectsConnectionOrderbyInput]) {
  postFacet(where: {status: PUBLISH, query: $query}) {
    facets {
      selected
      name
      label
      choices {
        value
        label
        count
      }
    }
    posts(first: 10, after: $after, where: {search: $search, orderby: $orderBy}) {
      pageInfo {
        hasNextPage
        endCursor
      }
      nodes {
        title
        excerpt
      }
    }
  }
}
```

## WooCommerce Support

Support for WooCommerce Products can be added with following configuration:

```php
add_action('graphql_register_types', function () {
    register_graphql_facet_type('product');
});

add_filter('facetwp_graphql_facet_connection_config', function (array $default_graphql_config, array $config) {
    $type = $config['type'];
    $singular = $config['singular'];
    $field = $config['field'];
    $plural = $config['plural'];

    return [
        'fromType'          => $field,
        'toType'            => $singular,
        'fromFieldName'     => lcfirst($plural),
        'connectionArgs'    => Products::get_connection_args(),
        'resolveNode'       => function ($node, $_args, $context) use ($type) {
            return $context->get_loader($type)->load_deferred($node->ID);
        },
        'resolve'           => function ($source, $args, $context, $info) use ($type) {
            $resolver = new PostObjectConnectionResolver($source, $args, $context, $info, $type);

            if ($type === 'product') {
              $resolver = Products::set_ordering_query_args( $resolver, $args );
            }

            return $resolver
                    ->set_query_arg('post__in', $source['results'])
                    ->get_connection();
        },
    ];
}, 100, 2);
```

## Limitations
Currently the plugin only has been tested using Checkbox and Radio facet types. Support for additional types is in development.

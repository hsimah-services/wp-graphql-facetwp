![Logo](./logo.png)
# WPGraphQL for FacetWP

Adds WPGraphQL support for [FacetWP](https://facetwp.com/).

* [Join the WPGraphQL community on Slack.](https://join.slack.com/t/wp-graphql/shared_invite/zt-3vloo60z-PpJV2PFIwEathWDOxCTTLA)
* [Documentation](#usage)
-----

![Packagist License](https://img.shields.io/packagist/l/hsimah-services/wp-graphql-facetwp?color=green) ![Packagist Version](https://img.shields.io/packagist/v/hsimah-services/wp-graphql-facetwp?label=stable) ![GitHub commits since latest release (by SemVer)](https://img.shields.io/github/commits-since/hsimah-services/wp-graphql-facetwp/0.4.1) ![GitHub forks](https://img.shields.io/github/forks/hsimah-services/wp-graphql-facetwp?style=social) ![GitHub Repo stars](https://img.shields.io/github/stars/hsimah-services/wp-graphql-facetwp?style=social)<br />
![CodeQuality](https://img.shields.io/github/actions/workflow/status/hsimah-services/wp-graphql-facetwp/code-quality.yml?branch=develop&label=Code%20Quality)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/hsimah-services/wp-graphql-facetwp/integration-testing.yml?branch=develop&label=Integration%20Testing)
![Coding Standards](https://img.shields.io/github/actions/workflow/status/hsimah-services/wp-graphql-facetwp/code-standard.yml?branch=develop&label=WordPress%20Coding%20Standards)

-----
## Overview

This plugin exposes configured facets through the graph schema. Once registered for a type, a query is available. The payload includes both facet choices and information and a connection to the post type data. This allows for standard GraphQL pagination of the returned data set.

This plugin has been tested and is functional with SearchWP.

## System Requirements

* PHP 7.4-8.1.x
* WordPress 5.4.1+
* WPGraphQL 1.6.0+ (1.9.0+ recommended)
* FacetWP 4.0

## Quick Install

1. Install & activate [WPGraphQL](https://www.wpgraphql.com/).
2. Install & activate [FacetWP](https://facetwp.com/).
3. Download the [latest release](https://github.com/hsimah-services/wp-graphql-facetwp/releases) `.zip` file, upload it to your WordPress install, and activate the plugin.

### With Composer

```console
composer require hsimah-services/wp-graphql-facetwp
```
## Updating and Versioning

As we work towards a 1.0 Release, we will need to introduce **numerous** breaking changes. We will do our best to group multiple breaking changes together in a single release, to make it easier on developers to keep their projects up-to-date.

Until we hit v1.0, we're using a modified version of [SemVer](https://semver.org/), where:

* v0.**x**: "Major" releases. These releases introduce new features, and _may_ contain breaking changes to either the PHP API or the GraphQL schema
* v0.x.**y**: "Minor" releases. These releases introduce new features and enhancements and address bugs. They _do not_ contain breaking changes.
* v0.x.y.**z**: "Patch" releases. These releases are reserved for addressing issue with the previous release only.

## Usage:

- _The WPGraphQL documentation can be found [here](https://docs.wpgraphql.com)._ <br />
- _The FacetWP documentation can be found [here](https://facetwp.com/documentation/)._

### Registering a facet to WPGraphQL

**It is assumed that facets have been configured.**

To register a FacetWP query in the WPGraphQL schema for a WordPress post type (eg `post`) simply call the following function:

```php
// Register facet for Posts
add_action( 'graphql_facetwp_init', function () {
  register_graphql_facet_type( 'post' );
} );
```

This will create a WPGraphQL `postFacet` field on the `RootQuery`. The payload includes a collection of queried `facets` and a `posts` connection. The connection is a standard WPGraphQL connection supporting pagination and server side ordering. The connection payload only includes filtered posts.

### Example query


**Note** This is not a complete list of GraphQL fields and types added to the schema. Please refer to the WPGraph<strong>i</strong>QL IDE for more queries and their documentation.

```graphql
query GetPostsByFacet( $query: FacetQueryArgs, $after: String, $search: String, $orderBy: [PostObjectsConnectionOrderbyInput] ) {
  postFacet(
    where: { 
      status: PUBLISH,
      query: $query # The query arguments are determined by the Facet type.
    }
  ) {
    facets { # The facet configuration
      selected
      name
      label
      choices {
        value
        label
        count
      }
    }
    posts ( # The results of the facet query. Can be filtered by WPGraphQL connection where args 
      first: 10,
      after: $after,
      where: { search: $search, orderby: $orderBy}
    ) {
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

### WooCommerce Support

Support for WooCommerce Products can be added with following configuration:

```php
add_action( 'graphql_register_types', function () {
  register_graphql_facet_type( 'product' );
});

add_filter( 'facetwp_graphql_facet_connection_config', 
  function ( array $default_graphql_config, array $config ) {
    $type     = $config['type'];
    $singular = $config['singular'];
    $field    = $config['field'];
    $plural   = $config['plural'];

    return [
        'fromType'          => $field,
        'toType'            => $singular,
        'fromFieldName'     => lcfirst( $plural ),
        'connectionArgs'    => Products::get_connection_args(),
        'resolveNode'       => function ( $node, $_args, $context) use ( $type ) {
            return $context->get_loader( $type )->load_deferred( $node->ID );
        },
        'resolve'           => function ( $source, $args, $context, $info ) use ( $type ) {
            $resolver = new PostObjectConnectionResolver( $source, $args, $context, $info, $type);

            if ( $type === 'product' ) {
              $resolver = Products::set_ordering_query_args( $resolver, $args );
            }

            if( ! empty( $source['results'] ) ) {
              $resolver->->set_query_arg( 'post__in', $source['results'] );
            }

            return $resolver ->get_connection();
        },
    ];
  },
   100,
  2
);
```

### Limitations
Currently the plugin only has been tested using Checkbox and Radio facet types. Support for additional types is in development.

## Testing

1. Update your `.env` file to your testing environment specifications.
2. Run `composer install-test-env` to create the test environment.
3. Run your test suite with [Codeception](https://codeception.com/docs/02-GettingStarted#Running-Tests).
E.g. `vendor/bin/codecept run wpunit` will run all WPUnit tests.

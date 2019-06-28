![Logo](./logo.png)
# WPGraphQL-FacetWP: WPGraphQL provider for FacetWP

## Quick Install
Download and install like any WordPress plugin.

## Documentation
The WPGraphQL documentation can be found [here](https://docs.wpgraphql.com).
The FacetWP documentation can be found [here](https://facetwp.com/documentation/).

- Requires PHP 5.5+
- Requires WordPress 4.7+
- Requires WPGraphQL 0.3.2+
- Requires FacetWP 3.3.9+

## Overview
This plugin exposes configured facets through the graph schema. Once registered for a type, a query is available. The payload includes both facet choices and information and a connection to the post type data. This allows for standard GraphQL pagination of the returned data set.

## Usage:
**It is assumed that facets have been configured**

To register a FacetWP query in the WPGraphQL schema for a WordPress post type (eg `post`) simply call the following function:
```
// Register facet for Posts
register_graphql_facet_type( 'post' );
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

## Limitations
Currently the plugin only has been tested using Checkbox and Radio facet types. Support for additional types is in development.

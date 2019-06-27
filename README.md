WPGraphQL provider for FacetWP

Usage:
```
$type = 'post'; // set WP post type for facet to filter
register_graphql_facet_type( $type );
```

This will register a WPGraphQL `TypeFacet` field on the `RootQuery`. The payload includes a collection of queried `facets` and a `types` connection. The connection is a standard WPGraphQL connection supporting pagination and server side ordering. The connection payload only includes filtered posts.

See FacetWP and WPGraphQL documentation for more info at this time, or join #facetwp on the WPGraphQL slack workspace.

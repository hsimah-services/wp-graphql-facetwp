# Changelog

## Unreleased
- chore: update Composer dependencies.
- chore: switch `poolshark/wp-graphql-stubs` for `axepress/wp-graphql-stubs`
- chore: stub `FWP()` and FacetWP class properties.
- chore: change stubfile extensions to `.php`.

## v.0.4.0
This _major_ release refactors the underlying PHP codebase, bringing with it support for the latest versions of WPGraphQL and FacetWP. Care has been taken to ensure there are _no breaking changes_ to the GraphQL schema.

- feat!: Refactor plugin PHP classes and codebase structure to follow ecosystem patterns.
- feat!: Bump minimum version of WPGraphQL to `v1.6.0`.
- feat!: Bump minimum PHP version to `v7.4`.
- feat!: Bump minimum FacetWP version to `v4.0`.
- fix: Implement `WPVIP` PHP coding standards.
- fix: Implement and meet `PHPStan` level 8 coding standards.
- tests: Implement basic Codeception acceptance tests.
- ci: Add Github workflows for PRs and releases.
- chore: update Composer dependencies.
- chore: switch commit flow to `develop` => `main` and set default branch to `develop`. The existing `master` branch will be removed on 1 October 2022.

## v0.3.0
- feat: Updates with default connection inputs and WooCommerce integration hooks.

## v0.2.0
- fix: Updated deprecated calls to WPGraphQL functions.
- docs: Updated documentation to remind users to register Facets during GraphQL init.

## v0.1.1
- fix: facet connection used old style node resolver.

## v0.1.0
Initial release.


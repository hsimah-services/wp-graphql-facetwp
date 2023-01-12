<?php
/**
 * WPGraphQL test case
 *
 * For testing WPGraphQL responses.
 *
 * @since 0.8.0
 * @package Tests\WPGraphQL\TestCase
 */

namespace Tests\WPGraphQL\FacetWP\TestCase;

use ReflectionProperty;

/**
 * Class - GraphQLTestCase
 */
class FWPGraphQLTestCase extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	public $fwp;

	/**
	 * Creates users and loads factories.
	 */
	public function setUp() : void {
		parent::setUp();
	}

	/**
	 * Post test tear down.
	 */
	public function tearDown(): void {
		$this->clearFacets();
		$this->clearSchema();
		// Then...
		parent::tearDown();
	}

	public function register_facet( array $config = [] ) : void {
		$defaults = [
			'label' => 'Categories',
			'name' => 'categories',
			'type' => 'checkboxes',
			'source' => 'tax/category',
			'parent_term' => '',
			'hierarchical' => 'no',
			'orderby' => 'count',
			'count' => '20',
			'show_expanded' => 'no',
			'ghosts' => 'no',
			'preserve_ghosts' => 'no',
			'operator' => 'and',
			'soft_limit' => '5',
			'show_in_graphql' => true,
		];

		$config = array_merge( $defaults, $config );

		FWP()->helper->settings['facets'][] = $config;
	}

	public function clearFacets() : void {
		unset( FWP()->helper->settings['facets'] );

		// Clear the FacetRegistry::$facets property.
		$facets_property = new ReflectionProperty( 'WPGraphQL\FacetWP\Registry\FacetRegistry', 'facets' );
		$facets_property->setAccessible( true );
		$facets_property->setValue( null, null );
	}
}

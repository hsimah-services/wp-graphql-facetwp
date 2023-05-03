<?php
/**
 * FWPGraphQL test case
 *
 * For testing WPGraphQL FacetWP response
 *
 * @since 0.4.0
 * @package Tests\WPGraphQL\FacetWP\TestCase
 */

namespace Tests\WPGraphQL\FacetWP\TestCase;

use ReflectionProperty;

/**
 * Class - FWPGraphQLTestCase
 */
class FWPGraphQLTestCase extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	/**
	 * Creates users and loads factories.
	 */
	public function setUp() : void {
		parent::setUp();
		$this->clearFacets();
		$this->clearSchema();

		unset( FWP()->helper->term_cache );
	}

	/**
	 * Post test tear down.
	 */
	public function tearDown(): void {
		$this->clearFacets();
		$this->clearSchema();

		unset( FWP()->helper->term_cache );
		FWP()->indexer->index();

		// Then...
		parent::tearDown();
	}

	public function register_facet( array $config = [] ) : void {
		$defaults = $this->tester->get_default_checkbox_facet_args();

		$config = array_merge( $defaults, $config );

		FWP()->helper->settings['facets'][] = $config;
	}

	public function clearFacets() : void {
		FWP()->helper->settings['facets'] = [];
		unset( FWP()->facet->facets );

		// Clear the FacetRegistry::$facets property.
		$facets_property = new ReflectionProperty( 'WPGraphQL\FacetWP\Registry\FacetRegistry', 'facets' );
		$facets_property->setAccessible( true );
		$facets_property->setValue( null, null );
	}
}

<?php
/**
 * Facet test case
 *
 * For testing individual facets in WPGraphQL.
 *
 * @since @todo
 * @package Tests\WPGraphQL\FacetWP\TestCase
 */

namespace Tests\WPGraphQL\FacetWP\TestCase;

use ReflectionClass;

/**
 * Class - FacetTestCase
 */
class FacetTestCase extends FWPGraphQLTestCase {
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
		// Then...
		parent::tearDown();
	}
}

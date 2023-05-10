<?php

use WPGraphQL\FacetWP\Main;

class MainTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \WpunitTesterActions
	 */
	protected $tester;
	public $instance;

	public function setUp(): void {
		// Before...
		parent::setUp();
		\WPGraphQL::clear_schema();

		// Clear Plugin instance
		$reflection = new ReflectionClass( Main::class );
		$property   = $reflection->getProperty( 'instance' );
		$property->setAccessible( true );
		$property->setValue( null );
	}

	public function tearDown(): void {
		// Your tear down methods here.

		unset( $this->instance );
		\WPGraphQL::clear_schema();

		// Then...
		parent::tearDown();
	}

	// Tests
	/**
	 * Test instance
	 *
	 * @covers \WPGraphQL\FacetWP\Main
	 */
	public function testInstance() {
		$this->instance = new Main();

		$this->assertTrue( $this->instance instanceof Main );
	}
	/**
	 * Test instance
	 *
	 * @covers \WPGraphQL\FacetWP\Main
	 */
	public function testInstanceBeforeInstantiation() {
		$instances = Main::instance();
		$this->assertNotEmpty( $instances );
	}
}

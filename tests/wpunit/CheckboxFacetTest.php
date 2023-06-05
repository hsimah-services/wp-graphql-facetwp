<?php

use Tests\WPGraphQL\FacetWP\TestCase\FacetTestCase;

/**
 * Tests Checkbox facet.
 */
class CheckboxFacetTest extends FacetTestCase {
	protected $term_one_id;
	protected $term_two_id;
	protected $term_one_post_ids;
	protected $term_two_post_ids;
	protected $facet_config;
	protected $tester;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		$this->facet_config = $this->tester->get_default_checkbox_facet_args();

		// Create facet.
		$this->register_facet( $this->facet_config );

		
		// Create test data.
		$this->term_one_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category One',
			]
		);
		$this->term_two_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category Two',
			]
		);

		$this->term_one_post_ids = $this->factory()->post->create_many(
			5,
			[
				'post_category' => [ $this->term_one_id ],
				'post_status'   => 'publish',
			]
		);
		$this->term_two_post_ids = $this->factory()->post->create_many(
			5,
			[
				'post_category' => [ $this->term_two_id ],
				'post_status'   => 'publish',
			]
		);

	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		foreach( $this->term_one_post_ids as $id ) {
			wp_delete_post( $id, true );
		}
		foreach( $this->term_two_post_ids as $id ) {
			wp_delete_post( $id, true );
		}

		wp_delete_term( $this->term_one_id, 'category' );
		wp_delete_term( $this->term_two_id, 'category' );

		parent::tearDown();
	}

	public function testFacetInputExists() : void {
		// Check if `FacetsInput` exists with our facet input field.
		$query = '
			query GetFacetInput {
				__type( name: "FacetsInput" ) {
					name
					kind
					inputFields {
						name
						type {
							name
							kind
							ofType {
								name
								kind
							}
						}
					}
				}
			}
		';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertIsValidQueryResponse( $actual );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertEquals( 'FacetsInput', $actual['data']['__type']['name'], 'FacetsInput type does not exist' );
		$this->assertEquals( $this->facet_config['name'], $actual['data']['__type']['inputFields'][0]['name'], 'FacetsInput does not have the correct input field' );
		$this->assertEquals( 'LIST', $actual['data']['__type']['inputFields'][0]['type']['kind'], 'FacetsInput input field is not a list' );
		$this->assertEquals( 'String', $actual['data']['__type']['inputFields'][0]['type']['ofType']['name'], 'FacetsInput input field is not a list of strings' );

		// Check if `facets` is registered  on the `RootQueryToPostConnectionWhereArgs` input.
		$query = '
			query GetRootQueryToPostConnectionWhereArgs {
				__type( name: "RootQueryToPostConnectionWhereArgs" ) {
					name
					kind
					inputFields {
						name
						type {
							name
							kind
							ofType {
								name
								kind
							}
						}
					}
				}
			}
		';

		$actual = graphql( compact( 'query' ) );

		$this->assertIsValidQueryResponse( $actual );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$key = array_search( 'facets', array_column( $actual['data']['__type']['inputFields'], 'name' ) );
		$this->assertNotFalse( $key, 'facets input field does not exist on RootQueryToPostConnectionWhereArgs' );
		$this->assertEquals( 'FacetsInput', $actual['data']['__type']['inputFields'][ $key ]['type']['name'], 'facets input field is not of type FacetsInput' );
	}
	
	public function getQuery() : string {
		return '
			query GetPostsByFacet( $where: RootQueryToPostConnectionWhereArgs ) {
				posts( where: $where ) {
					edges {
						activeFacets{
							type
							name
						}
					}
					nodes {
						databaseId
						title
						categories {
							nodes {
								databaseId
								name
							}
						}
					}
				}
			}
		';
	}

	public function testFilterConnectionByFacet() : void {
		$query = $this->getQuery();

		$expected_term = get_term( $this->term_one_id, 'category' );

		$variables = [
			'where' => [
				'facets' => [
					$this->facet_config['name'] => $expected_term->slug,
				],
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertIsValidQueryResponse( $actual );
		$this->assertArrayNotHasKey( 'errors', $actual );

		// Ensure filter worked.
		$this->assertCount( 5, $actual['data']['posts']['nodes'], 'Expected 5 posts to be returned' );
		// Test the individual posts returned.
		foreach( $actual['data']['posts']['nodes'] as $post ) {
			$this->assertEquals( $expected_term->term_id, $post['categories']['nodes'][0]['databaseId'], 'Post returned is not in the expected category' );
		}
	}

}

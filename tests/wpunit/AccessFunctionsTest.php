<?php

use Tests\WPGraphQL\FacetWP\TestCase\FWPGraphQLTestCase;

/**
 * Tests access functons
 */
class AccessFunctionsTest extends FWPGraphQLTestCase {
	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		$settings                                 = get_option( 'graphql_general_settings' );
		$settings['public_introspection_enabled'] = 'on';
		update_option( 'graphql_general_settings', $settings );
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		$this->clearSchema();
		parent::tearDown();
	}


	public function testRegisterGraphQLFacetType() {
		// Create test data.
		$term_one_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category One',
			]
		);
		$term_two_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category Two',
			]
		);

		$term_one_post_ids = $this->factory()->post->create_many(
			5,
			[
				'post_category' => [ $term_one_id ],
				'post_status'   => 'publish',
			]
		);

		$term_two_post_ids = $this->factory()->post->create_many(
			5,
			[
				'post_category' => [ $term_two_id ],
				'post_status'   => 'publish',
			]
		);

		// Run indexer.
		FWP()->indexer->index();

		// Register facet.
		register_graphql_facet_type( 'post' );

		$query = '
			query GetPostsByFacet($query: FacetQueryArgs ) {
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
						type
						settings {
							overflowText
							placeholder
							autoRefresh
							decimalSeparator
							format
							noResultsText
							operator
							prefix
							range {
								min
								max
							}
							searchText
							showExpanded
							start {
								max
								min
							}
							step
							suffix
							thousandsSeparator
						}
					}
					pager {
						page
						per_page
						total_pages
						total_rows
					}
					posts(first: 10 ) {
						pageInfo {
							hasNextPage
							endCursor
						}
						nodes {
							title
							excerpt
							categories {
								nodes {
									databaseId
									name
								}
							}
						}
					}
				}
			}
		';

		$variables = [
			'query' => [
				'categories' => [ 'category-one' ],
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertIsValidQueryResponse( $actual );
		$this->assertArrayNotHasKey( 'errors', $actual );

		// Check that the facet is returned.
		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'postFacet',
					[
						$this->expectedNode(
							'facets',
							[
								$this->expectedField( 'selected', [ 'category-one' ] ),
								$this->expectedField( 'name', 'categories' ),
								$this->expectedField( 'label', 'Categories' ),
								$this->expectedNode(
									'choices',
									[
										$this->expectedField( 'value', 'category-one' ),
										$this->expectedField( 'label', 'Category One' ),
										$this->expectedField( 'count', 5 ),
									]
								),
								$this->expectedField( 'type', 'checkboxes' ),
								$this->expectedObject(
									'settings',
									[
										$this->expectedField('showExpanded', 'no' )
									]
								),
							],
							0
						),
						$this->expectedNode(
							'pager',
							[
								$this->expectedField( 'page', 1 ),
								$this->expectedField( 'per_page', 10 ),
								$this->expectedField( 'total_pages', 1 ),
								$this->expectedField( 'total_rows', 5 ),
							]
						),
					]
				)
			]
		);

		// Check that the correct posts are returned.
		$this->assertNonEmptyMultidimensionalArray( $actual['data']['postFacet']['posts']['nodes'] );
		$this->assertCount( 5, $actual['data']['postFacet']['posts']['nodes'] );
		$posts_nodes_categories = array_column( $actual['data']['postFacet']['posts']['nodes'], 'categories' );
		
		foreach ($posts_nodes_categories as $node ) {
			$this->assertEquals( $term_one_id, $node['nodes'][0]['databaseId'] );
		}
	}

}

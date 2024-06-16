<?php

use Tests\WPGraphQL\FacetWP\TestCase\FWPGraphQLTestCase;
use WPGraphQL\Type\WPEnumType;

/**
 * Tests access functons
 */
class AccessFunctionsTest extends FWPGraphQLTestCase {
	protected \WpunitTester $tester;

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
		delete_option( 'graphql_general_settings' );

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

		$config = array_merge(
			$this->tester->get_default_checkbox_facet_args(),
			[
				'name'   => 'categories',
				'label'  => 'Categories',
				'source' => 'tax/category',
			]
		);

		$this->register_facet( 'checkbox', $config );

		// Run indexer.
		FWP()->indexer->index();

		// Register facet.
		register_graphql_facet_type( 'post' );

		// Query for fields registered to FacetQueryArgs
		$query = '
			query GetFacetQueryArgs{
				__type(name: "FacetQueryArgs") {
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

		$expected = get_graphql_allowed_facets()[0];

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
				$expected['graphql_field_name'] => [ 'category-one' ],
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertResponseIsValid( $actual );
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
								$this->expectedField( 'name', $expected['name'] ),
								$this->expectedField( 'label', $expected['label'] ),
								$this->expectedNode(
									'choices',
									[
										$this->expectedField( 'value', 'category-one' ),
										$this->expectedField( 'label', 'Category One' ),
										$this->expectedField( 'count', 5 ),
									]
								),
								$this->expectedField( 'type', WPEnumType::get_safe_name( $expected['type']) ),
								$this->expectedObject(
									'settings',
									[
										$this->expectedField( 'showExpanded', $expected['show_expanded'] ),
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
				),
			]
		);

		// Check that the correct posts are returned.
		$this->assertNonEmptyMultidimensionalArray( $actual['data']['postFacet']['posts']['nodes'] );
		$this->assertCount( 5, $actual['data']['postFacet']['posts']['nodes'] );
		$posts_nodes_categories = array_column( $actual['data']['postFacet']['posts']['nodes'], 'categories' );

		foreach ( $posts_nodes_categories as $node ) {
			$this->assertEquals( $term_one_id, $node['nodes'][0]['databaseId'] );
		}

		// Cleanup
		wp_delete_term( $term_one_id, 'category' );
		wp_delete_term( $term_two_id, 'category' );
	}

	public function testGetGraphqlAllowedFacets() {
		$expected = [
			'name'      => 'test',
			'label'     => 'Test',
			'type'      => 'checkboxes',
			'source'    => 'post_type',
			'post_type' => 'post',
			'choices'   => [
				[
					'value' => 'test',
					'label' => 'Test',
				],
			],
		];
		// Register exposed facet.
		$this->register_facet( 'checkbox', $expected );
		// Register unexposed facet.
		$this->register_facet(
			'checkbox',
			[
				'name'            => 'test2',
				'label'           => 'Test2',
				'type'            => 'checkboxes',
				'source'          => 'post_type',
				'post_type'       => 'post',
				'choices'         => [
					[
						'value' => 'test2',
						'label' => 'Test2',
					],
				],
				'show_in_graphql' => false,
			]
		);
		// Register facet with explicit properties.
		$this->register_facet(
			'checkbox',
			[
				'name'               => 'test3',
				'label'              => 'Test3',
				'type'               => 'checkboxes',
				'source'             => 'post_type',
				'post_type'          => 'post',
				'show_in_graphql'    => true,
				'graphql_field_name' => 'myTestField',
			]
		);

		$allowed_facets = get_graphql_allowed_facets();

		codecept_debug( $allowed_facets );

		// Check only one facet is returned.
		$this->assertEquals( 2, count( $allowed_facets ), 'Only exposed facet should be returned' );

		// Check that the first the correct facet. and the default properties are set.
		$this->assertEquals( $expected['name'], $allowed_facets[0]['name'], 'The first facet should be returned' );
		$this->assertTrue( $allowed_facets[0]['show_in_graphql'] );
		$this->assertEquals( graphql_format_field_name( $expected['name'] ), $allowed_facets[0]['graphql_field_name'], 'The GraphQL field name should be set by default based on the name' );
		$this->assertEquals( [ 'list_of' => 'String' ], $allowed_facets[0]['graphql_type'], 'A checkbox facet should return a String[]' );

		// Test that the GraphQL properties can be overridden.
		$this->assertEquals( 'test3', $allowed_facets[1]['name'], 'The last facet should be returned' );
		$this->assertTrue( $allowed_facets[1]['show_in_graphql'] );
		$this->assertEquals( 'myTestField', $allowed_facets[1]['graphql_field_name'], 'The GraphQL field name should be overridden' );
	}

}

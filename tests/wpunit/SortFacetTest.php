<?php

use Tests\WPGraphQL\FacetWP\TestCase\FWPGraphQLTestCase;
use WPGraphQL\Type\WPEnumType;

/**
 * Tests Sort Facet functionality.
 */
class SortFacetTest extends FWPGraphQLTestCase {
	protected \WpunitTester $tester;
	protected $post_ids;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Create posts.
		$this->post_ids = $this->generate_posts( 10 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		// cleanup.
		foreach ( $this->post_ids as $id ) {
			wp_delete_post( $id, true );
		}

		parent::tearDown();
	}

	protected function get_facet_config( array $overrides = [] ) : array {
		$default_config = $this->tester->get_default_sort_facet_args();

		return array_merge( $default_config, $overrides );
	}

	protected function get_sort_option_orderby( string $type, string $order = 'DESC' ) : array {
		$possible_configs = [
			'date'          => [
				'key'   => 'date',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'ID'            => [
				'key'   => 'ID',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'name'          => [
				'key'   => 'name',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'title'         => [
				'key'   => 'title',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'type'          => [
				'key'   => 'type',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'modified'      => [
				'key'   => 'modified',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'comment_count' => [
				'key'   => 'comment_count',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'menu_order'    => [
				'key'   => 'menu_order',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'post__in'      => [
				'key'   => 'post__in',
				'order' => $order,
				'type'  => 'CHAR',
			],
			'test_meta'     => [
				'key'   => 'cf/test_meta',
				'order' => $order,
				'type'  => 'CHAR',
			],
		];

		return $possible_configs[ $type ];
	}

	protected function generate_posts( int $amt, array $default_args = [] ) : array {
		$results      = [];
		$default_args = array_merge(
			[
				'post_status' => 'publish',
				'post_type'   => 'page',
			],
			$default_args
		);

		for ( $i = 0; $i < $amt; $i++ ) {
			// Stagger the date created and modified times.
			$default_args['post_date']     = date( 'Y-m-d H:i:s', strtotime( "-$i days" ) );
			$default_args['post_modified'] = date( 'Y-m-d H:i:s', strtotime( "-$i days" ) );
			// Set a menu order.
			$default_args['menu_order'] = $i;

			$results[] = $this->factory()->post->create( $default_args );
		}

		return $results;
	}

	protected function get_query() : string {
		return '
			query SortFacetQuery( $query: FacetQueryArgs ){
				pageFacet( where: { query: $query } ) {
					facets {
						label
						name
						type
						selected
						settings {
							defaultLabel
							sortOptions {
								label
								name
								orderby {
									key
									order
								}
							}
						}
					}
					pages {
						nodes {
							databaseId
							date
							modified
							slug
							title
							menuOrder
						}
					}
				}
			}
		';
	}

	protected function assertValidSort( array $actual, string $key, string $direction ) : void {
		for ( $i = 0; $i < count( $actual ); $i++ ) {
			// Bail if we're at the end of the array.
			if ( ! isset( $actual[ $i + 1 ] ) ) {
				break;
			}

			error_log( 'checking if ' . $direction . ' ' . print_r( $actual[ $i ], true ) . print_r( $actual[ $i + 1 ], true ) );

			if ( 'ASC' === $direction ) {
				$this->assertLessThanOrEqual( $actual[ $i + 1 ][ $key ], $actual[ $i ][ $key ], $key . ' is not in ascending order.' );
			} else {
				$this->assertGreaterThanOrEqual( $actual[ $i + 1 ][ $key ], $actual[ $i ][ $key ], $key . ' is not in descending order.' );
			}
		}
	}

	public function testSortFacet() : void {
		$sort_types = [
			'date'       => 'date',
			'ID'         => 'databaseId',
			'name'       => 'slug',
			'title'      => 'title',
			'modified'   => 'modified',
			'menu_order' => 'menuOrder',
		];

		$sort_options = [];

		foreach ( array_keys( $sort_types ) as $key ) {
			$sort_options[] = [
				'label'   => $key . ' (ASC)',
				'name'    => $key . '_asc',
				'orderby' => [
					$this->get_sort_option_orderby( $key, 'ASC' ),
				],
			];
			$sort_options[] = [
				'label'   => $key . ' (DESC)',
				'name'    => $key . '_desc',
				'orderby' => [
					$this->get_sort_option_orderby( $key, 'DESC' ),
				],
			];
		}

		$facet_config = $this->get_facet_config(
			[
				'name'         => 'post_sort',
				'label'        => 'Post sort',
				'sort_options' => $sort_options,
			]
		);

		$this->register_facet( 'sort', $facet_config );
		FWP()->indexer->index();

		// Register facet.
		register_graphql_facet_type( 'page' );

		$query = $this->get_query();

		$variables = [
			'query' => [
				$facet_config['name'] => WPEnumType::get_safe_name( $facet_config['sort_options'][0]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );
		$this->assertQuerySuccessful(
			$actual,
			[
				// Test Facet.
				$this->expectedNode(
					'pageFacet.facets',
					[
						$this->expectedField( 'name', $facet_config['name'] ),
						$this->expectedField( 'label', $facet_config['label'] ),
						$this->expectedField( 'type', $facet_config['type'] ),
						$this->expectedObject(
							'settings',
							[
								$this->expectedField( 'defaultLabel', $facet_config['default_label'] ),
								$this->expectedNode(
									'sortOptions',
									[
										$this->expectedField( 'label', $facet_config['sort_options'][0]['label'] ),
										$this->expectedField( 'name', $facet_config['sort_options'][0]['name'] ),
										$this->expectedNode(
											'orderby',
											[
												$this->expectedField( 'key', $facet_config['sort_options'][0]['orderby'][0]['key'] ),
												$this->expectedField( 'order', WPEnumType::get_safe_name( $facet_config['sort_options'][0]['orderby'][0]['order'] ) ),
											],
											0
										),
									],
									0
								),
							],
						),
					],
					0
				),
			]
		);
		// Test the cont of the sort options.
		$this->assertCount( count( $facet_config['sort_options'] ), $actual['data']['pageFacet']['facets'][0]['settings']['sortOptions'] );

		// Test the post sorting.
		foreach ( $sort_options as $option ) {
			$variables = [
				'query' => [
					$facet_config['name'] => WPEnumType::get_safe_name( $option['name'] ),
				],
			];

			$actual = $this->graphql( compact( 'query', 'variables' ) );

			$this->assertArrayNotHasKey( 'errors', $actual );

			$posts = $actual['data']['pageFacet']['pages']['nodes'];

			$this->assertCount( count( $this->post_ids ), $posts );

			$actual_post_ids = wp_list_pluck( $posts, 'databaseId' );
			sort( $actual_post_ids );
			$this->assertEqualSets( $this->post_ids, $actual_post_ids );

			$this->assertValidSort( $posts, $sort_types[ $option['orderby'][0]['key'] ], $option['orderby'][0]['order'] );
		}
	}

	public function testMultiSortFacetByCommentCount() : void {
		$comment_id_1 = $this->factory->comment->create( [ 'comment_post_ID' => $this->post_ids[3] ] );
		$comment_id_2 = $this->factory->comment->create( [ 'comment_post_ID' => $this->post_ids[3] ] );
		$comment_id_3 = $this->factory->comment->create( [ 'comment_post_ID' => $this->post_ids[7] ] );

		$sort_options = [
			[
				'label'   => 'Comment count (ASC)',
				'name'    => 'comment_count_asc',
				'orderby' => [
					$this->get_sort_option_orderby( 'comment_count', 'ASC' ),
					$this->get_sort_option_orderby( 'date', 'DESC' ),
				],
			],
			[
				'label'   => 'Comment count (DESC)',
				'name'    => 'comment_count_desc',
				'orderby' => [
					$this->get_sort_option_orderby( 'comment_count', 'DESC' ),
					$this->get_sort_option_orderby( 'date', 'ASC' ),
				],
			],
		];

		$facet_config = $this->get_facet_config(
			[
				'name'         => 'comment_sort',
				'label'        => 'Comment multisort',
				'sort_options' => $sort_options,
			]
		);

		$this->register_facet( 'sort', $facet_config );
		FWP()->indexer->index();

		$this->clearSchema();

		register_graphql_facet_type( 'page' );

		$query = $this->get_query();

		// Test ascending.
		$variables = [
			'query' => [
				$facet_config['name'] => WPEnumType::get_safe_name( $facet_config['sort_options'][0]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['pageFacet']['pages']['nodes'];

		// Test most comment post is last.
		$this->assertEquals( $this->post_ids[3], $posts[ count( $posts ) - 1 ]['databaseId'] );
		// Test second most comment post is second to last.
		$this->assertEquals( $this->post_ids[7], $posts[ count( $posts ) - 2 ]['databaseId'] );

		// Test the rest of the posts are in the correct order.
		$this->assertValidSort( array_slice( $posts, 0, count( $posts ) - 2 ), 'date', 'DESC' );

		// Test descending.
		$variables = [
			'query' => [
				$facet_config['name'] => WPEnumType::get_safe_name( $facet_config['sort_options'][1]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['pageFacet']['pages']['nodes'];

		// Test most comment post is first.
		$this->assertEquals( $this->post_ids[3], $posts[0]['databaseId'] );
		// Test second most comment post is second.
		$this->assertEquals( $this->post_ids[7], $posts[1]['databaseId'] );

		// Test the rest of the posts are in the correct order.
		$this->assertValidSort( array_slice( $posts, 2 ), 'date', 'ASC' );

		// Cleanup.
		wp_delete_comment( $comment_id_1, true );
		wp_delete_comment( $comment_id_2, true );
		wp_delete_comment( $comment_id_3, true );
	}

	public function testMultiSortFacetByCustomField() : void {
		// Update the post meta.
		update_post_meta( $this->post_ids[3], 'test_meta', 'a' );
		update_post_meta( $this->post_ids[7], 'test_meta', 'b' );

		$sort_options = [
			[
				'label'   => 'Test meta (ASC)',
				'name'    => 'test_meta_asc',
				'orderby' => [
					$this->get_sort_option_orderby( 'test_meta', 'ASC' ),
					$this->get_sort_option_orderby( 'date', 'DESC' ),
				],
			],
			[
				'label'   => 'Test meta (DESC)',
				'name'    => 'test_meta_desc',
				'orderby' => [
					$this->get_sort_option_orderby( 'test_meta', 'DESC' ),
					$this->get_sort_option_orderby( 'date', 'ASC' ),
				],
			],
		];

		$facet_config = $this->get_facet_config(
			[
				'name'         => 'test_meta_sort',
				'label'        => 'Test meta multisort',
				'sort_options' => $sort_options,
			]
		);

		$this->register_facet( 'sort', $facet_config );
		FWP()->indexer->index();

		$this->clearSchema();

		register_graphql_facet_type( 'page' );

		$query = $this->get_query();

		// Test ascending.
		$variables = [
			'query' => [
				$facet_config['name'] => WPEnumType::get_safe_name( $facet_config['sort_options'][0]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['pageFacet']['pages']['nodes'];

		// Check ascending order.
		$first_post_meta  = get_post_meta( $posts[0]['databaseId'], 'test_meta', true );
		$second_post_meta = get_post_meta( $posts[1]['databaseId'], 'test_meta', true );

		$this->assertLessThanOrEqual( $second_post_meta, $first_post_meta );

		// Test descending.
		$variables = [
			'query' => [
				$facet_config['name'] => WPEnumType::get_safe_name( $facet_config['sort_options'][1]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['pageFacet']['pages']['nodes'];

		$first_post_meta  = get_post_meta( $posts[0]['databaseId'], 'test_meta', true );
		$second_post_meta = get_post_meta( $posts[1]['databaseId'], 'test_meta', true );
		$this->assertGreaterThanOrEqual( $second_post_meta, $first_post_meta );
	}

	public function testSortFacetWithFacet() : void {
		// Create test data.
		$term_one_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Term one',
			]
		);

		$term_two_id = $this->factory()->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Term two',
			]
		);

		$term_one_post_ids = $this->generate_posts(
			3,
			[
				'post_type'     => 'post',
				'post_category' => [ $term_one_id ],
			]
		);

		$term_two_post_ids = $this->generate_posts(
			3,
			[
				'post_type'     => 'post',
				'post_category' => [ $term_two_id ],
			]
		);

		// Checkbox Facet
		$checkbox_config = array_merge(
			$this->tester->get_default_checkbox_facet_args(),
			[
				'name'   => 'categories',
				'label'  => 'Categories',
				'source' => 'tax/category',
			]
		);

		$this->register_facet( 'checkbox', $checkbox_config );

		$sort_options = [
			[
				'label'   => 'Title (ASC)',
				'name'    => 'title_asc',
				'orderby' => [
					$this->get_sort_option_orderby( 'title', 'ASC' ),
				],
			],
			[
				'label'   => 'Title (DESC)',
				'name'    => 'title_desc',
				'orderby' => [
					$this->get_sort_option_orderby( 'title', 'DESC' ),
				],
			],
		];

		$facet_config = $this->get_facet_config(
			[
				'name'         => 'title_sort',
				'label'        => 'Title multisort',
				'sort_options' => $sort_options,
			]
		);

		$this->register_facet( 'sort', $facet_config );
		FWP()->indexer->index();

		register_graphql_facet_type( 'post' );

		$query = '
			query GetSortCheckboxFacetPosts($query: FacetQueryArgs ) {
				postFacet( where: {query: $query} ) {
					posts {
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
			}
		';

		// Test ascending
		$variables = [
			'query' => [
				$checkbox_config['name'] => 'term-one',
				$facet_config['name']    => WPEnumType::get_safe_name( $facet_config['sort_options'][0]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['postFacet']['posts']['nodes'];

		foreach ( $posts as $posts ) {
			$this->assertContains( $term_one_id, wp_list_pluck( $posts['categories']['nodes'], 'databaseId' ) );
		}

		$this->assertValidSort( $posts, 'title', 'ASC' );

		// Test descending.
		$variables = [
			'query' => [
				$checkbox_config['name'] => 'term-one',
				$facet_config['name']    => WPEnumType::get_safe_name( $facet_config['sort_options'][1]['name'] ),
			],
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$posts = $actual['data']['postFacet']['posts']['nodes'];

		foreach ( $posts as $posts ) {
			$this->assertContains( $term_one_id, wp_list_pluck( $posts['categories']['nodes'], 'databaseId' ) );
		}

		$this->assertValidSort( $posts, 'title', 'DESC' );
	}

	public function testMultiSortFacetQueryWithPostType() : void {
		$this->markTestIncomplete( 'Facets are currently limited to a single post type.' );
	}

	public function testMultiSortFacetQueryWithPostIn() : void {
		$this->markTestIncomplete( 'Need to test the Proximity facet, or some other facet that relies on the sort facet.' );
	}

}

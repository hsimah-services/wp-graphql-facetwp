<?php
/**
 * GraphQL Object Type - FacetPager.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Registry\FacetRegistry;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FacetPager
 */
class FacetPager extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetPager';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		if ( ! FacetRegistry::use_graphql_pagination() ) {
			parent::init();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'FacetWP Pager', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'page'        => [
				'type'        => 'Int',
				'description' => __( 'The current page', 'wpgraphql-facetwp' ),
			],
			'per_page'    => [
				'type'        => 'Int',
				'description' => __( 'Results per page', 'wpgraphql-facetwp' ),
			],
			'total_rows'  => [
				'type'        => 'Int',
				'description' => __( 'Total results', 'wpgraphql-facetwp' ),
			],
			'total_pages' => [
				'type'        => 'Int',
				'description' => __( 'Total pages in results', 'wpgraphql-facetwp' ),
			],
		];
	}
}

<?php
/**
 * GraphQL Object Type - FacetPager.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   @todo
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Registry\FacetRegistry;
use WPGraphQL\FacetWP\Type\WPObject\AbstractObject;

/**
 * Class - FacetPager
 */
class FacetPager extends AbstractObject {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetPager';

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
	public static function get_description() : string {
		return __( 'FacetWP Pager', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
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

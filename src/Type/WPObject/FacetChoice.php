<?php
/**
 * GraphQL Object Type - FacetChoice.
 *
 * @package WPGraphQL\FacetWP\Type\WPObject
 * @since   0.4.0
 */

namespace WPGraphQL\FacetWP\Type\WPObject;

use WPGraphQL\FacetWP\Type\WPObject\AbstractObject;

/**
 * Class - FacetChoice
 */
class FacetChoice extends AbstractObject {
	/**
	 * Type registered in WPGraphQL.
	 *
	 * @var string
	 */
	public static $type = 'FacetChoice';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'FacetWP choice', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'count'    => [
				'type'        => 'Int',
				'description' => __( 'Count', 'wpgraphql-facetwp' ),
			],
			'depth'    => [
				'type'        => 'Int',
				'description' => __( 'Depth', 'wpgraphql-facetwp' ),
			],
			'label'    => [
				'type'        => 'String',
				'description' => __( 'Taxonomy label or post title', 'wpgraphql-facetwp' ),
			],
			'parentId' => [
				'type'        => 'Int',
				'description' => __( 'Parent Term ID (Taxonomy choices only', 'wpgraphql-facetwp' ),
			],
			'termId'   => [
				'type'        => 'Int',
				'description' => __( 'Term ID (Taxonomy choices only)', 'wpgraphql-facetwp' ),
			],
			'value'    => [
				'type'        => 'String',
				'description' => __( 'Taxonomy value or post ID', 'wpgraphql-facetwp' ),
			],
		];
	}
}

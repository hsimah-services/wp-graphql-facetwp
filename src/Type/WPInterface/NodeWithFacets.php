<?php
/**
 * The NodeWithFacets GraphQL interface.
 *
 * @package WPGraphQL\FacetWP\Type\WPInterface
 */

namespace WPGraphQL\FacetWP\Type\WPInterface;

use WPGraphQL;
use WPGraphQL\FacetWP\Registry\FacetRegistry;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - NodeWithFacets
 */
class NodeWithFacets extends InterfaceType implements TypeWithInterfaces {

	/**
	 * The WPGraphQL TypeRegistry instance.
	 *
	 * @var ?\WPGraphQL\Registry\TypeRegistry
	 */
	protected static $type_registry = null;

	/**
	 * {@inheritDoc}
	 */
	public static function type_name() : string {
		return 'NodeWithFacets';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register( $type_registry = null ): void {
		parent::register( $type_registry );

		// Register facets to post type.
		register_graphql_interfaces_to_types( self::get_type_name(), 'ContentType' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		$config['eagerlyLoadType'] = true;

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'A node with registered Facets', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'facets' => [
				'type'        => [ 'list_of' => FacetConfig::get_type_name() ],
				'description' => __( 'The Facets registered to this node.', 'wpgraphql-facetwp' ),
				'resolve'     => function() {
					return FacetRegistry::get_allowed_facets();
				},
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [ 'Node' ];
	}
}

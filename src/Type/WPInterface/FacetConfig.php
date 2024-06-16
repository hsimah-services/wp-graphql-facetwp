<?php
/**
 * The FacetConfig GraphQL interface.
 *
 * @package WPGraphQL\FacetWP\Type\WPInterface
 */

namespace WPGraphQL\FacetWP\Type\WPInterface;

use WPGraphQL\FacetWP\Type\Enum\FacetTypeEnum;
use WPGraphQL\FacetWP\Type\WPObject\FacetChoice;
use WPGraphQL\FacetWP\Type\WPObject\FacetSettings;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;

/**
 * Class - FacetConfig
 */
class FacetConfig extends InterfaceType {
	use TypeResolverTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'FacetConfig';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Facet configuration.', 'wpgraphql-facetwp' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'choices'  => [
				'type'        => [
					'list_of' => FacetChoice::get_type_name(),
				],
				'description' => __( 'Facet choices', 'wpgraphql-facetwp' ),
			],
			'label'    => [
				'type'        => 'String',
				'description' => __( 'Facet label.', 'wpgraphql-facetwp' ),
			],
			'name'     => [
				'type'        => 'String',
				'description' => __( 'Facet name.', 'wpgraphql-facetwp' ),
			],
			'settings' => [
				'type'        => FacetSettings::get_type_name(),
				'description' => __( 'Facet settings', 'wpgraphql-facetwp' ),
			],
			'type'     => [
				'type'        => FacetTypeEnum::get_type_name(),
				'description' => __( 'Facet type.', 'wpgraphql-facetwp' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param array<string,mixed> $value The value.
	 */
	public static function get_resolved_type_name( $value ): ?string {
		return graphql_format_type_name( $value['type'] ) . 'Facet';
	}
}

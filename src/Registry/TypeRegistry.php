<?php
/**
 * Registers Plugin types to the GraphQL schema.
 *
 * @package WPGraphQL\FacetWP\Registry
 * @since 0.4.0
 */

namespace WPGraphQL\FacetWP\Registry;

use Exception;
use WPGraphQL\FacetWP\Type\Enum;
use WPGraphQL\FacetWP\Type\Input;
use WPGraphQL\FacetWP\Type\WPInterface;
use WPGraphQL\FacetWP\Type\WPObject;
use WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - TypeRegistry
 */
class TypeRegistry implements Registrable {
	/**
	 * Registers types, connections, unions, and mutations to GraphQL schema.
	 */
	public static function init(): void {
		self::initialize_registry();
	}

	/**
	 * Initializes the plugin type registry.
	 */
	private static function initialize_registry(): void {
		$classes_to_register = array_merge(
			self::enums(),
			self::inputs(),
			self::interfaces(),
			self::objects()
		);

		self::register_types( $classes_to_register );
	}

	/**
	 * List of Enum classes to register.
	 *
	 * @return string[]
	 */
	private static function enums(): array {
		// Enums to register.
		$classes_to_register = [
			Enum\ProximityRadiusOptions::class,
			Enum\SortOptionsEnum::class,
		];

		/**
		 * Filters the list of enum classes to register.
		 *
		 * Useful for adding/removing FacetWP specific enums to the schema.
		 *
		 * @param array           $classes_to_register Array of classes to be registered to the schema.
		 */
		return apply_filters( 'graphql_facetwp_registered_enum_classes', $classes_to_register );
	}

	/**
	 * List of Input classes to register.
	 *
	 * @return string[]
	 */
	private static function inputs(): array {
		$classes_to_register = [
			Input\DateRangeArgs::class,
			Input\NumberRangeArgs::class,
			Input\ProximityArgs::class,
			Input\SliderArgs::class,
		];

		/**
		 * Filters the list of input classes to register.
		 *
		 * Useful for adding/removing FacetWP specific inputs to the schema.
		 *
		 * @param array           $classes_to_register Array of classes to be registered to the schema.
		 */
		return apply_filters( 'graphql_facetwp_registered_input_classes', $classes_to_register );
	}

	/**
	 * List of Interface classes to register.
	 *
	 * @return string[]
	 */
	private static function interfaces(): array {
		$classes_to_register = [
			WPInterface\FacetConfig::class,
		];

		/**
		 * Filters the list of interface classes to register.
		 *
		 * Useful for adding/removing FacetWP specific inputs to the schema.
		 *
		 * @param array           $classes_to_register Array of classes to be registered to the schema.
		 */
		return apply_filters( 'graphql_facetwp_registered_interface_classes', $classes_to_register );
	}

		/**
		 * List of Object classes to register.
		 *
		 * @return string[]
		 */
	public static function objects(): array {
		$classes_to_register = [
			WPObject\FacetChoice::class,
			WPObject\FacetPager::class,
			WPObject\FacetRangeSettings::class,
			WPObject\FacetSortOptionOrderBySetting::class,
			WPObject\FacetSortOptionSetting::class,
			WPObject\FacetSettings::class,
			WPObject\Facet::class,
		];

		/**
		 * Filters the list of object classes to register.
		 *
		 * Useful for adding/removing FacetWP specific objects to the schema.
		 *
		 * @param array           $classes_to_register = Array of classes to be registered to the schema.
		 */
		return apply_filters( 'graphql_facetwp_registered_object_classes', $classes_to_register );
	}

	/**
	 * Loops through a list of classes to manually register each GraphQL to the registry, and stores the type name and class in the local registry.
	 *
	 * Classes must extend WPGraphQL\Type\AbstractType.
	 *
	 * @param string[] $classes_to_register .
	 *
	 * @throws \Exception .
	 */
	private static function register_types( array $classes_to_register ): void {
		// Bail if there are no classes to register.
		if ( empty( $classes_to_register ) ) {
			return;
		}

		foreach ( $classes_to_register as $class ) {
			if ( ! is_a( $class, Registrable::class, true ) ) {
				// translators: PHP class.
				throw new Exception(
					sprintf(
						// translators: PHP class.
						esc_html__( 'To be registered to the WPGraphQL schema, %s needs to implement \WPGraphQL\FacetWP\Vendor\AxeWP\GraphQL\Interfaces\Registrable.', 'wpgraphql-facetwp' ),
						esc_html( $class )
					)
				);
			}

			// Register the type to the GraphQL schema.
			$class::init();
		}
	}
}

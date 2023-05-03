<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67b8ec155949e29fef68cbc0ba7a77b0
{
    public static $files = array (
        '2624831776b6fbc9c15e25c2fb7f42d3' => __DIR__ . '/../..' . '/access-functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPGraphQL\\FacetWP\\' => 18,
        ),
        'A' => 
        array (
            'AxeWP\\GraphQL\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPGraphQL\\FacetWP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'AxeWP\\GraphQL\\' => 
        array (
            0 => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src',
        ),
    );

    public static $classMap = array (
        'AxeWP\\GraphQL\\Abstracts\\ConnectionType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/ConnectionType.php',
        'AxeWP\\GraphQL\\Abstracts\\EnumType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/EnumType.php',
        'AxeWP\\GraphQL\\Abstracts\\FieldsType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/FieldsType.php',
        'AxeWP\\GraphQL\\Abstracts\\InputType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/InputType.php',
        'AxeWP\\GraphQL\\Abstracts\\InterfaceType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/InterfaceType.php',
        'AxeWP\\GraphQL\\Abstracts\\MutationType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/MutationType.php',
        'AxeWP\\GraphQL\\Abstracts\\ObjectType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/ObjectType.php',
        'AxeWP\\GraphQL\\Abstracts\\Type' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/Type.php',
        'AxeWP\\GraphQL\\Abstracts\\UnionType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/UnionType.php',
        'AxeWP\\GraphQL\\Helper\\Helper' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Helper/Helper.php',
        'AxeWP\\GraphQL\\Interfaces\\GraphQLType' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/GraphQLType.php',
        'AxeWP\\GraphQL\\Interfaces\\Registrable' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/Registrable.php',
        'AxeWP\\GraphQL\\Interfaces\\TypeWithConnections' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithConnections.php',
        'AxeWP\\GraphQL\\Interfaces\\TypeWithFields' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithFields.php',
        'AxeWP\\GraphQL\\Interfaces\\TypeWithInputFields' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithInputFields.php',
        'AxeWP\\GraphQL\\Interfaces\\TypeWithInterfaces' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithInterfaces.php',
        'AxeWP\\GraphQL\\Traits\\TypeNameTrait' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Traits/TypeNameTrait.php',
        'AxeWP\\GraphQL\\Traits\\TypeResolverTrait' => __DIR__ . '/..' . '/axepress/wp-graphql-plugin-boilerplate/src/Traits/TypeResolverTrait.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WPGraphQL\\FacetWP\\CoreSchemaFilters' => __DIR__ . '/../..' . '/src/CoreSchemaFilters.php',
        'WPGraphQL\\FacetWP\\Main' => __DIR__ . '/../..' . '/src/Main.php',
        'WPGraphQL\\FacetWP\\Registry\\FacetRegistry' => __DIR__ . '/../..' . '/src/Registry/FacetRegistry.php',
        'WPGraphQL\\FacetWP\\Registry\\TypeRegistry' => __DIR__ . '/../..' . '/src/Registry/TypeRegistry.php',
        'WPGraphQL\\FacetWP\\Type\\Enum\\ProximityRadiusOptions' => __DIR__ . '/../..' . '/src/Type/Enum/ProximityRadiusOptions.php',
        'WPGraphQL\\FacetWP\\Type\\Input\\DateRangeArgs' => __DIR__ . '/../..' . '/src/Type/Input/DateRangeArgs.php',
        'WPGraphQL\\FacetWP\\Type\\Input\\NumberRangeArgs' => __DIR__ . '/../..' . '/src/Type/Input/NumberRangeArgs.php',
        'WPGraphQL\\FacetWP\\Type\\Input\\ProximityArgs' => __DIR__ . '/../..' . '/src/Type/Input/ProximityArgs.php',
        'WPGraphQL\\FacetWP\\Type\\Input\\SliderArgs' => __DIR__ . '/../..' . '/src/Type/Input/SliderArgs.php',
        'WPGraphQL\\FacetWP\\Type\\WPObject\\Facet' => __DIR__ . '/../..' . '/src/Type/WPObject/Facet.php',
        'WPGraphQL\\FacetWP\\Type\\WPObject\\FacetChoice' => __DIR__ . '/../..' . '/src/Type/WPObject/FacetChoice.php',
        'WPGraphQL\\FacetWP\\Type\\WPObject\\FacetPager' => __DIR__ . '/../..' . '/src/Type/WPObject/FacetPager.php',
        'WPGraphQL\\FacetWP\\Type\\WPObject\\FacetRangeSettings' => __DIR__ . '/../..' . '/src/Type/WPObject/FacetRangeSettings.php',
        'WPGraphQL\\FacetWP\\Type\\WPObject\\FacetSettings' => __DIR__ . '/../..' . '/src/Type/WPObject/FacetSettings.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\ConnectionType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/ConnectionType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\EnumType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/EnumType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\FieldsType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/FieldsType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\InputType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/InputType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\InterfaceType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/InterfaceType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\MutationType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/MutationType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\ObjectType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/ObjectType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\Type' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/Type.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Abstracts\\UnionType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Abstracts/UnionType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Helper\\Helper' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Helper/Helper.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\GraphQLType' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/GraphQLType.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\Registrable' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/Registrable.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\TypeWithConnections' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithConnections.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\TypeWithFields' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithFields.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\TypeWithInputFields' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithInputFields.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Interfaces\\TypeWithInterfaces' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Interfaces/TypeWithInterfaces.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Traits\\TypeNameTrait' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Traits/TypeNameTrait.php',
        'WPGraphQL\\FacetWP\\Vendor\\AxeWP\\GraphQL\\Traits\\TypeResolverTrait' => __DIR__ . '/../..' . '/src/vendor/axepress/wp-graphql-plugin-boilerplate/src/Traits/TypeResolverTrait.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67b8ec155949e29fef68cbc0ba7a77b0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67b8ec155949e29fef68cbc0ba7a77b0::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit67b8ec155949e29fef68cbc0ba7a77b0::$classMap;

        }, null, ClassLoader::class);
    }
}

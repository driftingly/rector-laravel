<?php

declare(strict_types=1);

namespace RectorLaravel\Set;

final class LaravelSetList
{
    /**
     * Version sets bound to the installed package versions, picked up by
     * RectorConfig::configure()->withComposerBased(laravel: true)
     */
    public const string COMPOSER_BASED = __DIR__ . '/../../config/sets/composer-based.php';

    public const string ARRAY_STR_FUNCTIONS_TO_STATIC_CALL = __DIR__ . '/../../config/sets/laravel-array-str-functions-to-static-call.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_50 = __DIR__ . '/../../config/sets/laravel50.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_51 = __DIR__ . '/../../config/sets/laravel51.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_52 = __DIR__ . '/../../config/sets/laravel52.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_53 = __DIR__ . '/../../config/sets/laravel53.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_54 = __DIR__ . '/../../config/sets/laravel54.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_55 = __DIR__ . '/../../config/sets/laravel55.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_56 = __DIR__ . '/../../config/sets/laravel56.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_57 = __DIR__ . '/../../config/sets/laravel57.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_58 = __DIR__ . '/../../config/sets/laravel58.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_60 = __DIR__ . '/../../config/sets/laravel60.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_70 = __DIR__ . '/../../config/sets/laravel70.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_80 = __DIR__ . '/../../config/sets/laravel80.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_90 = __DIR__ . '/../../config/sets/laravel90.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_100 = __DIR__ . '/../../config/sets/laravel100.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_110 = __DIR__ . '/../../config/sets/laravel110.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_120 = __DIR__ . '/../../config/sets/laravel120.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_130 = __DIR__ . '/../../config/sets/laravel130.php';

    /**
     * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead.
     */
    public const string LARAVEL_130_WITHOUT_ATTRIBUTES = __DIR__ . '/../../config/sets/laravel130-without-attributes.php';

    public const string LARAVEL_ARRAYACCESS_TO_METHOD_CALL = __DIR__ . '/../../config/sets/laravel-arrayaccess-to-method-call.php';

    public const string LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL = __DIR__ . '/../../config/sets/laravel-array-str-functions-to-static-call.php';

    public const string LARAVEL_CODE_QUALITY = __DIR__ . '/../../config/sets/laravel-code-quality.php';

    public const string LARAVEL_COLLECTION = __DIR__ . '/../../config/sets/laravel-collection.php';

    public const string LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME = __DIR__ . '/../../config/sets/laravel-container-string-to-fully-qualified-name.php';

    public const string LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER = __DIR__ . '/../../config/sets/laravel-eloquent-magic-method-to-query-builder.php';

    public const string LARAVEL_FACADE_ALIASES_TO_FULL_NAMES = __DIR__ . '/../../config/sets/laravel-facade-aliases-to-full-names.php';

    public const string LARAVEL_FACTORIES = __DIR__ . '/../../config/sets/laravel-factories.php';

    public const string LARAVEL_IF_HELPERS = __DIR__ . '/../../config/sets/laravel-if-helpers.php';

    public const string LARAVEL_LEGACY_FACTORIES_TO_CLASSES = __DIR__ . '/../../config/sets/laravel-legacy-factories-to-classes.php';

    public const string LARAVEL_STATIC_TO_INJECTION = __DIR__ . '/../../config/sets/laravel-static-to-injection.php';

    public const string LARAVEL_TESTING = __DIR__ . '/../../config/sets/laravel-testing.php';

    public const string LARAVEL_TYPE_DECLARATIONS = __DIR__ . '/../../config/sets/laravel-type-declarations.php';

    public const string LUMEN = __DIR__ . '/../../config/sets/lumen.php';
}

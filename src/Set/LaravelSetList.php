<?php

declare(strict_types=1);

namespace RectorLaravel\Set;

use Rector\Set\Contract\SetListInterface;

final class LaravelSetList implements SetListInterface
{
    /**
     * @var string
     */
    public const ARRAY_STR_FUNCTIONS_TO_STATIC_CALL = __DIR__ . '/../../config/sets/laravel-array-str-functions-to-static-call.php';

    /**
     * @var string
     */
    public const LARAVEL_50 = __DIR__ . '/../../config/sets/laravel50.php';

    /**
     * @var string
     */
    public const LARAVEL_51 = __DIR__ . '/../../config/sets/laravel51.php';

    /**
     * @var string
     */
    public const LARAVEL_52 = __DIR__ . '/../../config/sets/laravel52.php';

    /**
     * @var string
     */
    public const LARAVEL_53 = __DIR__ . '/../../config/sets/laravel53.php';

    /**
     * @var string
     */
    public const LARAVEL_54 = __DIR__ . '/../../config/sets/laravel54.php';

    /**
     * @var string
     */
    public const LARAVEL_55 = __DIR__ . '/../../config/sets/laravel55.php';

    /**
     * @var string
     */
    public const LARAVEL_56 = __DIR__ . '/../../config/sets/laravel56.php';

    /**
     * @var string
     */
    public const LARAVEL_57 = __DIR__ . '/../../config/sets/laravel57.php';

    /**
     * @var string
     */
    public const LARAVEL_58 = __DIR__ . '/../../config/sets/laravel58.php';

    /**
     * @var string
     */
    public const LARAVEL_60 = __DIR__ . '/../../config/sets/laravel60.php';

    /**
     * @var string
     */
    public const LARAVEL_70 = __DIR__ . '/../../config/sets/laravel70.php';

    /**
     * @var string
     */
    public const LARAVEL_80 = __DIR__ . '/../../config/sets/laravel80.php';

    /**
     * @var string
     */
    public const LARAVEL_90 = __DIR__ . '/../../config/sets/laravel90.php';

    /**
     * @var string
     */
    public const LARAVEL_100 = __DIR__ . '/../../config/sets/laravel100.php';

    /**
     * @var string
     */
    public const LARAVEL_110 = __DIR__ . '/../../config/sets/laravel110.php';

    /**
     * @var string
     */
    public const LARAVEL_ARRAYACCESS_TO_METHOD_CALL = __DIR__ . '/../../config/sets/laravel-arrayaccess-to-method-call.php';

    /**
     * @var string
     */
    public const LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL = __DIR__ . '/../../config/sets/laravel-array-str-functions-to-static-call.php';

    /**
     * @var string
     */
    public const LARAVEL_CODE_QUALITY = __DIR__ . '/../../config/sets/laravel-code-quality.php';

    /**
     * @var string
     */
    public const LARAVEL_COLLECTION = __DIR__ . '/../../config/sets/laravel-collection.php';

    /**
     * @var string
     */
    public const LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME = __DIR__ . '/../../config/sets/laravel-container-string-to-fully-qualified-name.php';

    /**
     * @var string
     */
    public const LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER = __DIR__ . '/../../config/sets/laravel-eloquent-magic-method-to-query-builder.php';

    /**
     * @var string
     */
    public const LARAVEL_FACADE_ALIASES_TO_FULL_NAMES = __DIR__ . '/../../config/sets/laravel-facade-aliases-to-full-names.php';

    /**
     * @var string
     */
    public const LARAVEL_IF_HELPERS = __DIR__ . '/../../config/sets/laravel-if-helpers.php';

    /**
     * @var string
     */
    public const LARAVEL_LEGACY_FACTORIES_TO_CLASSES = __DIR__ . '/../../config/sets/laravel-legacy-factories-to-classes.php';

    /**
     * @var string
     */
    public const LARAVEL_STATIC_TO_INJECTION = __DIR__ . '/../../config/sets/laravel-static-to-injection.php';
}

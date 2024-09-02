<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;

// @see https://medium.freecodecamp.org/moving-away-from-magic-or-why-i-dont-want-to-use-laravel-anymore-2ce098c979bd
// @see https://laravel.com/docs/5.7/facades#facades-vs-dependency-injection

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $internalFunctions = get_defined_functions()['internal'];

    $rectorConfig
        ->ruleWithConfiguration(
            FuncCallToStaticCallRector::class,
            array_filter(
                [
                    new FuncCallToStaticCall('array_add', 'Illuminate\Support\Arr', 'add'),
                    new FuncCallToStaticCall('array_collapse', 'Illuminate\Support\Arr', 'collapse'),
                    new FuncCallToStaticCall('array_divide', 'Illuminate\Support\Arr', 'divide'),
                    new FuncCallToStaticCall('array_dot', 'Illuminate\Support\Arr', 'dot'),
                    new FuncCallToStaticCall('array_except', 'Illuminate\Support\Arr', 'except'),
                    new FuncCallToStaticCall('array_first', 'Illuminate\Support\Arr', 'first'),
                    new FuncCallToStaticCall('array_flatten', 'Illuminate\Support\Arr', 'flatten'),
                    new FuncCallToStaticCall('array_forget', 'Illuminate\Support\Arr', 'forget'),
                    new FuncCallToStaticCall('array_get', 'Illuminate\Support\Arr', 'get'),
                    new FuncCallToStaticCall('array_has', 'Illuminate\Support\Arr', 'has'),
                    new FuncCallToStaticCall('array_last', 'Illuminate\Support\Arr', 'last'),
                    new FuncCallToStaticCall('array_only', 'Illuminate\Support\Arr', 'only'),
                    new FuncCallToStaticCall('array_pluck', 'Illuminate\Support\Arr', 'pluck'),
                    new FuncCallToStaticCall('array_prepend', 'Illuminate\Support\Arr', 'prepend'),
                    new FuncCallToStaticCall('array_pull', 'Illuminate\Support\Arr', 'pull'),
                    new FuncCallToStaticCall('array_random', 'Illuminate\Support\Arr', 'random'),
                    new FuncCallToStaticCall('array_sort', 'Illuminate\Support\Arr', 'sort'),
                    new FuncCallToStaticCall('array_sort_recursive', 'Illuminate\Support\Arr', 'sortRecursive'),
                    new FuncCallToStaticCall('array_where', 'Illuminate\Support\Arr', 'where'),
                    new FuncCallToStaticCall('array_wrap', 'Illuminate\Support\Arr', 'wrap'),
                    new FuncCallToStaticCall('array_set', 'Illuminate\Support\Arr', 'set'),
                    new FuncCallToStaticCall('camel_case', 'Illuminate\Support\Str', 'camel'),
                    new FuncCallToStaticCall('ends_with', 'Illuminate\Support\Str', 'endsWith'),
                    new FuncCallToStaticCall('kebab_case', 'Illuminate\Support\Str', 'kebab'),
                    new FuncCallToStaticCall('snake_case', 'Illuminate\Support\Str', 'snake'),
                    new FuncCallToStaticCall('starts_with', 'Illuminate\Support\Str', 'startsWith'),
                    new FuncCallToStaticCall('str_after', 'Illuminate\Support\Str', 'after'),
                    new FuncCallToStaticCall('str_before', 'Illuminate\Support\Str', 'before'),
                    new FuncCallToStaticCall('str_contains', 'Illuminate\Support\Str', 'contains'),
                    new FuncCallToStaticCall('str_finish', 'Illuminate\Support\Str', 'finish'),
                    new FuncCallToStaticCall('str_is', 'Illuminate\Support\Str', 'is'),
                    new FuncCallToStaticCall('str_limit', 'Illuminate\Support\Str', 'limit'),
                    new FuncCallToStaticCall('str_plural', 'Illuminate\Support\Str', 'plural'),
                    new FuncCallToStaticCall('str_random', 'Illuminate\Support\Str', 'random'),
                    new FuncCallToStaticCall('str_replace_array', 'Illuminate\Support\Str', 'replaceArray'),
                    new FuncCallToStaticCall('str_replace_first', 'Illuminate\Support\Str', 'replaceFirst'),
                    new FuncCallToStaticCall('str_replace_last', 'Illuminate\Support\Str', 'replaceLast'),
                    new FuncCallToStaticCall('str_singular', 'Illuminate\Support\Str', 'singular'),
                    new FuncCallToStaticCall('str_slug', 'Illuminate\Support\Str', 'slug'),
                    new FuncCallToStaticCall('str_start', 'Illuminate\Support\Str', 'start'),
                    new FuncCallToStaticCall('studly_case', 'Illuminate\Support\Str', 'studly'),
                    new FuncCallToStaticCall('title_case', 'Illuminate\Support\Str', 'title'),
                ],
                function ($function) use ($internalFunctions) {
                    return ! in_array($function->getOldFuncName(), $internalFunctions, true);
                }
            )
        );
};

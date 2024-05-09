<?php

use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;
use RectorLaravel\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../config.php');

    $targetClasses = [
        'Illuminate\Support\Facades\Validator',
        'Illuminate\Contracts\Validation\Factory',
    ];

    foreach ($targetClasses as $targetClass) {
        $rectorConfig->ruleWithConfiguration(
            AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector::class,
            [
                new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                    $targetClass,
                    'make',
                    1,
                    0,
                    new StringType,
                ),
                new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                    $targetClass,
                    'make',
                    1,
                    1,
                    new MixedType(true),
                ),
                new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                    $targetClass,
                    'make',
                    1,
                    2,
                    new ObjectType('Closure'),
                ),
            ]
        );
    }

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Http\Request',
                'validate',
                0,
                0,
                new StringType,
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Http\Request',
                'validate',
                0,
                1,
                new MixedType(true),
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Http\Request',
                'validate',
                0,
                2,
                new ObjectType('Closure'),
            ),
        ]
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Contracts\Validation\Validator',
                'after',
                0,
                0,
                new ObjectType('Illuminate\Contracts\Validation\Validator'),
            ),
        ]
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Contracts\Validation\Validator',
                'sometimes',
                2,
                0,
                new ObjectType('Illuminate\Support\Fluent'),
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Contracts\Validation\Validator',
                'sometimes',
                2,
                1,
                new MixedType(true),
            ),
        ]
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Validation\Rule',
                'forEach',
                0,
                0,
                new MixedType(true),
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Validation\Rule',
                'forEach',
                0,
                1,
                new StringType,
            ),
        ]
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Validation\Rules\Exists',
                'where',
                0,
                0,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder'),
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Validation\Rules\Exists',
                'where',
                1,
                0,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder'),
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Validation\Rules\Exists',
                'using',
                0,
                0,
                new ObjectType('Illuminate\Contracts\Database\Query\Builder'),
            ),
        ]
    );
};

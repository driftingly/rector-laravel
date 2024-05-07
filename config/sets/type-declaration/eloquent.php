<?php

use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;
use RectorLaravel\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../config.php');

    $generator = new \RectorLaravel\Util\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRectorConfigGenerator();

    $builderClass = new ObjectType(
        'Illuminate\Contracts\Database\Query\Builder'
    );

    /** @var class-string[] $classesToApplyTo */
    $classesToApplyTo = [
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Contracts\Database\Query\Builder',
        'Illuminate\Contracts\Database\Eloquent\Builder',
        'Illuminate\Database\Eloquent\Builder',
        'Illuminate\Database\Query\Builder',
    ];

    $basicRuleConfiguration = [
        ...$generator->generate(
            [0, 1, 2, 'builder'],
            $classesToApplyTo,
            0,
            'where',
            $builderClass,
        ),
        ...$generator->generate(
            [0, 'builder'],
            $classesToApplyTo,
            0,
            'orWhere',
            $builderClass,
        ),
        ...$generator->generate(
            [0, 'builder'],
            $classesToApplyTo,
            0,
            'whereNot',
            $builderClass,
        ),
        ...$generator->generate(
            [0, 'builder'],
            $classesToApplyTo,
            0,
            'whereExists',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 'builder'],
            $classesToApplyTo,
            0,
            'whereHas',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 'builder'],
            $classesToApplyTo,
            0,
            'orWhereHas',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 'builder'],
            $classesToApplyTo,
            0,
            'whereDoesntHave',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 'builder'],
            $classesToApplyTo,
            0,
            'orWhereDoesntHave',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 'builder'],
            $classesToApplyTo,
            0,
            'withWhereHas',
            $builderClass,
        ),
        ...$generator->generate(
            [1, 2, 'builder'],
            $classesToApplyTo,
            0,
            'when',
            $builderClass,
        ),
        ...$generator->generate(
            [2, 'builder'],
            $classesToApplyTo,
            0,
            'whereHasMorph',
            $builderClass,
        ),
        ...$generator->generate(
            [2, 'builder'],
            $classesToApplyTo,
            0,
            'orWhereHasMorph',
            $builderClass,
        ),
        ...$generator->generate(
            [2, 'builder'],
            $classesToApplyTo,
            0,
            'whereDoesntHaveMorph',
            $builderClass,
        ),
        ...$generator->generate(
            [2, 'builder'],
            $classesToApplyTo,
            0,
            'orWhereDoesntHaveMorph',
            $builderClass,
        ),
    ];

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        $basicRuleConfiguration
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        [
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Database\Eloquent\Model',
                'handleLazyLoadingViolationUsing',
                0,
                0,
                new ObjectType('Illuminate\Database\Eloquent\Model')
            ),
            new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'Illuminate\Database\Eloquent\Model',
                'handleLazyLoadingViolationUsing',
                0,
                1,
                new \PHPStan\Type\StringType,
            ),
        ]
    );
};

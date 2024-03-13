<?php

use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;
use RectorLaravel\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../config.php');

    $builderClass = new ObjectType(
        'Illuminate\Contracts\Database\Query\Builder'
    );

    $classesToApplyTo = [
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Contracts\Database\Query\Builder',
        'Illuminate\Contracts\Database\Eloquent\Builder',
        'Illuminate\Database\Eloquent\Builder',
        'Illuminate\Database\Query\Builder',
    ];

    $basicPositionOne = [
        'where', 'orWhere', 'whereNot', 'whereExists',
    ];
    $basicPositionTwo = [
        'where', 'whereHas', 'orWhereHas', 'whereDoesntHave', 'orWhereDoesntHave', 'withWhereHas', 'when',
    ];
    $basicPositionThree = [
        'where', 'whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph', 'when',
    ];

    $basicRuleConfiguration = [];
    $arrayRuleConfiguration = [];

    foreach ($classesToApplyTo as $targetClass) {
        foreach ($basicPositionOne as $method) {
            $basicRuleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                0,
                0,
                $builderClass,
            );
        }
        foreach ($basicPositionTwo as $method) {
            $basicRuleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                1,
                0,
                $builderClass,
            );
        }
        foreach ($basicPositionThree as $method) {
            $basicRuleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                2,
                0,
                $builderClass,
            );
        }
    }

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        $basicRuleConfiguration
    );

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector::class,
        $arrayRuleConfiguration,
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

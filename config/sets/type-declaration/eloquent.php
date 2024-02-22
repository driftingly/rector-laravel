<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../config.php');

    $builderClass = new \PHPStan\Type\ObjectType(
        'Illuminate\Contracts\Database\Query\Builder'
    );

    $classesToApplyTo = [
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Contracts\Database\Query\Builder',
        'Illuminate\Contracts\Database\Eloquent\Builder',
    ];

    $positionOne = [
        'where', 'orWhere', 'whereNot', 'whereExists',
    ];
    $positionTwo = [
        'where', 'whereHas', 'orWhereHas', 'whereDoesntHave', 'orWhereDoesntHave', 'withWhereHas', 'when',
    ];
    $positionThree = [
        'where', 'whereHasMorph', 'orWhereHasMorph', 'whereDoesntHaveMorph', 'orWhereDoesntHaveMorph', 'when',
    ];

    $ruleConfiguration = [];

    foreach ($classesToApplyTo as $targetClass) {
        foreach ($positionOne as $method) {
            $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                0,
                0,
                $builderClass,
            );
        }
        foreach ($positionTwo as $method) {
            $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                1,
                0,
                $builderClass,
            );
        }
        foreach ($positionThree as $method) {
            $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
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
        $ruleConfiguration
    );

};

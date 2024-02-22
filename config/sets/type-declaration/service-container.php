<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../config.php');

    $applicationClass = new \PHPStan\Type\ObjectType(
        'Illuminate\Contracts\Foundation\Application'
    );

    $classesToApplyTo = [
        'Illuminate\Support\Facades\App',
        'Illuminate\Contracts\Foundation\Application',
    ];

    $ruleConfiguration = [];

    foreach ($classesToApplyTo as $targetClass) {
        foreach ([
            'bind', 'bindIf', 'singleton', 'singletonIf', 'scoped', 'scopedIf',
        ] as $method) {
            $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                $targetClass,
                $method,
                1,
                0,
                $applicationClass,
            );
        }
        $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
            $targetClass,
            'resolving',
            1,
            1,
            $applicationClass,
        );
        $ruleConfiguration[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
            $targetClass,
            'extends',
            1,
            1,
            $applicationClass,
        );
    }

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRector::class,
        $ruleConfiguration
    );
};

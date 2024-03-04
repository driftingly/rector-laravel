<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(
        AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector::class,
        [
            new \Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                'SomeClass',
                'someMethod',
                0,
                0,
                new \PHPStan\Type\StringType,
            ),
        ]
    );
};

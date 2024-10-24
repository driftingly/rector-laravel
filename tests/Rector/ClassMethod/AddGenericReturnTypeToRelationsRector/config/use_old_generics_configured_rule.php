<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(AddGenericReturnTypeToRelationsRector::class, [
        'shouldUseNewGenerics' => false,
    ]);
};

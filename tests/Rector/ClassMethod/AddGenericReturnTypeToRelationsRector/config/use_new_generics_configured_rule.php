<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\Source\NewApplication;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->when(AddGenericReturnTypeToRelationsRector::class)
        ->needs('$applicationClass')
        ->give(NewApplication::class);

    $rectorConfig->rule(AddGenericReturnTypeToRelationsRector::class);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector;
use RectorLaravel\Rector\Class_\AddHasFactoryToModelsRector;
use RectorLaravel\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(AddHasFactoryToModelsRector::class);
    $rectorConfig->rule(AddExtendsAnnotationToModelFactoriesRector::class);
    $rectorConfig->rule(FactoryCountAndStateToMethodCallsRector::class);
};

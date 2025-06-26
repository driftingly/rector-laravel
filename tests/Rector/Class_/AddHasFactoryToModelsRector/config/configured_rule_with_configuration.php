<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AddHasFactoryToModelsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(AddHasFactoryToModelsRector::class, [
        'RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\Fixture\Configured\User',
        'RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRectorConfigured\Fixture\SkipIfAlreadyPresent',
    ]);
};

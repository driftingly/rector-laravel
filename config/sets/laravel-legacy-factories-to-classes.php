<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector;
use RectorLaravel\Rector\MethodCall\FactoryApplyingStatesRector;
use RectorLaravel\Rector\Namespace_\FactoryDefinitionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // https://laravel.com/docs/7.x/database-testing#writing-factories
    // https://laravel.com/docs/8.x/database-testing#defining-model-factories
    $rectorConfig->rule(FactoryDefinitionRector::class);

    // https://laravel.com/docs/7.x/database-testing#using-factories
    // https://laravel.com/docs/8.x/database-testing#creating-models-using-factories
    $rectorConfig->rule(FactoryApplyingStatesRector::class);
    $rectorConfig->rule(FactoryFuncCallToStaticCallRector::class);
};

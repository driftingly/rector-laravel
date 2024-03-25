<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector;

// see https://laravel.com/docs/11.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // https://github.com/laravel/framework/pull/47237
    $rectorConfig->rule(ModelCastsPropertyToCastsMethodRector::class);

    // https://github.com/laravel/framework/pull/49634
    $rectorConfig->rule(RefactorBlueprintGeometryColumnsRector::class);
};

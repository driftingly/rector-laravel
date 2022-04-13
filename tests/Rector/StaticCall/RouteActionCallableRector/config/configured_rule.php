<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Rector\StaticCall\RouteActionCallableRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $services = $rectorConfig->services();

    $services->set(RouteActionCallableRector::class)
        ->call('configure', [[
            RouteActionCallableRector::NAMESPACE => 'Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source',
        ]]);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\RouteActionCallableRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(RouteActionCallableRector::class, [
        RouteActionCallableRector::CONTROLLER_NAMESPACE => 'RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source',
    ]);
};

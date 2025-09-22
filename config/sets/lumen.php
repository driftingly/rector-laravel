<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector;
use RectorLaravel\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(LumenRoutesStringActionToUsesArrayRector::class);
    $rectorConfig->rule(LumenRoutesStringMiddlewareToArrayRector::class);
};

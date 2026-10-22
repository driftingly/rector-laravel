<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\StaticFacadeToServiceMethodCallRector;
use RectorLaravel\ValueObject\StaticCallToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(StaticFacadeToServiceMethodCallRector::class, [
        new StaticCallToMethodCall('Illuminate\Support\Facades\Auth', '*', 'Illuminate\Auth\AuthManager', '*'),
        new StaticCallToMethodCall('Illuminate\Support\Facades\DB', 'table', 'Illuminate\Database\DatabaseManager', 'table'),
    ]);
};

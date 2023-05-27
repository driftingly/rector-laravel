<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector;
use RectorLaravel\ValueObject\ArgumentFuncCallToMethodCall;
use RectorLaravel\ValueObject\ArrayFuncCallToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(ArgumentFuncCallToMethodCallRector::class, [
            new ArgumentFuncCallToMethodCall('view', 'Illuminate\Contracts\View\Factory', 'make'),
            new ArgumentFuncCallToMethodCall('route', 'Illuminate\Routing\UrlGenerator', 'route'),
            new ArgumentFuncCallToMethodCall('back', 'Illuminate\Routing\Redirector', 'back', 'back'),
            new ArgumentFuncCallToMethodCall('broadcast', 'Illuminate\Contracts\Broadcasting\Factory', 'event'),

            new ArrayFuncCallToMethodCall('config', 'Illuminate\Contracts\Config\Repository', 'set', 'get'),
            new ArrayFuncCallToMethodCall('session', 'Illuminate\Session\SessionManager', 'put', 'get'),
        ]);
};

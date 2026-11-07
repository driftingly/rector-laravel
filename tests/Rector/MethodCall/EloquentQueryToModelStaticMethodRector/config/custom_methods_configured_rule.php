<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\EloquentQueryToModelStaticMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(EloquentQueryToModelStaticMethodRector::class, [
        EloquentQueryToModelStaticMethodRector::METHODS => [
            'get' => 'all',
            'active' => 'active',
            'whereStatus' => 'ofStatus',
        ],
    ]);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\FuncCall\FuncCallToStaticCallRector;
use RectorLaravel\ValueObject\FuncCallToStaticCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(FuncCallToStaticCallRector::class, [
        new FuncCallToStaticCall('array_get', 'Illuminate\Support\Arr', 'get'),
        new FuncCallToStaticCall('str_slug', 'Illuminate\Support\Str', 'slug'),
    ]);
};

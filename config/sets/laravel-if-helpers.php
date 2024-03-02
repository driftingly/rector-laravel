<?php

declare(strict_types=1);
use RectorLaravel\Rector\If_\AbortIfRector;
use RectorLaravel\Rector\If_\ReportIfRector;
use RectorLaravel\Rector\If_\ThrowIfRector;

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(AbortIfRector::class);
    $rectorConfig->rule(ReportIfRector::class);
    $rectorConfig->rule(ThrowIfRector::class);
};

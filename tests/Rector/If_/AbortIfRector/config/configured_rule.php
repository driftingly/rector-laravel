<?php

declare(strict_types=1);
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\If_\AbortIfRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(AbortIfRector::class);
};

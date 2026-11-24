<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(AddExceptionToLoggerContextInCatchBlockRector::class);
};

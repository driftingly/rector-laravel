<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\ValueObject\ApplyDefaultWithFuncCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(ApplyDefaultInsteadOfNullCoalesceRector::class, [
        new ApplyDefaultWithFuncCall('config'),
    ]);
};

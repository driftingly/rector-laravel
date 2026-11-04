<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\ValueObject\ApplyDefaultInsteadOfNullCoalesce;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(ApplyDefaultInsteadOfNullCoalesceRector::class, [
        new ApplyDefaultInsteadOfNullCoalesce('custom_get', argumentPosition: 2),
    ]);
};

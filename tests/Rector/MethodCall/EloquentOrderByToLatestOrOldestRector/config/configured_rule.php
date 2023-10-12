<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(
        EloquentOrderByToLatestOrOldestRector::class,
        [
            EloquentOrderByToLatestOrOldestRector::ALLOWED_PATTERNS => [
                'created_at',
                'submitted_a*',
                '*tested_at',
                '$renameable_variable_name',
            ],
        ],
    );
};

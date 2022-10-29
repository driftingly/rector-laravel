<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(
        OptionalToNullsafeOperatorRector::class,
        [
            OptionalToNullsafeOperatorRector::EXCLUDE_METHODS => ['present'],
        ],
    );
};

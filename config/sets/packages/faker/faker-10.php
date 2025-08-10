<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerPropertyFetchWithMethodCallRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../config.php');
    $rectorConfig->rule(ReplaceFakerPropertyFetchWithMethodCallRector::class);
};

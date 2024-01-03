<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->importNames(importDocBlockNames: false);
    $rectorConfig->importShortClasses(false);
    $rectorConfig->rule(ReplaceAssertTimesSendWithAssertSentTimesRector::class);
};

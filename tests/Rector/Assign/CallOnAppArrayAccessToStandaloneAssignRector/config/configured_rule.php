<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(CallOnAppArrayAccessToStandaloneAssignRector::class);
};

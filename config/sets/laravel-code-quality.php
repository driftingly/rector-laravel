<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(CallOnAppArrayAccessToStandaloneAssignRector::class);
};

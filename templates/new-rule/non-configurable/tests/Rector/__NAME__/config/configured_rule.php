<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use __NAMESPACE__\__NAME__;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(__NAME__::class);
};

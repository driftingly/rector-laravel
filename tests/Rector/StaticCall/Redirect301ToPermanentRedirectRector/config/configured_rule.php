<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $services = $rectorConfig->services();

    $services->set(Redirect301ToPermanentRedirectRector::class);
};

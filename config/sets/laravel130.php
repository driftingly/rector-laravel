<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;

// see https://laravel.com/docs/13.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(FillablePropertyToFillableAttributeRector::class);
};

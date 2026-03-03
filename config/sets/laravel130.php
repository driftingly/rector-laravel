<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AppendsPropertyToAppendsAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;

// see https://laravel.com/docs/13.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(AppendsPropertyToAppendsAttributeRector::class);
    $rectorConfig->rule(FillablePropertyToFillableAttributeRector::class);
    $rectorConfig->rule(HiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(TablePropertyToTableAttributeRector::class);
};

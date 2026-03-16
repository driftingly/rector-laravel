<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AppendsPropertyToAppendsAttributeRector;
use RectorLaravel\Rector\Class_\BackoffPropertyToBackoffAttributeRector;
use RectorLaravel\Rector\Class_\ConnectionPropertyToConnectionAttributeRector;
use RectorLaravel\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\GuardedPropertyToGuardedAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector;
use RectorLaravel\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector;
use RectorLaravel\Rector\Class_\QueuePropertyToQueueAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\TouchesPropertyToTouchesAttributeRector;
use RectorLaravel\Rector\Class_\TriesPropertyToTriesAttributeRector;
use RectorLaravel\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector;

// see https://laravel.com/docs/13.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->rule(AppendsPropertyToAppendsAttributeRector::class);
    $rectorConfig->rule(BackoffPropertyToBackoffAttributeRector::class);
    $rectorConfig->rule(ConnectionPropertyToConnectionAttributeRector::class);
    $rectorConfig->rule(FailOnTimeoutPropertyToFailOnTimeoutAttributeRector::class);
    $rectorConfig->rule(FillablePropertyToFillableAttributeRector::class);
    $rectorConfig->rule(GuardedPropertyToGuardedAttributeRector::class);
    $rectorConfig->rule(HiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(JobConnectionPropertyToJobConnectionAttributeRector::class);
    $rectorConfig->rule(MaxExceptionsPropertyToMaxExceptionsAttributeRector::class);
    $rectorConfig->rule(QueuePropertyToQueueAttributeRector::class);
    $rectorConfig->rule(TablePropertyToTableAttributeRector::class);
    $rectorConfig->rule(TimeoutPropertyToTimeoutAttributeRector::class);
    $rectorConfig->rule(TouchesPropertyToTouchesAttributeRector::class);
    $rectorConfig->rule(TriesPropertyToTriesAttributeRector::class);
    $rectorConfig->rule(UniqueForPropertyToUniqueForAttributeRector::class);
};

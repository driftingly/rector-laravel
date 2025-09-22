<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector;
use RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector;
use RectorLaravel\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector;
use RectorLaravel\Rector\StaticCall\CarbonSetTestNowToTravelToRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(JsonCallToExplicitJsonCallRector::class);
    $rectorConfig->rule(AssertStatusToAssertMethodRector::class);
    $rectorConfig->rule(AssertWithClassStringToTypeHintedClosureRector::class);
    $rectorConfig->rule(CarbonSetTestNowToTravelToRector::class);
};

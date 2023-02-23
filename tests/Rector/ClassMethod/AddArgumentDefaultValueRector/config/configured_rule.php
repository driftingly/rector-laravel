<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use RectorLaravel\ValueObject\AddArgumentDefaultValue;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(AddArgumentDefaultValueRector::class, [
        new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null),
    ]);
};

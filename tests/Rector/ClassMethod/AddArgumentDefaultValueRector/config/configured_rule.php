<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use Rector\Laravel\ValueObject\AddArgumentDefaultValue;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $services = $rectorConfig->services();

    $services->set(AddArgumentDefaultValueRector::class)
        ->configure([new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null)]);
};

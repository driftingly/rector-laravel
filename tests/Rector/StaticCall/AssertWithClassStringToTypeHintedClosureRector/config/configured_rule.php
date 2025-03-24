<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\AssertWithClassStringToTypeHintedClosureRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AssertWithClassStringToTypeHintedClosureRector::class);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\UseForwardCallsTraitRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(UseForwardCallsTraitRector::class);
};

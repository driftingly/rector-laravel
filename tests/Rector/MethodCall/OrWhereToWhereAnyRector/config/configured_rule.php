<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\OrWhereToWhereAnyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(OrWhereToWhereAnyRector::class);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\RequestInputToTypedMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RequestInputToTypedMethodRector::class);
};

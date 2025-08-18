<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(MakeModelAttributesAndScopesProtectedRector::class);
};

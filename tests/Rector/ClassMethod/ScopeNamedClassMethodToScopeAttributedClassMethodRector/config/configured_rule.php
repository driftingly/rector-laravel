<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ScopeNamedClassMethodToScopeAttributedClassMethodRector::class);
};

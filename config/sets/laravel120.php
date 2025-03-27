<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;

// see https://laravel.com/docs/12.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // https://github.com/laravel/framework/pull/54628
    $rectorConfig->rule(ContainerBindConcreteWithClosureOnlyRector::class);
    // https://github.com/laravel/framework/pull/54450
    $rectorConfig->rule(ScopeNamedClassMethodToScopeAttributedClassMethodRector::class);
};

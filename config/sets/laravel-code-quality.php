<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector;
use RectorLaravel\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector;
use RectorLaravel\Rector\MethodCall\ReverseConditionableMethodCallRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(CallOnAppArrayAccessToStandaloneAssignRector::class);
    $rectorConfig->rule(ReverseConditionableMethodCallRector::class);
    $rectorConfig->rule(ApplyDefaultInsteadOfNullCoalesceRector::class);
    $rectorConfig->rule(MakeModelAttributesAndScopesProtectedRector::class);
    $rectorConfig->rule(AppEnvironmentComparisonToParameterRector::class);
};

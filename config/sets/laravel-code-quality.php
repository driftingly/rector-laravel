<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector;
use RectorLaravel\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector;
use RectorLaravel\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector;
use RectorLaravel\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector;
use RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector;
use RectorLaravel\Rector\Class_\AnonymousMigrationsRector;
use RectorLaravel\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector;
use RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector;
use RectorLaravel\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector;
use RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector;
use RectorLaravel\Rector\FuncCall\RemoveRedundantValueCallsRector;
use RectorLaravel\Rector\FuncCall\RemoveRedundantWithCallsRector;
use RectorLaravel\Rector\FuncCall\SleepFuncToSleepStaticCallRector;
use RectorLaravel\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector;
use RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector;
use RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector;
use RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector;
use RectorLaravel\Rector\MethodCall\ReverseConditionableMethodCallRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector;
use RectorLaravel\Rector\StaticCall\CarbonToDateFacadeRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(CallOnAppArrayAccessToStandaloneAssignRector::class);
    $rectorConfig->rule(ReverseConditionableMethodCallRector::class);
    $rectorConfig->rule(ApplyDefaultInsteadOfNullCoalesceRector::class);
    $rectorConfig->rule(MakeModelAttributesAndScopesProtectedRector::class);
    $rectorConfig->rule(AppEnvironmentComparisonToParameterRector::class);
    $rectorConfig->rule(ThrowIfAndThrowUnlessExceptionsToUseClassStringRector::class);
    $rectorConfig->rule(EnvVariableToEnvHelperRector::class);
    $rectorConfig->rule(RequestVariablesToRequestFacadeRector::class);
    $rectorConfig->rule(ServerVariableToRequestFacadeRector::class);
    $rectorConfig->rule(SessionVariableToSessionFacadeRector::class);
    $rectorConfig->rule(SubStrToStartsWithOrEndsWithStaticMethodCallRector::class);
    $rectorConfig->rule(NowFuncWithStartOfDayMethodCallToTodayFuncRector::class);
    $rectorConfig->rule(RemoveRedundantValueCallsRector::class);
    $rectorConfig->rule(RemoveRedundantWithCallsRector::class);
    $rectorConfig->rule(OptionalToNullsafeOperatorRector::class);
    $rectorConfig->rule(ValidationRuleArrayStringValueToArrayRector::class);
    $rectorConfig->rule(RedirectBackToBackHelperRector::class);
    $rectorConfig->rule(RedirectRouteToToRouteHelperRector::class);
    $rectorConfig->rule(AnonymousMigrationsRector::class);
    $rectorConfig->rule(SleepFuncToSleepStaticCallRector::class);
    $rectorConfig->rule(CarbonToDateFacadeRector::class);
    $rectorConfig->rule(DispatchToHelperFunctionsRector::class);
    $rectorConfig->rule(NotFilledBlankFuncCallToBlankFilledFuncCallRector::class);
    $rectorConfig->rule(EloquentOrderByToLatestOrOldestRector::class);
};

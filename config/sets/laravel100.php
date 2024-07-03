<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameProperty;
use RectorLaravel\Rector\Cast\DatabaseExpressionCastsToMethodCallRector;
use RectorLaravel\Rector\Class_\ReplaceExpectsMethodsInTestsRector;
use RectorLaravel\Rector\Class_\UnifyModelDatesWithCastsRector;
use RectorLaravel\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector;
use RectorLaravel\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector;
use RectorLaravel\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector;
use RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector;

// see https://laravel.com/docs/10.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // https://github.com/laravel/framework/pull/32856/files
    $rectorConfig->rule(UnifyModelDatesWithCastsRector::class);

    // https://github.com/laravel/framework/pull/44784/files
    $rectorConfig->rule(DatabaseExpressionCastsToMethodCallRector::class);
    $rectorConfig->rule(DatabaseExpressionToStringToMethodCallRector::class);

    // https://github.com/laravel/framework/pull/41136/files
    $rectorConfig->rule(ReplaceExpectsMethodsInTestsRector::class);
    $rectorConfig->rule(ReplaceAssertTimesSendWithAssertSentTimesRector::class);
    $rectorConfig->rule(ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector::class);

    $rectorConfig
        ->ruleWithConfiguration(RenamePropertyRector::class, [
            // https://github.com/laravel/laravel/commit/edcbe6de7c3f17070bf0ccaa2e2b785158ae5ceb
            new RenameProperty('Illuminate\Foundation\Http\Kernel', 'routeMiddleware', 'middlewareAliases'),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [
            // https://github.com/laravel/framework/pull/42591/files
            new MethodCallRename('Illuminate\Support\Facades\Bus', 'dispatchNow', 'dispatchSync'),
            new MethodCallRename('Illuminate\Foundation\Bus\Dispatchable', 'dispatchNow', 'dispatchSync'),
            new MethodCallRename('Illuminate\Foundation\Bus\DispatchesJobs', 'dispatchNow', 'dispatchSync'),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameFunctionRector::class, [
            // https://github.com/laravel/framework/pull/42591/files
            'dispatch_now' => 'dispatch_sync',
        ]);

    // https://laravel.com/docs/10.x/upgrade#dispatch-return
    $rectorConfig
        ->rule(DispatchNonShouldQueueToDispatchSyncRector::class);
};

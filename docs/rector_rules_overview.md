# 51 Rules Overview

## AddArgumentDefaultValueRector

Adds default value for arguments in defined methods.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector`](../src/Rector/ClassMethod/AddArgumentDefaultValueRector.php)

<br>

## AddExtendsAnnotationToModelFactoriesRector

Adds the `@extends` annotation to Factories.

- class: [`RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector`](../src/Rector/Class_/AddExtendsAnnotationToModelFactoriesRector.php)

<br>

## AddGenericReturnTypeToRelationsRector

Add generic return type to relations in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector`](../src/Rector/ClassMethod/AddGenericReturnTypeToRelationsRector.php)

<br>

## AddGuardToLoginEventRector

Add new `$guard` argument to Illuminate\Auth\Events\Login

- class: [`RectorLaravel\Rector\New_\AddGuardToLoginEventRector`](../src/Rector/New_/AddGuardToLoginEventRector.php)

<br>

## AddMockConsoleOutputFalseToConsoleTestsRector

Add "$this->mockConsoleOutput = false"; to console tests that work with output content

- class: [`RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector`](../src/Rector/Class_/AddMockConsoleOutputFalseToConsoleTestsRector.php)

<br>

## AddParentBootToModelClassMethodRector

Add `parent::boot();` call to `boot()` class method in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector`](../src/Rector/ClassMethod/AddParentBootToModelClassMethodRector.php)

<br>

## AddParentRegisterToEventServiceProviderRector

Add `parent::register();` call to `register()` class method in child of `Illuminate\Foundation\Support\Providers\EventServiceProvider`

- class: [`RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector`](../src/Rector/ClassMethod/AddParentRegisterToEventServiceProviderRector.php)

<br>

## AnonymousMigrationsRector

Convert migrations to anonymous classes.

- class: [`RectorLaravel\Rector\Class_\AnonymousMigrationsRector`](../src/Rector/Class_/AnonymousMigrationsRector.php)

<br>

## AppEnvironmentComparisonToParameterRector

Replace `$app->environment() === 'local'` with `$app->environment('local')`

- class: [`RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector`](../src/Rector/Expr/AppEnvironmentComparisonToParameterRector.php)

<br>

## ArgumentFuncCallToMethodCallRector

Move help facade-like function calls to constructor injection

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector`](../src/Rector/FuncCall/ArgumentFuncCallToMethodCallRector.php)

<br>

## AssertStatusToAssertMethodRector

Replace `(new \Illuminate\Testing\TestResponse)->assertStatus(200)` with `(new \Illuminate\Testing\TestResponse)->assertOk()`

- class: [`RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector`](../src/Rector/MethodCall/AssertStatusToAssertMethodRector.php)

<br>

## CallOnAppArrayAccessToStandaloneAssignRector

Replace magical call on `$this->app["something"]` to standalone type assign variable

- class: [`RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector`](../src/Rector/Assign/CallOnAppArrayAccessToStandaloneAssignRector.php)

<br>

## CashierStripeOptionsToStripeRector

Renames the Billable `stripeOptions()` to `stripe().`

- class: [`RectorLaravel\Rector\Class_\CashierStripeOptionsToStripeRector`](../src/Rector/Class_/CashierStripeOptionsToStripeRector.php)

<br>

## ChangeQueryWhereDateValueWithCarbonRector

Add `parent::boot();` call to `boot()` class method in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector`](../src/Rector/MethodCall/ChangeQueryWhereDateValueWithCarbonRector.php)

<br>

## DatabaseExpressionCastsToMethodCallRector

Convert DB Expression string casts to `getValue()` method calls.

- class: [`RectorLaravel\Rector\Cast\DatabaseExpressionCastsToMethodCallRector`](../src/Rector/Cast/DatabaseExpressionCastsToMethodCallRector.php)

<br>

## DatabaseExpressionToStringToMethodCallRector

Convert DB Expression `__toString()` calls to `getValue()` method calls.

- class: [`RectorLaravel\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector`](../src/Rector/MethodCall/DatabaseExpressionToStringToMethodCallRector.php)

<br>

## EloquentMagicMethodToQueryBuilderRector

The EloquentMagicMethodToQueryBuilderRule is designed to automatically transform certain magic method calls on Eloquent Models into corresponding Query Builder method calls.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector`](../src/Rector/StaticCall/EloquentMagicMethodToQueryBuilderRector.php)

<br>

## EloquentOrderByToLatestOrOldestRector

Changes `orderBy()` to `latest()` or `oldest()`

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector`](../src/Rector/MethodCall/EloquentOrderByToLatestOrOldestRector.php)

<br>

## EloquentWhereRelationTypeHintingParameterRector

Add type hinting to where relation has methods e.g. `whereHas`, `orWhereHas`, `whereDoesntHave`, `orWhereDoesntHave`, `whereHasMorph`, `orWhereHasMorph`, `whereDoesntHaveMorph`, `orWhereDoesntHaveMorph`

- class: [`RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector`](../src/Rector/MethodCall/EloquentWhereRelationTypeHintingParameterRector.php)

<br>

## EloquentWhereTypeHintClosureParameterRector

Change typehint of closure parameter in where method of Eloquent Builder

- class: [`RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector`](../src/Rector/MethodCall/EloquentWhereTypeHintClosureParameterRector.php)

<br>

## EmptyToBlankAndFilledFuncRector

Replace use of the unsafe `empty()` function with Laravel's safer `blank()` & `filled()` functions.

- class: [`RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector`](../src/Rector/Empty_/EmptyToBlankAndFilledFuncRector.php)

<br>

## FactoryApplyingStatesRector

Call the state methods directly instead of specify the name of state.

- class: [`RectorLaravel\Rector\MethodCall\FactoryApplyingStatesRector`](../src/Rector/MethodCall/FactoryApplyingStatesRector.php)

<br>

## FactoryDefinitionRector

Upgrade legacy factories to support classes.

- class: [`RectorLaravel\Rector\Namespace_\FactoryDefinitionRector`](../src/Rector/Namespace_/FactoryDefinitionRector.php)

<br>

## FactoryFuncCallToStaticCallRector

Use the static factory method instead of global factory function.

- class: [`RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector`](../src/Rector/FuncCall/FactoryFuncCallToStaticCallRector.php)

<br>

## HelperFuncCallToFacadeClassRector

Change `app()` func calls to facade calls

- class: [`RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector`](../src/Rector/FuncCall/HelperFuncCallToFacadeClassRector.php)

<br>

## JsonCallToExplicitJsonCallRector

Change method calls from `$this->json` to `$this->postJson,` `$this->putJson,` etc.

- class: [`RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector`](../src/Rector/MethodCall/JsonCallToExplicitJsonCallRector.php)

<br>

## LumenRoutesStringActionToUsesArrayRector

Changes action in rule definitions from string to array notation.

- class: [`RectorLaravel\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector`](../src/Rector/MethodCall/LumenRoutesStringActionToUsesArrayRector.php)

<br>

## LumenRoutesStringMiddlewareToArrayRector

Changes middlewares from rule definitions from string to array notation.

- class: [`RectorLaravel\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector`](../src/Rector/MethodCall/LumenRoutesStringMiddlewareToArrayRector.php)

<br>

## MigrateToSimplifiedAttributeRector

Migrate to the new Model attributes syntax

- class: [`RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector`](../src/Rector/ClassMethod/MigrateToSimplifiedAttributeRector.php)

<br>

## MinutesToSecondsInCacheRector

Change minutes argument to seconds in `Illuminate\Contracts\Cache\Store` and Illuminate\Support\Facades\Cache

- class: [`RectorLaravel\Rector\StaticCall\MinutesToSecondsInCacheRector`](../src/Rector/StaticCall/MinutesToSecondsInCacheRector.php)

<br>

## ModelCastsPropertyToCastsMethodRector

Refactor Model `$casts` property with `casts()` method

- class: [`RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector`](../src/Rector/Class_/ModelCastsPropertyToCastsMethodRector.php)

<br>

## NotFilledBlankFuncCallToBlankFilledFuncCallRector

Swap the use of NotBooleans used with `filled()` and `blank()` to the correct helper.

- class: [`RectorLaravel\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector`](../src/Rector/FuncCall/NotFilledBlankFuncCallToBlankFilledFuncCallRector.php)

<br>

## NowFuncWithStartOfDayMethodCallToTodayFuncRector

Use `today()` instead of `now()->startOfDay()`

- class: [`RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector`](../src/Rector/FuncCall/NowFuncWithStartOfDayMethodCallToTodayFuncRector.php)

<br>

## OptionalToNullsafeOperatorRector

Convert simple calls to optional helper to use the nullsafe operator

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector`](../src/Rector/PropertyFetch/OptionalToNullsafeOperatorRector.php)

<br>

## PropertyDeferToDeferrableProviderToRector

Change deprecated `$defer` = true; to `Illuminate\Contracts\Support\DeferrableProvider` interface

- class: [`RectorLaravel\Rector\Class_\PropertyDeferToDeferrableProviderToRector`](../src/Rector/Class_/PropertyDeferToDeferrableProviderToRector.php)

<br>

## Redirect301ToPermanentRedirectRector

Change "redirect" call with 301 to "permanentRedirect"

- class: [`RectorLaravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector`](../src/Rector/StaticCall/Redirect301ToPermanentRedirectRector.php)

<br>

## RedirectBackToBackHelperRector

Replace `redirect()->back()` and `Redirect::back()` with `back()`

- class: [`RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector`](../src/Rector/MethodCall/RedirectBackToBackHelperRector.php)

<br>

## RedirectRouteToToRouteHelperRector

Replace `redirect()->route("home")` and `Redirect::route("home")` with `to_route("home")`

- class: [`RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector`](../src/Rector/MethodCall/RedirectRouteToToRouteHelperRector.php)

<br>

## RemoveDumpDataDeadCodeRector

It will removes the dump data just like dd or dump functions from the code.`

- class: [`RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector`](../src/Rector/FuncCall/RemoveDumpDataDeadCodeRector.php)

<br>

## RemoveModelPropertyFromFactoriesRector

Removes the `$model` property from Factories.

- class: [`RectorLaravel\Rector\Class_\RemoveModelPropertyFromFactoriesRector`](../src/Rector/Class_/RemoveModelPropertyFromFactoriesRector.php)

<br>

## ReplaceAssertTimesSendWithAssertSentTimesRector

Replace assertTimesSent with assertSentTimes

- class: [`RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector`](../src/Rector/StaticCall/ReplaceAssertTimesSendWithAssertSentTimesRector.php)

<br>

## ReplaceExpectsMethodsInTestsRector

Replace expectJobs and expectEvents methods in tests

- class: [`RectorLaravel\Rector\Class_\ReplaceExpectsMethodsInTestsRector`](../src/Rector/Class_/ReplaceExpectsMethodsInTestsRector.php)

<br>

## ReplaceFakerInstanceWithHelperRector

Replace `$this->faker` with the `fake()` helper function in Factories

- class: [`RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector`](../src/Rector/PropertyFetch/ReplaceFakerInstanceWithHelperRector.php)

<br>

## ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector

Replace `withoutJobs`, `withoutEvents` and `withoutNotifications` with Facade `fake`

- class: [`RectorLaravel\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector`](../src/Rector/MethodCall/ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector.php)

<br>

## RequestStaticValidateToInjectRector

Change static `validate()` method to `$request->validate()`

- class: [`RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector`](../src/Rector/StaticCall/RequestStaticValidateToInjectRector.php)

<br>

## RouteActionCallableRector

Use PHP callable syntax instead of string syntax for controller route declarations.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\StaticCall\RouteActionCallableRector`](../src/Rector/StaticCall/RouteActionCallableRector.php)

<br>

## SleepFuncToSleepStaticCallRector

Use `Sleep::sleep()` and `Sleep::usleep()` instead of the `sleep()` and `usleep()` function.

- class: [`RectorLaravel\Rector\FuncCall\SleepFuncToSleepStaticCallRector`](../src/Rector/FuncCall/SleepFuncToSleepStaticCallRector.php)

<br>

## SubStrToStartsWithOrEndsWithStaticMethodCallRector

Use `Str::startsWith()` or `Str::endsWith()` instead of `substr()` === `$str`

- class: [`RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector`](../src/Rector/Expr/SubStrToStartsWithOrEndsWithStaticMethodCallRector/SubStrToStartsWithOrEndsWithStaticMethodCallRector.php)

<br>

## ThrowIfRector

Change if throw to throw_if

- class: [`RectorLaravel\Rector\If_\ThrowIfRector`](../src/Rector/If_/ThrowIfRector.php)

<br>

## UnifyModelDatesWithCastsRector

Unify Model `$dates` property with `$casts`

- class: [`RectorLaravel\Rector\Class_\UnifyModelDatesWithCastsRector`](../src/Rector/Class_/UnifyModelDatesWithCastsRector.php)

<br>

## UseComponentPropertyWithinCommandsRector

Use `$this->components` property within commands

- class: [`RectorLaravel\Rector\MethodCall\UseComponentPropertyWithinCommandsRector`](../src/Rector/MethodCall/UseComponentPropertyWithinCommandsRector.php)

<br>

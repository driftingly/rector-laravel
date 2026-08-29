<?php

declare(strict_types=1);

use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use Rector\Arguments\NodeAnalyzer\ArgumentAddingScope;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Arguments\ValueObject\ArgumentAdderWithoutDefaultValue;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveInterfacesRector;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Removing\Rector\ClassMethod\ArgumentRemoverRector;
use Rector\Removing\ValueObject\ArgumentRemover;
use Rector\Renaming\Rector\Class_\RenameAttributeRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameAttribute;
use Rector\Renaming\ValueObject\RenameProperty;
use Rector\Renaming\ValueObject\RenameStaticMethod;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\Rector\StaticCall\StaticCallToFuncCallRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use Rector\Transform\ValueObject\StaticCallToFuncCall;
use Rector\Transform\ValueObject\StringToClassConstant;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeDeclaration;
use Rector\TypeDeclaration\ValueObject\AddReturnTypeDeclaration;
use Rector\ValueObject\Visibility;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use RectorLaravel\Rector\Cast\DatabaseExpressionCastsToMethodCallRector;
use RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector;
use RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector;
use RectorLaravel\Rector\Class_\AliasesPropertyToAliasesAttributeRector;
use RectorLaravel\Rector\Class_\AppendsPropertyToAppendsAttributeRector;
use RectorLaravel\Rector\Class_\BackoffPropertyToBackoffAttributeRector;
use RectorLaravel\Rector\Class_\CashierStripeOptionsToStripeRector;
use RectorLaravel\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector;
use RectorLaravel\Rector\Class_\CollectsPropertyToCollectsAttributeRector;
use RectorLaravel\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\ConnectionPropertyToConnectionAttributeRector;
use RectorLaravel\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector;
use RectorLaravel\Rector\Class_\DelayPropertyToDelayAttributeRector;
use RectorLaravel\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector;
use RectorLaravel\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector;
use RectorLaravel\Rector\Class_\EmptyGuardedPropertyToUnguardedAttributeRector;
use RectorLaravel\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector;
use RectorLaravel\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\GuardedPropertyToGuardedAttributeRector;
use RectorLaravel\Rector\Class_\HelpPropertyToHelpAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector;
use RectorLaravel\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector;
use RectorLaravel\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector;
use RectorLaravel\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector;
use RectorLaravel\Rector\Class_\PropertyDeferToDeferrableProviderToRector;
use RectorLaravel\Rector\Class_\QueuePropertyToQueueAttributeRector;
use RectorLaravel\Rector\Class_\ReplaceExpectsMethodsInTestsRector;
use RectorLaravel\Rector\Class_\ReplaceQueueTraitsWithQueueableRector;
use RectorLaravel\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector;
use RectorLaravel\Rector\Class_\SignaturePropertyToSignatureAttributeRector;
use RectorLaravel\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector;
use RectorLaravel\Rector\Class_\TouchesPropertyToTouchesAttributeRector;
use RectorLaravel\Rector\Class_\TriesPropertyToTriesAttributeRector;
use RectorLaravel\Rector\Class_\UnifyModelDatesWithCastsRector;
use RectorLaravel\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector;
use RectorLaravel\Rector\Class_\VisiblePropertyToVisibleAttributeRector;
use RectorLaravel\Rector\Class_\WithoutIncrementingPropertyToWithoutIncrementingAttributeRector;
use RectorLaravel\Rector\Class_\WithoutTimestampsPropertyToWithoutTimestampsAttributeRector;
use RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector;
use RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector;
use RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector;
use RectorLaravel\Rector\MethodCall\AssertSeeToAssertSeeHtmlRector;
use RectorLaravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;
use RectorLaravel\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector;
use RectorLaravel\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector;
use RectorLaravel\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector;
use RectorLaravel\Rector\New_\AddGuardToLoginEventRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerPropertyFetchWithMethodCallRector;
use RectorLaravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector;
use RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector;
use RectorLaravel\ValueObject\AddArgumentDefaultValue;

/**
 * Single composer-based config, picked up by `RectorConfig::configure()->withComposerBased(laravel: true)`.
 *
 * Every rule below is bound to the package version that introduced its upgrade. Configurable rules use
 * `ruleWithConfigurationComposerVersionBound()`; non-configurable rules implement
 * `Rector\VersionBonding\Contract\ComposerPackageConstraintInterface` and self-filter, so a plain `rule()`
 * registration only runs when the installed package version satisfies the constraint.
 *
 * A rule stays active from its version upwards, so a direct upgrade across several major versions is covered
 * by this one config.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.0 — see https://laravel.com/docs/5.0/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Cache\CacheManager' => 'Illuminate\Contracts\Cache\Repository',
        'Illuminate\Database\Eloquent\SoftDeletingTrait' => 'Illuminate\Database\Eloquent\SoftDeletes',
    ], 'laravel/framework', '>=5.0 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'links', 'render'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getFrom', 'firstItem'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getTo', 'lastItem'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getPerPage', 'perPage'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getCurrentPage', 'currentPage'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getLastPage', 'lastPage'),
        new MethodCallRename('Illuminate\Contracts\Pagination\Paginator', 'getTotal', 'total'),
    ], 'laravel/framework', '>=5.0 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.1 — see https://laravel.com/docs/5.1/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Validation\Validator' => 'Illuminate\Contracts\Validation\Validator',
    ], 'laravel/framework', '>=5.1 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.2 — see https://laravel.com/docs/5.2/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Auth\Access\UnauthorizedException' => 'Illuminate\Auth\Access\AuthorizationException',
        'Illuminate\Http\Exception\HttpResponseException' => 'Illuminate\Foundation\Validation\ValidationException',
        'Illuminate\Foundation\Composer' => 'Illuminate\Support\Composer',
    ], 'laravel/framework', '>=5.2 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(StringToClassConstantRector::class, [
        new StringToClassConstant('artisan.start', 'Illuminate\Console\Events\ArtisanStarting', 'class'),
        new StringToClassConstant('auth.attempting', 'Illuminate\Auth\Events\Attempting', 'class'),
        new StringToClassConstant('auth.login', 'Illuminate\Auth\Events\Login', 'class'),
        new StringToClassConstant('auth.logout', 'Illuminate\Auth\Events\Logout', 'class'),
        new StringToClassConstant('cache.missed', 'Illuminate\Cache\Events\CacheMissed', 'class'),
        new StringToClassConstant('cache.hit', 'Illuminate\Cache\Events\CacheHit', 'class'),
        new StringToClassConstant('cache.write', 'Illuminate\Cache\Events\KeyWritten', 'class'),
        new StringToClassConstant('cache.delete', 'Illuminate\Cache\Events\KeyForgotten', 'class'),
        new StringToClassConstant('illuminate.query', 'Illuminate\Database\Events\QueryExecuted', 'class'),
        new StringToClassConstant('illuminate.queue.before', 'Illuminate\Queue\Events\JobProcessing', 'class'),
        new StringToClassConstant('illuminate.queue.after', 'Illuminate\Queue\Events\JobProcessed', 'class'),
        new StringToClassConstant('illuminate.queue.failed', 'Illuminate\Queue\Events\JobFailed', 'class'),
        new StringToClassConstant('illuminate.queue.stopping', 'Illuminate\Queue\Events\WorkerStopping', 'class'),
        new StringToClassConstant('mailer.sending', 'Illuminate\Mail\Events\MessageSending', 'class'),
        new StringToClassConstant('router.matched', 'Illuminate\Routing\Events\RouteMatched', 'class'),
    ], 'laravel/framework', '>=5.2 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.3 — see https://laravel.com/docs/5.3/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTraitUseRector::class, [
        'Illuminate\Foundation\Auth\Access\AuthorizesResources',
    ], 'laravel/framework', '>=5.3 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Support\Collection', 'lists', 'pluck'),
        new MethodCallRename('Illuminate\Database\Query\Builder', 'lists', 'pluck'),
        new MethodCallRename('Illuminate\Database\Eloquent\Collection', 'withHidden', 'makeVisible'),
        new MethodCallRename('Illuminate\Database\Eloquent\Model', 'withHidden', 'makeVisible'),
    ], 'laravel/framework', '>=5.3 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveInterfacesRector::class, [
        'Illuminate\Contracts\Bus\SelfHandling',
    ], 'laravel/framework', '>=5.3 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Database\Eloquent\ScopeInterface' => 'Illuminate\Database\Eloquent\Scope',
        'Illuminate\View\Expression' => 'Illuminate\Support\HtmlString',
    ], 'laravel/framework', '>=5.3 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(StaticCallToFuncCallRector::class, [
        new StaticCallToFuncCall('Illuminate\Support\Str', 'randomBytes', 'random_bytes'),
        new StaticCallToFuncCall('Illuminate\Support\Str', 'equals', 'hash_equals'),
    ], 'laravel/framework', '>=5.3 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.4 — see https://laravel.com/docs/5.4/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(StringToClassConstantRector::class, [
        new StringToClassConstant('kernel.handled', 'Illuminate\Foundation\Http\Events\RequestHandled', 'class'),
        new StringToClassConstant('locale.changed', 'Illuminate\Foundation\Events\LocaleUpdated', 'class'),
        new StringToClassConstant('illuminate.log', 'Illuminate\Log\Events\MessageLogged', 'class'),
    ], 'laravel/framework', '>=5.4 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Console\AppNamespaceDetectorTrait' => 'Illuminate\Console\DetectsApplicationNamespace',
        'Illuminate\Http\Exception\HttpResponseException' => 'Illuminate\Http\Exceptions\HttpResponseException',
        'Illuminate\Http\Exception\PostTooLargeException' => 'Illuminate\Http\Exceptions\PostTooLargeException',
        'Illuminate\Foundation\Http\Middleware\VerifyPostSize' => 'Illuminate\Foundation\Http\Middleware\ValidatePostSize',
        'Symfony\Component\HttpFoundation\Session\SessionInterface' => 'Illuminate\Contracts\Session\Session',
    ], 'laravel/framework', '>=5.4 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Database\Eloquent\Relations\BelongsToMany', 'setJoin', 'performJoin'),
        new MethodCallRename('Illuminate\Database\Eloquent\Relations\BelongsToMany', 'getRelatedIds', 'allRelatedIds'),
        new MethodCallRename('Illuminate\Routing\Router', 'middleware', 'aliasMiddleware'),
        new MethodCallRename('Illuminate\Routing\Route', 'getPath', 'uri'),
        new MethodCallRename('Illuminate\Routing\Route', 'getUri', 'uri'),
        new MethodCallRename('Illuminate\Routing\Route', 'getMethods', 'methods'),
        new MethodCallRename('Illuminate\Routing\Route', 'getParameter', 'parameter'),
        new MethodCallRename('Illuminate\Contracts\Session\Session', 'set', 'put'),
        new MethodCallRename('Illuminate\Contracts\Session\Session', 'getToken', 'token'),
        new MethodCallRename('Illuminate\Support\Facades\Request', 'setSession', 'setLaravelSession'),
        new MethodCallRename('Illuminate\Http\Request', 'setSession', 'setLaravelSession'),
        new MethodCallRename('Illuminate\Routing\UrlGenerator', 'forceSchema', 'forceScheme'),
        new MethodCallRename('Illuminate\Validation\Validator', 'addError', 'addFailure'),
        new MethodCallRename('Illuminate\Validation\Validator', 'doReplacements', 'makeReplacements'),
        new MethodCallRename('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase', 'seeInDatabase', 'assertDatabaseHas'),
        new MethodCallRename('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase', 'missingFromDatabase', 'assertDatabaseMissing'),
        new MethodCallRename('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase', 'dontSeeInDatabase', 'assertDatabaseMissing'),
        new MethodCallRename('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase', 'notSeeInDatabase', 'assertDatabaseMissing'),
    ], 'laravel/framework', '>=5.4 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.5 — see https://laravel.com/docs/5.5/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Console\Command', 'fire', 'handle'),
    ], 'laravel/framework', '>=5.5 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenamePropertyRector::class, [
        new RenameProperty('Illuminate\Database\Eloquent\Concerns\HasEvents', 'events', 'dispatchesEvents'),
        new RenameProperty('Illuminate\Database\Eloquent\Relations\Pivot', 'parent', 'pivotParent'),
    ], 'laravel/framework', '>=5.5 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Translation\LoaderInterface' => 'Illuminate\Contracts\Translation\Loader',
    ], 'laravel/framework', '>=5.5 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.6 — see https://laravel.com/docs/5.6/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Validation\ValidatesWhenResolvedTrait', 'validate', 'validateResolved'),
        new MethodCallRename('Illuminate\Contracts\Validation\ValidatesWhenResolved', 'validate', 'validateResolved'),
    ], 'laravel/framework', '>=5.6 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility('Illuminate\Routing\Router', 'addRoute', Visibility::PUBLIC),
        new ChangeMethodVisibility('Illuminate\Contracts\Auth\Access\Gate', 'raw', Visibility::PUBLIC),
        new ChangeMethodVisibility('Illuminate\Database\Grammar', 'getDateFormat', Visibility::PUBLIC),
    ], 'laravel/framework', '>=5.6 <6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.7 — see https://laravel.com/docs/5.7/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility('Illuminate\Routing\Router', 'addRoute', Visibility::PUBLIC),
        new ChangeMethodVisibility('Illuminate\Contracts\Auth\Access\Gate', 'raw', Visibility::PUBLIC),
    ], 'laravel/framework', '>=5.7 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Auth\Middleware\Authenticate', 'authenticate', 0, 'request'),
        new ArgumentAdder('Illuminate\Foundation\Auth\ResetsPasswords', 'sendResetResponse', 0, 'request', null, new ObjectType('Illuminate\Http\Illuminate\Http')),
        new ArgumentAdder('Illuminate\Foundation\Auth\SendsPasswordResetEmails', 'sendResetLinkResponse', 0, 'request', null, new ObjectType('Illuminate\Http\Illuminate\Http')),
        new ArgumentAdder('Illuminate\Database\ConnectionInterface', 'select', 2, 'useReadPdo', true),
        new ArgumentAdder('Illuminate\Database\ConnectionInterface', 'selectOne', 2, 'useReadPdo', true),
    ], 'laravel/framework', '>=5.7 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentRemoverRector::class, [
        new ArgumentRemover('Illuminate\Foundation\Application', 'register', 1, [
            'name' => 'options',
        ]),
    ], 'laravel/framework', '>=5.7 <6.0');

    $rectorConfig->rule(Redirect301ToPermanentRedirectRector::class);
    $rectorConfig->rule(AddParentBootToModelClassMethodRector::class);
    $rectorConfig->rule(ChangeQueryWhereDateValueWithCarbonRector::class);
    $rectorConfig->rule(AddMockConsoleOutputFalseToConsoleTestsRector::class);
    $rectorConfig->rule(AddGuardToLoginEventRector::class);

    // ---------------------------------------------------------------------------------------------
    // Laravel 5.8 — see https://laravel.com/docs/5.8/upgrade
    // ---------------------------------------------------------------------------------------------
    // https://laravel-news.com/laravel-5-8-deprecates-string-and-array-helpers
    $internalFunctions = get_defined_functions()['internal'];
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FuncCallToStaticCallRector::class, array_filter([
        new FuncCallToStaticCall('array_add', 'Illuminate\Support\Arr', 'add'),
        new FuncCallToStaticCall('array_collapse', 'Illuminate\Support\Arr', 'collapse'),
        new FuncCallToStaticCall('array_divide', 'Illuminate\Support\Arr', 'divide'),
        new FuncCallToStaticCall('array_dot', 'Illuminate\Support\Arr', 'dot'),
        new FuncCallToStaticCall('array_except', 'Illuminate\Support\Arr', 'except'),
        new FuncCallToStaticCall('array_first', 'Illuminate\Support\Arr', 'first'),
        new FuncCallToStaticCall('array_flatten', 'Illuminate\Support\Arr', 'flatten'),
        new FuncCallToStaticCall('array_forget', 'Illuminate\Support\Arr', 'forget'),
        new FuncCallToStaticCall('array_get', 'Illuminate\Support\Arr', 'get'),
        new FuncCallToStaticCall('array_has', 'Illuminate\Support\Arr', 'has'),
        new FuncCallToStaticCall('array_last', 'Illuminate\Support\Arr', 'last'),
        new FuncCallToStaticCall('array_only', 'Illuminate\Support\Arr', 'only'),
        new FuncCallToStaticCall('array_pluck', 'Illuminate\Support\Arr', 'pluck'),
        new FuncCallToStaticCall('array_prepend', 'Illuminate\Support\Arr', 'prepend'),
        new FuncCallToStaticCall('array_pull', 'Illuminate\Support\Arr', 'pull'),
        new FuncCallToStaticCall('array_random', 'Illuminate\Support\Arr', 'random'),
        new FuncCallToStaticCall('array_sort', 'Illuminate\Support\Arr', 'sort'),
        new FuncCallToStaticCall('array_sort_recursive', 'Illuminate\Support\Arr', 'sortRecursive'),
        new FuncCallToStaticCall('array_where', 'Illuminate\Support\Arr', 'where'),
        new FuncCallToStaticCall('array_wrap', 'Illuminate\Support\Arr', 'wrap'),
        new FuncCallToStaticCall('array_set', 'Illuminate\Support\Arr', 'set'),
        new FuncCallToStaticCall('camel_case', 'Illuminate\Support\Str', 'camel'),
        new FuncCallToStaticCall('ends_with', 'Illuminate\Support\Str', 'endsWith'),
        new FuncCallToStaticCall('kebab_case', 'Illuminate\Support\Str', 'kebab'),
        new FuncCallToStaticCall('snake_case', 'Illuminate\Support\Str', 'snake'),
        new FuncCallToStaticCall('starts_with', 'Illuminate\Support\Str', 'startsWith'),
        new FuncCallToStaticCall('str_after', 'Illuminate\Support\Str', 'after'),
        new FuncCallToStaticCall('str_before', 'Illuminate\Support\Str', 'before'),
        new FuncCallToStaticCall('str_contains', 'Illuminate\Support\Str', 'contains'),
        new FuncCallToStaticCall('str_finish', 'Illuminate\Support\Str', 'finish'),
        new FuncCallToStaticCall('str_is', 'Illuminate\Support\Str', 'is'),
        new FuncCallToStaticCall('str_limit', 'Illuminate\Support\Str', 'limit'),
        new FuncCallToStaticCall('str_plural', 'Illuminate\Support\Str', 'plural'),
        new FuncCallToStaticCall('str_random', 'Illuminate\Support\Str', 'random'),
        new FuncCallToStaticCall('str_replace_array', 'Illuminate\Support\Str', 'replaceArray'),
        new FuncCallToStaticCall('str_replace_first', 'Illuminate\Support\Str', 'replaceFirst'),
        new FuncCallToStaticCall('str_replace_last', 'Illuminate\Support\Str', 'replaceLast'),
        new FuncCallToStaticCall('str_singular', 'Illuminate\Support\Str', 'singular'),
        new FuncCallToStaticCall('str_slug', 'Illuminate\Support\Str', 'slug'),
        new FuncCallToStaticCall('str_start', 'Illuminate\Support\Str', 'start'),
        new FuncCallToStaticCall('studly_case', 'Illuminate\Support\Str', 'studly'),
        new FuncCallToStaticCall('title_case', 'Illuminate\Support\Str', 'title'),
    ], fn ($function) => ! in_array($function->getOldFuncName(), $internalFunctions, true)), 'laravel/framework', '>=5.8 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(AddReturnTypeDeclarationRector::class, [
        new AddReturnTypeDeclaration('Illuminate\Contracts\Cache\Repository', 'put', new BooleanType),
        new AddReturnTypeDeclaration('Illuminate\Contracts\Cache\Repository', 'forever', new BooleanType),
        new AddReturnTypeDeclaration('Illuminate\Contracts\Cache\Store', 'put', new BooleanType),
        new AddReturnTypeDeclaration('Illuminate\Contracts\Cache\Store', 'putMany', new BooleanType),
        new AddReturnTypeDeclaration('Illuminate\Contracts\Cache\Store', 'forever', new BooleanType),
    ], 'laravel/framework', '>=5.8 <6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenamePropertyRector::class, [
        new RenameProperty('Illuminate\Routing\UrlGenerator', 'cachedSchema', 'cachedScheme'),
    ], 'laravel/framework', '>=5.8 <6.0');

    $rectorConfig->rule(PropertyDeferToDeferrableProviderToRector::class);

    // ---------------------------------------------------------------------------------------------
    // Laravel 6.0 — see https://laravel.com/docs/6.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Auth\Access\Gate', 'access', 'inspect'),
        new MethodCallRename('Illuminate\Support\Facades\Lang', 'trans', 'get'),
        new MethodCallRename('Illuminate\Support\Facades\Lang', 'transChoice', 'choice'),
        new MethodCallRename('Illuminate\Translation\Translator', 'getFromJson', 'get'),
    ], 'laravel/framework', '>=6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameStaticMethodRector::class, [
        new RenameStaticMethod('Illuminate\Support\Facades\Input', 'get', 'Illuminate\Support\Facades\Request', 'input'),
    ], 'laravel/framework', '>=6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Support\Facades\Input' => 'Illuminate\Support\Facades\Request',
    ], 'laravel/framework', '>=6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility('Illuminate\Foundation\Http\FormRequest', 'validationData', Visibility::PUBLIC),
    ], 'laravel/framework', '>=6.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Database\Capsule\Manager', 'table', 1, 'as'),
    ], 'laravel/framework', '>=6.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 7.0 — see https://laravel.com/docs/7.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(AddParamTypeDeclarationRector::class, [
        new AddParamTypeDeclaration('Illuminate\Contracts\Debug\ExceptionHandler', 'report', 0, new ObjectType('Throwable')),
        new AddParamTypeDeclaration('Illuminate\Contracts\Debug\ExceptionHandler', 'shouldReport', 0, new ObjectType('Throwable')),
        new AddParamTypeDeclaration('Illuminate\Contracts\Debug\ExceptionHandler', 'render', 1, new ObjectType('Throwable')),
        new AddParamTypeDeclaration('Illuminate\Contracts\Debug\ExceptionHandler', 'renderForConsole', 1, new ObjectType('Throwable')),
    ], 'laravel/framework', '>=7.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Contracts\Routing\UrlRoutable', 'resolveRouteBinding', 1, 'field'),
    ], 'laravel/framework', '>=7.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Support\Facades\Blade', 'component', 'aliasComponent'),
        new MethodCallRename('Illuminate\Database\Eloquent\Concerns\HidesAttributes', 'addHidden', 'makeHidden'),
        new MethodCallRename('Illuminate\Database\Eloquent\Concerns\HidesAttributes', 'addVisible', 'makeVisible'),
    ], 'laravel/framework', '>=7.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Http\Resources\Json\Resource' => 'Illuminate\Http\Resources\Json\JsonResource',
        'Illuminate\Foundation\Testing\TestResponse' => 'Illuminate\Testing\TestResponse',
        'Illuminate\Foundation\Testing\Assert' => 'Illuminate\Testing\Assert',
    ], 'laravel/framework', '>=7.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 8.0 — see https://laravel.com/docs/8.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdderWithoutDefaultValue('Illuminate\Contracts\Database\Eloquent\Castable', 'castUsing', 0, 'arguments', new ArrayType(new MixedType, new MixedType)),
    ], 'laravel/framework', '>=8.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(AddArgumentDefaultValueRector::class, [
        new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null),
    ], 'laravel/framework', '>=8.0');

    $rectorConfig->rule(AddParentRegisterToEventServiceProviderRector::class);

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenamePropertyRector::class, [
        new RenameProperty('Illuminate\Support\Manager', 'app', 'container'),
        new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
        new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
    ], 'laravel/framework', '>=8.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Mail\PendingMail', 'sendNow', 'send'),
        new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
        new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
        new MethodCallRename('Illuminate\Testing\TestResponse', 'decodeResponseJson', 'json'),
    ], 'laravel/framework', '>=8.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 9.0 — see https://laravel.com/docs/9.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Contracts\Foundation\Application', 'storagePath', 0, 'path', '', null, ArgumentAddingScope::SCOPE_CLASS_METHOD),
    ], 'laravel/framework', '>=9.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Foundation\Application', 'langPath', 0, 'path', '', null, ArgumentAddingScope::SCOPE_CLASS_METHOD),
    ], 'laravel/framework', '>=9.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ArgumentAdderRector::class, [
        new ArgumentAdder('Illuminate\Database\Eloquent\Model', 'touch', 0, 'attribute', null, null, ArgumentAddingScope::SCOPE_CLASS_METHOD),
    ], 'laravel/framework', '>=9.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility('Illuminate\Foundation\Exceptions\Handler', 'ignore', Visibility::PUBLIC),
    ], 'laravel/framework', '>=9.0');

    $rectorConfig->rule(ReplaceFakerInstanceWithHelperRector::class);
    $rectorConfig->rule(AddExtendsAnnotationToModelFactoriesRector::class);

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Support\Enumerable', 'reduceWithKeys', 'reduce'),
        new MethodCallRename('Illuminate\Support\Enumerable', 'reduceMany', 'reduceSpread'),
        new MethodCallRename('Illuminate\Mail\Message', 'getSwiftMessage', 'getSymfonyMessage'),
        new MethodCallRename('Illuminate\Mail\Mailable', 'withSwiftMessage', 'withSymfonyMessage'),
        new MethodCallRename('Illuminate\Notifications\Messages\MailMessage', 'withSwiftMessage', 'withSymfonyMessage'),
        new MethodCallRename('Illuminate\Mail\Mailer', 'getSwiftMailer', 'getSymfonyTransport'),
        new MethodCallRename('Illuminate\Mail\Mailer', 'setSwiftMailer', 'setSymfonyTransport'),
        new MethodCallRename('Illuminate\Mail\MailManager', 'createTransport', 'createSymfonyTransport'),
        new MethodCallRename('Illuminate\Testing\TestResponse', 'assertDeleted', 'assertModelMissing'),
    ], 'laravel/framework', '>=9.0');

    $rectorConfig->rule(MigrateToSimplifiedAttributeRector::class);

    // ---------------------------------------------------------------------------------------------
    // Laravel 10.0 — see https://laravel.com/docs/10.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->rule(UnifyModelDatesWithCastsRector::class);
    $rectorConfig->rule(DatabaseExpressionCastsToMethodCallRector::class);
    $rectorConfig->rule(DatabaseExpressionToStringToMethodCallRector::class);
    $rectorConfig->rule(ReplaceExpectsMethodsInTestsRector::class);
    $rectorConfig->rule(ReplaceAssertTimesSendWithAssertSentTimesRector::class);
    $rectorConfig->rule(ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector::class);

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenamePropertyRector::class, [
        new RenameProperty('Illuminate\Foundation\Http\Kernel', 'routeMiddleware', 'middlewareAliases'),
    ], 'laravel/framework', '>=10.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Support\Facades\Bus', 'dispatchNow', 'dispatchSync'),
        new MethodCallRename('Illuminate\Foundation\Bus\Dispatchable', 'dispatchNow', 'dispatchSync'),
        new MethodCallRename('Illuminate\Foundation\Bus\DispatchesJobs', 'dispatchNow', 'dispatchSync'),
    ], 'laravel/framework', '>=10.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameFunctionRector::class, [
        'dispatch_now' => 'dispatch_sync',
    ], 'laravel/framework', '>=10.0');

    $rectorConfig->rule(DispatchNonShouldQueueToDispatchSyncRector::class);

    // ---------------------------------------------------------------------------------------------
    // Laravel 11.0 — see https://laravel.com/docs/11.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->rule(ModelCastsPropertyToCastsMethodRector::class);
    $rectorConfig->rule(RefactorBlueprintGeometryColumnsRector::class);
    $rectorConfig->rule(AssertSeeToAssertSeeHtmlRector::class);
    $rectorConfig->rule(ReplaceQueueTraitsWithQueueableRector::class);

    // ---------------------------------------------------------------------------------------------
    // Laravel 12.0 — see https://laravel.com/docs/12.x/upgrade
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->rule(ContainerBindConcreteWithClosureOnlyRector::class);
    $rectorConfig->rule(ScopeNamedClassMethodToScopeAttributedClassMethodRector::class);

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Illuminate\Http\Request', 'get', 'input'),
        new MethodCallRename('Illuminate\Support\Facades\Request', 'get', 'input'),
    ], 'laravel/framework', '>=12.0');

    // ---------------------------------------------------------------------------------------------
    // Laravel 13.0 — see https://laravel.com/docs/13.x/upgrade
    // ---------------------------------------------------------------------------------------------
    // https://laravel.com/docs/13.x/upgrade#request-forgery-protection
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken' => 'Illuminate\Foundation\Http\Middleware\PreventRequestForgery',
        'Illuminate\Foundation\Http\Middleware\ValidateCsrfToken' => 'Illuminate\Foundation\Http\Middleware\PreventRequestForgery',
    ], 'laravel/framework', '>=13.0');

    // Console
    $rectorConfig->rule(AliasesPropertyToAliasesAttributeRector::class);
    $rectorConfig->rule(CommandHiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(DescriptionPropertyToDescriptionAttributeRector::class);
    $rectorConfig->rule(HelpPropertyToHelpAttributeRector::class);
    $rectorConfig->rule(SignaturePropertyToSignatureAttributeRector::class);

    // Eloquent
    $rectorConfig->rule(AppendsPropertyToAppendsAttributeRector::class);
    $rectorConfig->rule(CollectedByPropertyToCollectedByAttributeRector::class);
    $rectorConfig->rule(ConnectionPropertyToConnectionAttributeRector::class);
    $rectorConfig->rule(DateFormatPropertyToDateFormatAttributeRector::class);
    $rectorConfig->rule(EmptyGuardedPropertyToUnguardedAttributeRector::class);
    $rectorConfig->rule(FillablePropertyToFillableAttributeRector::class);
    $rectorConfig->rule(GuardedPropertyToGuardedAttributeRector::class);
    $rectorConfig->rule(HiddenPropertyToHiddenAttributeRector::class);
    $rectorConfig->rule(RouteKeyMethodToRouteKeyAttributeRector::class);
    $rectorConfig->rule(TablePropertyToTableAttributeRector::class);
    $rectorConfig->rule(TouchesPropertyToTouchesAttributeRector::class);
    $rectorConfig->rule(VisiblePropertyToVisibleAttributeRector::class);
    $rectorConfig->rule(WithoutIncrementingPropertyToWithoutIncrementingAttributeRector::class);
    $rectorConfig->rule(WithoutTimestampsPropertyToWithoutTimestampsAttributeRector::class);

    // API Resource
    $rectorConfig->rule(CollectsPropertyToCollectsAttributeRector::class);
    $rectorConfig->rule(PreserveKeysPropertyToPreserveKeysAttributeRector::class);

    // Form Request
    $rectorConfig->rule(ErrorBagPropertyToErrorBagAttributeRector::class);
    $rectorConfig->rule(StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector::class);

    // Queue
    $rectorConfig->rule(BackoffPropertyToBackoffAttributeRector::class);
    $rectorConfig->rule(DelayPropertyToDelayAttributeRector::class);
    $rectorConfig->rule(DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector::class);
    $rectorConfig->rule(FailOnTimeoutPropertyToFailOnTimeoutAttributeRector::class);
    $rectorConfig->rule(JobConnectionPropertyToJobConnectionAttributeRector::class);
    $rectorConfig->rule(MaxExceptionsPropertyToMaxExceptionsAttributeRector::class);
    $rectorConfig->rule(QueuePropertyToQueueAttributeRector::class);
    $rectorConfig->rule(TimeoutPropertyToTimeoutAttributeRector::class);
    $rectorConfig->rule(TriesPropertyToTriesAttributeRector::class);
    $rectorConfig->rule(UniqueForPropertyToUniqueForAttributeRector::class);

    // ---------------------------------------------------------------------------------------------
    // fakerphp/faker 1.0
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->rule(ReplaceFakerPropertyFetchWithMethodCallRector::class);

    // ---------------------------------------------------------------------------------------------
    // livewire/livewire 3.0
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->rule(LivewireComponentQueryStringToUrlAttributeRector::class);
    $rectorConfig->rule(LivewireComponentComputedMethodToComputedAttributeRector::class);

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameAttributeRector::class, [
        new RenameAttribute('Livewire\Attributes\Rule', 'Livewire\Attributes\Validate'),
    ], 'livewire/livewire', '>=3.0');

    // ---------------------------------------------------------------------------------------------
    // livewire/livewire 4.0
    // @see https://livewire.laravel.com/docs/4.x/upgrading#update-component-imports
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Livewire\Volt\Component' => 'Livewire\Component',
    ], 'livewire/livewire', '>=4.0');

    // ---------------------------------------------------------------------------------------------
    // laravel/cashier 13.0
    // @see https://github.com/laravel/cashier-stripe/blob/master/UPGRADE.md#upgrading-to-130-from-12x
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Laravel\Cashier\Billable', 'subscribedToPlan', 'subscribedToPrice'),
        new MethodCallRename('Laravel\Cashier\Billable', 'onPlan', 'onPrice'),
        new MethodCallRename('Laravel\Cashier\Billable', 'planTaxRates', 'priceTaxRates'),
        new MethodCallRename('Laravel\Cashier\SubscriptionBuilder', 'plan', 'price'),
        new MethodCallRename('Laravel\Cashier\SubscriptionBuilder', 'meteredPlan', 'meteredPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasMultiplePlans', 'hasMultiplePrices'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasSinglePlan', 'hasSinglePrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasPlan', 'hasPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'addPlan', 'addPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'addPlanAndInvoice', 'addPriceAndInvoice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'removePlan', 'removePrice'),
    ], 'laravel/cashier', '>=13.0');

    $rectorConfig->rule(CashierStripeOptionsToStripeRector::class);

    // ---------------------------------------------------------------------------------------------
    // laravel/cashier 14.0
    // @see https://github.com/laravel/cashier-stripe/blob/master/UPGRADE.md#upgrading-to-140-from-13x
    // ---------------------------------------------------------------------------------------------
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameMethodRector::class, [
        new MethodCallRename('Laravel\Cashier\Billable', 'removePaymentMethod', 'deletePaymentMethod'),
        new MethodCallRename('Laravel\Cashier\Payment', 'isCancelled', 'isCanceled'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'cancelled', 'canceled'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'markAsCancelled', 'markAsCanceled'),
    ], 'laravel/cashier', '>=14.0');
};

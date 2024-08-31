# 66 Rules Overview

## AbortIfRector

Change if abort to abort_if

- class: [`RectorLaravel\Rector\If_\AbortIfRector`](../src/Rector/If_/AbortIfRector.php)

```diff
-if ($condition) {
-    abort(404);
-}
-if (!$condition) {
-    abort(404);
-}
+abort_if($condition, 404);
+abort_unless($condition, 404);
```

<br>

## AddArgumentDefaultValueRector

Adds default value for arguments in defined methods.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector`](../src/Rector/ClassMethod/AddArgumentDefaultValueRector.php)

```diff
 class SomeClass
 {
-    public function someMethod($value)
+    public function someMethod($value = false)
     {
     }
 }
```

<br>

## AddExtendsAnnotationToModelFactoriesRector

Adds the `@extends` annotation to Factories.

- class: [`RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector`](../src/Rector/Class_/AddExtendsAnnotationToModelFactoriesRector.php)

```diff
 use Illuminate\Database\Eloquent\Factories\Factory;

+/**
+ * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
+ */
 class UserFactory extends Factory
 {
     protected $model = \App\Models\User::class;
 }
```

<br>

## AddGenericReturnTypeToRelationsRector

Add generic return type to relations in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector`](../src/Rector/ClassMethod/AddGenericReturnTypeToRelationsRector.php)

```diff
 use App\Account;
 use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Relations\HasMany;

 class User extends Model
 {
+    /** @return HasMany<Account> */
     public function accounts(): HasMany
     {
         return $this->hasMany(Account::class);
     }
 }
```

<br>

## AddGuardToLoginEventRector

Add new `$guard` argument to Illuminate\Auth\Events\Login

- class: [`RectorLaravel\Rector\New_\AddGuardToLoginEventRector`](../src/Rector/New_/AddGuardToLoginEventRector.php)

```diff
 use Illuminate\Auth\Events\Login;

 final class SomeClass
 {
     public function run(): void
     {
-        $loginEvent = new Login('user', false);
+        $guard = config('auth.defaults.guard');
+        $loginEvent = new Login($guard, 'user', false);
     }
 }
```

<br>

## AddMockConsoleOutputFalseToConsoleTestsRector

Add "$this->mockConsoleOutput = false"; to console tests that work with output content

- class: [`RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector`](../src/Rector/Class_/AddMockConsoleOutputFalseToConsoleTestsRector.php)

```diff
 use Illuminate\Support\Facades\Artisan;
 use Illuminate\Foundation\Testing\TestCase;

 final class SomeTest extends TestCase
 {
+    public function setUp(): void
+    {
+        parent::setUp();
+
+        $this->mockConsoleOutput = false;
+    }
+
     public function test(): void
     {
         $this->assertEquals('content', \trim((new Artisan())::output()));
     }
 }
```

<br>

## AddParentBootToModelClassMethodRector

Add `parent::boot();` call to `boot()` class method in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector`](../src/Rector/ClassMethod/AddParentBootToModelClassMethodRector.php)

```diff
 use Illuminate\Database\Eloquent\Model;

 class Product extends Model
 {
     public function boot()
     {
+        parent::boot();
     }
 }
```

<br>

## AddParentRegisterToEventServiceProviderRector

Add `parent::register();` call to `register()` class method in child of `Illuminate\Foundation\Support\Providers\EventServiceProvider`

- class: [`RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector`](../src/Rector/ClassMethod/AddParentRegisterToEventServiceProviderRector.php)

```diff
 use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

 class EventServiceProvider extends ServiceProvider
 {
     public function register()
     {
+        parent::register();
     }
 }
```

<br>

## AnonymousMigrationsRector

Convert migrations to anonymous classes.

- class: [`RectorLaravel\Rector\Class_\AnonymousMigrationsRector`](../src/Rector/Class_/AnonymousMigrationsRector.php)

```diff
 use Illuminate\Database\Migrations\Migration;

-class CreateUsersTable extends Migration
+return new class extends Migration
 {
     // ...
-}
+};
```

<br>

## AppEnvironmentComparisonToParameterRector

Replace `$app->environment() === 'local'` with `$app->environment('local')`

- class: [`RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector`](../src/Rector/Expr/AppEnvironmentComparisonToParameterRector.php)

```diff
-$app->environment() === 'production';
+$app->environment('production');
```

<br>

## ArgumentFuncCallToMethodCallRector

Move help facade-like function calls to constructor injection

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector`](../src/Rector/FuncCall/ArgumentFuncCallToMethodCallRector.php)

```diff
 class SomeController
 {
+    /**
+     * @var \Illuminate\Contracts\View\Factory
+     */
+    private $viewFactory;
+
+    public function __construct(\Illuminate\Contracts\View\Factory $viewFactory)
+    {
+        $this->viewFactory = $viewFactory;
+    }
+
     public function action()
     {
-        $template = view('template.blade');
-        $viewFactory = view();
+        $template = $this->viewFactory->make('template.blade');
+        $viewFactory = $this->viewFactory;
     }
 }
```

<br>

## AssertStatusToAssertMethodRector

Replace `(new \Illuminate\Testing\TestResponse)->assertStatus(200)` with `(new \Illuminate\Testing\TestResponse)->assertOk()`

- class: [`RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector`](../src/Rector/MethodCall/AssertStatusToAssertMethodRector.php)

```diff
 class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
 {
     public function testOk()
     {
-        $this->get('/')->assertStatus(200);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_OK);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
+        $this->get('/')->assertOk();
+        $this->get('/')->assertOk();
+        $this->get('/')->assertOk();
     }

     public function testNoContent()
     {
-        $this->get('/')->assertStatus(204);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
+        $this->get('/')->assertNoContent();
+        $this->get('/')->assertNoContent();
+        $this->get('/')->assertNoContent();
     }

     public function testUnauthorized()
     {
-        $this->get('/')->assertStatus(401);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_UNAUTHORIZED);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
+        $this->get('/')->assertUnauthorized();
+        $this->get('/')->assertUnauthorized();
+        $this->get('/')->assertUnauthorized();
     }

     public function testForbidden()
     {
-        $this->get('/')->assertStatus(403);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_FORBIDDEN);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
+        $this->get('/')->assertForbidden();
+        $this->get('/')->assertForbidden();
+        $this->get('/')->assertForbidden();
     }

     public function testNotFound()
     {
-        $this->get('/')->assertStatus(404);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_NOT_FOUND);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
+        $this->get('/')->assertNotFound();
+        $this->get('/')->assertNotFound();
+        $this->get('/')->assertNotFound();
     }

     public function testMethodNotAllowed()
     {
-        $this->get('/')->assertStatus(405);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_METHOD_NOT_ALLOWED);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED);
+        $this->get('/')->assertMethodNotAllowed();
+        $this->get('/')->assertMethodNotAllowed();
+        $this->get('/')->assertMethodNotAllowed();
     }

     public function testUnprocessableEntity()
     {
-        $this->get('/')->assertStatus(422);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
+        $this->get('/')->assertUnprocessable();
+        $this->get('/')->assertUnprocessable();
+        $this->get('/')->assertUnprocessable();
     }

     public function testGone()
     {
-        $this->get('/')->assertStatus(410);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_GONE);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_GONE);
+        $this->get('/')->assertGone();
+        $this->get('/')->assertGone();
+        $this->get('/')->assertGone();
     }

     public function testInternalServerError()
     {
-        $this->get('/')->assertStatus(500);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
+        $this->get('/')->assertInternalServerError();
+        $this->get('/')->assertInternalServerError();
+        $this->get('/')->assertInternalServerError();
     }

     public function testServiceUnavailable()
     {
-        $this->get('/')->assertStatus(503);
-        $this->get('/')->assertStatus(\Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE);
-        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
+        $this->get('/')->assertServiceUnavailable();
+        $this->get('/')->assertServiceUnavailable();
+        $this->get('/')->assertServiceUnavailable();
     }
 }
```

<br>

## AvoidNegatedCollectionFilterOrRejectRector

Avoid negated conditionals in `filter()` by using `reject()`, or vice versa.

- class: [`RectorLaravel\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector`](../src/Rector/MethodCall/AvoidNegatedCollectionFilterOrRejectRector.php)

```diff
 use Illuminate\Support\Collection;

 $collection = new Collection([0, 1, null, -1]);
-$collection->filter(fn (?int $number): bool => ! is_null($number));
-$collection->filter(fn (?int $number): bool => ! $number);
-$collection->reject(fn (?int $number) => ! $number > 0);
+$collection->reject(fn (?int $number): bool => is_null($number)); // Avoid negation
+$collection->reject(fn (?int $number): bool => (bool) $number); // Explicitly cast
+$collection->filter(fn (?int $number): bool => $number > 0); // Adds return type
```

<br>

## CallOnAppArrayAccessToStandaloneAssignRector

Replace magical call on `$this->app["something"]` to standalone type assign variable

- class: [`RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector`](../src/Rector/Assign/CallOnAppArrayAccessToStandaloneAssignRector.php)

```diff
 class SomeClass
 {
     /**
      * @var \Illuminate\Contracts\Foundation\Application
      */
     private $app;

     public function run()
     {
-        $validator = $this->app['validator']->make('...');
+        /** @var \Illuminate\Validation\Factory $validationFactory */
+        $validationFactory = $this->app['validator'];
+        $validator = $validationFactory->make('...');
     }
 }
```

<br>

## CarbonSetTestNowToTravelToRector

Use the `$this->travelTo()` method in Laravel's `TestCase` class instead of the `Carbon::setTestNow()` method.

- class: [`RectorLaravel\Rector\StaticCall\CarbonSetTestNowToTravelToRector`](../src/Rector/StaticCall/CarbonSetTestNowToTravelToRector.php)

```diff
 use Illuminate\Support\Carbon;
 use Illuminate\Foundation\Testing\TestCase;

 class SomeTest extends TestCase
 {
     public function test()
     {
-        Carbon::setTestNow('2024-08-11');
+        $this->travelTo('2024-08-11');
     }
 }
```

<br>

## CashierStripeOptionsToStripeRector

Renames the Billable `stripeOptions()` to `stripe().`

- class: [`RectorLaravel\Rector\Class_\CashierStripeOptionsToStripeRector`](../src/Rector/Class_/CashierStripeOptionsToStripeRector.php)

```diff
 use Illuminate\Database\Eloquent\Model;
 use Laravel\Cashier\Billable;

 class User extends Model
 {
     use Billable;

-    public function stripeOptions(array $options = []) {
+    public function stripe(array $options = []) {
         return [];
     }
 }
```

<br>

## ChangeQueryWhereDateValueWithCarbonRector

Add `parent::boot();` call to `boot()` class method in child of `Illuminate\Database\Eloquent\Model`

- class: [`RectorLaravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector`](../src/Rector/MethodCall/ChangeQueryWhereDateValueWithCarbonRector.php)

```diff
 use Illuminate\Database\Query\Builder;

 final class SomeClass
 {
     public function run(Builder $query)
     {
-        $query->whereDate('created_at', '<', Carbon::now());
+        $dateTime = Carbon::now();
+        $query->whereDate('created_at', '<=', $dateTime);
+        $query->whereTime('created_at', '<=', $dateTime);
     }
 }
```

<br>

## DatabaseExpressionCastsToMethodCallRector

Convert DB Expression string casts to `getValue()` method calls.

- class: [`RectorLaravel\Rector\Cast\DatabaseExpressionCastsToMethodCallRector`](../src/Rector/Cast/DatabaseExpressionCastsToMethodCallRector.php)

```diff
 use Illuminate\Support\Facades\DB;

-$string = (string) DB::raw('select 1');
+$string = DB::raw('select 1')->getValue(DB::connection()->getQueryGrammar());
```

<br>

## DatabaseExpressionToStringToMethodCallRector

Convert DB Expression `__toString()` calls to `getValue()` method calls.

- class: [`RectorLaravel\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector`](../src/Rector/MethodCall/DatabaseExpressionToStringToMethodCallRector.php)

```diff
 use Illuminate\Support\Facades\DB;

-$string = DB::raw('select 1')->__toString();
+$string = DB::raw('select 1')->getValue(DB::connection()->getQueryGrammar());
```

<br>

## DispatchNonShouldQueueToDispatchSyncRector

Dispatch non ShouldQueue jobs to dispatchSync

- class: [`RectorLaravel\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector`](../src/Rector/FuncCall/DispatchNonShouldQueueToDispatchSyncRector.php)

```diff
-dispatch(new SomeJob());
-Bus::dispatch(new SomeJob());
-$this->dispatch(new SomeJob());
+dispatch_sync(new SomeJob());
+Bus::dispatchSync(new SomeJob());
+$this->dispatchSync(new SomeJob());
```

<br>

## DispatchToHelperFunctionsRector

Use the event or dispatch helpers instead of the static dispatch method.

- class: [`RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector`](../src/Rector/StaticCall/DispatchToHelperFunctionsRector.php)

```diff
-ExampleEvent::dispatch($email);
+event(new ExampleEvent($email));
```

<br>

```diff
-ExampleJob::dispatch($email);
+dispatch(new ExampleJob($email));
```

<br>

## EloquentMagicMethodToQueryBuilderRector

The EloquentMagicMethodToQueryBuilderRule is designed to automatically transform certain magic method calls on Eloquent Models into corresponding Query Builder method calls.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector`](../src/Rector/StaticCall/EloquentMagicMethodToQueryBuilderRector.php)

```diff
 use App\Models\User;

-$user = User::find(1);
+$user = User::query()->find(1);
```

<br>

## EloquentOrderByToLatestOrOldestRector

Changes `orderBy()` to `latest()` or `oldest()`

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector`](../src/Rector/MethodCall/EloquentOrderByToLatestOrOldestRector.php)

```diff
 use Illuminate\Database\Eloquent\Builder;

 $column = 'tested_at';

-$builder->orderBy('created_at');
-$builder->orderBy('created_at', 'desc');
-$builder->orderBy('submitted_at');
-$builder->orderByDesc('submitted_at');
-$builder->orderBy($allowed_variable_name);
+$builder->oldest();
+$builder->latest();
+$builder->oldest('submitted_at');
+$builder->latest('submitted_at');
+$builder->oldest($allowed_variable_name);
 $builder->orderBy($unallowed_variable_name);
 $builder->orderBy('unallowed_column_name');
```

<br>

## EloquentWhereRelationTypeHintingParameterRector

Add type hinting to where relation has methods e.g. `whereHas`, `orWhereHas`, `whereDoesntHave`, `orWhereDoesntHave`, `whereHasMorph`, `orWhereHasMorph`, `whereDoesntHaveMorph`, `orWhereDoesntHaveMorph`

- class: [`RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector`](../src/Rector/MethodCall/EloquentWhereRelationTypeHintingParameterRector.php)

```diff
-User::whereHas('posts', function ($query) {
+User::whereHas('posts', function (\Illuminate\Contracts\Database\Query\Builder $query) {
     $query->where('is_published', true);
 });

-$query->whereHas('posts', function ($query) {
+$query->whereHas('posts', function (\Illuminate\Contracts\Database\Query\Builder $query) {
     $query->where('is_published', true);
 });
```

<br>

## EloquentWhereTypeHintClosureParameterRector

Change typehint of closure parameter in where method of Eloquent Builder

- class: [`RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector`](../src/Rector/MethodCall/EloquentWhereTypeHintClosureParameterRector.php)

```diff
-$query->where(function ($query) {
+$query->where(function (\Illuminate\Contracts\Database\Eloquent\Builder $query) {
     $query->where('id', 1);
 });
```

<br>

## EmptyToBlankAndFilledFuncRector

Replace use of the unsafe `empty()` function with Laravel's safer `blank()` & `filled()` functions.

- class: [`RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector`](../src/Rector/Empty_/EmptyToBlankAndFilledFuncRector.php)

```diff
-empty([]);
-!empty([]);
+blank([]);
+filled([]);
```

<br>

## FactoryApplyingStatesRector

Call the state methods directly instead of specify the name of state.

- class: [`RectorLaravel\Rector\MethodCall\FactoryApplyingStatesRector`](../src/Rector/MethodCall/FactoryApplyingStatesRector.php)

```diff
-$factory->state('delinquent');
-$factory->states('premium', 'delinquent');
+$factory->delinquent();
+$factory->premium()->delinquent();
```

<br>

## FactoryDefinitionRector

Upgrade legacy factories to support classes.

- class: [`RectorLaravel\Rector\Namespace_\FactoryDefinitionRector`](../src/Rector/Namespace_/FactoryDefinitionRector.php)

```diff
 use Faker\Generator as Faker;

-$factory->define(App\User::class, function (Faker $faker) {
-    return [
-        'name' => $faker->name,
-        'email' => $faker->unique()->safeEmail,
-    ];
-});
+class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory
+{
+    protected $model = App\User::class;
+    public function definition()
+    {
+        return [
+            'name' => $this->faker->name,
+            'email' => $this->faker->unique()->safeEmail,
+        ];
+    }
+}
```

<br>

## FactoryFuncCallToStaticCallRector

Use the static factory method instead of global factory function.

- class: [`RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector`](../src/Rector/FuncCall/FactoryFuncCallToStaticCallRector.php)

```diff
-factory(User::class);
+User::factory();
```

<br>

## HelperFuncCallToFacadeClassRector

Change `app()` func calls to facade calls

- class: [`RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector`](../src/Rector/FuncCall/HelperFuncCallToFacadeClassRector.php)

```diff
 class SomeClass
 {
     public function run()
     {
-        return app('translator')->trans('value');
+        return \Illuminate\Support\Facades\App::get('translator')->trans('value');
     }
 }
```

<br>

## JsonCallToExplicitJsonCallRector

Change method calls from `$this->json` to `$this->postJson,` `$this->putJson,` etc.

- class: [`RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector`](../src/Rector/MethodCall/JsonCallToExplicitJsonCallRector.php)

```diff
-$this->json("POST", "/api/v1/users", $data);
+$this->postJson("/api/v1/users", $data);
```

<br>

## LivewireComponentComputedMethodToComputedAttributeRector

Converts the computed methods of a Livewire component to use the Computed Attribute

- class: [`RectorLaravel\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector`](../src/Rector/Class_/LivewireComponentComputedMethodToComputedAttributeRector.php)

```diff
 use Livewire\Component;

 class MyComponent extends Component
 {
-    public function getFooBarProperty()
+    #[\Livewire\Attributes\Computed]
+    public function fooBar()
     {
     }
 }
```

<br>

## LivewireComponentQueryStringToUrlAttributeRector

Converts the `$queryString` property of a Livewire component to use the Url Attribute

- class: [`RectorLaravel\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector`](../src/Rector/Class_/LivewireComponentQueryStringToUrlAttributeRector.php)

```diff
 use Livewire\Component;

 class MyComponent extends Component
 {
+    #[\Livewire\Attributes\Url]
     public string $something = '';

+    #[\Livewire\Attributes\Url]
     public string $another = '';
-
-    protected $queryString = [
-        'something',
-        'another',
-    ];
 }
```

<br>

## LumenRoutesStringActionToUsesArrayRector

Changes action in rule definitions from string to array notation.

- class: [`RectorLaravel\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector`](../src/Rector/MethodCall/LumenRoutesStringActionToUsesArrayRector.php)

```diff
-$router->get('/user', 'UserController@get');
+$router->get('/user', ['uses => 'UserController@get']);
```

<br>

## LumenRoutesStringMiddlewareToArrayRector

Changes middlewares from rule definitions from string to array notation.

- class: [`RectorLaravel\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector`](../src/Rector/MethodCall/LumenRoutesStringMiddlewareToArrayRector.php)

```diff
-$router->get('/user', ['middleware => 'test']);
-$router->post('/user', ['middleware => 'test|authentication']);
+$router->get('/user', ['middleware => ['test']]);
+$router->post('/user', ['middleware => ['test', 'authentication']]);
```

<br>

## MigrateToSimplifiedAttributeRector

Migrate to the new Model attributes syntax

- class: [`RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector`](../src/Rector/ClassMethod/MigrateToSimplifiedAttributeRector.php)

```diff
 use Illuminate\Database\Eloquent\Model;

 class User extends Model
 {
-    public function getFirstNameAttribute($value)
+    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
     {
-        return ucfirst($value);
-    }
-
-    public function setFirstNameAttribute($value)
-    {
-        $this->attributes['first_name'] = strtolower($value);
-        $this->attributes['first_name_upper'] = strtoupper($value);
+        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
+            return ucfirst($value);
+        }, set: function ($value) {
+            return ['first_name' => strtolower($value), 'first_name_upper' => strtoupper($value)];
+        });
     }
 }
```

<br>

## MinutesToSecondsInCacheRector

Change minutes argument to seconds in `Illuminate\Contracts\Cache\Store` and Illuminate\Support\Facades\Cache

- class: [`RectorLaravel\Rector\StaticCall\MinutesToSecondsInCacheRector`](../src/Rector/StaticCall/MinutesToSecondsInCacheRector.php)

```diff
 class SomeClass
 {
     public function run()
     {
-        Illuminate\Support\Facades\Cache::put('key', 'value', 60);
+        Illuminate\Support\Facades\Cache::put('key', 'value', 60 * 60);
     }
 }
```

<br>

## ModelCastsPropertyToCastsMethodRector

Refactor Model `$casts` property with `casts()` method

- class: [`RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector`](../src/Rector/Class_/ModelCastsPropertyToCastsMethodRector.php)

```diff
 use Illuminate\Database\Eloquent\Model;

 class Person extends Model
 {
-    protected $casts = [
-        'age' => 'integer',
-    ];
+    protected function casts(): array
+    {
+        return [
+            'age' => 'integer',
+        ];
+    }
 }
```

<br>

## NotFilledBlankFuncCallToBlankFilledFuncCallRector

Swap the use of NotBooleans used with `filled()` and `blank()` to the correct helper.

- class: [`RectorLaravel\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector`](../src/Rector/FuncCall/NotFilledBlankFuncCallToBlankFilledFuncCallRector.php)

```diff
-!filled([]);
-!blank([]);
+blank([]);
+filled([]);
```

<br>

## NowFuncWithStartOfDayMethodCallToTodayFuncRector

Use `today()` instead of `now()->startOfDay()`

- class: [`RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector`](../src/Rector/FuncCall/NowFuncWithStartOfDayMethodCallToTodayFuncRector.php)

```diff
-$now = now()->startOfDay();
+$now = today();
```

<br>

## OptionalToNullsafeOperatorRector

Convert simple calls to optional helper to use the nullsafe operator

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector`](../src/Rector/PropertyFetch/OptionalToNullsafeOperatorRector.php)

```diff
-optional($user)->getKey();
-optional($user)->id;
+$user?->getKey();
+$user?->id;
 // macro methods
 optional($user)->present()->getKey();
```

<br>

## PropertyDeferToDeferrableProviderToRector

Change deprecated `$defer` = true; to `Illuminate\Contracts\Support\DeferrableProvider` interface

- class: [`RectorLaravel\Rector\Class_\PropertyDeferToDeferrableProviderToRector`](../src/Rector/Class_/PropertyDeferToDeferrableProviderToRector.php)

```diff
 use Illuminate\Support\ServiceProvider;
+use Illuminate\Contracts\Support\DeferrableProvider;

-final class SomeServiceProvider extends ServiceProvider
+final class SomeServiceProvider extends ServiceProvider implements DeferrableProvider
 {
-    /**
-     * @var bool
-     */
-    protected $defer = true;
 }
```

<br>

## Redirect301ToPermanentRedirectRector

Change "redirect" call with 301 to "permanentRedirect"

- class: [`RectorLaravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector`](../src/Rector/StaticCall/Redirect301ToPermanentRedirectRector.php)

```diff
 class SomeClass
 {
     public function run()
     {
-        Illuminate\Routing\Route::redirect('/foo', '/bar', 301);
+        Illuminate\Routing\Route::permanentRedirect('/foo', '/bar');
     }
 }
```

<br>

## RedirectBackToBackHelperRector

Replace `redirect()->back()` and `Redirect::back()` with `back()`

- class: [`RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector`](../src/Rector/MethodCall/RedirectBackToBackHelperRector.php)

```diff
 use Illuminate\Support\Facades\Redirect;

 class MyController
 {
     public function store()
     {
-        return redirect()->back()->with('error', 'Incorrect Details.')
+        return back()->with('error', 'Incorrect Details.')
     }

     public function update()
     {
-        return Redirect::back()->with('error', 'Incorrect Details.')
+        return back()->with('error', 'Incorrect Details.')
     }
 }
```

<br>

## RedirectRouteToToRouteHelperRector

Replace `redirect()->route("home")` and `Redirect::route("home")` with `to_route("home")`

- class: [`RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector`](../src/Rector/MethodCall/RedirectRouteToToRouteHelperRector.php)

```diff
 use Illuminate\Support\Facades\Redirect;

 class MyController
 {
     public function store()
     {
-        return redirect()->route('home')->with('error', 'Incorrect Details.')
+        return to_route('home')->with('error', 'Incorrect Details.')
     }

     public function update()
     {
-        return Redirect::route('home')->with('error', 'Incorrect Details.')
+        return to_route('home')->with('error', 'Incorrect Details.')
     }
 }
```

<br>

## RefactorBlueprintGeometryColumnsRector

refactors calls with the pre Laravel 11 methods for blueprint geometry columns

- class: [`RectorLaravel\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector`](../src/Rector/MethodCall/RefactorBlueprintGeometryColumnsRector.php)

```diff
-$blueprint->point('coordinates')->spatialIndex();
+$blueprint->geometry('coordinates', 'point')->spatialIndex();
```

<br>

## RemoveDumpDataDeadCodeRector

It will removes the dump data just like dd or dump functions from the code.`

- class: [`RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector`](../src/Rector/FuncCall/RemoveDumpDataDeadCodeRector.php)

```diff
 class MyController
 {
     public function store()
     {
-        dd('test');
         return true;
     }

     public function update()
     {
-        dump('test');
         return true;
     }
 }
```

<br>

## RemoveModelPropertyFromFactoriesRector

Removes the `$model` property from Factories.

- class: [`RectorLaravel\Rector\Class_\RemoveModelPropertyFromFactoriesRector`](../src/Rector/Class_/RemoveModelPropertyFromFactoriesRector.php)

```diff
 use Illuminate\Database\Eloquent\Factories\Factory;

 class UserFactory extends Factory
 {
-    protected $model = \App\Models\User::class;
 }
```

<br>

## RemoveRedundantValueCallsRector

Removes redundant value helper calls

- class: [`RectorLaravel\Rector\FuncCall\RemoveRedundantValueCallsRector`](../src/Rector/FuncCall/RemoveRedundantValueCallsRector.php)

```diff
-value(new Object())->something();
+(new Object())->something();
```

<br>

## RemoveRedundantWithCallsRector

Removes redundant with helper calls

- class: [`RectorLaravel\Rector\FuncCall\RemoveRedundantWithCallsRector`](../src/Rector/FuncCall/RemoveRedundantWithCallsRector.php)

```diff
-with(new Object())->something();
+(new Object())->something();
```

<br>

## ReplaceAssertTimesSendWithAssertSentTimesRector

Replace assertTimesSent with assertSentTimes

- class: [`RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector`](../src/Rector/StaticCall/ReplaceAssertTimesSendWithAssertSentTimesRector.php)

```diff
-Notification::assertTimesSent(1, SomeNotification::class);
+Notification::assertSentTimes(SomeNotification::class, 1);
```

<br>

## ReplaceExpectsMethodsInTestsRector

Replace expectJobs and expectEvents methods in tests

- class: [`RectorLaravel\Rector\Class_\ReplaceExpectsMethodsInTestsRector`](../src/Rector/Class_/ReplaceExpectsMethodsInTestsRector.php)

```diff
 use Illuminate\Foundation\Testing\TestCase;

 class SomethingTest extends TestCase
 {
     public function testSomething()
     {
-        $this->expectsJobs([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
-        $this->expectsEvents(\App\Events\SomeEvent::class);
+        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\SomeJob::class, \App\Jobs\SomeOtherJob::class]);
+        \Illuminate\Support\Facades\Event::fake([\App\Events\SomeEvent::class]);

         $this->get('/');
+
+        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeJob::class);
+        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\SomeOtherJob::class);
+        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SomeEvent::class);
     }
 }
```

<br>

## ReplaceFakerInstanceWithHelperRector

Replace `$this->faker` with the `fake()` helper function in Factories

- class: [`RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector`](../src/Rector/PropertyFetch/ReplaceFakerInstanceWithHelperRector.php)

```diff
 class UserFactory extends Factory
 {
     public function definition()
     {
         return [
-            'name' => $this->faker->name,
-            'email' => $this->faker->unique()->safeEmail,
+            'name' => fake()->name,
+            'email' => fake()->unique()->safeEmail,
         ];
     }
 }
```

<br>

## ReplaceServiceContainerCallArgRector

Changes the string or class const used for a service container make call

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector`](../src/Rector/MethodCall/ReplaceServiceContainerCallArgRector.php)

```diff
-app('encrypter')->encrypt('...');
-\Illuminate\Support\Facades\Application::make('encrypter')->encrypt('...');
+app(Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('...');
+\Illuminate\Support\Facades\Application::make(Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('...');
```

<br>

## ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector

Replace `withoutJobs`, `withoutEvents` and `withoutNotifications` with Facade `fake`

- class: [`RectorLaravel\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector`](../src/Rector/MethodCall/ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector.php)

```diff
-$this->withoutJobs();
-$this->withoutEvents();
-$this->withoutNotifications();
+\Illuminate\Support\Facades\Bus::fake();
+\Illuminate\Support\Facades\Event::fake();
+\Illuminate\Support\Facades\Notification::fake();
```

<br>

## ReportIfRector

Change if report to report_if

- class: [`RectorLaravel\Rector\If_\ReportIfRector`](../src/Rector/If_/ReportIfRector.php)

```diff
-if ($condition) {
-    report(new Exception());
-}
-if (!$condition) {
-    report(new Exception());
-}
+report_if($condition, new Exception());
+report_unless($condition, new Exception());
```

<br>

## RequestStaticValidateToInjectRector

Change static `validate()` method to `$request->validate()`

- class: [`RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector`](../src/Rector/StaticCall/RequestStaticValidateToInjectRector.php)

```diff
 use Illuminate\Http\Request;

 class SomeClass
 {
-    public function store()
+    public function store(\Illuminate\Http\Request $request)
     {
-        $validatedData = Request::validate(['some_attribute' => 'required']);
+        $validatedData = $request->validate(['some_attribute' => 'required']);
     }
 }
```

<br>

## ReverseConditionableMethodCallRector

Reverse conditionable method calls

- class: [`RectorLaravel\Rector\MethodCall\ReverseConditionableMethodCallRector`](../src/Rector/MethodCall/ReverseConditionableMethodCallRector.php)

```diff
-$conditionable->when(!$condition, function () {});
+$conditionable->unless($condition, function () {});
```

<br>

```diff
-$conditionable->unless(!$condition, function () {});
+$conditionable->when($condition, function () {});
```

<br>

## RouteActionCallableRector

Use PHP callable syntax instead of string syntax for controller route declarations.

:wrench: **configure it!**

- class: [`RectorLaravel\Rector\StaticCall\RouteActionCallableRector`](../src/Rector/StaticCall/RouteActionCallableRector.php)

```diff
-Route::get('/users', 'UserController@index');
+Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
```

<br>

## SleepFuncToSleepStaticCallRector

Use `Sleep::sleep()` and `Sleep::usleep()` instead of the `sleep()` and `usleep()` function.

- class: [`RectorLaravel\Rector\FuncCall\SleepFuncToSleepStaticCallRector`](../src/Rector/FuncCall/SleepFuncToSleepStaticCallRector.php)

```diff
-sleep(5);
+\Illuminate\Support\Sleep::sleep(5);
```

<br>

## SubStrToStartsWithOrEndsWithStaticMethodCallRector

Use `Str::startsWith()` or `Str::endsWith()` instead of `substr()` === `$str`

- class: [`RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector`](../src/Rector/Expr/SubStrToStartsWithOrEndsWithStaticMethodCallRector/SubStrToStartsWithOrEndsWithStaticMethodCallRector.php)

```diff
-if (substr($str, 0, 3) === 'foo') {
+if (Str::startsWith($str, 'foo')) {
     // do something
 }
```

<br>

## ThrowIfAndThrowUnlessExceptionsToUseClassStringRector

changes use of a new throw instance to class string

- class: [`RectorLaravel\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector`](../src/Rector/FuncCall/ThrowIfAndThrowUnlessExceptionsToUseClassStringRector.php)

```diff
-throw_if($condition, new MyException('custom message'));
+throw_if($condition, MyException::class, 'custom message');
```

<br>

## ThrowIfRector

Change if throw to throw_if

- class: [`RectorLaravel\Rector\If_\ThrowIfRector`](../src/Rector/If_/ThrowIfRector.php)

```diff
-if ($condition) {
-    throw new Exception();
-}
-if (!$condition) {
-    throw new Exception();
-}
+throw_if($condition, new Exception());
+throw_unless($condition, new Exception());
```

<br>

## TypeHintTappableCallRector

Automatically type hints your tappable closures

- class: [`RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector`](../src/Rector/FuncCall/TypeHintTappableCallRector.php)

```diff
-tap($collection, function ($collection) {}
+tap($collection, function (Collection $collection) {}
```

<br>

```diff
-(new Collection)->tap(function ($collection) {}
+(new Collection)->tap(function (Collection $collection) {}
```

<br>

## UnifyModelDatesWithCastsRector

Unify Model `$dates` property with `$casts`

- class: [`RectorLaravel\Rector\Class_\UnifyModelDatesWithCastsRector`](../src/Rector/Class_/UnifyModelDatesWithCastsRector.php)

```diff
 use Illuminate\Database\Eloquent\Model;

 class Person extends Model
 {
     protected $casts = [
-        'age' => 'integer',
+        'age' => 'integer', 'birthday' => 'datetime',
     ];
-
-    protected $dates = ['birthday'];
 }
```

<br>

## UseComponentPropertyWithinCommandsRector

Use `$this->components` property within commands

- class: [`RectorLaravel\Rector\MethodCall\UseComponentPropertyWithinCommandsRector`](../src/Rector/MethodCall/UseComponentPropertyWithinCommandsRector.php)

```diff
 use Illuminate\Console\Command;

 class CommandWithComponents extends Command
 {
     public function handle()
     {
-        $this->ask('What is your name?');
-        $this->line('A line!');
-        $this->info('Info!');
-        $this->error('Error!');
+        $this->components->ask('What is your name?');
+        $this->components->line('A line!');
+        $this->components->info('Info!');
+        $this->components->error('Error!');
     }
 }
```

<br>

## ValidationRuleArrayStringValueToArrayRector

Convert string validation rules into arrays for Laravel's Validator.

- class: [`RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector`](../src/Rector/MethodCall/ValidationRuleArrayStringValueToArrayRector.php)

```diff
 Validator::make($data, [
-    'field' => 'required|nullable|string|max:255',
+    'field' => ['required', 'nullable', 'string', 'max:255'],
 ]);
```

<br>

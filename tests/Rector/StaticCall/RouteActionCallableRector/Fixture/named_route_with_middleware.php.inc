<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', ['as' => 'users.index', 'middleware' => 'auth', 'uses' => 'SomeController@index']);
Route::get('/users', ['as' => 'users.index', 'uses' => 'SomeController@index', 'middleware' => 'auth']);
Route::get('/users', ['uses' => 'SomeController@index', 'middleware' => 'auth', 'as' => 'users.index']);
Route::get('/users', ['uses' => 'SomeController@index', 'as' => 'users.index', 'middleware' => 'auth']);
Route::get('/users', ['middleware' => 'auth', 'as' => 'users.index', 'uses' => 'SomeController@index']);
Route::get('/users', ['middleware' => 'auth', 'uses' => 'SomeController@index', 'as' => 'users.index']);
Route::get('/users', ['as' => 'users.index', 'middleware' => ['auth', 'throttle:20,1'], 'uses' => 'SomeController@index']);
Route::get('/users', ['as' => 'users.index', 'uses' => 'SomeController@index', 'middleware' => ['auth', 'throttle:20,1']]);
Route::get('/users', ['uses' => 'SomeController@index', 'as' => 'users.index', 'middleware' => ['auth', 'throttle:20,1']]);
Route::get('/users', ['uses' => 'SomeController@index', 'middleware' => ['auth', 'throttle:20,1'], 'as' => 'users.index']);
Route::get('/users', ['middleware' => ['auth', 'throttle:20,1'], 'uses' => 'SomeController@index', 'as' => 'users.index']);
Route::get('/users', ['middleware' => ['auth', 'throttle:20,1'], 'as' => 'users.index', 'uses' => 'SomeController@index']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index')->middleware(['auth', 'throttle:20,1']);

?>

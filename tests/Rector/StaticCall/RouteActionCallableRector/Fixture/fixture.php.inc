<?php

namespace Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', 'SomeController@index');
Route::get('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::post('/users', 'SomeController@index');
Route::patch('/users', 'SomeController@index');
Route::put('/users', 'SomeController@index');
Route::delete('/users', 'SomeController@index');
Route::any('/users', 'SomeController@index');
Route::match(['GET', 'POST'], '/users', 'SomeController@index');
Route::fallback('SomeController@index');
Route::options('/users', 'SomeController@index');
Route::middleware([])->options('/users', 'SomeController@index');
Route::get('/users', ['as' => 'users.index', 'uses' => 'SomeController@index']);
Route::get('/users', ['uses' => 'SomeController@index', 'as' => 'users.index']);

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::get('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::post('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::patch('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::put('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::delete('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::any('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::match(['GET', 'POST'], '/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::fallback([\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::options('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::middleware([])->options('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::get('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index');
Route::get('/users', [\Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index');

?>

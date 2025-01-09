<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', 'SomeController@index');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::post('/users', 'SomeController@index');
Route::patch('/users', 'SomeController@index');
Route::put('/users', 'SomeController@index');
Route::delete('/users', 'SomeController@index');
Route::any('/users', 'SomeController@index');
Route::match(['GET', 'POST'], '/users', 'SomeController@index');
Route::fallback('SomeController@index');
Route::options('/users', 'SomeController@index');
Route::middleware([])->options('/users', 'SomeController@index');

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::get('/users', 'SomeController@index');
})

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::post('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::patch('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::put('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::delete('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::any('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::match(['GET', 'POST'], '/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::fallback([\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::options('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);
Route::middleware([])->options('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeNamespace\SomeController::class, 'index']);
})

?>

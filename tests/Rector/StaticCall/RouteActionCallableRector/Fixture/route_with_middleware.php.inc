<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', ['middleware' => 'auth', 'uses' => 'SomeController@index']);
Route::get('/users', ['uses' => 'SomeController@index', 'middleware' => 'auth']);
Route::get('/users', ['middleware' => ['auth', 'throttle:20,1'], 'uses' => 'SomeController@index']);
Route::get('/users', ['uses' => 'SomeController@index', 'middleware' => ['auth', 'throttle:20,1']]);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->middleware('auth');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->middleware(['auth', 'throttle:20,1']);
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->middleware(['auth', 'throttle:20,1']);

?>

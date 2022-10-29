<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', ['as' => 'users.index', 'uses' => 'SomeController@index']);
Route::get('/users', ['uses' => 'SomeController@index', 'as' => 'users.index']);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index');
Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index'])->name('users.index');

?>

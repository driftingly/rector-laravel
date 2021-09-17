<?php

namespace Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', 'SomeController@methodNotExists');
Route::get('/users', 'SomeControllerNotExists@index');

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('/users', 'SomeController@methodNotExists');
Route::get('/users', 'SomeControllerNotExists@index');

?>

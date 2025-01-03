<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Some' . 'Namespace'], function () {
    Route::get('/users', 'SomeController@index');
});

Route::get('/users', 'SomeController@index');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Some' . 'Namespace'], function () {
    Route::get('/users', 'SomeController@index');
});

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);

?>

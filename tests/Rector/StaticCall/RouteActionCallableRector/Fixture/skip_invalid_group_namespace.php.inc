<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

// the concat should make the rule ignore the group and any routes within it
Route::group(['namespace' => 'Some' . 'Namespace'], function () {
    Route::get('/users', 'SomeController@index');
});

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::get('/users', 'SomeController@index');

    Route::group(['namespace' => 'SomeOther' . 'Namespace'], function () {
        Route::get('/users', 'SomeController@index');
    });
});

Route::get('/users', 'SomeController@index');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

// the concat should make the rule ignore the group and any routes within it
Route::group(['namespace' => 'Some' . 'Namespace'], function () {
    Route::get('/users', 'SomeController@index');
});

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeNamespace\SomeController::class, 'index']);

    Route::group(['namespace' => 'SomeOther' . 'Namespace'], function () {
        Route::get('/users', 'SomeController@index');
    });
});

Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeController::class, 'index']);

?>

<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::group(['namespace' => 'SomeOtherNamespace'], function () {
        Route::get('/users', 'SomeController@index');
    });

    Route::get('/users', 'SomeController@index');
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Fixture;

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'SomeNamespace'], function () {
    Route::group(['namespace' => 'SomeOtherNamespace'], function () {
        Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeNamespace\SomeOtherNamespace\SomeController::class, 'index']);
    });

    Route::get('/users', [\RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\Source\SomeNamespace\SomeController::class, 'index']);
});

?>

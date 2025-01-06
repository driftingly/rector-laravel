<?php

namespace RectorLaravel\Tests\Sets\Laravel80\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('foo/bar', 'FooController@index');

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel80\Fixture;

use Illuminate\Support\Facades\Route;

Route::get('foo/bar', [\App\Http\Controllers\FooController::class, 'index']);

?>

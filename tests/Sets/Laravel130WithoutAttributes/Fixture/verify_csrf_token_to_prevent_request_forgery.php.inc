<?php

namespace RectorLaravel\Tests\Sets\Laravel130WithoutAttributes\Fixture;

Route::post('/foo', function () {})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

class CustomCsrfMiddleware extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    protected $except = [
        'stripe/*',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel130WithoutAttributes\Fixture;

Route::post('/foo', function () {})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

class CustomCsrfMiddleware extends \Illuminate\Foundation\Http\Middleware\PreventRequestForgery
{
    protected $except = [
        'stripe/*',
    ];
}

?>

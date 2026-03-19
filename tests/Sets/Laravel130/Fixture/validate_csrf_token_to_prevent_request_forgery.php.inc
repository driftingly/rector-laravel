<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

Route::post('/bar', function () {})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

class CustomValidateCsrfMiddleware extends \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken
{
    protected $except = [
        'webhooks/*',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

Route::post('/bar', function () {})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);

class CustomValidateCsrfMiddleware extends \Illuminate\Foundation\Http\Middleware\PreventRequestForgery
{
    protected $except = [
        'webhooks/*',
    ];
}

?>

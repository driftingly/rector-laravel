<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

Route::post('/bar', function () {})->withoutMiddleware([ValidateCsrfToken::class]);

class CustomValidateCsrfMiddleware extends ValidateCsrfToken
{
    protected $except = [
        'webhooks/*',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

Route::post('/bar', function () {})->withoutMiddleware([PreventRequestForgery::class]);

class CustomValidateCsrfMiddleware extends PreventRequestForgery
{
    protected $except = [
        'webhooks/*',
    ];
}

?>

<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::post('/foo', function () {})->withoutMiddleware([VerifyCsrfToken::class]);

class CustomCsrfMiddleware extends VerifyCsrfToken
{
    protected $except = [
        'stripe/*',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

Route::post('/foo', function () {})->withoutMiddleware([PreventRequestForgery::class]);

class CustomCsrfMiddleware extends PreventRequestForgery
{
    protected $except = [
        'stripe/*',
    ];
}

?>

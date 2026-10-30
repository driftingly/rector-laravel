<?php

// bootstrap/app.php runs its callbacks while the application boots, before facades are registered

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: $_SERVER['TRUSTED_PROXIES']);
    })
    ->create();

<?php

declare(strict_types=1);

namespace Illuminate\Foundation\Http\Middleware;

if (class_exists('Illuminate\Foundation\Http\Middleware\ValidateCsrfToken')) {
    return;
}

class ValidateCsrfToken {}

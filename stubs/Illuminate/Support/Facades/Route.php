<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

if (class_exists('\Illuminate\Support\Facades\Route')) {
    return;
}

/**
 * @method static \Illuminate\Routing\RouteRegistrar middleware(array|string|null $middleware)
 */
class Route {}

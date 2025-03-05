<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

if (class_exists('\Illuminate\Support\Facades\Route')) {
    return;
}

require_once __DIR__ . '/Facade.php';

/**
 * @method static \Illuminate\Routing\RouteRegistrar middleware(array|string|null $middleware)
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}

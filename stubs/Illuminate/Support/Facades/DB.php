<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Database\Query\Expression;

if (class_exists('\Illuminate\Support\Facades\DB')) {
    return;
}

require_once __DIR__ . '/Facade.php';

/**
 * @method static Expression raw(mixed $value)
 */
class DB extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}

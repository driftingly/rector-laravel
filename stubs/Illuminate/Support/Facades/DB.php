<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Database\Query\Expression;

if (class_exists('\Illuminate\Support\Facades\DB')) {
    return;
}

/**
 * @method static Expression raw(mixed $value)
 */
class DB {}

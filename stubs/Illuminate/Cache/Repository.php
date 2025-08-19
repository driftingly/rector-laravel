<?php

declare(strict_types=1);

namespace Illuminate\Cache;

use ArrayAccess;
use Illuminate\Contracts\Cache\Repository as CacheContract;

if (class_exists('Illuminate\Cache\Repository')) {
    return;
}

class Repository implements ArrayAccess, CacheContract {}

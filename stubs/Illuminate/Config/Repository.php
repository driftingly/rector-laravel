<?php

declare(strict_types=1);

namespace Illuminate\Config;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;

if (class_exists('Illuminate\Config\Repository')) {
    return;
}

class Repository implements ArrayAccess, ConfigContract {}

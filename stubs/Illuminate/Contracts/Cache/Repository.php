<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Cache;

if (interface_exists('Illuminate\Contracts\Cache\Repository')) {
    return;
}

interface Repository {}

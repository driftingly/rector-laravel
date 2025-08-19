<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Config;

if (interface_exists('Illuminate\Contracts\Config\Repository')) {
    return;
}

interface Repository {}

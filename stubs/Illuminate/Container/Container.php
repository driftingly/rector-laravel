<?php

namespace Illuminate\Container;

use ArrayAccess;
use Illuminate\Contracts\Container\Container as ContainerContract;

if (class_exists('Illuminate\Container\Container')) {
    return;
}

class Container implements ArrayAccess, ContainerContract {}

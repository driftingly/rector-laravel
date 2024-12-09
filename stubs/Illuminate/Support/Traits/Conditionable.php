<?php

namespace Illuminate\Support\Traits;

if (trait_exists('Illuminate\Support\Traits\Conditionable')) {
    return;
}

trait Conditionable
{
    public function when($value = null, ?callable $callback = null, ?callable $default = null) {}

    public function unless($value = null, ?callable $callback = null, ?callable $default = null) {}
}

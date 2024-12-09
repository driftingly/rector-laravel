<?php

namespace Illuminate\Support\Traits;

if (trait_exists('Illuminate\Support\Traits\Tappable')) {
    return;
}

trait Tappable
{
    public function tap($callback = null) {}
}

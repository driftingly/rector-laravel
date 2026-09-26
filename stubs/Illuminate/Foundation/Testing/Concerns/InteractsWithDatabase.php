<?php

namespace Illuminate\Foundation\Testing\Concerns;

if (trait_exists('Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase')) {
    return;
}

trait InteractsWithDatabase {}

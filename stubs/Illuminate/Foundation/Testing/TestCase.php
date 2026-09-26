<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

if (class_exists('Illuminate\Foundation\Testing\TestCase')) {
    return;
}

class TestCase
{
    use InteractsWithDatabase;
}

<?php

namespace Illuminate\Database\Eloquent\Factories;

if (trait_exists('Illuminate\Database\Eloquent\Factories\HasFactory')) {
    return;
}

trait HasFactory
{
    public static function factory($count = null, $state = []) {}
}

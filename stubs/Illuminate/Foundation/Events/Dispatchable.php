<?php

namespace Illuminate\Foundation\Events;

if (class_exists('Illuminate\Foundation\Events\Dispatchable')) {
    return;
}
trait Dispatchable
{
    public static function dispatch(...$arguments)
    {
    }

    public static function dispatchIf($boolean, ...$arguments)
    {
    }

    public static function dispatchUnless($boolean, ...$arguments)
    {
    }

    public static function broadcast()
    {
    }
}

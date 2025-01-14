<?php

namespace Illuminate\Database\Eloquent;

if (class_exists('Illuminate\Database\Eloquent\Model')) {
    return;
}

/**
 * @method static creating(\Closure $closure)
 */
abstract class Model
{
    /**
     * Exists in the Illuminate/Database/Eloquent/Concerns/HasTimestamps trait
     * Put here for simplicity
     */
    public function touch($attribute = null)
    {
        return true;
    }
}

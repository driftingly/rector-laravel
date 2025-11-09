<?php

namespace Illuminate\Database\Eloquent\Factories;

if (class_exists('Illuminate\Database\Eloquent\Factories\Factory')) {
    return;
}

/** @template TModel of \Illuminate\Database\Eloquent\Model */
abstract class Factory
{
    /** @var class-string<TModel> */
    protected $model;
}

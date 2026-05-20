<?php

declare(strict_types=1);

namespace Illuminate\Http\Resources\Json;

if (class_exists('Illuminate\Http\Resources\Json\JsonResource')) {
    return;
}

class JsonResource
{
    public static $wrap = 'data';

    protected $preserveKeys = false;

    protected $collects;
}

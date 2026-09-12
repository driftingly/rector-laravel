<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

if (class_exists('Illuminate\Database\Eloquent\Attributes\Table')) {
    return;
}

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    /**
     * Create a new attribute instance.
     */
    public function __construct(
        public string $name,
        public ?string $key = null,
        public ?string $keyType = null,
        public ?bool $incrementing = null,
    ) {}
}

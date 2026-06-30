<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

if (class_exists('Illuminate\Database\Eloquent\Attributes\ObservedBy')) {
    return;
}

#[Attribute(Attribute::TARGET_CLASS)]
class ObservedBy
{
    /**
     * @param  string|list<string>  $observer
     */
    public function __construct(
        public string|array $observer,
    ) {}
}

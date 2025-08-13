<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class NotAModel
{
    public function firstName(): Attribute
    {
        return Attribute::get(fn () => 'test');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}

?>

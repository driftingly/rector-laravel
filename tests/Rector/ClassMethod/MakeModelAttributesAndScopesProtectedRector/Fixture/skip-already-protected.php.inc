<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class User extends Model
{
    protected function firstName(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->first_name));
    }

    protected function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}

?>

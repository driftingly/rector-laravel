<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Source\ParentUser;

class User extends ParentUser
{
    protected function firstName(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->first_name));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}

?>

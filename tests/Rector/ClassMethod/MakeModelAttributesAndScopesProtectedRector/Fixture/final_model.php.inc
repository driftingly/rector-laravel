<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

final class User extends Model
{
    private function firstName(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->first_name));
    }

    private function getFirstNameAttribute() {}

    private function setFirstNameAttribute() {}

    private function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    #[Scope]
    private function verified(Builder $query): Builder
    {
        return $query;
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

final class User extends Model
{
    protected function firstName(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->first_name));
    }

    protected function getFirstNameAttribute() {}

    protected function setFirstNameAttribute() {}

    protected function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    #[Scope]
    protected function verified(Builder $query): Builder
    {
        return $query;
    }
}

?>

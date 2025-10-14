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

    private function scopeInactive(Builder $query): void
    {
        $query->where('active', false);
    }

    private function scopeParam($query): void
    {
        $query->where('active', false);
    }

    private function scopeNone($query)
    {
        $query->where('active', false);
    }

    private function scopeReturn(Builder $query)
    {
        $query->where('active', false);
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

    protected function scopeInactive(Builder $query): void
    {
        $query->where('active', false);
    }

    protected function scopeParam($query): void
    {
        $query->where('active', false);
    }

    protected function scopeNone($query)
    {
        $query->where('active', false);
    }

    protected function scopeReturn(Builder $query)
    {
        $query->where('active', false);
    }

    #[Scope]
    protected function verified(Builder $query): Builder
    {
        return $query;
    }
}

?>

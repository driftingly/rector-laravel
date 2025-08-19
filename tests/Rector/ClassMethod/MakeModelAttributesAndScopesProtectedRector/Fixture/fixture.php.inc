<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class User extends Model
{
    public function firstName(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->first_name));
    }

    public function getFirstNameAttribute() {}

    public function setFirstNameAttribute() {}

    public function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    #[Scope]
    public function verified(Builder $query): Builder
    {
        return $query;
    }

    public function regularMethod(): string
    {
        return 'test';
    }

    public static function scopeFoo(): void {}
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class User extends Model
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

    public function regularMethod(): string
    {
        return 'test';
    }

    public static function scopeFoo(): void {}
}

?>

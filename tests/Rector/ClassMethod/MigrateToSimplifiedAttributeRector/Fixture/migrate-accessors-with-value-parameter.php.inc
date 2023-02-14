<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return ucfirst($value);
        });
    }
}
?>

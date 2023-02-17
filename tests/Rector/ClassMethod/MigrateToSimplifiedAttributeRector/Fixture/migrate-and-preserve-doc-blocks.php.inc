<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    /** some comment */
    public function getFirstNameAttribute()
    {
        return ucfirst($this->first_name);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    /** some comment */
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return ucfirst($this->first_name);
        });
    }
}
?>

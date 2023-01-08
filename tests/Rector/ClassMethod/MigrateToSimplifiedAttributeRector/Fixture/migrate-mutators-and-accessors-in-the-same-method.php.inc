<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
        $this->attributes['first_name_upper'] = strtoupper($value);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return ucfirst($value);
        }, set: function ($value) {
            return ['first_name' => strtolower($value), 'first_name_upper' => strtoupper($value)];
        });
    }
}
?>

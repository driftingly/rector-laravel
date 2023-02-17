<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Set the user's first name.
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Set the user's first name.
     */
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: function ($value) {
            return ['first_name' => strtolower($value)];
        });
    }
}
?>

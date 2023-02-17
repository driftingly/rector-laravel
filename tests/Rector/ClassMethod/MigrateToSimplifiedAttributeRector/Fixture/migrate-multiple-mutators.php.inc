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
        $someCalculation = $value * 2;
        $this->test = 'asdf';
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
    /**
     * Set the user's first name.
     */
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: function ($value) {
            $someCalculation = $value * 2;
            $this->test = 'asdf';
            return ['first_name' => strtolower($value), 'first_name_upper' => strtoupper($value)];
        });
    }
}
?>

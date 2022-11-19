<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}
?>
-----
<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    protected function lastName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return ucfirst($value);
        });
    }
}
?>
<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function getFirstNameAttribute()
    {
        return ucfirst($this->first_name);
    }
}
?>
-----
<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return ucfirst($this->first_name);
        });
    }
}
?>
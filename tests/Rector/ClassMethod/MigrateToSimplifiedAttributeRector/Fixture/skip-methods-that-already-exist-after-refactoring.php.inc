<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function lastName()
    {
        return 'some value';
    }
}

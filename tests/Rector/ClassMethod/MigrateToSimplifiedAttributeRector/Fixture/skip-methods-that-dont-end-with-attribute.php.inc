<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function getLastName($value)
    {
        return ucfirst($value);
    }
}

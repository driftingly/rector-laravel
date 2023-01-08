<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function getAttribute($value)
    {
        return ucfirst($value);
    }

    public function setAttribute($value)
    {
        return ucfirst($value);
    }
}

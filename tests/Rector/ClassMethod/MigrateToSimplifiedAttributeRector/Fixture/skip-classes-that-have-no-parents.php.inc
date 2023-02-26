<?php
class SomeOtherModel
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}

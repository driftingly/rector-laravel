<?php
class SomeModel
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}

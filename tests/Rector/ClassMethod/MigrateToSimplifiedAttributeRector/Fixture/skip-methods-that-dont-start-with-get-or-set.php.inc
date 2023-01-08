<?php
class SomeModel extends \Illuminate\Database\Eloquent\Model
{
    public function notgetLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function notsetLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}
?>

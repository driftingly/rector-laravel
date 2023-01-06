<?php
namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

class NotEloquentModel extends Model
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}
?>

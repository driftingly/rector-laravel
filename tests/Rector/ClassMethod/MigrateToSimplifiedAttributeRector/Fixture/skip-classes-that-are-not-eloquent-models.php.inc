<?php
namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\Fixture;

class SomeModel extends Model
{
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
}

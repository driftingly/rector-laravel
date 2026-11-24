<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

abstract class SkipAbstractModel extends Model
{
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

?>

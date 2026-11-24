<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

class SkipStatementBeforeReturn extends Model
{
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        logger()->info('factory');

        return UserFactory::new();
    }
}

?>

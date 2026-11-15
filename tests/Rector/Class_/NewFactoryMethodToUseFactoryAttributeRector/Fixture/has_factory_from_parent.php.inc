<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

class BaseModel extends Model
{
    use HasFactory;
}

class User extends BaseModel
{
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

class BaseModel extends Model
{
    use HasFactory;
}

#[\Illuminate\Database\Eloquent\Attributes\UseFactory(UserFactory::class)]
class User extends BaseModel
{
}

?>

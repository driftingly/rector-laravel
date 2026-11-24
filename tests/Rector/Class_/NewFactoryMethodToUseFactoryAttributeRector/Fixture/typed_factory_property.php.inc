<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

class User extends Model
{
    use HasFactory;

    protected static string $factory = UserFactory::class;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends Factory {}

#[\Illuminate\Database\Eloquent\Attributes\UseFactory(UserFactory::class)]
class User extends Model
{
    use HasFactory;
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model {}
class User extends Model {}

class Post extends Model
{
    public function country(): HasManyThrough
    {
        return $this->hasManyThrough(Country::class, User::class);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model {}
class User extends Model {}

class Post extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture\Country, \RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\Fixture\User, $this>
     */
    public function country(): HasManyThrough
    {
        return $this->hasManyThrough(Country::class, User::class);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserCollection extends \Illuminate\Database\Eloquent\Collection {}

class User extends Model
{
    /**
     * @param array<int, \Illuminate\Database\Eloquent\Model> $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new UserCollection($models);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserCollection extends \Illuminate\Database\Eloquent\Collection {}

#[\Illuminate\Database\Eloquent\Attributes\CollectedBy(UserCollection::class)]
class User extends Model
{
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class UserCollection extends \Illuminate\Database\Eloquent\Collection {}

class User extends Model
{
    protected $collectionClass = UserCollection::class;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class UserCollection extends \Illuminate\Database\Eloquent\Collection {}

#[\Illuminate\Database\Eloquent\Attributes\CollectedBy(UserCollection::class)]
class User extends Model
{
}

?>

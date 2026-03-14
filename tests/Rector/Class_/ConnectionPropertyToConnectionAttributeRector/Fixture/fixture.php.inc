<?php

namespace RectorLaravel\Tests\Rector\Class_\ConnectionPropertyToConnectionAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'sqlite';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ConnectionPropertyToConnectionAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Connection('sqlite')]
class User extends Model
{
}

?>

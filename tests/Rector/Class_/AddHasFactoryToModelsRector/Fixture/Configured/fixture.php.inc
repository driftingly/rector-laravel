<?php

namespace RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\Fixture\Configured;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\Fixture\Configured;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
}

?>

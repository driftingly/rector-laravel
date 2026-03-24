<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class DoNotRemoveInvalidProperties extends Model
{
    protected $table = 'users';
    public $primaryKey = 'user_id';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users')]
class DoNotRemoveInvalidProperties extends Model
{
    public $primaryKey = 'user_id';
}

?>

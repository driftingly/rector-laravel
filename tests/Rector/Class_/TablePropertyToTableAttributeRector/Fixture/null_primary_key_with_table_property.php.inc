<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NullPrimaryKeyWithTableProperty extends Model
{
    protected $table = 'users';
    protected $primaryKey = null;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users')]
class NullPrimaryKeyWithTableProperty extends Model
{
    protected $primaryKey = null;
}

?>

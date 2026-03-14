<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'users')]
class CombineExistingAttribute extends Model
{
    protected $primaryKey = 'user_id';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'users', key: 'user_id')]
class CombineExistingAttribute extends Model
{
}

?>

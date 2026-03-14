<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'customers')]
class ReplaceExistingAttribute extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'user_id';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(table: 'users', key: 'user_id')]
class ReplaceExistingAttribute extends Model
{
}

?>

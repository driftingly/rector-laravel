<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users', timestamps: false)]
class CombineExistingAttributeWithTimestamps extends Model
{
    protected $primaryKey = 'user_id';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TablePropertyToTableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Table(name: 'users', timestamps: false, key: 'user_id')]
class CombineExistingAttributeWithTimestamps extends Model
{
}

?>

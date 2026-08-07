<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, table: 'role_user');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, table: \RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source\RoleUser::class);
    }
}

?>

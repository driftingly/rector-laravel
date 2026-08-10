<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\User;

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

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\User;

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, table: \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\RoleUser::class);
    }
}

?>

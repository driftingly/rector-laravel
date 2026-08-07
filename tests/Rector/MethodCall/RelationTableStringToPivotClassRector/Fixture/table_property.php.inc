<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->withPivot('expires_at')
            ->orderBy('role_user.expires_at');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, \RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source\RoleUser::class, 'user_id', 'role_id')
            ->withPivot('expires_at')
            ->orderBy('role_user.expires_at');
    }
}

?>

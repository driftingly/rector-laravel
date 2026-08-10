<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Role;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\CommentUser;

class SkipConflictingUsingPivot extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->using(CommentUser::class);
    }
}

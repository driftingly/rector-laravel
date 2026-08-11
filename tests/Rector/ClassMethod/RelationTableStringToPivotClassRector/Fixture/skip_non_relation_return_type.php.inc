<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Role;

class SkipNonRelationReturnType extends Model
{
    public function roles(): array
    {
        return $this->belongsToMany(Role::class, 'role_user')->get()->all();
    }
}

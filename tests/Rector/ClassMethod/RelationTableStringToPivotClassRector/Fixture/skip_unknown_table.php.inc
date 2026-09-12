<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\Permission;

class SkipUnknownTable extends Model
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }
}

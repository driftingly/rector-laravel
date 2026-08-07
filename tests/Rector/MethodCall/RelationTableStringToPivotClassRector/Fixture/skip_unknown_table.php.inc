<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipUnknownTable extends Model
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }
}

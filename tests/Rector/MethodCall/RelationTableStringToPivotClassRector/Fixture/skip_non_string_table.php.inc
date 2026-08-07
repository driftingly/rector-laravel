<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNonStringTable extends Model
{
    public function tags($table)
    {
        return $this->belongsToMany(Tag::class, $table);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}

<?php

namespace RectorLaravel\Tests\Rector\Class_\FillablePropertyToFillableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipEmptyFillable extends Model
{
    protected $fillable = [];
}

?>

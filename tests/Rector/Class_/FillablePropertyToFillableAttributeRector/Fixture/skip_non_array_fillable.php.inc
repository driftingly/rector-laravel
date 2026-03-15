<?php

namespace RectorLaravel\Tests\Rector\Class_\FillablePropertyToFillableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class NonArrayFillable extends Model
{
    protected $fillable = 'name';
}

?>

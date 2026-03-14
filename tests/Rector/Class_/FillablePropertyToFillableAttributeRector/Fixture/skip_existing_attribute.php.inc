<?php

namespace RectorLaravel\Tests\Rector\Class_\FillablePropertyToFillableAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['name', 'email'])]
class SkipExistingAttribute extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];
}

?>

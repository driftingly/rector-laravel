<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;
}

?>

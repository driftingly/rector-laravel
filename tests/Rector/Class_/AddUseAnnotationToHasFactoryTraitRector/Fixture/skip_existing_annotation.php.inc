<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Source\UserFactory> */
    use HasFactory;
}

?>

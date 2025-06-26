<?php

namespace RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\Fixture\Configured;

use Illuminate\Database\Eloquent\Model;

class SkipIfAlreadyPresent extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
}

?>

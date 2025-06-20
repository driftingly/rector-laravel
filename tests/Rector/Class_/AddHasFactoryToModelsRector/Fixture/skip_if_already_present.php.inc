<?php

namespace RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector;

use Illuminate\Database\Eloquent\Model;

class SkipIfAlreadyPresent extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
}

?>

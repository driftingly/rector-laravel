<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutRelationsPropertyToWithoutRelationsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class SkipNotFinal implements ShouldQueue
{
    public $withoutRelations = true;
}

?>

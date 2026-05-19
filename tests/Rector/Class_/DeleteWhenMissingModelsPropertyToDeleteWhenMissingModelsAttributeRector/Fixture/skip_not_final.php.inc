<?php

namespace RectorLaravel\Tests\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class SkipNotFinal implements ShouldQueue
{
    public $deleteWhenMissingModels = true;
}

?>

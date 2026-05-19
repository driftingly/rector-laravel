<?php

namespace RectorLaravel\Tests\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\DeleteWhenMissingModels]
final class SkipExistingAttribute implements ShouldQueue
{
    public $deleteWhenMissingModels = true;
}

?>

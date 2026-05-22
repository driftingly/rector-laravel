<?php

namespace RectorLaravel\Tests\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SkipDeleteWhenMissingModelsFalse implements ShouldQueue
{
    public $deleteWhenMissingModels = false;
}

?>

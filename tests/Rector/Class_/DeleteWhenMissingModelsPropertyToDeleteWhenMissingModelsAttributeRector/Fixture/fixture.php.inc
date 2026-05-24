<?php

namespace RectorLaravel\Tests\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProcessPodcast implements ShouldQueue
{
    public $deleteWhenMissingModels = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\DeleteWhenMissingModelsPropertyToDeleteWhenMissingModelsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\DeleteWhenMissingModels]
final class ProcessPodcast implements ShouldQueue
{
}

?>

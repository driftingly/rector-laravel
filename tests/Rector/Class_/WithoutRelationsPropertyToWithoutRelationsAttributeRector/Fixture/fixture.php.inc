<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutRelationsPropertyToWithoutRelationsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProcessPodcast implements ShouldQueue
{
    public $withoutRelations = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutRelationsPropertyToWithoutRelationsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\WithoutRelations]
final class ProcessPodcast implements ShouldQueue
{
}

?>

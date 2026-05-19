<?php

namespace RectorLaravel\Tests\Rector\Class_\DelayPropertyToDelayAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProcessPodcast implements ShouldQueue
{
    public $delay = 10;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\DelayPropertyToDelayAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Delay(10)]
final class ProcessPodcast implements ShouldQueue
{
}

?>

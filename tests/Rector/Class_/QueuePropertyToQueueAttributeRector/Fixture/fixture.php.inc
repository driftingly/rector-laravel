<?php

namespace RectorLaravel\Tests\Rector\Class_\QueuePropertyToQueueAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $queue = 'podcasts';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\QueuePropertyToQueueAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Queue('podcasts')]
final class SomeJob implements ShouldQueue
{
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\QueuePropertyToQueueAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $queue = 'podcasts';
}

?>

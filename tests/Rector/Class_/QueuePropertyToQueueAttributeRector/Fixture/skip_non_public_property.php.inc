<?php

namespace RectorLaravel\Tests\Rector\Class_\QueuePropertyToQueueAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedQueueJob implements ShouldQueue
{
    protected $queue = 'podcasts';
}

?>

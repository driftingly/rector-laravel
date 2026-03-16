<?php

namespace RectorLaravel\Tests\Rector\Class_\QueuePropertyToQueueAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Queue;

#[Queue('podcasts')]
final class SkipAlreadyAttributedQueueJob implements ShouldQueue
{
    public $queue = 'podcasts';
}

?>

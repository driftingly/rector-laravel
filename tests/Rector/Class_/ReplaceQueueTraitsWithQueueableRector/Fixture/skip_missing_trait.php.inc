<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SkipMissingTrait
{
    use Dispatchable, InteractsWithQueue, Queueable;
}

?>

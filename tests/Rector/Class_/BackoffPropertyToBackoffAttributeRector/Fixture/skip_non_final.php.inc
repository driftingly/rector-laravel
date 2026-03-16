<?php

namespace RectorLaravel\Tests\Rector\Class_\BackoffPropertyToBackoffAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $backoff = 3;
}

?>

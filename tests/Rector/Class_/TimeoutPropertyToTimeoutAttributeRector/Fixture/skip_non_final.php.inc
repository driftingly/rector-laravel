<?php

namespace RectorLaravel\Tests\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $timeout = 120;
}

?>

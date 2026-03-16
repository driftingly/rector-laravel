<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $failOnTimeout = true;
}

?>

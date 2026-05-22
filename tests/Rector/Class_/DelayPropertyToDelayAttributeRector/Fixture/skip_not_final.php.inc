<?php

namespace RectorLaravel\Tests\Rector\Class_\DelayPropertyToDelayAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class SkipNotFinal implements ShouldQueue
{
    public $delay = 10;
}

?>

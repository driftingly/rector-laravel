<?php

namespace RectorLaravel\Tests\Rector\Class_\DelayPropertyToDelayAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SkipDelayString implements ShouldQueue
{
    public $delay = '10';
}

?>

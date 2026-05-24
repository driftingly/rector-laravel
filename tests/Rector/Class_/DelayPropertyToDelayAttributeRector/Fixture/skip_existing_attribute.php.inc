<?php

namespace RectorLaravel\Tests\Rector\Class_\DelayPropertyToDelayAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Delay(10)]
final class SkipExistingAttribute implements ShouldQueue
{
    public $delay = 10;
}

?>

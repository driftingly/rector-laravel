<?php

namespace RectorLaravel\Tests\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Timeout;

#[Timeout(120)]
final class SkipAlreadyAttributedTimeoutJob implements ShouldQueue
{
    public $timeout = 120;
}

?>

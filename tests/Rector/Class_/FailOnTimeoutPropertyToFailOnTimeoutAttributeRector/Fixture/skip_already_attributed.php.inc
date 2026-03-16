<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\FailOnTimeout;

#[FailOnTimeout(true)]
final class SkipAlreadyAttributedFailOnTimeoutJob implements ShouldQueue
{
    public $failOnTimeout = true;
}

?>

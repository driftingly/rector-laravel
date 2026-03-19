<?php

namespace RectorLaravel\Tests\Rector\Class_\BackoffPropertyToBackoffAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Backoff;

#[Backoff(3)]
final class SkipAlreadyAttributedBackoffJob implements ShouldQueue
{
    public $backoff = 3;
}

?>

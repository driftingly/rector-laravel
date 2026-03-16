<?php

namespace RectorLaravel\Tests\Rector\Class_\BackoffPropertyToBackoffAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedBackoffJob implements ShouldQueue
{
    protected $backoff = 3;
}

?>

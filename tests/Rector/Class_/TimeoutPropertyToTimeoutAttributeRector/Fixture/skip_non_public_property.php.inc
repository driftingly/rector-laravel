<?php

namespace RectorLaravel\Tests\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedTimeoutJob implements ShouldQueue
{
    protected $timeout = 120;
}

?>

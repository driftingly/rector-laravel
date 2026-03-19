<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedFailOnTimeoutJob implements ShouldQueue
{
    protected $failOnTimeout = true;
}

?>

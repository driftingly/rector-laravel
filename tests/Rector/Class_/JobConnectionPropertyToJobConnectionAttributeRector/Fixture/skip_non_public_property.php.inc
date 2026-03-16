<?php

namespace RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedConnectionJob implements ShouldQueue
{
    protected $connection = 'redis';
}

?>

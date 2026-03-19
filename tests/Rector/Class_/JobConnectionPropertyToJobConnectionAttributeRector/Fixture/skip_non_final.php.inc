<?php

namespace RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $connection = 'redis';
}

?>

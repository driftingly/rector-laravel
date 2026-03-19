<?php

namespace RectorLaravel\Tests\Rector\Class_\TriesPropertyToTriesAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $tries = 3;
}

?>

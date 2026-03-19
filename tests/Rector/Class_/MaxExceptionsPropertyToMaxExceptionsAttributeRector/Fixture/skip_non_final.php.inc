<?php

namespace RectorLaravel\Tests\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $maxExceptions = 3;
}

?>

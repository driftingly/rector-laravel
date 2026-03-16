<?php

namespace RectorLaravel\Tests\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedMaxExceptionsJob implements ShouldQueue
{
    protected $maxExceptions = 3;
}

?>

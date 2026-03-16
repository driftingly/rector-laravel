<?php

namespace RectorLaravel\Tests\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\MaxExceptions;

#[MaxExceptions(3)]
final class SkipAlreadyAttributedMaxExceptionsJob implements ShouldQueue
{
    public $maxExceptions = 3;
}

?>

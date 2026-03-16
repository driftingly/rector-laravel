<?php

namespace RectorLaravel\Tests\Rector\Class_\TriesPropertyToTriesAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Tries;

#[Tries(3)]
final class SkipAlreadyAttributedTriesJob implements ShouldQueue
{
    public $tries = 3;
}

?>

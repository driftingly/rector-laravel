<?php

namespace RectorLaravel\Tests\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\UniqueFor;

#[UniqueFor(1800)]
final class SkipAlreadyAttributedUniqueForJob implements ShouldQueue
{
    public $uniqueFor = 1800;
}

?>

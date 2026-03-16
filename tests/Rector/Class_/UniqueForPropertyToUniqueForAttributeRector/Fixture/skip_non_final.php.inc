<?php

namespace RectorLaravel\Tests\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

class NonFinalJob implements ShouldQueue
{
    public $uniqueFor = 3600;
}

?>

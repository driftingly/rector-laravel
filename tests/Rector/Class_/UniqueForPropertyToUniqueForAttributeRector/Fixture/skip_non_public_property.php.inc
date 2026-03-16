<?php

namespace RectorLaravel\Tests\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedUniqueForJob implements ShouldQueue
{
    protected $uniqueFor = 3600;
}

?>

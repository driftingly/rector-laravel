<?php

namespace RectorLaravel\Tests\Rector\Class_\TriesPropertyToTriesAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class ProtectedTriesJob implements ShouldQueue
{
    protected $tries = 3;
}

?>

<?php

namespace RectorLaravel\Tests\Rector\Class_\TriesPropertyToTriesAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $tries = 3;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TriesPropertyToTriesAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Tries(3)]
final class SomeJob implements ShouldQueue
{
}

?>

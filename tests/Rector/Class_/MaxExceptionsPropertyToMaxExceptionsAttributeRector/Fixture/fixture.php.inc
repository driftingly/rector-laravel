<?php

namespace RectorLaravel\Tests\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $maxExceptions = 3;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\MaxExceptionsPropertyToMaxExceptionsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\MaxExceptions(3)]
final class SomeJob implements ShouldQueue
{
}

?>

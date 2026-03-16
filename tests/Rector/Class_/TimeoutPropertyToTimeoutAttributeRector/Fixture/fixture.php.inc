<?php

namespace RectorLaravel\Tests\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $timeout = 120;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\TimeoutPropertyToTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Timeout(120)]
final class SomeJob implements ShouldQueue
{
}

?>

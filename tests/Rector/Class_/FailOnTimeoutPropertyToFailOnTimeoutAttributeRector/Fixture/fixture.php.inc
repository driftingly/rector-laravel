<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $failOnTimeout = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnTimeoutPropertyToFailOnTimeoutAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\FailOnTimeout(true)]
final class SomeJob implements ShouldQueue
{
}

?>

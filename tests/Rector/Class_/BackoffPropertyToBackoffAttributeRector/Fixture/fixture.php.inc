<?php

namespace RectorLaravel\Tests\Rector\Class_\BackoffPropertyToBackoffAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $backoff = 3;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\BackoffPropertyToBackoffAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Backoff(3)]
final class SomeJob implements ShouldQueue
{
}

?>

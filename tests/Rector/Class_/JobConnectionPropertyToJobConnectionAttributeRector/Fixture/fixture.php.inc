<?php

namespace RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $connection = 'redis';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\Connection('redis')]
final class SomeJob implements ShouldQueue
{
}

?>

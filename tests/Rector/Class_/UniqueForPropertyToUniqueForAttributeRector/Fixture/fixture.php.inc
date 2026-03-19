<?php

namespace RectorLaravel\Tests\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SomeJob implements ShouldQueue
{
    public $uniqueFor = 1800;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\UniqueForPropertyToUniqueForAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\UniqueFor(1800)]
final class SomeJob implements ShouldQueue
{
}

?>

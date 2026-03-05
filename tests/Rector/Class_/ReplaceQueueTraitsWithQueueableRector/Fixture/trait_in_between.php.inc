<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TraitInBetween
{
    use Dispatchable;
    use SomeOtherTrait;
    use InteractsWithQueue;
    use Queueable;
    use AnotherTrait;
    use SerializesModels;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Foundation\Queue\Queueable;

class TraitInBetween
{
    use Queueable;
    use SomeOtherTrait;
    use AnotherTrait;
}

?>

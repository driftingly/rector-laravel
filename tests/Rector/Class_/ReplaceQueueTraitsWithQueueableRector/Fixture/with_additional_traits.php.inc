<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobWithAdditionalTraits
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SomeOtherTrait;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobWithAdditionalTraits
{
    use Queueable, SomeOtherTrait;
}

?>

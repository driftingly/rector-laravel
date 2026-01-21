<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SomeJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Foundation\Queue\Queueable;

class SomeJob
{
    use Queueable;
}

?>

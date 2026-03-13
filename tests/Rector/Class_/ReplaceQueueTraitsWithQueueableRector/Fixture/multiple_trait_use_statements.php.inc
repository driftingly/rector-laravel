<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MultipleTraitUseStatements
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\Fixture;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MultipleTraitUseStatements
{
    use \Illuminate\Foundation\Queue\Queueable;
}

?>

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source;

use Illuminate\Foundation\Bus\Dispatchable;
use stdClass;

class ClassWithDispatchableTrait extends stdClass
{
    use Dispatchable;
}

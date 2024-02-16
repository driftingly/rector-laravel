<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source;

class ClassWithDispatchableTrait
{
    use Illuminate\Foundation\Bus\Dispatchable;
}

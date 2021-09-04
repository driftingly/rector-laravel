<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector\Source;

use Illuminate\Foundation\Bus\Dispatchable;

class Job
{
    use Dispatchable;
}

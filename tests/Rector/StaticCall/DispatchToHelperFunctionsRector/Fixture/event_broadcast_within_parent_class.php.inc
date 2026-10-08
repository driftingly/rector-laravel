<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable;

abstract class ParentBroadcastEvent
{
    use Dispatchable;

    public function announce()
    {
        self::broadcast();
        static::broadcast();
    }
}

class ChildBroadcastEvent extends ParentBroadcastEvent
{
    public function announceFromParent()
    {
        parent::broadcast();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable;

abstract class ParentBroadcastEvent
{
    use Dispatchable;

    public function announce()
    {
        broadcast(new static());
        broadcast(new static());
    }
}

class ChildBroadcastEvent extends ParentBroadcastEvent
{
    public function announceFromParent()
    {
        broadcast(new static());
    }
}

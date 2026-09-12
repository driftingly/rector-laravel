<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable as EventDispatchable;

class ParentEventDispatchable
{
    use EventDispatchable;

    public function announce()
    {
        self::dispatch();
    }
}

class ChildEventDispatchable extends ParentEventDispatchable
{
    public function announceFromParent()
    {
        parent::dispatch();
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\DispatchToHelperFunctionsRector\Fixture;

use Illuminate\Foundation\Events\Dispatchable as EventDispatchable;

class ParentEventDispatchable
{
    use EventDispatchable;

    public function announce()
    {
        event(new static());
    }
}

class ChildEventDispatchable extends ParentEventDispatchable
{
    public function announceFromParent()
    {
        event(new static());
    }
}

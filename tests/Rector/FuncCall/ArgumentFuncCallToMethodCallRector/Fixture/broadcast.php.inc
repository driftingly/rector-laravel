<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Broadcast
{
    public function action()
    {
        return broadcast('template.blade');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Broadcast
{
    public function __construct(private readonly \Illuminate\Contracts\Broadcasting\Factory $factory)
    {
    }
    public function action()
    {
        return $this->factory->event('template.blade');
    }
}

?>

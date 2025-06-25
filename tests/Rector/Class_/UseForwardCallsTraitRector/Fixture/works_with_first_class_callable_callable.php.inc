<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardCallsTraitRector\Fixture;

class WorksWithFirstClassCallableCallable
{
    public function __call($method, $args)
    {
        return call_user_func_array($this->foo(...), $args);
    }

    private function foo()
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardCallsTraitRector\Fixture;

class WorksWithFirstClassCallableCallable
{
    use \Illuminate\Support\Traits\ForwardCalls;
    public function __call($method, $args)
    {
        return $this->forwardCallTo($this, 'foo', $args);
    }

    private function foo()
    {
    }
}

?>

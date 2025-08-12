<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardsCallsTraitRector\Fixture;

class Fixture
{
    private SomeCall $foo;

    public function __call($method, $args)
    {
        return call_user_func_array([$this->foo, $method], $args);
    }

    public function another($arg)
    {
        return call_user_func([$this->foo, 'another'], 'a', $arg);
    }

    public function foobar($arg)
    {
        return call_user_func([$this->foo, 'another']);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardsCallsTraitRector\Fixture;

class Fixture
{
    use \Illuminate\Support\Traits\ForwardsCalls;
    private SomeCall $foo;

    public function __call($method, $args)
    {
        return $this->forwardCallTo($this->foo, $method, $args);
    }

    public function another($arg)
    {
        return $this->forwardCallTo($this->foo, 'another', ['a', $arg]);
    }

    public function foobar($arg)
    {
        return $this->forwardCallTo($this->foo, 'another', []);
    }
}

?>

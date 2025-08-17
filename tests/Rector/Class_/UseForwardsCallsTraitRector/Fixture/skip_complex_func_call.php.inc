<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardsCallsTraitRector\Fixture;

class SkipComplexFuncCall
{
    public function __call($method, $args)
    {
        return call_user_func($this->foo, $args);
    }
}

?>

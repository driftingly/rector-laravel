<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardCallsTraitRector\Fixture;

class SkipComplexFuncCall
{
    public function __call($method, $args)
    {
        return call_user_func($this->foo, $args);
    }
}

?>

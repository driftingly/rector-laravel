<?php

namespace RectorLaravel\Tests\Rector\Class_\UseForwardCallsTraitRector\Fixture;

class SkipFirstClassCallableUse
{
    public function __call($method, $args)
    {
        return call_user_func(...);
    }
}

?>

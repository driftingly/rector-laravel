<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToNewRector\Fixture;

class SkipUnconfiguredAndFirstClassCallable
{
    public function run()
    {
        $callable = collect(...);

        return array_values([1, 2, 3]);
    }
}

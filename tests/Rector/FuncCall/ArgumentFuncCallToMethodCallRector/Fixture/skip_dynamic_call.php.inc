<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class SkipDynamicCall
{
    public function action(callable $route)
    {
        return $route('template.blade');
    }
}

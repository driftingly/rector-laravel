<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Source\ParentWithFinalConstructor;

class SkipFinalParentConstructor extends ParentWithFinalConstructor
{
    public function action()
    {
        return route('template.blade');
    }
}

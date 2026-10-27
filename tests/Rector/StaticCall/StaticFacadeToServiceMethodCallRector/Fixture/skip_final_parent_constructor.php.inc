<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Source\ParentWithFinalConstructor;

class SkipFinalParentConstructor extends ParentWithFinalConstructor
{
    public function store()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class SkipWithNoArg
{
    public function run()
    {
        return app()->isProduction();
    }
}

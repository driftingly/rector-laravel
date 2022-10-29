<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class SkipWithArgs
{
    public function run()
    {
        return app('translator', 'some');
    }
}

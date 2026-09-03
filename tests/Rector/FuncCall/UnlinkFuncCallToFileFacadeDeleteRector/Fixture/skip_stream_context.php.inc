<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class SkipStreamContext
{
    public function run(string $path, $context)
    {
        unlink($path, $context);
    }
}

?>

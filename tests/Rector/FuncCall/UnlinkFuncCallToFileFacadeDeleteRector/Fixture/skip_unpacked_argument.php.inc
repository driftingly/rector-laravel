<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class SkipUnpackedArgument
{
    public function run(array $arguments)
    {
        unlink(...$arguments);
    }
}

?>

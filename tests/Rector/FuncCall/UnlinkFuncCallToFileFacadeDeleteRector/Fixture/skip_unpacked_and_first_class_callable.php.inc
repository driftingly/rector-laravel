<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class SkipUnpackedAndFirstClassCallable
{
    public function run(array $arguments, array $paths)
    {
        unlink(...$arguments);

        array_map(unlink(...), $paths);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class FirstClassCallable
{
    public function run(array $paths)
    {
        array_map(unlink(...), $paths);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class FirstClassCallable
{
    public function run(array $paths)
    {
        array_map(\Illuminate\Support\Facades\File::delete(...), $paths);
    }
}

?>

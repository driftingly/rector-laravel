<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class NamedArgument
{
    public function run(string $path)
    {
        unlink(filename: $path);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class NamedArgument
{
    public function run(string $path)
    {
        \Illuminate\Support\Facades\File::delete($path);
    }
}

?>

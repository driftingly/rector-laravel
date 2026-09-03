<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class ErrorSuppressed
{
    public function run(string $path)
    {
        @unlink($path);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class ErrorSuppressed
{
    public function run(string $path)
    {
        @\Illuminate\Support\Facades\File::delete($path);
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class Fixture
{
    public function run(string $path)
    {
        unlink($path);
        unlink(storage_path('app/temp.txt'));

        if (! unlink($path)) {
            return false;
        }

        return \unlink($path);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\Fixture;

class Fixture
{
    public function run(string $path)
    {
        \Illuminate\Support\Facades\File::delete($path);
        \Illuminate\Support\Facades\File::delete(storage_path('app/temp.txt'));

        if (! \Illuminate\Support\Facades\File::delete($path)) {
            return false;
        }

        return \Illuminate\Support\Facades\File::delete($path);
    }
}

?>

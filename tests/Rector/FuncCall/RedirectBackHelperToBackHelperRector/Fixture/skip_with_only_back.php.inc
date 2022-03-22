<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>

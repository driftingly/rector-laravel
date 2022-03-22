<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        return redirect()->back()->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>

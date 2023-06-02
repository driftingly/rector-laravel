<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        $today = now()->addDay()->startOfDay();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        $today = now()->addDay()->startOfDay();
    }
}

?>

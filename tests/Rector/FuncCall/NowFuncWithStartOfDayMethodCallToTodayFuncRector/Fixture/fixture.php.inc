<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->startOfDay()->addDay();
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
        $today = today();
        $tomorrow = today()->addDay();
    }
}

?>

<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->startOfDay()->addDay();
        $utcToday = now('UTC')->startOfDay();
        $utcTomorrow = now(tz: 'UTC')->startOfDay()->addDay();
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
        $utcToday = today('UTC');
        $utcTomorrow = today(tz: 'UTC')->addDay();
    }
}

?>

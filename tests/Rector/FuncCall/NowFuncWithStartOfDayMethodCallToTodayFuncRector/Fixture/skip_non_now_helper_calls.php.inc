<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        $now->startOfDay();
        $this->now->startOfDay();
        $this->now()->startOfDay();

        now()->startOfDay();
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
        $now->startOfDay();
        $this->now->startOfDay();
        $this->now()->startOfDay();

        today();
    }
}

?>

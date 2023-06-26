<?php

namespace RectorLaravel\Tests\Rector\FuncCall\SleepFuncToSleepStaticCallRector\Fixture;

class Fixture
{
    public function store()
    {
        sleep(5);
        usleep(5);
        return true;
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\SleepFuncToSleepStaticCallRector\Fixture;

class Fixture
{
    public function store()
    {
        \Illuminate\Support\Sleep::sleep(5);
        \Illuminate\Support\Sleep::usleep(5);
        return true;
    }
}

?>

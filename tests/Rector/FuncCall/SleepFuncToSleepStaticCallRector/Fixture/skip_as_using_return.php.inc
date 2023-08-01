<?php

namespace RectorLaravel\Tests\Rector\FuncCall\SleepFuncToSleepStaticCallRector\Fixture;

class SkipAsUsingReturn
{
    public function run()
    {
        $var = sleep(5);

        if (sleep(5)) {
            return true;
        }

        while(sleep(5)) {
            return true;
        }
    }
}

?>

<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\Fixture;

class SkipAsIfCondition
{
    public function run()
    {
        if (dd('Tests')) {

        }

        return true;
    }
}

?>

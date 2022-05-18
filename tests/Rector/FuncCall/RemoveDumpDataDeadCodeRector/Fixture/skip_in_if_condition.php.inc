<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\Fixture;

class SkipWithArguments
{
    public function run()
    {
        if (dd('Tests')) {

        }

        return true;
    }
}

?>

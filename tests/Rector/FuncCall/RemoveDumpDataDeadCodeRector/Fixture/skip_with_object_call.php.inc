<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithObjectCall
{
    public function run(Object $object)
    {
        $object->dd();

        return true;
    }
}

?>

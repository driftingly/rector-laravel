<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>

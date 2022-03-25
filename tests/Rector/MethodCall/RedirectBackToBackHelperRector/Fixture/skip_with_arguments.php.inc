<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return redirect('/destination')->back()->with('error', 'Incorrect credential.');
    }
}

?>

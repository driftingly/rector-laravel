<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

class SkipWithOnlyBack
{
    public function run()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>

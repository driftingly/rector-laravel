<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

class SkipWithArguments
{
    public function run()
    {
        return redirect('/destination')->back()->with('error', 'Incorrect credential.');
    }
}

?>

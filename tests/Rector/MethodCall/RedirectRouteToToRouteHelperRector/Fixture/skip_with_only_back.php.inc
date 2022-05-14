<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

class SkipWithOnlyBack
{
    public function run()
    {
        return to_route('home')->with('error', 'Incorrect credential.');
    }
}

?>

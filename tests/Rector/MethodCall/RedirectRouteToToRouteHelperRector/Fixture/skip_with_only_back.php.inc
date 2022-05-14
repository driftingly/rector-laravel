<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectRouteToToRouteHelperRector\Fixture;

class SkipWithOnlyBack
{
    public function run()
    {
        return to_route('home')->with('error', 'Incorrect credential.');
    }
}

?>

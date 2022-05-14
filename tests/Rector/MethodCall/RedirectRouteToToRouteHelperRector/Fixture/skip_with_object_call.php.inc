<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectRouteToToRouteHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithObjectCall
{
    public function run(Redirect $redirect)
    {
        return $redirect->route('home')->with('error', 'Incorrect credential.');
    }
}

?>

<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithObjectCall
{
    public function run(Redirect $redirect)
    {
        return $redirect->back()->with('error', 'Incorrect credential.');
    }
}

?>

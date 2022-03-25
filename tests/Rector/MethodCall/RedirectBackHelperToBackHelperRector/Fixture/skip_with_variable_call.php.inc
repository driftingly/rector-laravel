<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithStaticCall
{
    public function run(Redirect $redirect)
    {
        return $redirect->back()->with('error', 'Incorrect credential.');
    }
}

?>

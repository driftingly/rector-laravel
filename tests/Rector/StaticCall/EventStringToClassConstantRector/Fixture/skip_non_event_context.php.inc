<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

view('auth.login');
route('auth.login');
redirect()->route('auth.logout');

$viewName = 'auth.login';

class SomeController
{
    public function show()
    {
        return view('auth.login');
    }
}

?>

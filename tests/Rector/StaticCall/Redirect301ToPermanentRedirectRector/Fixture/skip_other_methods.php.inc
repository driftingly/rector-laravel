<?php

namespace RectorLaravel\Tests\Rector\StaticCall\Redirect301ToPermanentRedirectRector\Fixture;

class SkipOtherMethods
{
    public function run()
    {
        \Illuminate\Routing\Route::localizedRedirect('/foo', '/bar', 301);
        \Illuminate\Support\Facades\Route::localizedRedirect('/foo', '/bar', 301);
    }
}

?>

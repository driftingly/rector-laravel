<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Http\Request;

class SkipFirstClassCallable
{
    public function handle(Request $request)
    {
        $callable = $request->get(...);
    }
}
?>
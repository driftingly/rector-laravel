<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Support\Facades\Request as RequestFacade;

class SkipNotMatchingStaticCall
{
    public function handle(Request $request)
    {
        $name = RequestFacade::getFoo('name');
        $name = \Request::get('name');
    }
}
?>

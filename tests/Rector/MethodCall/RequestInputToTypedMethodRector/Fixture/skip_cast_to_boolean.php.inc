<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class CastToBoolean
{
    public function store(Request $request)
    {
        $isActive = (bool) $request->input('is_active');
        $enabled = (bool) $request->get('enabled');
    }
}

?>

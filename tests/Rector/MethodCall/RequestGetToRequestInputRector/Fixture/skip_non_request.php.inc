<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

class SkipNonRequest
{
    public function handle($someObject)
    {
        $name = $someObject->get('name');
    }
}
?>
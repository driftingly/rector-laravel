<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeOtherReceivers
{
    public function store(Request $request, \Symfony\Component\HttpFoundation\Request $symfony, \Symfony\Component\HttpFoundation\InputBag $bag)
    {
        $query = (int) $request->query->get('age');
        $body = (float) $request->request->get('price');
        $header = (string) $request->headers->get('name');
        $symfonyValue = (int) $symfony->get('age');
        $bagValue = (int) $bag->get('age');
    }
}

?>

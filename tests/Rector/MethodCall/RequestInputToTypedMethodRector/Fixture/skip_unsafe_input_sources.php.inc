<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestInputToTypedMethodRector\Fixture;

use Illuminate\Http\Request;

class SkipUnsafeInputSources
{
    public function store(Request $request, string $key)
    {
        $age = (int) $request->get('age');
        $name = (string) $request->get('name', '');
        $price = (float) $request->get('price');
        $nested = (int) $request->get('user.age');
        $wildcard = (int) $request->get('users.*.age');
        $dynamic = (int) $request->get($key);
        $data = (int) $request->data('age');
        $offset = (int) $request['age'];
        $property = (float) $request->price;
        $dynamicProperty = (string) $request->{$key};
        $offsetNameFallback = (string) ($request['name'] ?? null);
        $offsetFallback = (int) ($request['age'] ?? null);
        $offsetPriceFallback = (float) ($request['price'] ?? null);
        $offsetActiveFallback = (bool) ($request['is_active'] ?? null);
        $propertyFallback = (string) ($request->name ?? null);
    }
}

?>
